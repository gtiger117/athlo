<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UpdateCartController extends Controller
{
    public function update_cart(Request $request)
    {
        // $this->authorize('create', Customer::class);
        $validator = Validator::make($request->all(), [
            'id' => [
                'required','integer',
                Rule::exists('tbpc_basket', 'CLMBASKET_ID'),
            ],
            'quantity' => ['required','integer'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        // Create a new user
        $user = DB::table('tbpc_basket')->where('CLMBASKET_ID', $request->id)->update([
            'CLMPRODUCTQTY' => $request->quantity
        ]);

        // You can customize the response as per your needs
        return response()->json([
            'message' => 'User updated successfully' 
        ], 201);
    }
}
