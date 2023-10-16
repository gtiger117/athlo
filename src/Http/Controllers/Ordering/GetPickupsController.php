<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\GetPickupsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetPickupsController extends Controller
{
    protected $getpickups;

    public function __construct(GetPickupsService $GetPickupsService)
    {
        $this->getpickups = $GetPickupsService;
    }
    public function get_pickups(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'pickup_group_id' => 'nullable|exists:pickup_groups,id'
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $postData = [
            'id'=> $request->id, 
            'pickup_group_id'=> $request->pickup_group_id,
        ];

        $pickup_response = Helper::get_pickups($postData);       
        $pickup_response = json_decode($pickup_response);

        // return true;
        return response()->json($pickup_response);
    }
}
