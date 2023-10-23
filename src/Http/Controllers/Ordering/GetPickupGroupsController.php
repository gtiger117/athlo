<?php

namespace App\Http\Controllers\Ordering;

use App\Http\Controllers\Controller;
use App\Models\PickupGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetPickupGroupsController extends Controller
{
    public function get_pickup_groups(Request $request)
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

        $perPage = 1000;
        $page = 1;

        $query = PickupGroup::select('*');

        
        if ($request->has('id') && $request->id != '') {
            $query = $query->whereIn('id',$request->id);
        }
        
        $query = $query->where('active', 1)
                ->orderBy('sort_order', 'desc')
                ->orderBy('name', 'desc')
                ->paginate($perPage, ['/*'], 'page', $page);
        foreach ($query as $key=>$row) {
            $name_array =json_decode($row->name);
            $query[$key]->name = isset($name_array->en)?$name_array->en:'';
        }

        $pickups = $query;
        
        return response()->json($pickups);
    }
}
