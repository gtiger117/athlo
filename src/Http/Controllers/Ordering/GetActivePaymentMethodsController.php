<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetActivePaymentMethodsController extends Controller
{
    public function get_active_payment_methods(Request $request)
    {
        // $this->authorize('view-any', PaymentMethod::class);
        
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:payment_method_types,id',
            'pickup_id' => 'nullable|integer|exists:pickups,id',
            'country_code' => 'nullable|string|exists:countries,code',
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

        $active_payment_methods = Helper::get_active_payment_methods($postData);       
        $active_payment_methods = json_decode($active_payment_methods);   
        // return true;
        return response()->json($active_payment_methods);
    }
}
