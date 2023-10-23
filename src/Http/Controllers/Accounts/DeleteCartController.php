<?php

namespace Gtiger117\Athlo\Http\Controllers\Accounts;

use Gtiger117\Athlo\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DeleteCartController extends Controller
{
    public function delete_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => [
                'required','integer',
                Rule::exists('tbaccounts_user', 'CLM_ACCOUNT_ID'),
            ]
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        
        DB::table('tbpc_basket')->where('CLMACCOUNT_ID', $request->customer_id)->where('CLMPRODUCTTYPE', 'b')->delete();

        return response()->json([
            'message' => 'Cart list removed successfully'
        ], 201);
    }
}
