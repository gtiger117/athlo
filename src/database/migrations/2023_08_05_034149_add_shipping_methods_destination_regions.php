<?php

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
        $ship_method_destinations = DB::table('tbship_methods_dest')
                ->join('regions', 'regions.id', '=', 'tbship_methods_dest.CLMDESTINATION_CODE')
                ->join('shipping_methods', 'shipping_methods.id', '=', 'tbship_methods_dest.CLMMETHODID')
                ->where('CLMDESTINATION_TYPE', 'R')
                ->select('shipping_methods.id as shipping_method_id', 'regions.id as region_id')
                ->get();
        foreach($ship_method_destinations as $ship_method_destination){
            $data = [
                'shipping_method_id' => $ship_method_destination->shipping_method_id,
                'region_id' => $ship_method_destination->region_id,
            ];            
            // Insert data into the 'users' table using the query builder
            DB::table('region_shipping_method')->insert($data);
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
