<?php
namespace App\Helpers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentGateway;
use App\Models\PaymentMethodType;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\ShippingMethodType;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Helper {
    public static function get_sub_categories($post_data = []){
        $subcategories_array = array();
        if (isset($post_data['categoryarray']) && count($post_data['categoryarray']) > 0) {
            foreach($post_data['categoryarray']  as $categoryId){
                $category = Category::find($categoryId);
                if(!empty($category)){
                    $category = Category::with('childrenCategories.subcategories')->find($categoryId);
                    $subcategories = $category->childrenCategories;
                    $subcategories_array[] = $category->CLMCATEGORY_ID;
                    foreach($subcategories as $subcategory){
                        $subcategories_array[] = $subcategory->CLMCATEGORY_ID;
                        foreach ($subcategory->subcategories as $nestedSubcategory) {
                            $subcategories_array[] = $nestedSubcategory->CLMCATEGORY_ID;
                            foreach ($nestedSubcategory->subcategories as $nestedSubcategory1) {
                                $subcategories_array[] = $nestedSubcategory1->CLMCATEGORY_ID;
                                foreach ($nestedSubcategory1->subcategories as $nestedSubcategory2) {
                                    $subcategories_array[] = $nestedSubcategory2->CLMCATEGORY_ID;
                                    foreach ($nestedSubcategory2->subcategories as $nestedSubcategory3) {
                                        $subcategories_array[] = $nestedSubcategory3->CLMCATEGORY_ID;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $subcategories_array;
    }
    public static function update_stock($post_data = [])
    {
        $order_items = DB::table('tbordering_temp_order_items')
                                ->where('CLMTEMPORDIT_ORDERID', $post_data['order_id'])
                                ->get();
        foreach($order_items as $order_item){

        }
        $orderid = $this->orderid; 
            
            $order_items_table_name = 'tbordering_orders_items';
            $order_items_column_prefix = 'CLMORDIT';
             
            $sqlQuery = "SELECT * FROM {$order_items_table_name} WHERE {$order_items_column_prefix}_ORDERID = '$orderid'"; 
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)){
                $ProductID = $myrow[$order_items_column_prefix . '_PRID']; 
                $ProductQty = $myrow[$order_items_column_prefix . '_QUANTITY']; 
                $ProductVariant = $myrow[$order_items_column_prefix . '_VARID']; 
                
                if ($ProductVariant != ''){
                    $sqlQuery = "SELECT * FROM tbpc_products_variants WHERE CLMPRODVAR_ID = '$ProductVariant' LIMIT 1";
                    $stmt1 = $this->conn->prepare($sqlQuery);
                    $stmt1->execute();
                    $myrow1 = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $CurrentQuantity = $myrow1['CLMPRODVAR_INVENTORY']; 
                    $ActiveStatus = $myrow1['CLMPRODVAR_ITEMACTIVE']; 
                    $NewQuantity = $CurrentQuantity - $ProductQty; 
                    
                    if ($NewQuantity <= 0) 
                        $ActiveStatus = 0; 
                     
                    $sqlq = "UPDATE tbpc_products_variants 
                                SET 
                                    CLMPRODVAR_ITEMACTIVE = '$ActiveStatus', 
                                    CLMPRODVAR_INVENTORY = '$NewQuantity' 
                                WHERE CLMPRODVAR_ID = '$ProductVariant' 
                                LIMIT 1"; 
                    $stmt1 = $this->conn->prepare($sqlQuery);
                    $stmt1->execute();
                }
                 
                 
                $sqlQuery = "SELECT 
                                CASE 
                                    WHEN CLMPRODUCT_NEXTSHIP_DATE > NOW() THEN CLMPRODUCT_STOCK + CLMPRODUCT_NEXTSHIP_QTY 
                                    ELSE CLMPRODUCT_STOCK 
                                END as available_stock, 
                                t1.* 
                            FROM tbpc_products t1 
                            WHERE t1.CLMPRODUCT_ID = '$ProductID' 
                            LIMIT 1"; 
                $stmt1 = $this->conn->prepare($sqlQuery);
                $stmt1->execute();
                $myrow1 = $stmt1->fetch(PDO::FETCH_ASSOC); 


                $CurrentQuantity = $myrow1['available_stock']; 
                $ActiveStatus = $myrow1['CLMPRODUCT_ACTIVE']; 
                $NewQuantity = $CurrentQuantity - $ProductQty; 
                $GroupID = $myrow1['CLMPRODUCT_GROUPID']; 
                if ($NewQuantity <= 0) 
                    $ActiveStatus = 0; 
                 
                $sqlQuery = "UPDATE tbpc_products SET CLMPRODUCT_ACTIVE = '$ActiveStatus', CLMPRODUCT_STOCK = '$NewQuantity' WHERE CLMPRODUCT_ID = '$ProductID' LIMIT 1"; 
                $stmt1 = $this->conn->prepare($sqlQuery);
                $stmt1->execute();
                if ($GroupID != ''){
                    $this->gid = $GroupID;
                    $this->UpdateGroupValues(); 
                }

                $sqlQuery = "UPDATE tbpc_products_groups p1 LEFT JOIN (SELECT 
                                p2.CLMPRODUCT_ACTIVE, 
                                p2.CLMPRODUCT_GROUPID As GroupID, 							
                                MIN(ROUND(p2.CLMPRODUCT_PRICE, 4)) As Price, 
                                MIN(ROUND(p2.CLMPRODUCT_OFF_PRICE, 4)) As OfferPrice, 
                                MIN(ROUND(p2.CLMPRODUCT_OFFERPRICE,4)) As OfferPrice2,
                                MIN(ROUND(p2.CLMPRODUCT_LOYPRICE,4)) As LoyaltyPrice, 
                                p2.CLMTAX_PERCENTAGE As TaxPercentage, 
                                p2.CLMPRODUCT_OFF_EXPDATE As OfferExpDate, 
                                COUNT(p2.CLMPRODUCT_ID) AS NumberofProducts, 
                                COUNT(p2.CLMPRODUCT_ID) AS NumberofActiveProducts, 							
                                MIN(ROUND(p2.CLMPRODUCT_PRICE * (100 + p2.CLMTAX_PERCENTAGE) / 100, 2)) As PricewithTax, 
                                MIN(ROUND(p2.CLMPRODUCT_OFF_PRICE * (100 + p2.CLMTAX_PERCENTAGE) / 100, 2)) As OfferPricewithTax, 
                                MIN(ROUND(p2.CLMPRODUCT_OFFERPRICE * (100 + p2.CLMTAX_PERCENTAGE) / 100, 2)) As OfferPrice2withTax  
                            FROM tbpc_products p2 
                            WHERE p2.CLMPRODUCT_ACTIVE=1 AND p2.CLMPRODUCT_GROUPID IS NOT NULL  
                            GROUP BY p2.CLMPRODUCT_GROUPID) AS tbl_conn ON p1.CLMPRODGROUP_ID = tbl_conn.GroupID  
                    SET p1.CLMPRODGROUP_ACTIVE = CASE WHEN tbl_conn.CLMPRODUCT_ACTIVE = 1 THEN '1' ELSE '0' END, 
                        p1.CLMPRODGROUP_QTY = CASE WHEN tbl_conn.NumberofProducts > 0 THEN tbl_conn.NumberofProducts ELSE '0' END, 
                        p1.CLMPRODGROUP_PRICE = tbl_conn.Price,                     
                        p1.CLMPRODGROUP_OFFERPRICE = tbl_conn.OfferPrice2,   
                        p1.CLMPRODGROUP_LOYPRICE = tbl_conn.LoyaltyPrice,  
                        p1.CLMPRODGROUP_OFF_PRICE = tbl_conn.OfferPrice, 
                        p1.CLMPRODGROUP_OFF_EXPDATE = tbl_conn.OfferExpDate,  
                        p1.CLMPRODGROUP_TAX_PERCENTAGE = tbl_conn.TaxPercentage,				
                        p1.CLMPRODGROUP_LAST_UPD_DATETIME = '$today' 
                    WHERE p1.CLMPRODGROUP_ID = '$gid'";
                    
            } 
            return true; 
    }
    public static function validate_voucher_method($post_data = [])
    {
        $voucher_array = ['id' => '', 'type' => '', 'amount' => '', 'tax' => '', 'active'=>false];
        if(isset($post_data['voucher_code']) && $post_data['voucher_code'] == '1234'){
            $voucher_array = ['id' => '1', 'type' => 'promotional', 'amount' => '10', 'tax' => '1.19', 'active' => true];
        }
        
        return json_encode($voucher_array);
    }
    public static function finalize_order($post_data = [])
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
                }
            }
        }
        $order = DB::table('tbordering_orders')
                    ->where('CLMORDER_ID', $post_data['id'])
                    ->first();

        DB::table('tbordering_temp_orders')->where('CLMTEMPORDER_ID', $post_data['id'])->delete();

        DB::table('tbpc_basket')->where('CLMACCOUNT_ID', $order->CLMORDER_CUSTOMER)->where('CLMPRODUCTTYPE', 'b')->delete();


        $return_order = [
            'message' => 'Order successfully finalised',
            'status' => true,
            'redirect_link' => env('APP_URL').'/page/finalizeorder/'.$order->CLMORDER_HASH,
            'order' => $order,
        ];
                    
        return json_encode($return_order);
    }
    public static function get_active_shipping_methods($post_data = [])
    {
        $total_items = 0;
        $total_amount = 0;
        foreach($post_data['items'] as $item){
            $total_items += $item['quantity'];

            $product = Product::select('CLMPRODGROUP_PICTURE as image',
                                        'CLMPRODGROUP_ML_NAME as name',
                                        DB::raw('CASE 
                                                    WHEN CLMPRODGROUP_OFF_PRICE > 0 THEN ROUND(CLMPRODGROUP_OFF_PRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2) 
                                                    WHEN CLMPRODGROUP_OFFERPRICE > 0 THEN ROUND(CLMPRODGROUP_OFFERPRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2) 
                                                    ELSE ROUND(CLMPRODGROUP_PRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2)
                                                END as pricesold'))
                                                ->where('CLMPRODGROUP_ID', $item['product_id'])->first();
            $total_amount += $product->pricesold * $item['quantity'];
        }
        $total_amount = round($total_amount, 2);
        
        if (isset($post_data['pickup_id']) && $post_data['pickup_id'] != '') {
            $sqlq_query = "SELECT 
                                    t.*, tax_id, 
                                    MIN(amount_with_tax) as total_amount_with_tax 
                                FROM(SELECT 
                                        t2.id, 
                                        t2.name, 
                                        t2.description, 
                                        t2.image, 
                                        t1.amount_with_tax,  
                                        t1.tax_id
                                    FROM shipping_methods t1 
                                    INNER JOIN shipping_method_types t2 ON t1.shipping_method_type_id = t2.id 
                                    WHERE 
                                        t2.delivery_type = 'pickup_point' AND 
                                        (t1.method_type = 'one_price' OR (t1.method_type = 'free_order_greater' AND order_amount >= '".$total_amount."')) AND 
                                        t1.active = 1 AND 
                                        t2.active = 1 AND                
                                            ((t1.all_pickups = 1 AND t1.id NOT IN (SELECT shipping_method_id 
                                                                                    FROM exclude_pickup_shipping_method p1 
                                                                                    WHERE p1.pickup_id = '".$post_data['pickup_id']."')) OR 
                                            (t1.all_pickups = 0 AND 
                                                t1.id IN (SELECT shipping_method_id 
                                                                                    FROM pickup_shipping_method p1 
                                                                                    WHERE p1.pickup_id = '".$post_data['pickup_id']."') AND 
                                                t1.id NOT IN (SELECT shipping_method_id 
                                                                                    FROM exclude_pickup_shipping_method p1 
                                                                                    WHERE p1.pickup_id = '".$post_data['pickup_id']."') 
                                                ) OR 
                                            (t1.id IN (SELECT p1.shipping_method_id 
                                                        FROM pickup_groups_shipping_method p1 
                                                        INNER JOIN pickup_groups p2 ON p1.pickup_group_id=p2.id 
                                                        INNER JOIN pickups p3 ON p2.id=p3.pickup_group_id 
                                                        WHERE p3.id='".$post_data['pickup_id']."')))) t 
                                    GROUP BY t.id 
                                    ";
            $query = DB::select($sqlq_query);
        }
        else if (isset($post_data['country_code']) && $post_data['country_code'] != '') {
            $sqlq_query = "SELECT t.*, 
                                    MIN(amount_with_tax) as total_amount_with_tax 
                                FROM(SELECT 
                                        t2.id, 
                                        t2.name, 
                                        t2.description, 
                                        t2.image, 
                                        t1.amount_with_tax,  
                                        t1.tax_id
                                    FROM shipping_methods t1 
                                    INNER JOIN shipping_method_types t2 ON t1.shipping_method_type_id = t2.id 
                                    WHERE 
                                        t2.delivery_type = 'customer_address' AND 
                                        (t1.method_type = 'one_price' OR (t1.method_type = 'free_order_greater' AND order_amount >= '".$total_amount."')) AND 
                                        t1.active = 1 AND 
                                        t2.active = 1 AND                
                                            ((t1.all_countries = 1 AND t1.id NOT IN (SELECT shipping_method_id 
                                                                                    FROM exclude_country_shipping_method p1 
                                                                                    INNER JOIN countries p2 ON p1.country_id=p2.id 
                                                                                    WHERE p2.code = '".$post_data['country_code']."')) OR 
                                            (t1.all_countries = 0 AND 
                                                t1.id IN (SELECT shipping_method_id 
                                                                                    FROM country_shipping_method p1 
                                                                                    INNER JOIN countries p2 ON p1.country_id=p2.id 
                                                                                    WHERE p2.code = '".$post_data['country_code']."') AND 
                                                t1.id NOT IN (SELECT shipping_method_id 
                                                                                    FROM exclude_country_shipping_method p1 
                                                                                    INNER JOIN countries p2 ON p1.country_id=p2.id 
                                                                                    WHERE p2.code = '".$post_data['country_code']."') 
                                                ) OR 
                                            (t1.id IN (SELECT p1.shipping_method_id 
                                                        FROM region_shipping_method p1 
                                                        INNER JOIN region_country p2 ON p1.region_id=p2.region_id 
                                                        INNER JOIN countries p3 ON p2.country_id=p3.id 
                                                        WHERE p3.code='".$post_data['country_code']."')))) t 
                                    GROUP BY t.id 
                                    ";
            $query = DB::select($sqlq_query);
        }
        if(isset($post_data['id']) && $post_data['id'] != ''){
            $sqlq_query = "SELECT * FROM ($sqlq_query) t WHERE id='".$post_data['id']."'";
        }

        // $export_array = [];
        foreach ($query as $key=>$row) {
            $name_array =json_decode($row->name);
            $query[$key]->name = isset($name_array->en)?$name_array->en:'';
            $description_array =json_decode($row->description);
            $query[$key]->description = isset($description_array->en)?$description_array->en:'';

            $taxes = Tax::where('id',$row->tax_id)->first();
            $amount = round($row->amount_with_tax * 100 / (100 + $taxes->percentage), 2);
            $query[$key]->amount = round($amount, 2);
            $query[$key]->tax = round($row->amount_with_tax - $amount, 2);
            $query[$key]->totalamount = round($row->total_amount_with_tax, 2);
            $query[$key]->image = '';
            if($row->image != ''){
                $query[$key]->image = env('APP_URL').'/storage/'.$row->image;
            }
        }

        $shippingmethods = $query;
        
        
        return json_encode($shippingmethods);
    }
    public static function get_menu($post_data = [])
    {
        $query = Category::select('tbpc_categories.CLMCATEGORY_ID as id',
                                    'tbpc_categories.CLMCATEGORY_ML_NAME as name', 
                                    'tbpc_categories.CLMCATEGORY_MAIN_PICTURE as image', 
                                    'tbpc_categories.CLMCATEGORY_ISPAGE as is_page', 
                                    'tbpc_categories.CLMCATEGORY_SEONAME as seo_name', 
                                    'tbpc_categories.CLMCATEGORY_SEOTITLE as seo_title', 
                                    'tbpc_categories.CLMCATEGORY_ML_SEODESCRIPTION as seo_description', 
                                    'tbpc_categories.CLMMASTER_CATEGORY_ID as parent_id', 
                                    'tbpc_categories.CLMCATEGORY_REFPAGEID as ref_page');

        if (isset($post_data['id']) && $post_data['id'] != '') {
            $query = $query->where('CLMCATEGORY_ID', $post_data['id']);
        }
        if (isset($post_data['visible']) && $post_data['visible'] != '') {
            $query = $query->where('CLMCATEGORY_VISIBLE', $post_data['visible'] );
        }
        if (isset($post_data['is_page']) && $post_data['is_page'] != '') {
            $query = $query->where('CLMCATEGORY_ISPAGE', $post_data['is_page']);
        }
        if (isset($post_data['parent']) && count($post_data['parent']) > 0) {
            $query = $query->whereIn('CLMCATEGORY_ID', function ($subquery) use ($post_data) {
                $subquery->select('CLMCATEGORY_ID')
                    ->from('tbpc_categories')
                    ->whereIn('CLMMASTER_CATEGORY_ID', $post_data['parent']);
            }); 
        }

        $query = $query
                ->orderBy('tbpc_categories.CLMCATEGORY_SORTING_NUMBER', 'asc')
                ->orderBy('tbpc_categories.CLMCATEGORY_ML_NAME', 'desc')
                ->get();

        foreach ($query as $key=>$row) {
            $query[$key]->name = strip_tags(html_entity_decode($row->name));
            
            if($row->seo_name == ''){
                $query[$key]->seo_name = $row->name;
            }
            if($row->seo_title == ''){
                $query[$key]->seo_title = $row->name;
            }
            if($row->image != ''){
                $query[$key]->image = env('APP_IMG_URL').'/product_catalog/categories/'.$row->id.'/'.$row->image;
            }
            $query[$key]->link = '/category/'.$row->id.'/'.urlencode(strtolower($row->name));
            if($row->is_page == '1' && is_numeric($row->ref_page)){
                $page_array = DB::table('tbcms_pages')
                ->where('CLMPAGE_ID', $row->ref_page)
                ->select('*')
                ->first();
                if($page_array){
                    if($page_array->CLMMODULE_ID == 'link'){
                        $query[$key]->link = $row->CLMPAGE_LIST_TEMPLATE;
                    }
                    else{
                        $query[$key]->link = '/page/'.$page_array->CLMPAGE_ID.'/'.urlencode(strtolower($page_array->CLMPAGE_ML_NAME));
                    }
                }
            }
        }

        $menus = $query;
        return json_encode($menus);
    }
    public static function get_active_payment_methods($post_data = [])
    {        
        if (isset($post_data['pickup_id']) && $post_data['pickup_id'] != '') {
            $sqlq_query = "SELECT 
                                t.*, 
                                MIN(amount_with_tax) as total_amount_with_tax 
                            FROM(SELECT 
                                    t2.id, 
                                    t2.name, 
                                    t2.description, 
                                    t2.image, 
                                    t1.amount_with_tax,  
                                    t1.tax_id, 
                                    t3.type as payment_gateway_type, 
                                    t3.ext_code as payment_gateway_code,   
                                    t1.id as payment_method_id,   
                                    t2.sort_order   
                                FROM payment_methods t1 
                                INNER JOIN payment_method_types t2 ON t1.payment_method_type_id = t2.id 
                                INNER JOIN payment_gateways t3 ON t3.id = t2.payment_gateway_id 
                                WHERE 
                                    t1.active = 1 AND 
                                    t2.active = 1 AND                
                                        ((t1.all_pickups = 1 AND t1.id NOT IN (SELECT payment_method_id 
                                                                                FROM exclude_pickup_paymethod p1 
                                                                                WHERE p1.pickup_id = '".$post_data['pickup_id']."')) OR 
                                        (t1.all_pickups = 0 AND 
                                            t1.id IN (SELECT payment_method_id 
                                                                                FROM include_pickup_paymethod p1 
                                                                                WHERE p1.pickup_id = '".$post_data['pickup_id']."') AND 
                                            t1.id NOT IN (SELECT payment_method_id 
                                                                                FROM exclude_pickup_paymethod p1 
                                                                                WHERE p1.pickup_id = '".$post_data['pickup_id']."') 
                                            ) OR 
                                        (t1.id IN (SELECT p1.payment_method_id 
                                                    FROM include_pickup_group_paymethod p1 
                                                    INNER JOIN pickup_groups p2 ON p1.pickup_group_id=p2.id 
                                                    INNER JOIN pickups p3 ON p2.id=p3.pickup_group_id 
                                                    WHERE p3.id='".$post_data['pickup_id']."')))) t 
                                GROUP BY t.id 
                                ORDER BY t.sort_order DESC";
        }
        else if (isset($post_data['country_code']) && $post_data['country_code'] != '') {
            $sqlq_query = "SELECT 
                                t.*, 
                                MIN(amount_with_tax) as total_amount_with_tax 
                            FROM(SELECT 
                                    t2.id, 
                                    t2.name, 
                                    t2.description, 
                                    t2.image, 
                                    t1.amount_with_tax,  
                                    t1.tax_id, 
                                    t3.type as payment_gateway_type,   
                                    t3.ext_code as payment_gateway_code,   
                                    t1.id as payment_method_id,   
                                    t2.sort_order     
                                FROM payment_methods t1 
                                INNER JOIN payment_method_types t2 ON t1.payment_method_type_id = t2.id 
                                INNER JOIN payment_gateways t3 ON t3.id = t2.payment_gateway_id 
                                WHERE 
                                    t1.active = 1 AND 
                                    t2.active = 1 AND                
                                        ((t1.all_countries = 1 AND t1.id NOT IN (SELECT payment_method_id 
                                                                                FROM exclude_country_paymethod p1 
                                                                                INNER JOIN countries p2 ON p1.country_id=p2.id 
                                                                                WHERE p2.code = '".$post_data['country_code']."')) OR 
                                        (t1.all_countries = 0 AND 
                                            t1.id IN (SELECT payment_method_id 
                                                                                FROM include_country_paymethod p1 
                                                                                INNER JOIN countries p2 ON p1.country_id=p2.id 
                                                                                WHERE p2.code = '".$post_data['country_code']."') AND 
                                            t1.id NOT IN (SELECT payment_method_id 
                                                                                FROM exclude_country_paymethod p1 
                                                                                INNER JOIN countries p2 ON p1.country_id=p2.id 
                                                                                WHERE p2.code = '".$post_data['country_code']."') 
                                            ) OR 
                                        (t1.id IN (SELECT p1.payment_method_id 
                                                    FROM region_payment_method p1 
                                                    INNER JOIN region_country p2 ON p1.region_id=p2.region_id 
                                                    INNER JOIN countries p3 ON p2.country_id=p3.id 
                                                    WHERE p3.code='".$post_data['country_code']."')))) t 
                                GROUP BY t.id 
                                ORDER BY t.sort_order DESC";            
        }

        if(isset($post_data['id']) && $post_data['id'] != ''){
            $sqlq_query = "SELECT * FROM ($sqlq_query) t WHERE id='".$post_data['id']."'";
        }

        $query = DB::select($sqlq_query);

        // $export_array = [];
        foreach ($query as $key=>$row) {
            $name_array =json_decode($row->name);
            $query[$key]->name = isset($name_array->en)?$name_array->en:'';
            $description_array =json_decode($row->description);
            $query[$key]->description = isset($description_array->en)?$description_array->en:'';

            $taxes = Tax::where('id',$row->tax_id)->first();
            $amount = round($row->amount_with_tax * 100 / (100 + $taxes->percentage), 2);
            $query[$key]->amount = round($amount, 2);
            $query[$key]->tax = round($row->amount_with_tax - $amount, 2);
            $query[$key]->totalamount = round($row->total_amount_with_tax, 2);
            $query[$key]->image = '';
            if($row->image != ''){
                $query[$key]->image = env('APP_URL').'/storage/'.$row->image;
            }
        }

        $paymentmethods = $query;
        
        
        return json_encode($paymentmethods);
    }
    public static function get_gift_amount($post_data = [])
    {
        $gift_array = ['amount' => 0, 'tax' => 0, 'total' => 0];
        $unit_amount = 1;
        $unit_tax = 0.19;
        $unit_total = 0.19;

        if($post_data['is_gift'] == true){
            $total_items = 0;
            foreach($post_data['items'] as $item){
                $total_items += $item['quantity'];
            }
            $gift_array = ['amount' => round($total_items * $unit_amount, 2), 'tax' => round($total_items * $unit_tax, 2), 'total' => round($total_items * $unit_total, 2)];
        }
        
        return json_encode($gift_array);
    }
    public static function get_active_pickups()
    {
        $active_pickups = [];
        $all_pickups = [];
        $all_pickups_script = Pickup::select('id')->where('active', 1)->get();
        foreach($all_pickups_script as $pickup){
            $all_pickups[] = $pickup->id;
        }

        $query = ShippingMethod::join('shipping_method_types', 'shipping_method_types.id', '=', 'shipping_methods.shipping_method_type_id')
                                ->select('shipping_methods.*')
                                ->where('delivery_type', 'pickup_point')
                                ->where('shipping_method_types.active', '1')
                                ->where('shipping_methods.active', '1')
                                ->get();
        foreach($query as $row){
            $pickups_to_include = [];
            $pickups_to_exclude = [];
            if($row->all_pickups == true){
                $pickups_to_include = array_merge($pickups_to_include, $all_pickups);
            }
            else{
                $pickups_to_include_query = DB::table('pickup_shipping_method')
                                ->select('pickup_id as id')
                                ->join('pickups', 'pickup_shipping_method.pickup_id', '=', 'pickups.id')
                                ->where('shipping_method_id', $row->id)
                                ->where('pickups.active', 1)
                                ->get();
                if($pickups_to_include_query){
                    foreach($pickups_to_include_query as $pickup){
                        $pickups_to_include[] = $pickup->id;
                    }
                }
            }
            if($row->exclude_pickups == true){
                $pickups_to_exclude_query = DB::table('exclude_pickup_shipping_method')
                                ->select('pickup_id as id')
                                ->join('pickups', 'exclude_pickup_shipping_method.pickup_id', '=', 'pickups.id')
                                ->where('shipping_method_id', $row->id)
                                ->where('pickups.active', 1)
                                ->get();
                if($pickups_to_exclude_query){
                    foreach($pickups_to_exclude_query as $pickup){
                        $pickups_to_exclude[] = $pickup->id;
                    }
                }
            }
            $pickups_to_include_query = DB::table('pickup_groups_shipping_method')
                                ->select('pickups.id')
                                ->join('pickups', 'pickup_groups_shipping_method.pickup_group_id', '=', 'pickups.pickup_group_id')
                                ->where('shipping_method_id', $row->id)
                                ->where('pickups.active', 1)
                                ->get();
            if($pickups_to_include_query){
                foreach($pickups_to_include_query as $pickup){
                    $pickups_to_include[] = $pickup->id;
                }
            }
            $pickups_to_include = array_unique($pickups_to_include);

            if(count($pickups_to_exclude) > 0){
                $collection = collect($pickups_to_include);
                $elementsToRemove = $pickups_to_exclude;
    
                $filteredCollection = $collection->reject(function ($value) use ($elementsToRemove) {
                    return in_array($value, $elementsToRemove);
                });
                
                $active_pickups = array_unique(array_merge($active_pickups, $filteredCollection));
            }
            else{
                $active_pickups = array_unique(array_merge($active_pickups, $pickups_to_include));
            }
        }
        return $active_pickups;
    }
    public static function get_pickups($post_data = [])
    {
        $perPage = 1000;
        $page = 1;

        $active_pickups = Helper::get_active_pickups();
        
        $query = Pickup::select("*");

        if (isset($post_data['id']) && $post_data['id'] != '') {
            $query = $query->where('id', $post_data['id']);
        }
        $query = $query->whereIn('id', $active_pickups);
        if (isset($post_data['pickup_group_id']) && $post_data['pickup_group_id'] != '') {
            $query = $query->where('pickup_group_id', $post_data['pickup_group_id']);
        }
        
        $query = $query->orderBy('name', 'asc')
                ->paginate($perPage, ['/*'], 'page', $page);

        foreach ($query as $key=>$row) {
            $query[$key]->displayname = $row->name;
        }

        $pickups = $query;
        
        return json_encode($pickups);
    }

    public static function add_order_payment($post_data = [])
    {
        $payment_gateway_query = PaymentGateway::where('ext_code', $post_data['payment_gateway'])->first();

        $now_date = Carbon::now()->format('Y-m-d H:i:s');

        $data = [            
            'order_id' => $post_data['order_id'], 
            'description' => $payment_gateway_query->name,
            'amount' => $post_data['amount'],
            'payment_way' => $post_data['payment_gateway'],
            'date' => $now_date,
            'date_created' => $now_date,
        ];
        DB::table('tbordering_orders_payments')->insert($data);

        $order_payment = DB::table('tbordering_orders_payments')
                        ->where('order_id', $post_data['order_id'])
                        ->orderBy('id', 'desc')
                        ->first();

        return json_encode([
            'message' => 'Order Payment Added successfully',
            'order_payment' => $order_payment,
        ]);
    }
    
    public static function create_payment_gateway_order($post_data = [])
    {
        $payment_gateway = [];

        // $postData = [
        //     'id' => $post_data['order_id'], 
        //     'hash' => $post_data['hash'], 
        //     'amount' => $post_data['amount'], 
        //     'gateway' =>$post_data['gateway'], 
        // ];

        switch($post_data['gateway']){
            case 'jcc':
                $createpaymentgatewayorder_response = Helper::create_jcc_order($post_data);       
                $payment_gateway = json_decode($createpaymentgatewayorder_response);
                break;
        }

        
        
        return json_encode($payment_gateway);
        
    }
    public static function create_jcc_order($post_data = []){
        $TEMPorder_Id = $post_data['order_id'];
        $hash = $post_data['hash'];
        $paymentAmount = round($post_data['amount'], 2);
        //CALL PAYMENT GATEWAY
        $purchase_amt = number_format($paymentAmount, 2, '.', '');
        $purchase_amt *= 100;
        $formatted_purchase_amt = str_pad($purchase_amt, 12, '0', STR_PAD_LEFT);
        $currency = 978;
        $currency_exp = 2;
        
        $version = '1.0.0';
        
        $merchant_id = env('MERCHANT_ID');
        $acquirer_id = env('ACQUIRER_ID');
        $password = env('PASSWORD');
        $gateway_url = env('GATEWAY_URL'); 
        

        $response_url = env('APP_URL')."/finalizepaymentgateway?type=".$post_data['type']."&hash=" . $hash;

        // $merchant_website = env('MERCHANT_WEBSITE');
        // $response_url = $merchant_website."/finalizepaymentgateway?type=".$post_data['type']."&hash=" . $hash;


        $capture_flag = 'A';
        $signature_method = 'SHA1';
        $to_encrypt = $password.$merchant_id.$acquirer_id.$TEMPorder_Id.'-'.date("YmdHis").$formatted_purchase_amt.$currency;
        $sha1_signature = sha1($to_encrypt);
        $base64_sha1_signature = base64_encode(pack('H*', $sha1_signature));
        $order_ref_code = $TEMPorder_Id.'_'.$post_data['type'];

        return '<form method="post" name="paymentForm" id="paymentForm" action="'.$gateway_url.'">
                    <input type="hidden" name="Version" value="'.$version.'">
                    <input type="hidden" name="MerID" value="'.$merchant_id.'">
                    <input type="hidden" name="AcqID" value="'.$acquirer_id.'">
                    <input type="hidden" name="MerRespURL" value="'.$response_url.'">
                    <input type="hidden" name="PurchaseAmt" value="'.$formatted_purchase_amt.'">
                    <input type="hidden" name="PurchaseCurrency" value="'.$currency.'">
                    <input type="hidden" name="PurchaseCurrencyExponent" value="'.$currency_exp.'">
                    <input type="hidden" name="OrderID" value="'.$order_ref_code.'">
                    <input type="hidden" name="CaptureFlag" value="'.$capture_flag.'">
                    <input type="hidden" name="Signature" value="'.$base64_sha1_signature.'">
                    <input type="hidden" name="SignatureMethod" value="'.$signature_method.'">
                </form>
                <script type="text/javascript">
                    document.forms["paymentForm"].submit();
                </script>';

    }
}
?>