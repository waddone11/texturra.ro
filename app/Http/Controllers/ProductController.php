<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\EmagCategory;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\AttributeValue;
use App\Models\Attribute;
use App\Models\ManufactoringType;

class ProductController extends Controller
{
    /**
     * Display products by category slug.
     */
    public function showByCategory($slug)
    {
        $activeCategory = Category::where('slug', $slug)->firstOrFail();

        return view('products.listing', [
            'categoryName' => $activeCategory->name,
            'activeCategory' => $activeCategory,
        ]);
    }

    /**
     * Display product details.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with(['category', 'variations.attributeValues.attribute'])->firstOrFail();
        $sizeAttributeId = Attribute::where('name', 'Mărime')->value('id');
        $colorAttributeId = Attribute::where('name', 'Culoare')->value('id');
        $materialAttributeId = Attribute::where('name', 'Material')->value('id');

        $availableSizes = $product->variations->pluck('attributeValues')
            ->flatten()
            ->where('attribute_id', $sizeAttributeId)
            ->unique('id');

        $availableColors = collect();
        if ($availableSizes->isNotEmpty()) {
            $firstSizeId = $availableSizes->first()->id;
            $availableColors = $product->variations->filter(function ($variation) use ($firstSizeId, $colorAttributeId) {
                return $variation->attributeValues->pluck('id')->contains($firstSizeId);
            })->pluck('attributeValues')->flatten()->where('attribute_id', $colorAttributeId)->unique('id');
        }

        $availableMaterials = collect();
        if ($availableColors->isNotEmpty()) {
            $firstColorId = $availableColors->first()->id;
            $availableMaterials = $product->variations->filter(function ($variation) use ($firstSizeId, $firstColorId, $materialAttributeId) {
                return $variation->attributeValues->pluck('id')->contains($firstSizeId) &&
                    $variation->attributeValues->pluck('id')->contains($firstColorId);
            })->pluck('attributeValues')->flatten()->where('attribute_id', $materialAttributeId)->unique('id');
        }

        $parsedCharacteristics = $product->parsedCharacteristics();
        $characteristicIds = collect($parsedCharacteristics)->pluck('id');
        $characteristicLabels = Characteristic::whereIn('characteristic_id', $characteristicIds)
            ->pluck('name', 'characteristic_id');

        $characteristicsWithLabels = collect($parsedCharacteristics)->map(function ($characteristic) use ($characteristicLabels) {
            return [
                'label' => $characteristicLabels[$characteristic['id']] ?? 'Unknown',
                'value' => $characteristic['value'],
            ];
        });
        $manufactoringTypes = ManufactoringType::orderBy('price')->get();

        return view('products.detail', compact(
            'product',
            'characteristicsWithLabels',
            'sizeAttributeId',
            'colorAttributeId',
            'materialAttributeId',
            'availableSizes',
            'availableColors',
            'availableMaterials',
            'manufactoringTypes'
        ));
    }

    public function getAvailableFilters(Request $request)
    {
        $categoryId = $request->input('category_id');
        $query = Product::where('category_id', $categoryId);

        if ($request->filled('sizes')) {
            $query->whereHas('variations.attributeValues', function ($q) use ($request) {
                $q->whereIn('value', $request->input('sizes'));
            });
        }

        if ($request->filled('colors')) {
            $query->whereHas('variations.attributeValues', function ($q) use ($request) {
                $q->whereIn('value', $request->input('colors'));
            });
        }

        if ($request->filled('materials')) {
            $query->whereHas('variations.attributeValues', function ($q) use ($request) {
                $q->whereIn('value', $request->input('materials'));
            });
        }

        // Get available filters based on the remaining products
        $availableSizes = $query->clone()->with('variations.attributeValues')->get()
            ->pluck('variations.*.attributeValues')
            ->flatten()
            ->where('attribute.name', 'Mărime')
            ->pluck('value')
            ->unique()
            ->values();

        $availableColors = $query->clone()->with('variations.attributeValues')->get()
            ->pluck('variations.*.attributeValues')
            ->flatten()
            ->where('attribute.name', 'Culoare')
            ->pluck('value')
            ->unique()
            ->values();

        $availableMaterials = $query->clone()->with('variations.attributeValues')->get()
            ->pluck('variations.*.attributeValues')
            ->flatten()
            ->where('attribute.name', 'Material')
            ->pluck('value')
            ->unique()
            ->values();

        return response()->json([
            'sizes' => $availableSizes,
            'colors' => $availableColors,
            'materials' => $availableMaterials,
        ]);
    }

}
