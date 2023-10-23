<?php

namespace Gtiger117\Athlo\Http\Controllers\Accounts;

use Gtiger117\Athlo\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AddRemoveWishlistController extends Controller
{
    public function add_remove_to_wish_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => [
                'required','integer',
                Rule::exists('tbaccounts_user', 'CLM_ACCOUNT_ID'),
            ],
            'product_id' => [
                'required','integer',
                Rule::exists('tbpc_products_groups', 'CLMPRODGROUP_ID'),
            ],
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:add,remove'
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $existingModel = DB::table('tbpc_basket')->where('CLMGROUPID', $request->product_id)
                                    ->where('CLMACCOUNT_ID', $request->customer_id)
                                    ->where('CLMPRODUCTTYPE', 'w')
                                    ->select('CLMBASKET_ID as id', 'CLMPRODUCTQTY as quantity')
                                    ->first();

        if($request->action == 'add'){
            $quantity = 1;
        }
        else if($request->action == 'remove'){
            $quantity = -1;
        }

        if ($existingModel) {
            // If the model exists, increment the desired field by 1
            DB::table('tbpc_basket')->where('CLMBASKET_ID', $existingModel->id)->update(['CLMPRODUCTQTY' => $quantity]);
        } 
        else {
            // If the model doesn't exist, create a new one with the desired field set to 1
            DB::table('tbpc_basket')->insert([
                                'CLMACCOUNT_ID' => $request->customer_id, 
                                'CLMGROUPID' => $request->product_id, 
                                'CLMPRODUCTQTY' => $quantity,
                                'CLMPRODUCTTYPE' => 'w'
                            ]);
        }
        DB::table('tbpc_basket')->where('CLMPRODUCTQTY', '<=', 0)->delete();

        return response()->json([
            'message' => 'Wish List updated successfully'
        ], 201);
    }
}
