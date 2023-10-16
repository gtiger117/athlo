<?php

namespace App\Http\Controllers\ProductCatalogue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetVariantsController extends Controller
{
    public function get_variant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'colour_id' => [
                'required','integer',
                Rule::exists('colours', 'id'),
            ],
            'size_id' => [
                'required','integer',
                Rule::exists('sizes', 'id'),
            ],
            'product_id' => [
                'required','integer',
                Rule::exists('products', 'id'),
            ]
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = DB::table('product_variants')
                ->where('colour_id', $request->colour_id)
                ->where('size_id', $request->size_id)
                ->where('product_id', $request->product_id)
                ->first();

        $variant = $query;

        return response()->json($variant);
    }
}
