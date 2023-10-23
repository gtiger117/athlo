<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetShippingController extends Controller
{
    public function get_active_shipping_methods(Request $request)
    {
        // $this->authorize('view-any', PaymentMethod::class);
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:shipping_method_types,id',
            'pickup_id' => 'nullable|integer|exists:pickups,id',
            'country_code' => 'nullable|string|exists:countries,code',
            'items' => 'required|array',
            'items.*.product_id' => [
                'required','integer',
                Rule::exists('tbpc_products_groups', 'CLMPRODGROUP_ID'),
            ],
            'items.*.variant_id' => [
                'nullable','integer',
                Rule::exists('tbpc_products', 'CLMPRODUCT_ID'),
            ],
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->pickup_id == '' && $request->country_code == '') {
            return response()->json([
                'errors' => 'Must add destination',
            ], 422);
        }

        $postData = [
            'id'=> $request->id, 
            'country_code'=> $request->country_code,
            'pickup_id'=> $request->pickup_id,
            'items'=> $request->items,
        ];

        $shipping_response = Helper::get_active_shipping_methods($postData);       
        $shipping_response = json_decode($shipping_response);   
        // return true;
        return response()->json($shipping_response);

    }
}
