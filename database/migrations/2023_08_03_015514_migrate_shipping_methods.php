<?php

use Gtiger117\Athlo\Models\ShippingMethod;
use Gtiger117\Athlo\Models\ShippingMethodType;
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
        for($i=1; $i<10; $i++){
            DB::table('shipping_method_types')->insert([
                'id' => $i,
                'name' => json_encode(array('en'=>$i)),
            ]);
        }
        /*
        $ship_methods = DB::table('tbship_methods')
                ->get();
        foreach($ship_methods as $ship_method){
            $ship_method = $ship_method->CLMMETHOD_DEST_DELIVERY_TYPE;
            if($ship_method == ''){
                $ship_method = 1;
            }
            
            // if($ship_method->CLMMETHOD_DESTINATION_TYPE == 'Pickup'){
                $delivery_type = 'pickup_point';
            // }
            // else{
            //     $delivery_type = 'customer_address';
            // }
            $data = [
                'id' => $ship_method->CLMMETHODID,
                'name' => $ship_method->CLM_PICKUP_TEXTNAME,
                'shipping_method_type_id' => $ship_method,
                'delivery_type' => $delivery_type,
                'method_type' => $delivery_type,
                'district' => $ship_method->CLM_PICKUP_ADR_STATE,
                'city' => $ship_method->CLM_PICKUP_ADR_CITY,
                'country' => $ship_method->CLM_PICKUP_ADR_COUNTRY,
            ];
            DB::table('pickups')->insert($data);
            
        }
        */
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
