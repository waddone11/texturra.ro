<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\FamilyType;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Support\Facades\Storage;

class EmagMarketplaceController_copy extends Controller
{
    protected $apiUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->apiUrl = config('app.emag_api_url');
        $this->username = config('app.emag_username');
        $this->password = config('app.emag_password');
    }

    /**
     * Fetch and organize all categories with pagination.
     */
    public function getCategories()
    {
        //set_time_limit(0);
        $currentPage = 1; // Start from the first page
        $itemsPerPage = 100; // Maximum allowed value per the API documentation

        try {
            $authHash = base64_encode($this->username . ':' . $this->password);
            $hasMorePages = true;

            while ($hasMorePages) {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHash,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl . '/category/read', [
                    'currentPage' => $currentPage,
                    'itemsPerPage' => $itemsPerPage,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $categories = $data['results'];

                    if (!empty($categories)) {
                        // Insert categories into the database
                        $this->insertCategories($categories);

                        // Move to the next page
                        $currentPage++;
                    } else {
                        // No more categories
                        $hasMorePages = false;
                    }
                } else {
                    return response()->json([
                        'error' => 'Failed to fetch categories',
                        'details' => $response->body(),
                    ], $response->status());
                }
            }

            return response()->json(['message' => 'All categories imported successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function fixCategoryParents()
    {
        try {
            // Retrieve all categories
            $categories = Category::all();

            foreach ($categories as $category) {
                // Update parent_id based on emag_parent_id
                if ($category->emag_parent_id) {
                    $parent = Category::where('emag_id', $category->emag_parent_id)->first();
                    if ($parent) {
                        $category->parent_id = $parent->id;
                    }
                }

                // Update status based on is_allowed
                $category->status = $category->is_allowed ? 1 : 0;

                // Save the updated category
                $category->save();
            }

            return response()->json(['message' => 'Category parents and statuses updated successfully!']);
        } catch (\Exception $e) {
            \Log::error('Error fixing category parents: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fixing category parents',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function fetchProducts()
    {
        //set_time_limit(0);
        try {
            $authHash = base64_encode($this->username . ':' . $this->password);
            $currentPage = 1;
            $itemsPerPage = 100; // Adjust this value based on API limits

            do {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHash,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl . '/product_offer/read', [
                    'currentPage' => $currentPage,
                    'itemsPerPage' => $itemsPerPage,
                ]);

                if (!$response->successful()) {
                    return response()->json([
                        'error' => 'Failed to fetch products',
                        'details' => $response->body(),
                    ], $response->status());
                }

                $data = $response->json();

                $products = $data['results'];
                foreach ($products as $product) {
                    Product::updateOrCreate(
                        ['emag_id' => $product['id']],
                        [
                            'name' => $product['name'],
                            'description' => $this->sanitizeDescription($product['description']),
                            'price' => $product['sale_price'],
                            'emag_price' => $product['sale_price'],
                            'currency' => $product['currency'],
                            'category_id' => $this->mapEmagCategoryToLocal($product['category_id']),
                            'emag_category_id' => $product['category_id'],
                            'brand_name' => $product['brand_name'] ?? null,
                            'part_number' => $product['part_number'] ?? null,
                            'general_stock' => $product['general_stock'] ?? 0,
                            'status' => $product['status'],
                            'images_emag' => json_encode($product['images'] ?? []),
                            'images_emag2' => json_encode($product['images'] ?? []),
                            'characteristics' => json_encode($product['characteristics'] ?? []),
                            'attachments' => json_encode($product['attachments'] ?? []),
                            'offer_details' => json_encode($product['offer_details'] ?? []),
                            'barcode' => $product['barcode'][0] ?? null,
                            'ean' => $product['ean'][0] ?? null,
                            'ownership' => $product['ownership'] ?? 0,
                            'min_sale_price' => $product['min_sale_price'] ?? 0,
                            'max_sale_price' => $product['max_sale_price'] ?? 0,
                            'recommended_price' => $product['recommended_price'] ?? 0,
                            'product_code' => $product['part_number'] ?? 'EMAG-' . $product['id'],
                        ]
                    );

                }

                $currentPage++;
            } while (!empty($products));

            return response()->json(['message' => 'Products imported successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function sanitizeDescription($description)
    {
        return preg_replace('/<img.*?>/', '', $description);
    }


    private function mapEmagCategoryToLocal($emagCategoryId)
    {
        $category = Category::where('emag_id', $emagCategoryId)->first();
        return $category ? $category->id : null;
    }


    /**
     * Handle JSON fields to ensure valid JSON.
     *
     * @param mixed $data
     * @return string
     */
    protected function handleJsonField($data)
    {
        if (is_string($data)) {
            // If already a JSON string, decode and re-encode to validate
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return json_encode($decoded);
            }
        } elseif (is_array($data)) {
            // If an array, encode directly
            return json_encode($data);
        }

        // Return an empty JSON array if invalid or empty
        return json_encode([]);
    }



    /**
     * Process fetched products.
     *
     * @param array $products
     */
    private function processProducts(array $products)
    {
        foreach ($products as $product) {
            // Example: Log product names (customize as needed)
            \Log::info("Processing product: " . $product['name']);
            // You can also save them into the database here.
        }
    }


    /**
 * Insert categories, characteristics, and family types into the database.
 */
    private function insertCategories($categories)
    {
        foreach ($categories as $category) {
            // Generate a unique slug
            $slugBase = Str::slug($category['name']);
            $uniqueSlug = $this->generateUniqueSlug($slugBase);
            //dd($category['is_allowed']);
            // Insert or update the category
            $dbCategory = Category::updateOrCreate(
                ['emag_id' => $category['id']],
                [
                    'name' => $category['name'],
                    'slug' => $uniqueSlug,
                    'description' => null,
                    'emag_parent_id' => $category['parent_id'],
                    'parent_id' => $this->getParentId($category['parent_id']),
                    'is_ean_mandatory' => $category['is_ean_mandatory'],
                    'is_warranty_mandatory' => $category['is_warranty_mandatory'],
                    'is_allowed' => $category['is_allowed'],
                ]
            );

            // Insert characteristics and family types as before
            $this->insertCharacteristics($category['characteristics'], $dbCategory->id);
            $this->insertFamilyTypes($category['family_types'], $dbCategory->id);
        }
    }

    /**
     * Generate a unique slug by appending a counter if necessary.
     *
     * @param string $slugBase
     * @return string
     */
    private function generateUniqueSlug($slugBase)
    {
        $slug = $slugBase;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$counter}";
            $counter++;
        }

        return $slug;
    }


    /**
     * Insert characteristics for a specific category.
     */
    private function insertCharacteristics($characteristics, $categoryId)
    {
        foreach ($characteristics as $characteristic) {
            Characteristic::updateOrCreate(
                ['characteristic_id' => $characteristic['id']],
                [
                    'category_id' => $categoryId,
                    'name' => $characteristic['name'],
                    'type_id' => $characteristic['type_id'],
                    'display_order' => $characteristic['display_order'],
                    'is_mandatory' => $characteristic['is_mandatory'],
                    'is_mandatory_for_mktp' => $characteristic['is_mandatory_for_mktp'],
                    'allow_new_value' => $characteristic['allow_new_value'],
                    'is_filter' => $characteristic['is_filter'],
                    'tags' => json_encode($characteristic['tags']),
                    'value_tags' => json_encode($characteristic['value_tags']),
                ]
            );
        }
    }

    /**
     * Insert family types for a specific category.
     */
    private function insertFamilyTypes($familyTypes, $categoryId)
    {
        foreach ($familyTypes as $familyType) {
            FamilyType::updateOrCreate(
                ['family_type_id' => $familyType['id']],
                [
                    'category_id' => $categoryId,
                    'name' => $familyType['name'],
                    'characteristics' => json_encode($familyType['characteristics']),
                ]
            );
        }
    }

    /**
     * Get the parent ID for a category.
     */
    private function getParentId($emagParentId)
    {
        if ($emagParentId) {
            $parent = Category::where('emag_id', $emagParentId)->first();
            return $parent ? $parent->id : null;
        }
        return null; // Root categories will have a null parent_id
    }


    public function resolveCategoryTree()
    {
        // Fetch all categories
        $categories = Category::all();

        // Create a lookup table for easy access
        $categoriesById = $categories->keyBy('id');

        // Traverse all categories
        foreach ($categories as $category) {
            if ($category->is_allowed) {
                continue; // Skip already allowed categories
            }

            $currentCategory = $category;
            $pathToRoot = []; // Track the path up the tree for debugging

            // Traverse up the tree until the topmost ancestor (parent_id = null)
            while ($currentCategory->parent_id && isset($categoriesById[$currentCategory->parent_id])) {
                $pathToRoot[] = $currentCategory->id; // Add to the path

                $parentCategory = $categoriesById[$currentCategory->parent_id];
                if ($parentCategory->is_allowed) {
                    // If an allowed parent is found, break
                    break;
                }

                $currentCategory = $parentCategory;
            }

            // If no allowed parent or ancestor exists, enforce the topmost ancestor as allowed
            if (!$currentCategory->parent_id) {
                $currentCategory->is_allowed = 1;
                $currentCategory->save();
            }

            // Debugging: Output the resolved path
            \Log::info('Resolved category path:', $pathToRoot);
        }

        return response()->json(['message' => 'Category tree resolved successfully!']);
    }


    public function saveImages()
    {
        set_time_limit(300); // Allow unlimited execution time

        // Process products in batches to avoid memory exhaustion
        Product::whereNotNull('images_emag')->chunk(50, function ($products) {
            foreach ($products as $product) {
                $decodedImages = json_decode($product->images_emag, true); // Decode JSON column

                // Validate JSON and ensure it's an array
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedImages)) {
                    \Log::error("Invalid JSON in images_emag for product ID {$product->id}");
                    continue; // Skip this product
                }

                $savedImages = []; // Array to store saved image paths

                // Loop through each image entry
                foreach ($decodedImages as $imageData) {
                    $imageUrl = $imageData['url'] ?? null; // Get the image URL

                    if ($imageUrl) {
                        try {
                            // Extract the file name from the URL
                            $fileName = basename(parse_url($imageUrl, PHP_URL_PATH));

                            // Define the storage path for the image
                            $filePath = "public/images/uploads/products/{$fileName}";

                            // Check if the file already exists
                            if (!Storage::exists($filePath)) {
                                // Download the image and save it in storage
                                $imageContents = Http::timeout(300)->get($imageUrl)->body(); // 30s timeout
                                Storage::put($filePath, $imageContents);
                            }

                            // Add the saved path to the array
                            $savedImages[] = Storage::url($filePath);
                        } catch (\Exception $e) {
                            // Log any errors for debugging
                            \Log::error("Failed to save image for product ID {$product->id}: " . $e->getMessage());
                        }
                    }
                }

                // Save the downloaded image paths as JSON in the `images` column
                if (!empty($savedImages)) {
                    // Save the paths as a JSON-encoded array
                    $product->update(['images' => json_encode($savedImages, JSON_UNESCAPED_SLASHES)]);
                }

            }
        });

        return response()->json(['message' => 'Images downloaded and saved successfully.']);
    }


    public function fetchSingleProduct($id)
    {
        try {
            $authHash = base64_encode($this->username . ':' . $this->password);

            // API call for a single product
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authHash,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/product_offer/read', [
                'filters' => [['field' => 'id', 'value' => $id]],
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to fetch product',
                    'details' => $response->body(),
                ], $response->status());
            }

            $data = $response->json();
            $productData = $data['results'][0] ?? null;
            dd($productData);
            if ($productData) {
                $product = Product::where('emag_id', $productData['id'])->first();

                if ($product) {
                    // Update the existing product
                    $product->images_emag = json_encode($productData['images'] ?? 'plm');
                    $product->images_emag2 = json_encode($productData['images'] ?? 'plm');
                    $product->save();

//                    $product->update([
//                        'name' => $productData['name'],
//                        'images_emag' => $productData['images'] ?? [], // Explicitly encode JSON
//                        'images_emag2' => $productData['images'] ?? [],
//                    ]);
                    return response()->json(['message' => 'Product updated successfully!', 'product' => $product]);
                } else {
                    return response()->json(['error' => 'Product not found in the database'], 404);
                }
            } else {
                return response()->json(['error' => 'Product not found in API response'], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    public function refetchImagesForProducts()
    {
        $productsWithoutImages = Product::where(function ($query) {
            $query->whereRaw('JSON_LENGTH(images_emag) = 0')
                ->orWhereNull('images_emag');
        })->get();

        $authHash = base64_encode($this->username . ':' . $this->password);

        foreach ($productsWithoutImages as $product) {

            try {
                // Fetch product data from the eMAG API
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHash,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl . '/product_offer/read', [
                    'filters' => [
                        ['field' => 'id', 'value' => $product->emag_id],
                    ],
                ]);

                if (!$response->successful()) {
                    \Log::error("Failed to fetch product ID {$product->emag_id}: " . $response->body());
                    continue;
                }

                $data = $response->json();
                $fetchedProduct = $data['results'][0] ?? null;
                dd($fetchedProduct, $product);
                if ($fetchedProduct) {
                    $product->images_emag = json_encode($fetchedProduct['images'] ?? 'plm');
                    $product->images_emag2 = json_encode($fetchedProduct['images'] ?? 'plm');
                    $product->save();

                    echo "Updated product ID {$product->id} with new images.\n";
                } else {
                    echo "No product data found for ID {$product->emag_id}.\n";
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching product ID {$product->emag_id}: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Images refetch completed.']);
    }


    public function fetchProducts()
    {
        try {
            $authHash = base64_encode($this->username . ':' . $this->password);
            $currentPage = 1;
            $itemsPerPage = 100;

            do {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHash,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl . '/product_offer/read', [
                    'currentPage' => $currentPage,
                    'itemsPerPage' => $itemsPerPage,
                ]);

                if (!$response->successful()) {
                    return response()->json([
                        'error' => 'Failed to fetch products',
                        'details' => $response->body(),
                    ], $response->status());
                }

                $data = $response->json();

                foreach ($data['results'] as $product) {
                    // Save raw response in the new table
                    \App\Models\EmagApiResponse::create([
                        'type' => 'product',
                        'emag_id' => $product['id'],
                        'response' => json_encode($product), // Save the raw response
                    ]);
                }

                $currentPage++;
            } while (!empty($data['results']));

            return response()->json(['message' => 'Products imported successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



}
