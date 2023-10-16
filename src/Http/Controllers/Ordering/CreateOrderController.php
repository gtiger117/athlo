<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Services\GetPaymentMethodService;
use App\Services\CreatePaymentGatewayOrderService;

class CreateOrderController extends Controller
{
    protected $getpaymentmethod;
    protected $getpickup;
    protected $createpaymentgatewayorder;

    public function __construct(GetPaymentMethodService $GetPaymentMethodService, CreatePaymentGatewayOrderService $CreatePaymentGatewayOrderService)
    {
        $this->getpaymentmethod = $GetPaymentMethodService;
        $this->createpaymentgatewayorder = $CreatePaymentGatewayOrderService;
    }

    public function create_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'delivery_date' => 'date',
            'customer' => 'required|array',
            'customer.id' => [
                'nullable','integer',
                Rule::exists('tbaccounts_user', 'CLM_ACCOUNT_ID'),
            ],
            'customer.name' => 'required',
            'customer.surname' => 'required',
            'customer.email' => 'required|email',
            'customer.phone' => 'required',
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
            'shipping_address' => 'required|array',
            'shipping_address.pickup_id' => 'nullable|integer',
            'shipping_address.address' => 'required_if:shipping_address.pickup_id,null',
            'shipping_address.number' => 'required_if:shipping_address.pickup_id,null',
            'shipping_address.city' => 'required_if:shipping_address.pickup_id,null',
            'shipping_address.country_code' => 'required_if:shipping_address.pickup_id,null',
            'shipping_address.postal_code' => 'required_if:shipping_address.pickup_id,null',
            'payment_method_type_id' => 'required|integer',
            'payment_method_type_id' => [
                'required','integer',
                Rule::exists('payment_method_types', 'id'),
            ],
            'shipping_method_type_id' => 'required|integer',
            'shipping_method_type_id' => [
                'required','integer',
                Rule::exists('shipping_method_types', 'id'),
            ]
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $postData = [
                        'id'=> $request->shipping_method_type_id, 
                        'country_code'=> $request->shipping_address['country_code'],
                        'pickup_id'=> $request->shipping_address['pickup_id'],
                        'items'=> $request->items,
                    ];
        $shipping_response = Helper::get_active_shipping_methods($postData);       
        $shipping_response = json_decode($shipping_response);
        
        if(isset($shipping_response[0])){
            $shipping_amount = $shipping_response[0]->amount;
            $shipping_tax = $shipping_response[0]->tax;
        }
        else{
            return response()->json([
                'errors' => 'Not shipped',
            ], 422);
        }


        $postData = [
                    'id' => $request->payment_method_type_id,
                    'country_code'=> $request->shipping_address['country_code'],
                    'pickup_id'=> $request->shipping_address['pickup_id'],
                ];

        $payment_methods_response = Helper::get_active_payment_methods($postData);       
        $payment_methods_response = json_decode($payment_methods_response);


        $payment_gateway_code = '';

        if(isset($payment_methods_response[0])){
            $payment_amount = $payment_methods_response[0]->amount;
            $payment_tax = $payment_methods_response[0]->tax;
            $payment_method_id = $payment_methods_response[0]->payment_method_id;
            $payment_gateway_code = $payment_methods_response[0]->payment_gateway_code;
        }
        else{
            return response()->json([
                'errors' => 'No payment method found',
            ], 422);
        }
        // echo 'payment_gateway_code: '.$payment_gateway_code.'<br>';
        

        $postData = [
            'is_gift'=> $request->is_gift, 
            'items'=> $request->items
        ];
        $gift_amount_response = Helper::get_gift_amount($postData);       
        $gift_amount_response = json_decode($gift_amount_response); 

        $is_gift = $request->is_gift;
        $gift_amount = $gift_amount_response->amount;
        $gift_tax = $gift_amount_response->tax;

        $discount_amount = 0;
        $discount_tax = 0;
        $voucher_id = null;
        $voucher_code = '';

        if ($request->has('voucher_code') && $request->voucher_code != '') {
            $postData = [
                'voucher_code' => $request->voucher_code, 
            ];
            $validate_voucher_response = Helper::validate_voucher_method($postData);       
            $validate_voucher_response = json_decode($validate_voucher_response);       

            if($validate_voucher_response->active == true){
                $discount_amount = $validate_voucher_response->amount;
                $discount_tax = $validate_voucher_response->tax;
                $voucher_id = $validate_voucher_response->id;
                $voucher_code = $request->voucher_code;
            }
        }
        // echo 'voucher_id: '.$voucher_id.'<br>';
        // return true;

        $total_quantity = 0;
        $total_price = 0;
        $total_tax = 0;
        $total_price_with_tax = 0;
        foreach($request->items as $item){
            $total_quantity += $item['quantity'];
            $product = DB::table('tbpc_products_groups')
                                ->where('CLMPRODGROUP_ID', $item['product_id'])
                                ->first();
            $price = $product->CLMPRODGROUP_PRICE;
            $tax_percentage = $product->CLMPRODGROUP_TAX_PERCENTAGE;
            if($item['variant_id'] != ''){
                $variant = DB::table('tbpc_products')
                                ->where('CLMPRODUCT_ID', $item['variant_id'])
                                ->where('CLMPRODUCT_GROUPID', $item['product_id'])
                                ->first();
                $price = $variant->CLMPRODUCT_PRICE;
                $tax_percentage = $variant->CLMTAX_PERCENTAGE;
            }
            $price_with_tax = round($price * (100 + $tax_percentage) / 100, 2);
            $product_tax = $price_with_tax - $price;

            $total_price += $item['quantity'] * $price;
            $total_tax += $product_tax * $item['quantity'];
            $total_price_with_tax += $price_with_tax * $item['quantity'];

        }
        $customer_name = $request->customer['name'] . ' ' . $request->customer['surname'];

        $address = $request->shipping_address['address'];
        $address_number = $request->shipping_address['number'];
        $city = $request->shipping_address['city'];
        $country = $request->shipping_address['country_code'];
        $postal_code = $request->shipping_address['postal_code']; 
        $pickup_id = null; 
        $pickup_name = ''; 

        $is_pickup = 0;
        if($request->shipping_address['pickup_id'] != ''){
            $postData = [
                'id' => $request->shipping_address['pickup_id'], 
            ];

            $pickup_response = Helper::get_pickups($postData);       
            $pickup_response = json_decode($pickup_response);

            // print_r($pickup_response);

            if(isset($pickup_response->data[0])){
                $address = $pickup_response->data[0]->address;
                $address_number = '';
                $city = $pickup_response->data[0]->city;
                $country = $pickup_response->data[0]->country;
                $postal_code = $pickup_response->data[0]->postal_code; 
                $pickup_id = $pickup_response->data[0]->id; 
                $pickup_name = $pickup_response->data[0]->displayname; 
                $is_pickup = 1;
            }
            else{
                return response()->json([
                    'errors' => 'No pickup found',
                ], 422);
            }
        }

        $max_temp_order_id = DB::table('tbordering_temp_orders')->max('CLMTEMPORDER_ID');
        $max_order_order_id = DB::table('tbordering_orders')->max('CLMORDER_ID');
        $max_closed_order_id = DB::table('tbordering_closed_orders')->max('CLMCLORD_ID');

        $order_id = max([(int)$max_temp_order_id, (int)$max_order_order_id, (int)$max_closed_order_id]) + 1;

        $state = $city;
        $hash = md5($order_id . date('Y-m-d'));
        

        $data = [
            
                    'CLMTEMPORDER_ID' => $order_id,
                    'CLMTEMPORDER_HASH' => $hash, 
                    'CLMTEMPORDER_DATE' => date('Y-m-d'), 
                    'CLMTEMPORDER_ITEMS' => $total_quantity,
                    'CLMTEMPORDER_PRICE_WO_TAX' => $total_price,
                    'CLMTEMPORDER_DISCOUNT_WO_TAX' => $total_price,
                    'CLMTEMPORDER_TAX' => $total_tax,
                    'CLMTEMPORDER_SHIPMENT' => $shipping_amount,
                    'CLMTEMPORDER_SHIPMENT_TAX'  => $shipping_tax,
                    'CLMTEMPORDER_VOUCHERID' => $voucher_id,
                    'CLMTEMPORDER_VOUCHERCODE' => $voucher_code,
                    'CLMTEMPORDER_CUSTOMER' => $request->customer['id'],
                    'CLMTEMPORDER_CUST_NAME' => $request->customer['name'],
                    'CLMTEMPORDER_CUST_SURNAME' => $request->customer['surname'],
                    'CLMTEMPORDER_EMAIL' => $request->customer['email'],
                    'CLMTEMPORDER_CUST_TELEPHONE' => $request->customer['phone'],
                    'CLMTEMPORDER_PICKUPID' => $pickup_id,
                    'CLMTEMPORDER_PICKUPNAME' => $pickup_name,
                    'CLMTEMPORDER_ADR_1' => $address,
                    'CLMTEMPORDER_ADR_2' => $address_number,
                    'CLMTEMPORDER_ADR_CODE' => $postal_code,
                    'CLMTEMPORDER_ADR_STATE' => $state,
                    'CLMTEMPORDER_ADR_CITY' => $city,
                    'CLMTEMPORDER_ADR_COUNTRY' => $country,
                    'CLMTEMPORDER_DELIVERY_METHOD_TYPE' => $request->shipping_method_type_id,                    
                    'CLMTEMPORDER_CURRENCY' => 'EUR',
                    'CLMTEMPORDER_CURRENCY_SIGN' => '€',
                    'CLMTEMPORDER_PAYMETHOD' => null,
                    'CLMTEMPORDER_PAYMETHOD_AMT' => $payment_amount,
                    'CLMTEMPORDER_PAYMETHOD_TAX' => $payment_tax,
                    'CLMTEMPORDER_IS_GIFT' => $is_gift,
                    'CLMTEMPORDER_GIFT_AMT' => $gift_amount,
                    'CLMTEMPORDER_GIFT_TAX' => $gift_tax,
                    'CLMTEMPORDER_PAYMETHOD' => $payment_gateway_code,
                    'CLMTEMPORDER_NOTES' => $request->notes,
                    'CLMTEMPORDER_GIFT_NOTES' => $request->gift_notes,
                    'CLMTEMPORDER_REFERRER' => $request->referrer
        ];
        

        $temporary_order = DB::table('tbordering_temp_orders')->insert($data);
        if($temporary_order){
            foreach($request->items as $item){                                
                
                $product =  DB::table('tbpc_products')
                                ->select('*', DB::raw('CASE 
                                                        WHEN CLMPRODUCT_OFF_PRICE > 0 THEN ROUND(CLMPRODUCT_OFF_PRICE * (100 + CLMTAX_PERCENTAGE) / 100, 2) 
                                                        WHEN CLMPRODUCT_OFFERPRICE > 0 THEN ROUND(CLMPRODUCT_OFFERPRICE * (100 + CLMTAX_PERCENTAGE) / 100, 2) 
                                                        ELSE ROUND(CLMPRODUCT_PRICE * (100 + CLMTAX_PERCENTAGE) / 100, 2)
                                                    END as pricesold'))
                                ->where('CLMPRODUCT_ID', $item['variant_id'])
                                ->where('CLMPRODUCT_GROUPID', $item['product_id'])
                                ->first();                                
                $product_name = $product->CLMPRODUCT_ML_NAME;
                $prodcode = $product->CLMPRODUCT_EXTCODE;
                $prodseccode = $product->CLMPRODUCT_SECEXTCODE;
                $tax = $product->CLMTAX_PERCENTAGE;
                $unit_price = ROUND($product->pricesold * 100 / (100 + $tax), 2);
                $unit_tax = $product->pricesold - $unit_price;

                $size_name =  DB::table('tbchars_values')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMCHAR_ID', env('SIZE_ID'))
                                ->where('CLMPRODUCTID', $item['variant_id'])
                                ->first();
                if($size_name){
                    $product_name .= ' (SIZE: '.$size_name->CLMCHARVALUE_ML_NAME.')';

                }
                $product_name .= ' (Code: '.$prodseccode.')';

                $data = [            
                            'CLMTEMPORDIT_ORDERID' => $order_id, 
                            'CLMTEMPORDIT_PRNAME' => $product_name,
                            'CLMTEMPORDIT_QUANTITY' => $item['quantity'],
                            'CLMTEMPORDIT_UNIT_PRICE' => $unit_price,
                            'CLMTEMPORDIT_UNIT_ORIGINALPRICE' => $unit_price,
                            'CLMTEMPORDIT_ACTUAL_PRICE_PAID' => $unit_price,
                            'CLMTEMPORDIT_TAX' => $unit_tax,
                            'CLMTEMPORDIT_PRID' => $item['variant_id'],
                            'CLMTEMPORDIT_PROD_EXTCODE' => $prodcode,
                            'CLMTEMPORDIT_LOYALTY_POINTS' => 0,
                            'CLMTEMPORDIT_PROD_SECEXTCODE' => $prodseccode
                        ];
                DB::table('tbordering_temp_order_items')->insert($data);
            }
            $order = DB::table('tbordering_temp_orders')
                        ->where('CLMTEMPORDER_ID', $order_id)
                        ->first();
         
            // You can customize the response as per your needs

            switch($payment_gateway_code){
                case 'del':
                    $postData = [
                        'id' => $order_id, 
                    ];
                    $order_response = Helper::finalize_order($postData);       
                    $order_response = json_decode($order_response);  

                    break;
                default:
                    $order_response = [
                        'message' => 'Going to payment gateway',
                        'status' => true,
                        'redirect_link' => env('APP_URL').'/orderpaymentgateway?hash='.$hash,
                        'order' => $order,
                    ];
                    break;
            }

            return response()->json($order_response);
        }

        return response()->json([
            'errors' => 'Failure to add temporary found',
        ], 422);
    }
}

