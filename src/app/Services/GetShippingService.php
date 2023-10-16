<?php

namespace App\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GetShippingService
{
    public function get_active_shipping_methods($post_data = [])
    {
        $perPage = 1000;
        $page = 1;
        
        $query = DB::table('shipping_method_types');
        
        if (isset($post_data['id']) && $post_data['id'] != '') {
            $query = $query->where('id',$post_data['id']);
        }
        
        $query = $query->select('shipping_method_types.*')
                ->where('shipping_method_types.active', 1)
                ->orderBy('shipping_method_types.name', 'asc')
                ->paginate($perPage, ['/*'], 'page', $page);

        foreach ($query as $key=>$row) {
            $name_array =json_decode($row->name);
            $query[$key]->name = isset($name_array->en)?$name_array->en:'';
            $description_array =json_decode($row->description);
            $query[$key]->description = isset($description_array->en)?$description_array->en:'';
            $query[$key]->amount = 20;
            $query[$key]->tax = 2.38;
        }

        $shippingmethods = $query;
        
        return response()->json($shippingmethods);
    }
}

?>