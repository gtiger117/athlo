<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodType;
use App\Models\VoucherOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreditCardGatewayController extends Controller
{
    public function create_credit_card_payment_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hash' => 'required|string|exists:tbordering_temp_orders,CLMTEMPORDER_HASH',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = DB::table('tbordering_temp_orders')
                                ->where('CLMTEMPORDER_HASH', $request->hash)
                                ->first();

        $payment_amount = round($order->CLMTEMPORDER_DISCOUNT_WO_TAX + $order->CLMTEMPORDER_TAX + $order->CLMTEMPORDER_GIFT_AMT + $order->CLMTEMPORDER_GIFT_TAX + $order->CLMTEMPORDER_SHIPMENT + $order->CLMTEMPORDER_SHIPMENT_TAX + $order->CLMTEMPORDER_PAYMETHOD_AMT + $order->CLMTEMPORDER_PAYMETHOD_TAX, 2);
        
        switch($order->CLMTEMPORDER_PAYMETHOD){
            case 'jcc':
                $data = [            
                            'order_id' => $order->CLMTEMPORDER_ID, 
                            'hash' => $order->CLMTEMPORDER_HASH, 
                            'amount' => $payment_amount,
                            'type' => 'order',
                        ];
                $createpaymentgatewayorder_response = Helper::create_jcc_order($data);       
                $payment_gateway = $createpaymentgatewayorder_response;
                echo $payment_gateway;
                break;
        }
    }
    public function create_credit_card_payment_voucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hash' => 'required|string|exists:voucher_orders,hash',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = VoucherOrder::where('hash', $request->hash)->first();
        $payment_method = PaymentMethodType::join('payment_methods', 'payment_methods.payment_method_type_id', '=', 'payment_method_types.id')
                                            ->join('payment_gateways', 'payment_gateways.id', '=', 'payment_method_types.payment_gateway_id')
                                            ->select('payment_method_types.*', 'payment_gateways.ext_code as method')
                                            ->where('payment_methods.active', 1)
                                            ->where('payment_method_types.active', 1)
                                            ->where('payment_gateways.type', 'visa')
                                            ->orderBy('sort_order', 'asc')
                                            ->first();

        
        $payment_amount = round($order->amount, 2);
        
        switch($payment_method->method){
            case 'jcc':
                $data = [            
                            'order_id' => $order->id, 
                            'hash' => $request->hash, 
                            'amount' => $payment_amount,
                            'type' => 'voucher',
                        ];
                $createpaymentgatewayorder_response = Helper::create_jcc_order($data);       
                $payment_gateway = $createpaymentgatewayorder_response;
                echo $payment_gateway;
                break;
        }
    }
    public function finalize_payment_gateway(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hash' => 'required|string|exists:tbordering_temp_orders,CLMTEMPORDER_HASH',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = DB::table('tbordering_temp_orders')
                                ->where('CLMTEMPORDER_HASH', $request->hash)
                                ->first();

        $payment_amount = round($order->CLMTEMPORDER_DISCOUNT_WO_TAX + $order->CLMTEMPORDER_TAX + $order->CLMTEMPORDER_GIFT_AMT + $order->CLMTEMPORDER_GIFT_TAX + $order->CLMTEMPORDER_SHIPMENT + $order->CLMTEMPORDER_SHIPMENT_TAX + $order->CLMTEMPORDER_PAYMETHOD_AMT + $order->CLMTEMPORDER_PAYMETHOD_TAX, 2);
        

        switch($order->CLMTEMPORDER_PAYMETHOD){
            case 'jcc':
                // $_POST['ResponseCode'] = 1;
                $response = $_POST;

                $status = 'failure';
                
                if ($response['ResponseCode'] == 1){
                    $status = 'success';
                    $postData = [
                        'id' => $order->CLMTEMPORDER_ID, 
                    ];
                    $order_response = Helper::finalize_order($postData);       
                    $order_response = json_decode($order_response);

                    $postData = [
                        'order_id'=> $order->CLMTEMPORDER_ID, 
                        'amount'=> $payment_amount,
                        'payment_gateway'=> $order->CLMTEMPORDER_PAYMETHOD,
                    ];
            
                    $add_order_payment_response = Helper::add_order_payment($postData);       
                    $add_order_payment_response = json_decode($add_order_payment_response); 
                }
                else{
                    
                }
                
                echo '<script type="text/javascript">window.location = "'.env('APP_URL').'/page/finalizeorder/'.$request->hash.'?status='.$status.'";</script>';

                break;
        }
    }
}
