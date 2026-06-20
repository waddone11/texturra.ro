<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function getValues($id)
    {
        $attribute = Attribute::with('values')->find($id);
        return response()->json($attribute->values);
    }

    public function store(Request $request) {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $attribute = Attribute::create(
            [
                'name' => $validated['name'],
                'description' => $validated['name'],
            ]
        );

        return response()->json($attribute);
    }

    public function addValue(Request $request, $id) {
        $validated = $request->validate(['value' => 'required|string|max:255']);

        $attribute = Attribute::findOrFail($id);
        $value = $attribute->values()->create(['value' => $validated['value']]);

        return response()->json($value);
    }
}


