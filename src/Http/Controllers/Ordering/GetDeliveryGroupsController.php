<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\PickupGroup;
use App\Models\ShippingMethodType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetDeliveryGroupsController extends Controller
{
    public function get_delivery_groups(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $active_pickups = Helper::get_active_pickups();

        $query = PickupGroup::select('*')->where('active', 1);

        
        if ($request->has('id') && $request->id != '') {
            $query = $query->whereIn('id',$request->id);
        }

        $query = $query->whereIn('id', function ($subquery)  use ($request, $active_pickups) {
            $subquery->select('pickup_group_id')
                ->from('pickups')
                ->whereIn('id', $active_pickups)
                ->where('active', 1);
        });
        
        $query = $query->where('active', 1)
                // ->orderBy('sort_order', 'desc')
                ->orderBy('name', 'asc')
                ->get();
        $delivery_groups = [];
        foreach ($query as $key => $row) {
            $delivery_groups[] = ['id' => $row->id, 'name' => $row->name];
        }
        $exist_address = ShippingMethodType::join('shipping_methods', 'shipping_methods.shipping_method_type_id', '=', 'shipping_method_types.id')
                                    ->where('shipping_method_types.active', 1)
                                    ->where('shipping_methods.active', 1)
                                    ->where('shipping_method_types.delivery_type', 'customer_address')
                                    ->first();
        if($exist_address){
            $delivery_groups[] = ['id' => 'address', 'name' => 'Deliver to address'];
        }

    // print_r($delivery_groups);

    //     $pickups = $query;
        
        return response()->json($delivery_groups);
    }
}
