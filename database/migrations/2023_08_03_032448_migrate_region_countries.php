<?php

use Gtiger117\Athlo\Models\Region;
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
        $ship_regions = DB::table('tbship_region_members')
                ->join('countries', 'countries.code', '=', 'tbship_region_members.CLMMEMBER_CODE')
                ->join('regions', 'regions.id', '=', 'tbship_region_members.CLMREGION_ID')
                ->where('CLMMEMBER_TYPE', 'C')
                ->select('countries.id as country_id', 'regions.id as region_id')
                ->get();
        foreach($ship_regions as $ship_region){
            $data = [
                'country_id' => $ship_region->country_id,
                'region_id' => $ship_region->region_id,
            ];
            
            // Insert data into the 'users' table using the query builder
            DB::table('region_country')->insert($data);
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
