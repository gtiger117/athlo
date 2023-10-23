<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AddRemoveCartController extends Controller
{
    public function add_remove_to_cart(Request $request)
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
            'product_variant_id' => [
                'required','integer',
                Rule::exists('tbpc_products', 'CLMPRODUCT_ID'),
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
                                    ->where('CLMPRODUCTID', $request->product_variant_id)
                                    ->where('CLMPRODUCTTYPE', 'b')
                                    ->select('CLMBASKET_ID as id', 'CLMPRODUCTQTY as quantity')
                                    ->first();

        if($request->action == 'add'){
            $quantity = $request->quantity;
        }
        else if($request->action == 'remove'){
            $quantity = (-1) * $request->quantity;
        }

        if ($existingModel) {
            // If the model exists, increment the desired field by 1
            DB::table('tbpc_basket')->where('CLMBASKET_ID', $existingModel->id)->update(['CLMPRODUCTQTY' => $existingModel->quantity + $quantity]);
        } 
        else {
            // If the model doesn't exist, create a new one with the desired field set to 1
            DB::table('tbpc_basket')->insert([
                                'CLMACCOUNT_ID' => $request->customer_id, 
                                'CLMGROUPID' => $request->product_id, 
                                'CLMPRODUCTID' => $request->product_variant_id,
                                'CLMPRODUCTQTY' => $quantity,
                                'CLMPRODUCTTYPE' => 'b'
                            ]);
        }
        DB::table('tbpc_basket')->where('CLMPRODUCTQTY', '<=', 0)->delete();

        return response()->json([
            'message' => 'Shopping Cart updated successfully'
        ], 201);
    }
    public function add_remove_to_carts(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'customer_id' => [
                'required','integer',
                Rule::exists('tbaccounts_user', 'CLM_ACCOUNT_ID'),
            ],
            'product_ids' => 'nullable|array',
            'product_variant_ids' => 'nullable|array',
            'quantitys' => 'nullable|array',
            'action' => 'required|in:add,remove'
        ]);
        // return $request->product_ids;
        for($i=0;$i<count($request->product_ids);$i++){

            // Check if the validation fails p
            if ($validator->fails()) {
                // Return the validation errors
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }
            $existingModel = DB::table('tbpc_basket')->where('CLMGROUPID', $request->product_ids[$i])
                                        ->where('CLMACCOUNT_ID', $request->customer_id)
                                        ->where('CLMPRODUCTID', $request->product_variant_ids[$i])
                                        ->where('CLMPRODUCTTYPE', 'b')
                                        ->select('CLMBASKET_ID as id', 'CLMPRODUCTQTY as quantity')
                                        ->first();

            if($request->action == 'add'){
                $quantity = $request->quantitys[$i];
            }
            else if($request->action == 'remove'){
                $quantity = (-1) * $request->quantitys[$i];
            }

            if ($existingModel) {
                // If the model exists, increment the desired field by 1
                DB::table('tbpc_basket')->where('CLMBASKET_ID', $existingModel->id)->update(['CLMPRODUCTQTY' => $existingModel->quantity + $quantity]);
            } 
            else {
                // If the model doesn't exist, create a new one with the desired field set to 1
                DB::table('tbpc_basket')->insert([
                                    'CLMACCOUNT_ID' => $request->customer_id, 
                                    'CLMGROUPID' => $request->product_ids[$i], 
                                    'CLMPRODUCTID' => $request->product_variant_ids[$i],
                                    'CLMPRODUCTQTY' => $quantity,
                                    'CLMPRODUCTTYPE' => 'b'
                                ]);
            }
            DB::table('tbpc_basket')->where('CLMPRODUCTQTY', '<=', 0)->delete();
        }

        // return response()->json([
        //     'message' => 'Shopping Cart updated successfully'
        // ], 201);
    }
}
