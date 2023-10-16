<?php

namespace App\Services;

use App\Models\PaymentMethodType;
use App\Models\Pickup;

class GetPickupsService
{
    public function get_active_pickups($post_data = [])
    {
        $perPage = 1000;
        $page = 1;

        $query = Pickup::select("*");

        if (isset($post_data['id']) && $post_data['id'] != '') {
            $query = $query->where('id', $post_data['id']);
        }
        if (isset($post_data['pickup_group_id']) && $post_data['pickup_group_id'] != '') {
            $query = $query->where('pickup_group_id', $post_data['pickup_group_id']);
        }
        
        $query = $query->orderBy('name', 'asc')
                ->paginate($perPage, ['/*'], 'page', $page);

        foreach ($query as $key=>$row) {
            $query[$key]->displayname = $row->name;
        }

        $pickups = $query;

        
        
        return response()->json($pickups);
    }
}

?>