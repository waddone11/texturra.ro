<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Vat;
use App\Models\EmagApiResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class EmagProductsMarketplaceController extends Controller
{
    public function __construct()
    {
        $this->apiUrl = config('app.emag_api_url');
        $this->username = config('app.emag_username');
        $this->password = config('app.emag_password');
    }

    public function processAllProducts()
    {
        set_time_limit(0); // Prevent timeout

        // Fetch all product responses
        $responses = EmagApiResponse::where('type', 'product')->get();

        if ($responses->isEmpty()) {
            return response()->json(['error' => 'No product responses found'], 404);
        }

        $processed = 0; // Counter for processed products

        foreach ($responses as $response) {
            $productData = json_decode($response->response, true);

            if (!$productData) {
                \Log::error("Invalid JSON for EmagApiResponse ID {$response->id}");
                continue; // Skip invalid data
            }

            try {
                // Process the product
                $product = Product::updateOrCreate(
                    ['emag_id' => $productData['id']],
                    [
                        'name' => $productData['name'],
                        'description' => $this->sanitizeDescription($productData['description']),
                        'price' => $productData['sale_price'],
                        'emag_price' => $productData['sale_price'],
                        'currency' => $productData['currency'],
                        'category_id' => $this->mapEmagCategoryToLocal($productData['category_id']),
                        'emag_category_id' => $productData['category_id'],
                        'brand_name' => $productData['brand_name'] ?? null,
                        'part_number' => $productData['part_number'] ?? null,
                        'general_stock' => $productData['general_stock'] ?? 0,
                        'status' => $productData['status'],
                        'images_emag' => json_encode($productData['images'] ?? []),
                        'characteristics' => json_encode($productData['characteristics'] ?? []),
                        'attachments' => json_encode($productData['attachments'] ?? []),
                        'offer_details' => json_encode($productData['offer_details'] ?? []),
                        'barcode' => $productData['barcode'][0] ?? null,
                        'ean' => $productData['ean'][0] ?? null,
                        'ownership' => $productData['ownership'] ?? 0,
                        'min_sale_price' => $productData['min_sale_price'] ?? 0,
                        'max_sale_price' => $productData['max_sale_price'] ?? 0,
                        'recommended_price' => $productData['recommended_price'] ?? 0,
                        'product_code' => $productData['part_number'] ?? 'EMAG-' . $productData['id'],
                    ]
                );

                // Save product stock
                $stockData = $productData['stock'][0] ?? null;
                if ($stockData) {
                    ProductStock::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'location' => 'default', // Adjust location based on your requirement
                            'quantity' => $stockData['value'] ?? 0,
                        ]
                    );
                }

                // Save images to storage
                $savedImages = $this->saveImages($productData['images'] ?? []);
                $product->update(['images' => $savedImages]);

                $processed++; // Increment processed counter
                sleep(1); // Delay for 1 second
            } catch (\Exception $e) {
                \Log::error("Failed to process product ID {$productData['id']}: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Processing completed', 'processed' => $processed]);
    }


    /**
     * Fetch a single product from our saved raw responses and process it.
     */
    public function fetchSingleProduct($id)
    {
        // Fetch the saved raw response from our table
        $response = EmagApiResponse::where('id', $id)
            ->where('type', 'product')
            ->first();

        if (!$response) {
            return response()->json(['error' => 'Product response not found in database'], 404);
        }

        $productData = json_decode($response->response, true);

        if (!$productData) {
            return response()->json(['error' => 'Invalid JSON data for product'], 400);
        }

        try {
            $product = Product::updateOrCreate(
                ['emag_id' => $productData['id']],
                [
                    'name' => $productData['name'],
                    'description' => $this->sanitizeDescription($productData['description']),
                    'price' => $productData['sale_price'],
                    'emag_price' => $productData['sale_price'],
                    'currency' => $productData['currency'],
                    'category_id' => $this->mapEmagCategoryToLocal($productData['category_id']),
                    'emag_category_id' => $productData['category_id'],
                    'brand_name' => $productData['brand_name'] ?? null,
                    'part_number' => $productData['part_number'] ?? null,
                    'general_stock' => $productData['general_stock'] ?? 0,
                    'status' => $productData['status'],
//                    'images_emag' => $productData['images'] ?? [],
//                    'characteristics' => $productData['characteristics'] ?? [],
//                    'attachments' => $productData['attachments'] ?? [],
//                    'offer_details' => $productData['offer_details'] ?? [],

                    'images_emag' => json_encode($productData['images'] ?? []), // Encode as JSON
                    'characteristics' => json_encode($productData['characteristics'] ?? []), // Encode as JSON
                    'attachments' => json_encode($productData['attachments'] ?? []), // Encode as JSON
                    'offer_details' => json_encode($productData['offer_details'] ?? []), // Encode as JSON


                    'barcode' => $productData['barcode'][0] ?? null,
                    'ean' => $productData['ean'][0] ?? null,
                    'ownership' => $productData['ownership'] ?? 0,
                    'min_sale_price' => $productData['min_sale_price'] ?? 0,
                    'max_sale_price' => $productData['max_sale_price'] ?? 0,
                    'recommended_price' => $productData['recommended_price'] ?? 0,
                    'product_code' => $productData['part_number'] ?? 'EMAG-' . $productData['id'],
                ]
            );

            // Save product stock
            $stockData = $productData['stock'][0] ?? null;
            if ($stockData) {
                ProductStock::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'location' => 'default', // Adjust location based on your requirement
                        'quantity' => $stockData['value'] ?? 0,
                    ]
                );
            }

            // Save images to storage
            $savedImages = $this->saveImages($productData['images'] ?? []);
            $product->update(['images' => $savedImages]);

            return response()->json(['message' => 'Product processed successfully!', 'product' => $product]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process product', 'message' => $e->getMessage()], 500);
        }
    }


    public function infoSingleProduct($id)
    {
        // Fetch the saved raw response from our table
        $response = EmagApiResponse::where('id', $id)
            ->where('type', 'product')
            ->first();

        if (!$response) {
            return response()->json(['error' => 'Product response not found in database'], 404);
        }

        $productData = json_decode($response->response, true);

        if (!$productData) {
            return response()->json(['error' => 'Invalid JSON data for product'], 400);
        }

        dd($productData);
    }

    /**
     * Sanitize product description.
     */
    private function sanitizeDescription($description)
    {
        return preg_replace('/<img.*?>/', '', $description);
    }

    /**
     * Map eMAG category ID to local category ID.
     */
    private function mapEmagCategoryToLocal($emagCategoryId)
    {
        $category = Category::where('emag_id', $emagCategoryId)->first();
        return $category ? $category->id : null;
    }

    /**
     * Save images from eMAG API response to storage.
     */
    private function saveImages($images)
    {
        $savedImages = [];

        foreach ($images as $imageData) {
            $imageUrl = $imageData['url'] ?? null;
            if ($imageUrl) {
                try {
                    $fileName = basename(parse_url($imageUrl, PHP_URL_PATH));
                    $filePath = "public/images/uploads/products/{$fileName}";

                    if (!Storage::exists($filePath)) {
                        $imageContents = Http::get($imageUrl)->body();
                        Storage::put($filePath, $imageContents);
                    }

                    $savedImages[] = Storage::url($filePath);
                } catch (\Exception $e) {
                    \Log::error("Failed to save image: " . $e->getMessage());
                }
            }
        }

        return $savedImages;
    }

    public function checkProductMatch($id)
    {
        try {
            $authHash = base64_encode($this->username . ':' . $this->password);

            // Make the API call using form-data
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authHash,
            ])->asForm()->post($this->apiUrl . '/product_offer/read', [
                'id' => $id, // Send the ID as form-data
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to fetch product from eMAG API',
                    'details' => $response->body(),
                ], $response->status());
            }

            $data = $response->json();
            $apiProduct = $data['results'][0] ?? null;

            if (!$apiProduct) {
                return response()->json(['error' => 'Product not found in API response'], 404);
            }

            // Debugging output
            dd($apiProduct);

            // Further processing logic goes here
            return response()->json(['message' => 'Product retrieved successfully!', 'product' => $apiProduct]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }


    public function refetchImagesForProducts()
    {
        // Get all products where images_emag is either an empty array or null
        $productsWithoutImages = Product::where(function ($query) {
            $query->whereRaw('JSON_LENGTH(images_emag) = 0')
                ->orWhereNull('images_emag');
        })->get();

        $authHash = base64_encode($this->username . ':' . $this->password);

        // Loop through each product
        foreach ($productsWithoutImages as $product) {
            try {
                // Fetch product data from the eMAG API
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHash,
                ])->asForm()->post($this->apiUrl . '/product_offer/read', [
                    'id' => $product->emag_id, // Send the ID as form-data
                ]);

                // Check if the response is successful
                if (!$response->successful()) {
                    \Log::error("Failed to fetch product ID {$product->emag_id}: " . $response->body());
                    continue;
                }

                $data = $response->json();
                $fetchedProduct = $data['results'][0] ?? null;

                if ($fetchedProduct) {
                    // Save images from API response
                    $savedImages = $this->saveImages($fetchedProduct['images'] ?? []);

                    // If no images from API, parse the description
                    if (empty($savedImages) && !empty($fetchedProduct['description'])) {
                        $parsedImages = $this->extractImagesFromDescription($fetchedProduct['description']);
                        $savedImages = $this->saveImages($parsedImages);
                    }

                    // Update the product's images fields with the new data
                    $product->update([
                        'images_emag' => json_encode($fetchedProduct['images'] ?? []), // Raw images from API
                        'images_emag2' => json_encode($fetchedProduct['images'] ?? []), // Duplicate storage
                        'images' => $savedImages, // Save processed images to `images` field
                    ]);

                    echo "Updated product ID {$product->emag_id} with new images<br/>.\n";
                } else {
                    echo "No product data found for ID <br/>.\n";
                }
            } catch (\Exception $e) {
                // Log errors for debugging purposes
                \Log::error("Error fetching product ID {$product->emag_id}: " . $e->getMessage());
            }

            // Pause for 1 second to prevent overwhelming the API
            sleep(1);
        }

        return response()->json(['message' => 'Images refetch completed.']);
    }

    private function extractImagesFromDescription($description)
    {
        $imageUrls = [];

        // Match all <img> tags and extract the src attributes
        preg_match_all('/<img[^>]+src="([^">]+)"/i', $description, $matches);

        if (!empty($matches[1])) {
            $imageUrls = $matches[1];
        }

        return $imageUrls;
    }


    public function refetchImagesForProductId($id)
    {
        try {
            $authHash = base64_encode($this->username . ':' . $this->password);
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authHash,
            ])->asForm()->post($this->apiUrl . '/product_offer/read', [
                'id' => $id, // Send the ID as form-data
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to fetch product from eMAG API',
                    'details' => $response->body(),
                ], $response->status());
            }

            $data = $response->json();
            $apiProduct = $data['results'][0] ?? null;

            if (!$apiProduct) {
                return response()->json(['error' => 'Product not found in API response'], 404);
            }

            //dd($apiProduct);
            if ($apiProduct) {
                // Save images from API response
                $product = Product::where('emag_id', $id)->first();
                if (empty($savedImages) && !empty($apiProduct['description'])) {
                    $parsedImages = $this->extractImagesFromDescription($apiProduct['description']);
                    $savedImages = $this->saveImages2($parsedImages);
                } else {
                    $savedImages = $this->saveImages2($apiProduct['images'] ?? []);
                }

                $product->update([
                    'images_emag' => json_encode($apiProduct['images'] ?? []), // Raw images from API
                    'images_emag2' => json_encode($apiProduct['images'] ?? []), // Duplicate storage
                    'images' => $savedImages, // Save processed images to `images` field
                ]);

                echo "Updated product ID {$product->emag_id} with new images<br/>.\n";
            } else {
                echo "No product data found for ID <br/>.\n";
            }

            return response()->json(['message' => 'Product retrieved successfully!', 'product' => $apiProduct]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }


    private function saveImages2($images)
    {
        $savedImages = [];

        foreach ($images as $imageUrl) {
            try {
                $fileName = basename(parse_url($imageUrl, PHP_URL_PATH));
                $filePath = "public/images/uploads/products/{$fileName}";

                if (!Storage::exists($filePath)) {
                    $imageContents = Http::get($imageUrl)->body();
                    Storage::put($filePath, $imageContents);
                }

                $savedImages[] = Storage::url($filePath);
            } catch (\Exception $e) {
                \Log::error("Failed to save image: " . $e->getMessage());
            }
        }

        return $savedImages;
    }

    public function updateProductVatRates()
    {
        $authHash = base64_encode(config('app.emag_username') . ':' . config('app.emag_password'));
        $products = Product::whereNull('vat_id')->get();

        foreach ($products as $product) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . $authHash,
                ])->asForm()->post(config('app.emag_api_url') . '/product_offer/read', [
                    'id' => $product->emag_id,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $vatId = $data['results'][0]['vat_id'] ?? null;

                    if ($vatId) {
                        // Map eMAG VAT IDs to local VAT table
                        $vat = Vat::where('rate', $this->mapVatRate($vatId))->first();
                        if ($vat) {
                            $product->update(['vat_id' => $vat->id]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Failed to update VAT for product ID {$product->id}: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'VAT rates updated successfully.']);
    }

    private function mapVatRate($vatId)
    {
        return match ($vatId) {
            1 => 19.00,
            2 => 9.00,
            3 => 5.00,
            4 => 0.00,
            default => null,
        };
    }



}
