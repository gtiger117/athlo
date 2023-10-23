<?php

use Gtiger117\Athlo\Models\Pickup;
use Gtiger117\Athlo\Models\PickupGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PickupGroup::create([
        //             'id' => '1',
        //             'name' => 'Our Store',
        //             'ext_code' => 'ourstore',
        //         ]);
        // PickupGroup::create([
        //             'id' => '2',
        //             'name' => 'External Provider',
        //             'ext_code' => 'externalprovider',
        //         ]);
        $pickups = DB::table('tbship_pickup')
                ->get();
        foreach($pickups as $pickup){
            $pickup_group_id = 2;
            if($pickup->CLM_PICKUP_TYPE == 'ourstore'){
                $pickup_group_id = 1;
            }
            $data = [
                'id' => $pickup->CLM_PICKUP_ID,
                'name' => json_encode(array('en'=>$pickup->CLM_PICKUP_TEXTNAME)),
                'pickup_group_id' => $pickup_group_id,
                'address' => $pickup->CLM_PICKUP_ADR_1,
                'district' => $pickup->CLM_PICKUP_ADR_STATE,
                'city' => $pickup->CLM_PICKUP_ADR_CITY,
                'country' => $pickup->CLM_PICKUP_ADR_COUNTRY,
            ];
            DB::table('pickups')->insert($data);
            
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
