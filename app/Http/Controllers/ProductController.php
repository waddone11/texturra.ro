<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Characteristic;
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
        $product = Product::where('slug', $slug)->with(['category', 'colors', 'materials'])->firstOrFail();

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
            'manufactoringTypes'
        ));
    }

}
