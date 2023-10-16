<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UpdateCustomerController extends Controller
{
    public function update_user(Request $request)
    {
        // $this->authorize('create', Customer::class);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'id' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('tbaccounts_user', 'CLM_CUST_EMAIL')->where(function ($query) use ($request){
                    // Exclude rows with a specific condition
                    $query->where('CLM_ACCOUNT_ID', '!=', $request->id);
                }),
            ],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        // Create a new user
        $user = DB::table('tbaccounts_user')->where('CLM_ACCOUNT_ID', $request->id)->update([
            'CLM_CUST_NAME' => $request->name,
            'CLM_CUST_SURNAME' => $request->surname,
            'CLM_CUST_EMAIL' => $request->email,
            'CLM_CUST_PHONE' => $request->phone,
            'CLM_ADR_POSTCODE' => $request->postcode,
            'CLM_ADR_CITY' => $request->city,
            'CLM_ADR_STATE' => $request->district,
            'CLM_ADR_COUNTRY' => $request->country,
        ]);

        // You can customize the response as per your needs
        return response()->json([
            'message' => 'User updated successfully' 
        ], 201);
    }
}
