<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

class Crud_old extends Component
{
    use WithPagination, WithFileUploads;

    public $name, $description, $ean, $price, $category_id, $images = [], $newImages = [];
    public $search = '';
    public $selectedCategoryName = 'Select a category';
    public $allAttributes;
    public $productVariants = [
        ['unitate' => null, 'ambalaj' => null, 'image' => null, 'stock' => null, 'price' => null],
    ];
    public $editingProductId;
    public $descriptions = [];
    public $productVariations = [];
    //public $newVariations = [];
    public $newVariations = [
        ['unitate' => null, 'ambalaj' => null, 'image' => null, 'stock' => null, 'price' => null],
    ];
    public $searchById = '';
    public $searchByName = '';
    public $selectedCategoryId = null;


    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'general_stock' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
        'newImages.*' => 'image|max:10240',
        'productVariants.*.image' => 'nullable|image|max:10240',
        'brand_name' => 'nullable|string|max:255',
    ];

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function filterByCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
        $this->selectedCategoryName = Category::find($categoryId)->name ?? 'Toate categoriile';
    }

    public function resetCategory()
    {
        $this->selectedCategoryId = null;
        $this->selectedCategoryName = 'Toate categoriile';
    }


    public function mount()
    {
        $this->allAttributes = Attribute::with('values')->get();
        $this->descriptions = [];
        $this->newVariations = [
            ['unitate' => null, 'ambalaj' => null, 'image' => null, 'stock' => null, 'price' => null],
        ];
    }

    public function render()
    {
        $query = Product::with(['category', 'variations.attributeValues']);

        // Apply search by ID filter
        if (!empty($this->searchById)) {
            $query->where('id', $this->searchById);
        }

        // Apply search by Name filter
        if (!empty($this->searchByName)) {
            $query->where('name', 'like', '%' . $this->searchByName . '%');
        }

        // Apply category filter
        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        // Additional filtering based on the current route
        $currentRoute = Route::currentRouteName();
        if ($currentRoute === 'admin.products-without-price') {
            // Products without a price (or with price set to 0)
            $query->where(function ($q) {
                $q->whereNull('price')
                    ->orWhere('price', 0);
            });
        }
        if ($currentRoute === 'admin.products-without-ean') {
            // Products without an EAN (or with an empty EAN)
            $query->where(function ($q) {
                $q->whereNull('ean')
                    ->orWhere('ean', '');
            });
        }

        $products = $query->orderBy('id', 'asc')->paginate(10);

        foreach ($products as $product) {
            if (!isset($this->descriptions[$product->id])) {
                $this->descriptions[$product->id] = $product->description;
            }
        }

        $allCategories = Category::with('children')->whereNull('parent_id')->get();
        $this->allAttributes = Attribute::with('values')->get();
        $categories = Category::with('children')->whereNull('parent_id')->get();

        return view('livewire.products.crud', [
            'products' => $products,
            'allCategories' => $allCategories,
            'allAttributes' => $this->allAttributes,
            'categories' => $categories,
        ])->extends('layouts.base')->section('content');
    }

    public function create()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $allAttributes = Attribute::with('values')->get(); // Load attributes here

        return view('livewire.products.create', [
            'categories' => $categories,
            'allAttributes' => $allAttributes,
        ]);
    }

    public function createProduct()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'ean'   => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'productVariants.*.unitate' => 'required|integer|exists:attribute_values,id',
            'productVariants.*.ambalaj' => 'required|integer|exists:attribute_values,id',
            'productVariants.*.stock' => 'required|integer|min:0',

            //'productVariants.*.image'       => 'nullable|image|max:10240',
        ]);

        // Calculate the general stock as the sum of all variant stocks
        $generalStock = collect($this->productVariants)->sum('stock');

        // Store images
        $imagePaths = [];
        if ($this->newImages) {
            foreach ($this->newImages as $image) {
                $imagePaths[] = $this->storeImage($image);
            }
        }

        // Create the product
        $uniqueProductCode = 'TEX-' . strtoupper(uniqid());
        $product = Product::create([
            'name'          => $this->name,
            'description'   => $this->description,
            'price'         => $this->price,
            'ean'           => $this->ean,
            'category_id'   => $this->category_id,
            'general_stock' => $this->general_stock,
            'product_code'  => $uniqueProductCode, // set a unique product code
            'slug'          => str_slug($this->name),
        ]);

        // Create product variations
        foreach ($this->productVariants as $variant) {
            // Generate a unique SKU for each variation
            $variantSku = 'SKU-' . strtoupper(uniqid());

            // Use general price if variant price is not provided
            $variantPrice = $variant['price'] ?? $this->price;

            $variation = ProductVariation::create([
                'product_id' => $product->id,
                'price' => $variantPrice,
                'stock' => $variant['stock'],
                'sku' => $variantSku,
            ]);

            // Attach attributes to the variant
            $attributesToAttach = [
                $variant['unitate'],
                $variant['ambalaj'],
            ];

            if (!empty($variant['image'])) {
                $variantImagePath = $this->storeVariantImage($variant['image']);
                $imageAttribute = Attribute::where('name', 'Image')->first();
                $imageValue = AttributeValue::firstOrCreate(
                    [
                        'attribute_id' => $imageAttribute->id,
                        'value'        => $variantImagePath,
                    ],
                    ['extra_info' => '{}']
                );
                $attributesToAttach[] = $imageValue->id;
            }

            // Filter out null values before attaching
            $variation->attributeValues()->attach(array_filter($attributesToAttach, function ($val) {
                return !is_null($val);
            }));
        }

        $this->dispatch('flashMessage', [
            'type' => 'success',
            'message' => 'Produsul a fost creat cu succes!!!',
        ]);
        $this->dispatch('refreshSidebarStats');
        $this->resetFields();
        $this->resetPage();
        $this->reset(['name', 'description', 'price', 'category_id']);
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $this->editingProductId = $id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->category_id = $product->category_id;

        // Map variations with their attribute values.
        // We’re building an array where each variation includes:
        // - its id, stock, price, and
        // - an 'attributes' sub-array with keys equal to the attribute names and values as the corresponding attribute_value id.
        $this->productVariations = $product->variations->map(function ($variation) {
            // Build an associative array with attribute name as key and the attribute value id as value.
            $attributes = $variation->attributeValues->mapWithKeys(function ($attrValue) {
                return [$attrValue->attribute->name => $attrValue->id];
            })->toArray();

            // Also get the image if available (assuming 'Image' is an attribute)
            $imageValue = $variation->attributeValues->where('attribute.name', 'Image')->first();

            return [
                'id'          => $variation->id,
                'stock'       => $variation->stock,
                'price'       => $variation->price,
                'attributes'  => $attributes,
                'image'       => $imageValue ? $imageValue->value : null, // optional: file path stored in "value"
            ];
        })->toArray();

        // For debugging, you could dd($this->productVariations) here.
        // dd($this->productVariations);
    }



    public function updateProductVariation($variationId, $data)
    {
        $variation = ProductVariation::findOrFail($variationId);
        $variation->update($data);

        $this->dispatch('flashMessage', [
            'type' => 'success',
            'message' => 'Varianta de produs a fost actualizată!',
        ]);

        // Reload variations
        $this->editProduct($variation->product_id);
    }


    public function deleteProductVariation($variationId)
    {
        $variation = ProductVariation::findOrFail($variationId);
        $variation->delete();

        $this->dispatch('flashMessage', [
            'type' => 'success',
            'message' => 'Varianta de produs a fost ștearsă!',
        ]);
    }

    public function updateProduct($id)
    {
        $product = Product::findOrFail($id);
        $descriptionValue = $this->description ?? $product->description ?? '';
        // Update product details
        $product->update([
            'name' => $this->name,
            'description' => $descriptionValue,
            'price' => $this->price,
            'ean' => $this->ean,
            'category_id' => $this->category_id,
        ]);

        // Append new images to existing images
        $existingImages = $product->images ?? [];
        $imagePaths = is_array($existingImages) ? $existingImages : json_decode($existingImages, true);

        if ($this->newImages) {
            foreach ($this->newImages as $image) {
                if (is_array($image)) {
                    foreach ($image as $file) {
                        $imagePaths[] = $this->storeImage($file);
                    }
                } else {
                    $imagePaths[] = $this->storeImage($image);
                }
            }
            $product->update(['images' => $imagePaths]);
        }

        // Update existing variations
        foreach ($this->productVariations as $variation) {
            if (isset($variation['id'])) {
                $productVariation = ProductVariation::findOrFail($variation['id']);
                $productVariation->update([
                    'price' => $variation['price'],
                    'stock' => $variation['stock'],
                ]);

                // Process the variant image if needed
                $imageField = $variation['image'] ?? null;
                if ($imageField && !is_numeric($imageField)) {
                    $variantImagePath = $this->storeVariantImage($imageField);
                    $imageAttribute = Attribute::where('name', 'Image')->first();
                    $imageValue = AttributeValue::firstOrCreate(
                        [
                            'attribute_id' => $imageAttribute->id,
                            'value'        => $variantImagePath,
                        ],
                        ['extra_info' => '{}']
                    );
                    $imageField = $imageValue->id;
                }

                $attributes = array_filter([
                    $variation['unitate'] ?? null,
                    $variation['ambalaj'] ?? null,
                    $imageField,
                ], function ($val) {
                    return !is_null($val);
                });

                $productVariation->attributeValues()->sync($attributes);
            }
        }
        // Add new variations
        foreach ($this->newVariations as $variation) {
            if (!empty($variation['unitate']) || !empty($variation['ambalaj']) || !empty($variation['image']) || !empty($variation['imagePreview'])) {
                $newVariation = $product->variations()->create([
                    'price' => $variation['price'],
                    'stock' => $variation['stock'],
                    'sku' => 'SKU-' . strtoupper(uniqid()),
                ]);

                $attributesToAttach = [
                    $variation['unitate'] ?? null,
                    $variation['ambalaj'] ?? null,
                ];

                // Check if an image file was uploaded directly
                if (!empty($variation['image'])) {
                    $variantImagePath = $this->storeVariantImage($variation['image']);
                    $imageAttribute = Attribute::where('name', 'Image')->first();
                    $imageValue = AttributeValue::firstOrCreate(
                        [
                            'attribute_id' => $imageAttribute->id,
                            'value' => $variantImagePath,
                        ],
                        ['extra_info' => '{}']
                    );
                    $attributesToAttach[] = $imageValue->id;
                } // Otherwise, if no file but there is an image preview (Base64)
                elseif (!empty($variation['imagePreview'])) {
                    $variantImagePath = $this->storeBase64Image($variation['imagePreview']);
                    $imageAttribute = Attribute::where('name', 'Image')->first();
                    $imageValue = AttributeValue::firstOrCreate(
                        [
                            'attribute_id' => $imageAttribute->id,
                            'value' => $variantImagePath,
                        ],
                        ['extra_info' => '{}']
                    );
                    $attributesToAttach[] = $imageValue->id;
                }

                $newVariation->attributeValues()->attach(array_filter($attributesToAttach, function ($val) {
                    return !is_null($val);
                }));
            }
        }

        $this->dispatch('flashMessage', [
            'type' => 'success',
            'message' => 'Produsul a fost actualizat cu succes!',
        ]);

        $this->editingProductId = null; // Reset editing state
        $this->resetFields();
    }


//    public function update($id)
//    {
//        $this->validate();
//        $product = Product::findOrFail($id);
//        $imagePaths = $product->images ?? [];
//        if ($this->newImages) {
//            foreach ($this->newImages as $image) {
//                $imagePaths[] = $this->storeImage($image);
//            }
//        }
//
//        $product->update([
//            'name' => $this->name,
//            'description' => $this->description,
//            'price' => $this->price,
//            'ean' => $this->ean,
//            'general_stock' => $this->general_stock,
//            'category_id' => $this->category_id,
//            'images' => $imagePaths,
//            'brand_name' => $this->brand_name,
//        ]);
//
//        session()->flash('success', 'Product updated successfully!');
//        return redirect()->route('admin.products');
//    }

    public function update($id)
    {
        $this->validate();
        $product = Product::findOrFail($id);
        $imagePaths = $product->images ?? [];
        if ($this->newImages) {
            foreach ($this->newImages as $image) {
                $imagePaths[] = $this->storeImage($image);
            }
        }

        $product->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'ean' => $this->ean,
            'general_stock' => $this->general_stock,
            'category_id' => $this->category_id,
            'images' => $imagePaths,
            'brand_name' => $this->brand_name,
        ]);

        session()->flash('success', 'Product updated successfully!');

        // Redirect to the current route instead of hardcoded 'admin.products'
        return redirect()->route(request()->route()->getName());
    }

    private function storeImage($image)
    {
        $cleanedName = preg_replace('/[^a-zA-Z0-9]+/', '_', strtolower($this->name));
        $uniqueSuffix = substr(md5(uniqid()), 0, 6);
        $filename = "{$cleanedName}_{$uniqueSuffix}.{$image->getClientOriginalExtension()}";
        $path = $image->storeAs('images/uploads/products', $filename, 'public');
        return "/storage/{$path}";
    }

    private function storeVariantImage($image)
    {
        if (is_string($image)) {
            return $image;
        }
        $cleanedName = preg_replace('/[^a-zA-Z0-9]+/', '_', strtolower($this->name));
        $uniqueSuffix = substr(md5(uniqid()), 0, 6);
        $filename = "{$cleanedName}_variant_{$uniqueSuffix}.{$image->getClientOriginalExtension()}";
        $path = $image->storeAs('images/uploads/products/variants', $filename, 'public');
        return "/storage/{$path}";
    }

    public function removeImage($productId, $imageKey)
    {
        $product = Product::find($productId);

        if ($product && isset($product->images[$imageKey])) {
            // Remove the image from the storage
            Storage::delete($product->images[$imageKey]);

            // Remove the image from the database
            $images = $product->images;
            unset($images[$imageKey]);
            $product->images = array_values($images);
            $product->save();

            $this->dispatch('flashMessage', ['type' => 'success', 'message' => 'Imagine ștearsă!']);
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $imagePaths = is_string($product->images) ? json_decode($product->images, true) : $product->images;

        if (is_array($imagePaths)) {
            foreach ($imagePaths as $imagePath) {
                Storage::delete('public/' . $imagePath);
            }
        }

        $product->delete();
        $this->dispatch('flashMessage', [
            'type' => 'success',
            'message' => 'Produsul a fost sters cu succes!!!',
        ]);
        $this->dispatch('refreshSidebarStats');
        $this->resetPage();
    }

    private function storeBase64Image($base64Data)
    {
        // Check if the string has a data URI scheme
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // e.g. jpg, png, gif

            $data = base64_decode($data);
            if ($data === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('Invalid image data');
        }

        // Generate a filename and store the file on disk.
        $cleanedName = preg_replace('/[^a-zA-Z0-9]+/', '_', strtolower($this->name));
        $uniqueSuffix = substr(md5(uniqid()), 0, 6);
        $filename = "{$cleanedName}_variant_{$uniqueSuffix}.{$type}";
        $path = $filename; // You can include folder path here if desired.
        Storage::disk('public')->put("images/uploads/products/variants/{$path}", $data);

        return "/storage/images/uploads/products/variants/{$path}";
    }


    public function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->price = null;
        $this->category_id = null;
        $this->selectedCategoryName = 'Select a category';
        $this->images = [];
        $this->newImages = [];
    }

}

