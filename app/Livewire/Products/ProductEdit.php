<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductVariation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductEdit extends Component
{
    use WithFileUploads;

    public $productId;
    public $name, $description, $price, $ean, $category_id, $general_stock;
    public $newImages = [];
    public $existingImages = [];
    public $allAttributes = [];
    public $productVariations = [];

    protected $listeners = [
        'loadProduct' => 'loadProduct',
    ];

    protected function rules()
    {
        return [
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'category_id'    => 'required|exists:categories,id',
            'general_stock'  => 'required|integer|min:0',
            // You can add a rule for EAN if needed
        ];
    }

    public function mount($productId)
    {
        // Load the product data when the component mounts
        $this->loadProduct($productId);
    }

    public function loadProduct($productId)
    {
        $this->productId = $productId;
        $product = Product::findOrFail($productId);

        $this->name           = $product->name;
        $this->description    = $product->description;
        $this->price          = $product->price;
        $this->ean            = $product->ean;
        $this->category_id    = $product->category_id;
        $this->general_stock  = $product->general_stock;
        $this->existingImages = $product->images;

        // Load attributes for the dynamic options section
        $this->allAttributes = Attribute::with('values')->get()->toArray();

        // Load existing product variations (each variation assumed to have one attribute/value pair)
        $this->productVariations = $product->variations->map(function ($variation) {
            $attrValue = $variation->attributeValues->first();
            if ($attrValue) {
                return [
                    'id'                 => $variation->id,
                    'attribute_id'       => $attrValue->attribute_id,
                    'attribute_value_id' => $attrValue->pivot->attribute_value_id,
                    'name'               => $attrValue->attribute->name,
                    'value'              => $attrValue->value,
                ];
            }
            return null;
        })->filter()->toArray();
    }

    public function updateProduct()
    {
        $this->validate();

        $product = Product::findOrFail($this->productId);
        $product->update([
            'name'          => $this->name,
            'description'   => $this->description,
            'price'         => $this->price,
            'ean'           => $this->ean,
            'category_id'   => $this->category_id,
            'general_stock' => $this->general_stock,
        ]);

        if ($this->newImages) {
            $existingImages = $product->images ?? [];
            foreach ($this->newImages as $image) {
                $existingImages[] = $this->storeImage($image);
            }
            $product->update(['images' => $existingImages]);
        }

        ProductVariation::where('product_id', $product->id)->delete();

        // Create new variations
        foreach ($this->productVariations as $variationData) {
            $variation = new ProductVariation();
            $variation->product_id = $product->id;
            $variation->price = $product->price; // or customize
            $variation->stock = $this->general_stock; // or customize
            $variation->sku = 'SKU-' . strtoupper(uniqid());
            $variation->save();

            // Attach attribute-value pair
            $variation->attributeValues()->attach([
                $variationData['attribute_id'] => [
                    'attribute_value_id' => $variationData['attribute_value_id']
                ]
            ]);
        }

        session()->flash('success', 'Produsul a fost actualizat cu succes!');
        $this->dispatch('flashMessage', ['type' => 'success', 'message' => 'Produsul a fost actualizat cu succes!']);

        return redirect()->route('admin.products');
    }

    public function deleteVariation($variationId)
    {
        $variation = ProductVariation::find($variationId);
        if ($variation) {
            $variation->delete();
        }
    }

    private function storeImage($image)
    {
        $cleanedName = Str::slug($this->name);
        $uniqueSuffix = substr(md5(uniqid()), 0, 6);
        $filename = "{$cleanedName}-{$uniqueSuffix}." . $image->getClientOriginalExtension();
        $path = $image->storeAs('images/uploads/products', $filename, 'public');
        return "/storage/{$path}";
    }

    public function removeImage($productId, $imageKey)
    {
        $product = Product::find($productId);
        if ($product && isset($product->images[$imageKey])) {
            Storage::delete('public/' . $product->images[$imageKey]);
            $images = $product->images;
            unset($images[$imageKey]);
            $product->images = array_values($images);
            $product->save();
            session()->flash('success', 'Imagine ștearsă!');
            $this->dispatch('flashMessage', ['type' => 'success', 'message' => 'Imagine ștearsă!']);
        }
    }

    public function addAttribute($name)
    {
        $name = trim($name);
        if (!$name) return;

        $attribute = Attribute::firstOrCreate(['name' => $name]);
        $this->allAttributes = Attribute::with('values')->get()->toArray();

        $this->dispatch('attributesUpdated', [
            'attributes' => $this->allAttributes
        ]);
    }

    public function addValue($attributeId, $value)
    {
        $value = trim($value);
        if (!$attributeId || !$value) return;

        $attribute = Attribute::find($attributeId);
        if ($attribute) {
            $attribute->values()->firstOrCreate(['value' => $value]);
            $this->allAttributes = Attribute::with('values')->get()->toArray();

            $this->dispatch('attributesUpdated', [
                'attributes' => $this->allAttributes
            ]);
        }
    }


    public function render()
    {
        $categories = Category::all();
        return view('livewire.products.product-edit', [
            'categories'    => $categories,
            'allAttributes' => $this->allAttributes,
        ]);
    }
}
