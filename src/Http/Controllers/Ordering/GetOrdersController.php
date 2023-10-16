<?php

namespace App\Http\Controllers\Ordering;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\PaymentGateway;
use App\Models\PaymentMethodType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GetOrdersController extends Controller
{
    public function get_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'hash' => 'nullable|string',
            'customer_id' => 'nullable|integer|exists:tbaccounts_user,CLM_ACCOUNT_ID',
            'perPage' => 'nullable|integer',
            'page' => 'nullable|integer',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $perPage = 20;
        $page = 1;
        if ($request->has('perPage') && is_numeric($request->perPage)) {
            $perPage = $request->perPage;
        }
        if ($request->has('page') && is_numeric($request->page)) {
            $page = $request->page;
        }

        $query_orders = DB::table('tbordering_orders')
                            ->selectRaw("CLMORDER_ID as id,
                                        CASE 
                                            WHEN CLMORDER_STATUS = 3 THEN 'to be cancelled' 
                                            ELSE 'open' 
                                        END as order_type,
                                        'open' as type,
                                        CLMORDER_REL_ORDER_ID as rel_order_id,
                                        CLMORDER_BANK_REF as bank_red,
                                        CLMORDER_HASH as order_hash,
                                        CLMORDER_DATE as order_date,
                                        CLMORDER_CUSTOMER as customer_id,
                                        CLMORDER_IP as order_ip,
                                        CLMORDER_COMPANY_ID as company_id,
                                        CLMORDER_ITEMS as number_of_items,
                                        CLMORDER_IS_GIFT as is_gift,
                                        ROUND(CLMORDER_PRICE_WO_TAX, 2) as price,
                                        ROUND(CLMORDER_PRICE_WO_TAX - CLMORDER_DISCOUNT_WO_TAX, 2) as discount,
                                        ROUND(CLMORDER_TAX + CLMORDER_DISCOUNT_WO_TAX, 2) as amount_after_discount,
                                        ROUND(CLMORDER_TAX, 2) as taxamt,
                                        ROUND(CLMORDER_PRICE_WO_TAX * CLMORDER_TAX / CLMORDER_DISCOUNT_WO_TAX - CLMORDER_TAX, 2) as taxdiscount,
                                        ROUND(CLMORDER_GIFT_AMT, 2) as giftamt,
                                        ROUND(CLMORDER_GIFT_TAX, 2) as gifttaxamt,
                                        ROUND(CLMORDER_SHIPMENT, 2) as shipmentamt,
                                        ROUND(CLMORDER_SHIPMENT_TAX, 2) as shipmenttaxamt,
                                        ROUND( CLMORDER_PAYMETHOD_AMT, 2) as paymethodeamt,
                                        ROUND(CLMORDER_PAYMETHOD_TAX, 2) as paymethodtaxamt,
                                        CLMORDER_STATUS as order_status,
                                        CLMORDER_CUST_NAME as customer_name,
                                        CLMORDER_CUST_SURNAME as customer_surname,
                                        CLMORDER_EMAIL as order_email,
                                        CLMORDER_BILLNAME as billing_name,
                                        CLMORDER_BILLSURNAME as billing_surname,
                                        CLMORDER_BILLADR_1 as billing_address1,
                                        CLMORDER_BILLADR_2 as billing_address2,
                                        CLMORDER_BILLADR_CITY as billling_city,
                                        CLMORDER_BILLADR_COUNTRY as billing_country,
                                        CLMORDER_BILLADR_CODE as billing_postal_code,
                                        CLMORDER_BILLADR_STATE as billing_state,
                                        CLMORDER_CUST_TELEPHONE as phone,
                                        CLMORDER_PICKUPID as pickupid,
                                        CLMORDER_PICKUPNAME as pickupname,
                                        CLMORDER_DELIVERY_METHOD_TYPE as delivery_method_type,
                                        CLMORDER_ADR_1 as address1,
                                        CLMORDER_ADR_2 as address2,
                                        CLMORDER_ADR_CODE as postal_code,
                                        CLMORDER_ADR_CITY as city,
                                        CLMORDER_ADR_STATE as state,
                                        CLMORDER_ADR_COUNTRY as country,
                                        CLMORDER_CURRENCY as currency,
                                        CLMORDER_CURRENCY_SIGN as currency_sign,
                                        CLMORDER_PAYMETHOD as paymethod,
                                        CLMORDER_TRACKING as tracking_number,
                                        CLMORDER_VOUCHERID as voucher_id,
                                        CLMORDER_VOUCHERCODE as voucher_code,
                                        CLMORDER_NOTES as notes,
                                        CLMORDER_GIFT_NOTES as gift_notes,
                                        CLMORDER_REFERRER as referrer,
                                        CLMORDER_CAMPAIGNID_FK as campaignid,
                                        CLMORDER_CAMPAIGNITEMID_FK as campignitemid,
                                        CLMORDER_CLICKANDCOLLECT as clickandcollect,
                                        CLMORDER_WAREHOUSE as warehouse");
        $query_closed_orders = DB::table('tbordering_closed_orders')
                            ->selectRaw("CLMCLORD_ID as id,
                                        CLMCLORD_STATUS as order_type,
                                        'closed' as type,
                                        CLMCLORD_REL_ORDER_ID as rel_order_id,
                                        CLMCLORD_BANK_REF as bank_red,
                                        CLMCLORD_HASH as order_hash,
                                        CLMCLORD_DATE as order_date,
                                        CLMCLORD_CUSTOMER as customer_id,
                                        CLMCLORD_IP as order_ip,
                                        CLMCLORD_COMPANY_ID as company_id,
                                        CLMCLORD_ITEMS as number_of_items,
                                        CLMCLORD_IS_GIFT as is_gift,
                                        ROUND(CLMCLORD_PRICE_WO_TAX, 2) as price,
                                        ROUND(CLMCLORD_PRICE_WO_TAX - CLMCLORD_DISCOUNT_WO_TAX, 2) as discount,
                                        ROUND(CLMCLORD_TAX + CLMCLORD_DISCOUNT_WO_TAX, 2) as amount_after_discount,
                                        ROUND(CLMCLORD_TAX, 2) as taxamt,
                                        ROUND(CLMCLORD_PRICE_WO_TAX * CLMCLORD_TAX / CLMCLORD_DISCOUNT_WO_TAX - CLMCLORD_TAX, 2) as taxdiscount,
                                        ROUND(CLMCLORD_GIFT_AMT, 2) as giftamt,
                                        ROUND(CLMCLORD_GIFT_TAX, 2) as gifttaxamt,
                                        ROUND(CLMCLORD_SHIPMENT, 2) as shipmentamt,
                                        ROUND(CLMCLORD_SHIPMENT_TAX, 2) as shipmenttaxamt,
                                        ROUND( CLMCLORD_PAYMETHOD_AMT, 2) as paymethodeamt,
                                        ROUND(CLMCLORD_PAYMETHOD_TAX, 2) as paymethodtaxamt,
                                        CLMCLORD_STATUS as order_status,
                                        CLMCLORD_CUST_NAME as customer_name,
                                        CLMCLORD_CUST_SURNAME as customer_surname,
                                        CLMCLORD_EMAIL as order_email,
                                        CLMCLORD_BILLNAME as billing_name,
                                        CLMCLORD_BILLSURNAME as billing_surname,
                                        CLMCLORD_BILLADR_1 as billing_address1,
                                        CLMCLORD_BILLADR_2 as billing_address2,
                                        CLMCLORD_BILLADR_CITY as billling_city,
                                        CLMCLORD_BILLADR_COUNTRY as billing_country,
                                        CLMCLORD_BILLADR_CODE as billing_postal_code,
                                        CLMCLORD_BILLADR_STATE as billing_state,
                                        CLMCLORD_CUST_TELEPHONE as phone,
                                        CLMCLORD_PICKUPID as pickupid,
                                        CLMCLORD_PICKUPNAME as pickupname,
                                        CLMCLORD_DELIVERY_METHOD_TYPE as delivery_method_type,
                                        CLMCLORD_ADR_1 as address1,
                                        CLMCLORD_ADR_2 as address2,
                                        CLMCLORD_ADR_CODE as postal_code,
                                        CLMCLORD_ADR_CITY as city,
                                        CLMCLORD_ADR_STATE as state,
                                        CLMCLORD_ADR_COUNTRY as country,
                                        CLMCLORD_CURRENCY as currency,
                                        CLMCLORD_CURRENCY_SIGN as currency_sign,
                                        CLMCLORD_PAYMETHOD as paymethod,
                                        CLMCLORD_TRACKING as tracking_number,
                                        CLMCLORD_VOUCHERID as voucher_id,
                                        CLMCLORD_VOUCHERCODE as voucher_code,
                                        CLMCLORD_NOTES as notes,
                                        CLMCLORD_GIFT_NOTES as gift_notes,
                                        CLMCLORD_REFERRER as referrer,
                                        CLMCLORD_CAMPAIGNID_FK as campaignid,
                                        CLMCLORD_CAMPAIGNITEMID_FK as campignitemid,
                                        CLMCLORD_CLICKANDCOLLECT as clickandcollect,
                                        CLMCLORD_WAREHOUSE as warehouse");

        if ($request->has('id') && $request->id != '') {
            $query_orders = $query_orders->where('CLMORDER_ID', $request->id);
            $query_closed_orders = $query_closed_orders->where('CLMCLORD_ID', $request->id);
        }
        if ($request->has('hash') && $request->hash != '') {
            $query_orders = $query_orders->where('CLMORDER_HASH', $request->hash);
            $query_closed_orders = $query_closed_orders->where('CLMCLORD_HASH', $request->hash);
        }
        if ($request->has('customer_id') && $request->customer_id != '') {
            $query_orders = $query_orders->where('CLMORDER_CUSTOMER', $request->customer_id);
            $query_closed_orders = $query_closed_orders->where('CLMCLORD_CUSTOMER', $request->customer_id);
        }
        $query = $query_orders->unionAll($query_closed_orders)->orderBy('order_date','desc')->paginate($perPage, ['/*'], 'page', $page);
        foreach($query as $key=>$row){
            $date = Carbon::parse($row->order_date);
            $query[$key]->order_date = $date->format('d-m-Y');

            if($row->country != ''){
                $country_query = Country::where('code',$row->country)->first();
                if($country_query){
                    $query[$key]->country = $country_query->name;
                }
            }
            if($row->paymethod != ''){
                
                $paymethod_query = PaymentMethodType::join('payment_gateways', 'payment_method_types.payment_gateway_id', '=', 'payment_gateways.id')
                                                        ->select('payment_method_types.name')
                                                        ->where('active', 1)
                                                        ->where('ext_code', $row->paymethod)
                                                        ->first();
                if($paymethod_query){
                    // echo 'here1';
                    // print_r($paymethod_query->name);
                    $query[$key]->paymethod = $paymethod_query->name;
                }
            }

            switch($row->type){
                case 'open':
                    $query_order_items = DB::table('tbordering_orders_items')
                            ->selectRaw("
                                        CLMORDIT_PRNAME as product_name, 
                                        '' as image, 
                                        CLMORDIT_PRID as product_id, 
                                        CLMORDIT_VARID as product_variant_id, 
                                        CLMORDIT_PROD_EXTCODE as product_external_code, 
                                        CLMORDIT_PROD_SECEXTCODE as model_code, 
                                        CLMORDIT_QUANTITY as qty, 
                                        CLMORDIT_UNIT_PRICE as unit_price, 
                                        CLMORDIT_TAX as tax_amount 
                            ")
                            ->where('CLMORDIT_ORDERID', $row->id)
                            ->get();
                    break;
                case 'closed':
                    $query_order_items = DB::table('tbordering_closed_order_items')
                            ->selectRaw("
                                        CLMCLORDIT_PRNAME as product_name, 
                                        '' as image, 
                                        CLMCLORDIT_PRID as product_id, 
                                        CLMCLORDIT_VARID as product_variant_id, 
                                        CLMCLORDIT_PROD_EXTCODE as product_external_code, 
                                        CLMCLORDIT_PROD_SECEXTCODE as model_code, 
                                        CLMCLORDIT_QUANTITY as qty, 
                                        CLMCLORDIT_UNIT_PRICE as unit_price, 
                                        CLMCLORDIT_TAX as tax_amount 
                            ")
                            ->where('CLMCLORDIT_ORDERID', $row->id)
                            ->get();
                    break;

            }
            foreach($query_order_items as $key1=>$order_item){
                $product = DB::table('tbpc_products')
                                ->join('tbpc_products_groups', 'tbpc_products.CLMPRODUCT_GROUPID', '=', 'tbpc_products_groups.CLMPRODGROUP_ID')
                                ->where('CLMPRODUCT_ID', $order_item->product_id)
                                ->select('CLMPRODGROUP_PICTURE as image', 'CLMPRODUCT_GROUPID as group_id')
                                ->first();
                if($product){
                    if($product->image != ''){
                        $query_order_items[$key1]->image = env('APP_IMG_URL').'/product_catalog/groups/'.$product->group_id.'/'.$product->image;;
                    }
                }
                else{
                    $product = DB::table('tbpc_products')
                                    ->where('CLMPRODUCT_ID', $order_item->product_id)
                                    ->select('CLMPRODUCT_MAIN_PICTURE as image')
                                    ->first();
                    if($product){
                        if($product->image != ''){
                            $query_order_items[$key1]->image = env('APP_IMG_URL').'/product_catalog/products/'.$product->group_id.'/'.$product->image;;
                        }
                    }
                }
            }
            $query[$key]->items = $query_order_items;


        }
        return response()->json($query);
    }
}
