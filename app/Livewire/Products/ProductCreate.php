<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductVariation;
use Illuminate\Support\Str;


class ProductCreate extends Component
{
    use WithFileUploads;

    public $name, $description, $price, $category_id, $ean, $general_stock, $newImages = [];
    public $allAttributes, $productVariations = [];

    protected $rules = [
        'name'           => 'required|string|max:255',
        'description'    => 'nullable|string',
        'price'          => 'required|numeric|min:0',
        'ean'            => 'required|string|max:255|unique:products,ean',
        'category_id'    => 'required|exists:categories,id',
        'general_stock'  => 'required|integer|min:0',
        'newImages.*'    => 'image|max:10240',
    ];

    protected $listeners = [
        'refreshAttributes' => 'loadAttributes',
        'addAttribute',
        'addValue'
    ];

    public function mount()
    {
        $this->allAttributes = Attribute::with('values')->get();
        // Start with an empty array – selections will be added via Alpine (and bound to productVariations)
        $this->productVariations = [];
    }

    public function loadAttributes()
    {
        $this->allAttributes = Attribute::with('values')->get()->toArray();
        $this->dispatch('attributesUpdated', ['attributes' => $this->allAttributes]);
    }

    public function addAttribute($name)
    {
        Attribute::create(['name' => $name]);
        $this->loadAttributes();
    }

    public function addValue($attribute_id, $value)
    {
        $attribute = Attribute::find($attribute_id);
        if ($attribute) {
            $attribute->values()->create(['value' => $value]);
            $this->loadAttributes();
        }
    }

    public function addProduct()
    {
        // Validate input
        $this->validate();

        $uniqueProductCode = 'TEX-' . strtoupper(uniqid());
        // Create the product record
        $product = Product::create([
            'name'          => $this->name,
            'description'   => $this->description,
            'price'         => $this->price,
            'ean'           => $this->ean,
            'category_id'   => $this->category_id,
            'general_stock' => $this->general_stock,
            'product_code'  => $uniqueProductCode, // set a unique product code
            'status'        => 1, // set status to active
        ]);

        // Handle product images
        if ($this->newImages) {
            $imagePaths = [];
            foreach ($this->newImages as $image) {
                $imagePaths[] = $this->storeImage($image);
            }
            $product->update(['images' => $imagePaths]);
        }

        // Determine how many variations have been selected.
        $numVariations = count($this->productVariations);

        // If more than one variation, split the general stock evenly.
        $variationStock = $this->general_stock;
        if ($numVariations > 1) {
            $variationStock = floor($this->general_stock / $numVariations);
        }

        // Create product variations based on the attribute-value pairs.
        foreach ($this->productVariations as $variation) {
            // Generate a unique SKU for each variation.
            $variantSku = 'SKU-' . strtoupper(uniqid());
            // Use product's price as the default variation price.
            $variantPrice = $this->price;

            $variationRecord = ProductVariation::create([
                'product_id' => $product->id,
                'price'      => $variantPrice,
                'stock'      => $variationStock,
                'sku'        => $variantSku,
            ]);

            // Attach the attribute-value pair via the pivot table.
            // (Make sure your ProductVariation model defines a proper many-to-many relationship named attributeValues.)
            $variationRecord->attributeValues()->attach([
                $variation['attribute_id'] => ['attribute_value_id' => $variation['attribute_value_id']]
            ]);
        }

        session()->flash('success', 'Produs creat cu succes!');

        $this->dispatch('flashMessage', ['type' => 'success', 'message' => 'Produsul a fost creat cu succes!']);
        return redirect()->route('admin.products');
    }

    private function storeImage($image)
    {
        // Generate a clean, slugged base name from the product name.
        $cleanedName = Str::slug($this->name);
        // Generate a short unique suffix.
        $uniqueSuffix = substr(md5(uniqid()), 0, 6);
        // Get the file's original extension.
        $extension = $image->getClientOriginalExtension();
        // Build the filename.
        $filename = "{$cleanedName}-{$uniqueSuffix}.{$extension}";
        // Store the file under "images/uploads/products" on the public disk.
        $path = $image->storeAs('images/uploads/products', $filename, 'public');
        // Return the full path with the /storage prefix.
        return "/storage/{$path}";
    }


    public function render()
    {
        return view('livewire.products.product-create', [
            'categories'    => Category::all(),
            'allAttributes' => $this->allAttributes,
        ]);
    }
}
