<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\FamilyType;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\EmagCategory;
use App\Models\EmagApiResponse;
use Illuminate\Support\Facades\Storage;

class EmagMarketplaceController extends Controller
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
        $currentPage = 1;
        $itemsPerPage = 100;

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
                        $this->insertCategories($categories);
                        $currentPage++;
                    } else {
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


    /**
     * Insert categories, characteristics, and family types into the database.
     */
    private function insertCategories($categories)
    {
        foreach ($categories as $category) {
            $slugBase = Str::slug($category['name']);
            $uniqueSlug = $this->generateUniqueSlug($slugBase);
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

            $this->insertCharacteristics($category['characteristics'], $dbCategory->id);
            $this->insertFamilyTypes($category['family_types'], $dbCategory->id);
        }
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
        return null;
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

                $category->status = $category->is_allowed ? 1 : 0;

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


    public function fetchAllProducts()
    {
        set_time_limit(0);
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

    public function syncCategoriesFromApi()
    {
        $currentPage = 1;
        $itemsPerPage = 100;
        $hasMorePages = true;

        try {
            do {
                // Fetch categories from the API
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                ])->post($this->apiUrl . '/category/read', [
                    'currentPage' => $currentPage,
                    'itemsPerPage' => $itemsPerPage,
                ]);

                if ($response->failed()) {
                    return response()->json([
                        'error' => 'Failed to fetch categories',
                        'details' => $response->body(),
                    ], $response->status());
                }

                $categories = $response->json()['results'];

                foreach ($categories as $category) {
                    $slug = Str::slug($category['name']);
                    $uniqueSlug = $this->generateUniqueSlug($slug);

                    EmagCategory::updateOrCreate(
                        ['emag_id' => $category['id']],
                        [
                            'name' => $category['name'],
                            'slug' => $uniqueSlug,
                            'emag_parent_id' => $category['parent_id'],
                            'is_ean_mandatory' => $category['is_ean_mandatory'],
                            'is_warranty_mandatory' => $category['is_warranty_mandatory'],
                            'is_allowed' => $category['is_allowed'],
                            'characteristics' => json_encode($category['characteristics']),
                            'family_types' => json_encode($category['family_types']),
                        ]
                    );
                }

                $hasMorePages = count($categories) === $itemsPerPage;
                $currentPage++;
            } while ($hasMorePages);

            return response()->json(['message' => 'All categories synchronized successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while syncing categories',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate a unique slug.
     *
     * @param string $slug
     * @return string
     */
    private function generateUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $counter = 1;

        while (EmagCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }



    public function categoriesTree()
    {
        set_time_limit(0);
        //$categories = EmagCategory::all(); // Retrieve all categories
        $categories = EmagCategory::where('status', 1)->get();
        $tree = $this->buildCategoryTree($categories); // Build the hierarchical tree
        return response()->json($tree); // Return as JSON
    }

    /**
     * Build a hierarchical tree structure of categories.
     *
     * @param \Illuminate\Support\Collection $categories
     * @param int|null $parentId
     * @return array
     */
//    private function buildCategoryTree($categories, $parentId = null)
//    {
//        set_time_limit(0);
//        $tree = [];
//        foreach ($categories as $category) {
//            if ($category->emag_parent_id == $parentId) {
//                $children = $this->buildCategoryTree($categories, $category->emag_id);
//                $category->children = $children;
//                $tree[] = $category;
//            }
//        }
//        return $tree;
//    }

    private function buildCategoryTree($categories, $parentId = null)
    {
        set_time_limit(0);
        $tree = [];
        foreach ($categories as $category) {
            if ($category->emag_parent_id == $parentId) {
                $children = $this->buildCategoryTree($categories, $category->emag_id);
                $tree[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'children' => $children, // Recursively add children with only id and name
                ];
            }
        }
        return $tree;
    }

    public function activateCategoriesFromProducts()
    {
        try {
            // Fetch all products
            $products = Product::select('emag_category_id')->distinct()->get();

            foreach ($products as $product) {
                // Get the starting emag_category_id from the product
                $currentCategoryId = $product->emag_category_id;

                // Traverse the category hierarchy and activate categories
                while ($currentCategoryId) {
                    // Find the category
                    $category = EmagCategory::where('emag_id', $currentCategoryId)->first();

                    if ($category) {
                        // Activate the category
                        $category->status = 1;
                        $category->save();

                        // Move to the parent category
                        $currentCategoryId = $category->emag_parent_id;
                    } else {
                        // Break the loop if the category doesn't exist
                        break;
                    }
                }
            }

            return response()->json(['message' => 'Categories activated successfully!']);
        } catch (\Exception $e) {
            \Log::error('Error activating categories: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while activating categories',
                'message' => $e->getMessage(),
            ], 500);
        }
    }




}
