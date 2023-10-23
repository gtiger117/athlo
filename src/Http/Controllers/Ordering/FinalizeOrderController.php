<?php

namespace Gtiger117\Athlo\Http\Controllers\Ordering;

use App\Helpers\Helper;
use Gtiger117\Athlo\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinalizeOrderController extends Controller
{
    public function finalize_order(Request $request)
    {
        // $this->authorize('view-any', PaymentMethod::class);
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:tbordering_temp_orders,CLMTEMPORDER_ID',
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
        ];
        $finalizeorder_response = Helper::finalize_order($postData);       
        $finalizeorder_response = json_decode($finalizeorder_response); 

        // return true;
        return response()->json($finalizeorder_response);
    }
    //
}
