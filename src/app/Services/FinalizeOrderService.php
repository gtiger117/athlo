<?php

namespace App\Services;

use App\Models\PaymentMethodType;
use App\Models\Pickup;
use Illuminate\Support\Facades\DB;

class FinalizeOrderService
{
    public function finalize_order($post_data = [])
    {
        $sqlq_order = "INSERT INTO tbordering_orders (CLMORDER_ID, 
                            CLMORDER_REL_ORDER_ID, 
                            CLMORDER_BANK_REF, 
                            CLMORDER_HASH, 
                            CLMORDER_DATE, 
                            CLMORDER_CUSTOMER, 
                            CLMORDER_IP, 
                            CLMORDER_COMPANY_ID, 
                            CLMORDER_ITEMS, 
                            CLMORDER_PRICE_WO_TAX, 
                            CLMORDER_DISCOUNT_WO_TAX, 
                            CLMORDER_TAX, 
                            CLMORDER_SHIPMENT, 
                            CLMORDER_SHIPMENT_TAX, 
                            CLMORDER_CUST_NAME, 
                            CLMORDER_CUST_SURNAME, 
                            CLMORDER_EMAIL, 
                            CLMORDER_CUST_TELEPHONE, 
                            CLMORDER_PICKUPID, 
                            CLMORDER_PICKUPNAME, 
                            CLMORDER_ADR_1, 
                            CLMORDER_ADR_2, 
                            CLMORDER_ADR_CODE, 
                            CLMORDER_ADR_CITY, 
                            CLMORDER_ADR_STATE, 
                            CLMORDER_ADR_COUNTRY, 
                            CLMORDER_DELIVERY_METHOD_TYPE, 
                            CLMORDER_BILLNAME, 
                            CLMORDER_BILLSURNAME, 
                            CLMORDER_BILLADR_1, 
                            CLMORDER_BILLADR_2, 
                            CLMORDER_BILLADR_CODE, 
                            CLMORDER_BILLADR_CITY, 
                            CLMORDER_BILLADR_STATE, 
                            CLMORDER_BILLADR_COUNTRY, 
                            CLMORDER_STATUS, 
                            CLMORDER_CURRENCY, 
                            CLMORDER_CURRENCY_SIGN, 
                            CLMORDER_PAYMETHOD, 
                            CLMORDER_NOTES, 
                            CLMORDER_PAYMETHOD_AMT, 
                            CLMORDER_VOUCHERID, 
                            CLMORDER_VOUCHERCODE, 
                            CLMORDER_PAYMETHOD_TAX,						
                            CLMORDER_IS_GIFT,
                            CLMORDER_GIFT_AMT,
                            CLMORDER_GIFT_TAX,
                            CLMORDER_GIFT_NOTES,
                            CLMORDER_REFERRER,
                            CLMORDER_TRACKING,
                            CLMORDER_CAMPAIGNID_FK,
                            CLMORDER_CAMPAIGNITEMID_FK,
                            CLMORDER_CLICKANDCOLLECT,
                            CLMORDER_WAREHOUSE,
                            CLMORDER_TYPE) 
                        SELECT 
                            CLMTEMPORDER_ID, 
                            CLMTEMPORDER_REL_ORDER_ID, 
                            CLMTEMPORDER_BANK_REF, 
                            CLMTEMPORDER_HASH, 
                            CLMTEMPORDER_DATE, 
                            CLMTEMPORDER_CUSTOMER, 
                            CLMTEMPORDER_IP, 
                            CLMTEMPORDER_COMPANY_ID, 
                            CLMTEMPORDER_ITEMS, 
                            CLMTEMPORDER_PRICE_WO_TAX, 
                            CLMTEMPORDER_DISCOUNT_WO_TAX, 
                            CLMTEMPORDER_TAX, 
                            CLMTEMPORDER_SHIPMENT, 
                            CLMTEMPORDER_SHIPMENT_TAX, 
                            CLMTEMPORDER_CUST_NAME, 
                            CLMTEMPORDER_CUST_SURNAME, 
                            CLMTEMPORDER_EMAIL, 
                            CLMTEMPORDER_CUST_TELEPHONE, 
                            CLMTEMPORDER_PICKUPID, 
                            CLMTEMPORDER_PICKUPNAME, 
                            CLMTEMPORDER_ADR_1, 
                            CLMTEMPORDER_ADR_2, 
                            CLMTEMPORDER_ADR_CODE, 
                            CLMTEMPORDER_ADR_CITY, 
                            CLMTEMPORDER_ADR_STATE, 
                            CLMTEMPORDER_ADR_COUNTRY, 
                            CLMTEMPORDER_DELIVERY_METHOD_TYPE, 
                            CLMTEMPORDER_BILLNAME, 
                            CLMTEMPORDER_BILLSURNAME, 
                            CLMTEMPORDER_BILLADR_1, 
                            CLMTEMPORDER_BILLADR_2, 
                            CLMTEMPORDER_BILLADR_CODE, 
                            CLMTEMPORDER_BILLADR_CITY, 
                            CLMTEMPORDER_BILLADR_STATE, 
                            CLMTEMPORDER_BILLADR_COUNTRY, 
                            CLMORDERST_ID, 
                            CLMTEMPORDER_CURRENCY, 
                            CLMTEMPORDER_CURRENCY_SIGN, 
                            CLMTEMPORDER_PAYMETHOD, 
                            CLMTEMPORDER_NOTES, 
                            CLMTEMPORDER_PAYMETHOD_AMT, 
                            CLMTEMPORDER_VOUCHERID, 
                            CLMTEMPORDER_VOUCHERCODE, 
                            CLMTEMPORDER_PAYMETHOD_TAX,						
                            CLMTEMPORDER_IS_GIFT,
                            CLMTEMPORDER_GIFT_AMT,
                            CLMTEMPORDER_GIFT_TAX,
                            CLMTEMPORDER_GIFT_NOTES, 
                            CLMTEMPORDER_REFERRER,
                            CLMTEMPORDER_TRACKING,
                            CLMTEMPORDER_CAMPAIGNID_FK,
                            CLMTEMPORDER_CAMPAIGNITEMID_FK,
                            CLMTEMPORDER_CLICKANDCOLLECT,
                            CLMTEMPORDER_WAREHOUSE, 
                            CLMTEMPORDER_TYPE 
                        FROM tbordering_temp_orders, tbordering_orders_statuses 
                        WHERE 
                            CLMTEMPORDER_ID = ".$post_data['id']." AND 
                            CLMORDERST_ID = 1 
                        LIMIT 1";
        $finalize_order = DB::insert($sqlq_order);

        if($finalize_order){
            $order_items = DB::table('tbordering_temp_order_items')
                                ->where('CLMTEMPORDIT_ORDERID', $post_data['id'])
                                ->get();

            foreach($order_items as $order_item){
                $sqlQuery_orderitems = "INSERT INTO tbordering_orders_items (CLMORDIT_ORDERID, 
                                                                        CLMORDIT_PRNAME, 
                                                                        CLMORDIT_QUANTITY, 
                                                                        CLMORDIT_UNIT_PRICE, 
                                                                        CLMORDIT_TAX, 
                                                                        CLMORDIT_UNIT_ORIGINALPRICE, 
                                                                        CLMORDIT_ACTUAL_PRICE_PAID, 
                                                                        CLMORDIT_LOYALTY_POINTS, 
                                                                        CLMORDIT_PRID, 
                                                                        CLMORDIT_VARID, 
                                                                        CLMORDIT_PROD_EXTCODE, 
                                                                        CLMORDIT_PROD_SECEXTCODE, 
                                                                        CLMORDIT_PROD_CAT1, 
                                                                        CLMORDIT_PROD_CAT2, 
                                                                        CLMORDIT_PROD_CAT3, 
                                                                        CLMORDIT_PROD_CAT4, 
                                                                        CLMORDIT_PROD_CAT5, 
                                                                        CLMORDIT_PROD_CHARVAL1, 
                                                                        CLMORDIT_PROD_CHARVAL2, 
                                                                        CLMORDIT_PROD_CHARVAL3, 
                                                                        CLMORDIT_PROD_CHARVAL4, 
                                                                        CLMORDIT_PROD_CHARVAL5, 
                                                                        CLMORDIT_PROD_CHARVAL6, 
                                                                        CLMORDIT_PROMO_ID, 
                                                                        CLMORDIT_OFFER_ID, 
                                                                        CLMORDIT_OFFER_LOYALTY) 
                                                                    SELECT 
                                                                        '".$post_data['id']."', 
                                                                        CLMTEMPORDIT_PRNAME, 
                                                                        CLMTEMPORDIT_QUANTITY, 
                                                                        CLMTEMPORDIT_UNIT_PRICE, 
                                                                        CLMTEMPORDIT_TAX, 
                                                                        CLMTEMPORDIT_UNIT_ORIGINALPRICE, 
                                                                        CLMTEMPORDIT_ACTUAL_PRICE_PAID, 
                                                                        CLMTEMPORDIT_LOYALTY_POINTS, 
                                                                        CLMTEMPORDIT_PRID, 
                                                                        CLMTEMPORDIT_VARID, 
                                                                        CLMTEMPORDIT_PROD_EXTCODE, 
                                                                        CLMTEMPORDIT_PROD_SECEXTCODE, 
                                                                        CLMTEMPORDIT_PROD_CAT1, 
                                                                        CLMTEMPORDIT_PROD_CAT2, 
                                                                        CLMTEMPORDIT_PROD_CAT3, 
                                                                        CLMTEMPORDIT_PROD_CAT4, 
                                                                        CLMTEMPORDIT_PROD_CAT5, 
                                                                        CLMTEMPORDIT_PROD_CHARVAL1, 
                                                                        CLMTEMPORDIT_PROD_CHARVAL2, 
                                                                        CLMTEMPORDIT_PROD_CHARVAL3, 
                                                                        CLMTEMPORDIT_PROD_CHARVAL4, 
                                                                        CLMTEMPORDIT_PROD_CHARVAL5, 
                                                                        CLMTEMPORDIT_PROD_CHARVAL6,
                                                                        CLMTEMPORDIT_PROMO_ID, 
                                                                        CLMTEMPORDIT_OFFER_ID, 
                                                                        CLMTEMPORDIT_OFFER_LOYALTY 
                                                                    FROM tbordering_temp_order_items 
                                                                    WHERE CLMTEMPORDIT_ID = '".$order_item->CLMTEMPORDIT_ID."' 
                                                                    LIMIT 1";
                $finalize_order_items = DB::insert($sqlQuery_orderitems);
                if($finalize_order_items){
                    $sqlQuery_voucheritems = "INSERT IGNORE INTO tbordering_orders_voucher_items (CLMORDVOUCH_ORDERITID,
                                    CLMORDVOUCH_QTY,
                                    CLMORDVOUCH_PRICE,
                                    CLMORDVOUCH_TAX,
                                    CLMORDVOUCH_PRODUCTID,
                                    CLMORDVOUCH_VOUCHERID,
                                    CLMORDVOUCH_RECIPIENT_NAME,
                                    CLMORDVOUCH_RECIPIENT_EMAIL,
                                    CLMORDVOUCH_RECIPIENT_MESSAGE,
                                    CLMORDVOUCH_SENDER_NAME,
                                    CLMORDVOUCH_SENDER_EMAIL)  
                                SELECT 
                                    '".$order_item->CLMTEMPORDIT_ID."',
                                   CLMTEMPORDVOUCH_QTY,
                                   CLMTEMPORDVOUCH_PRICE,
                                   CLMTEMPORDVOUCH_TAX,
                                   CLMTEMPORDVOUCH_PRODUCTID,
                                   CLMTEMPORDVOUCH_VOUCHERID,
                                   CLMTEMPORDVOUCH_RECIPIENT_NAME,
                                   CLMTEMPORDVOUCH_RECIPIENT_EMAIL,
                                   CLMTEMPORDVOUCH_RECIPIENT_MESSAGE,
                                   CLMTEMPORDVOUCH_SENDER_NAME,
                                   CLMTEMPORDVOUCH_SENDER_EMAIL 
                                FROM tbordering_temp_order_voucher_items t1 
                                WHERE CLMTEMPORDVOUCH_ORDERITID = '".$order_item->CLMTEMPORDIT_ID."'  
                                LIMIT 1";
                    $finalize_order_voucher_items = DB::insert($sqlQuery_voucheritems);
                    if($finalize_order_voucher_items){
                        $voucher_order_item = DB::table('tbordering_orders_voucher_items')
                                ->where('CLMORDVOUCH_ID', $order_item->CLMTEMPORDIT_ID)
                                ->first();
                        if($voucher_order_item){
                            $PrimaryVoucherID = $voucher_order_item->CLMORDVOUCH_ID;
                            $data = '1234567890';
                            $code = '0'.$voucher_order_item->CLMORDVOUCH_ID;
                            $code = $code.substr(str_shuffle($data), 0, 8);
                            $code = substr($code, 0, 8);
                            $value = round($voucher_order_item->CLMORDVOUCH_QTY * ($voucher_order_item->CLMORDVOUCH_PRICE + $voucher_order_item->CLMORDVOUCH_TAX), 2);
                            $perc = 0;
                            $f_id = 'NULL';
                            $vouchuser = 'NULL';
                            $presid = 'NULL';
                            $thres = 0;
                            $creid = $_SESSION['ID'];
                            $crerole = 'USER';
                            $woutDisc = 0;
                            if(!isset($voucher_months)){
                                $voucher_months = 12;
                            }
                                
                            $expiry = "'" . date('Y-m-d', strtotime("+$voucher_months months", strtotime(date("Y-m-d")))) . "'";
                            $today = date('Y-m-d H:i:s');

                            $data = [            
                                'CLM_VOUCHER_CODE' => '$code', 
                                'CLM_VOUCHER_VALUE' => '$value', 
                                'CLM_VOUCHER_PERCENTAGE' => '0', 
                                'CLM_VOUCHER_NUMBER' => '1',
                                'CLM_VOUCHER_ISACTIVE' => '1',
                                'CLM_VOUCHER_ONLY_USE_ONCE' => '0',
                                'CLM_VOUCHER_TYPE' => 'general', 
                                'CLM_VOUCHER_THRESHOLD' => '0', 
                                'CLM_VOUCHER_WITHOUTDISCOUNT' => $woutDisc, 
                                'CLM_VOUCHER_CRE_DATETIME' => $today, 
                                'CLM_VOUCHER_UPD_DATETIME' => $today, 
                                'CLM_VOUCHER_EXP_DATETIME' => $expiry, 
                                'CLM_VOUCHER_USER_ID' => NULL
                            ];
                    
            
                            $voucher_order = DB::table('tbvouchers')->insert($data);
                            if($voucher_order)
                            {
                                $VoucherID = $voucher_order->CLM_VOUCHER_ID;
                                $data = [
                                    'CLMORDVOUCH_VOUCHERID' => $VoucherID,
                                    'CLMORDVOUCH_ID' => $PrimaryVoucherID,
                                ];

                                DB::table('tbvouchers')
                                        ->where('CLM_VOUCHER_ID', $VoucherID)
                                        ->update($data);
                            }
                        }
                    }
                }
            }
        }
        $order = DB::table('tbordering_orders')
                    ->where('CLMORDER_ID', $post_data['id'])
                    ->first();

        DB::table('tbordering_temp_orders')->where('CLMTEMPORDER_ID', $post_data['id'])->delete();
                    
        return response()->json([
            'message' => 'Order successfully finalised',
            'status' => true,
            'redirect_link' => env('APP_URL').'/page/finalizeorder/'.$order->CLMORDER_HASH,
            'order' => $order,
        ], 201);
    }
}

?>