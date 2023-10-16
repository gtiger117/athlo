<?php

use App\Models\Tax;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $ship_regions = DB::table('tbship_methods')
                ->get();
        foreach($ship_regions as $ship_region){
            $method_type = '';

            $amount = 0;
            $order_amount = 0;
            $shipping_method_type_id = 1;
            if($ship_region->CLMMETHOD_DEST_DELIVERY_TYPE != ''){
                $shipping_method_type_id = $ship_region->CLMMETHOD_DEST_DELIVERY_TYPE;
            }

            if($ship_region->CLMMETHOD_TYPE == 'One Price for All'){
                $method_type = 'one_price';
                $amount = (float)$ship_region->CLMMETHOD_PRICE;
            }
            else if($ship_region->CLMMETHOD_TYPE == 'Free Shipping for Orders Greater than'){
                $method_type = 'free_order_greater';    
                $order_amount = (float)$ship_region->CLMMETHOD_PRICE;            
            }
            if($ship_region->CLMMETHOD_DESTINATION_TYPE == 'Delivery'){
                $delivery_type = 'customer_address';                 
            }
            else if($ship_region->CLMMETHOD_DESTINATION_TYPE == 'Pickup'){
                $delivery_type = 'pickup_point';
            }            
            
            $tax_string = Tax::where('percentage', $ship_region->CLMMETHOD_TAXPERCENTAGE)->first();
            $tax_id = $tax_string->id;
            
            $amount_with_tax = round($amount * (100 + $ship_region->CLMMETHOD_TAXPERCENTAGE) / 100, 2);            
            $order_amount_with_tax = round($order_amount * (100 + $ship_region->CLMMETHOD_TAXPERCENTAGE) / 100, 2);
            
            $data = [
                'id' => $ship_region->CLMMETHODID,
                'name' => $ship_region->CLMMETHOD_NAME,
                'method_type' => $method_type,
                'delivery_type' => $delivery_type,
                'tax_id' => $tax_id,
                'amount' => $amount,
                'amount_with_tax' => $amount_with_tax,
                'order_amount' => $order_amount,
                'order_amount_with_tax' => $order_amount_with_tax,
                'shipping_method_type_id' => $shipping_method_type_id,
                'public' => $ship_region->CLMMETHOD_PUBLIC,
                'created_at' => $ship_region->CLMMETHOD_CRE_DATETIME,
                'updated_at' => $ship_region->CLMMETHOD_LAST_UPD_DATETIME,
            ];
            
            // Insert data into the 'users' table using the query builder
            DB::table('shipping_methods')->insert($data);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
