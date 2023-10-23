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
        $ship_regions = DB::table('tbship_regions')
                ->where('CLMREGION_ALL_COUNTRIES', 0)
                ->get();
        foreach($ship_regions as $ship_region){
            $data = [
                'id' => $ship_region->CLMREGIONID,
                'name' => $ship_region->CLMREGION_NAME,
            ];
            
            // Insert data into the 'users' table using the query builder
            DB::table('regions')->insert($data);
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
