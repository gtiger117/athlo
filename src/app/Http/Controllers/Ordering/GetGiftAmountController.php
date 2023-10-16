<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetGiftAmountController extends Controller
{
    public function get_gift_amount(Request $request)
    {
        // $this->authorize('view-any', PaymentMethod::class);
        $validator = Validator::make($request->all(), [
            'is_gift' => 'required|integer',
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

        $postData = [
            'is_gift'=> $request->is_gift, 
            'items'=> $request->items,
        ];

        $gift_amount_response = Helper::get_gift_amount($postData);       
        $gift_amount_response = json_decode($gift_amount_response);   
        // return true;
        return response()->json($gift_amount_response);
    }
}
