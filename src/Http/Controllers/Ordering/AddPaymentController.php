<?php

namespace Gtiger117\Athlo\Http\Controllers\Ordering;

use App\Helpers\Helper;
use Gtiger117\Athlo\Http\Controllers\Controller;
use Gtiger117\Athlo\Models\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddPaymentController extends Controller
{
    public function add_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer', 
            'amount' => 'required|numeric', 
            'payment_gateway' => 'required|exists:payment_gateways,ext_code', 
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $postData = [
            'order_id'=> $request->order_id, 
            'amount'=> $request->amount,
            'payment_gateway'=> $request->payment_gateway,
        ];

        $add_order_payment_response = Helper::add_order_payment($postData);       
        $add_order_payment_response = json_decode($add_order_payment_response);   
        // return true;
        return response()->json($add_order_payment_response);

        
    }
}
