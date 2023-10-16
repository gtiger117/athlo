<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CreateCustomerController extends Controller
{
    public function create_user(Request $request)
    {
        // $this->authorize('create', Customer::class);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:tbaccounts_user,CLM_CUST_EMAIL',
            'password' => 'required|min:4',
            'confirm_password' => 'required|same:password',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        

        // Create a new user
        $user = Accounts::insert([
            'CLM_CUST_NAME' => $request->name,
            'CLM_CUST_SURNAME' => $request->surname,
            'CLM_CUST_EMAIL' => $request->email,
            'CLM_CUST_PWD' => md5($request->password),
            'CLM_CUST_PHONE' => $request->phone,
            'CLM_ADR_POSTCODE' => $request->postcode,
            'CLM_ADR_CITY' => $request->city,
            'CLM_ADR_STATE' => $request->district,
            'CLM_ADR_COUNTRY' => $request->country,
            'CLM_CUST_NAMEDAY' => '',
        ]);

        $user = DB::table('tbaccounts_user')->where('CLM_CUST_EMAIL', $request->email)->first();

        $_SESSION["email"] = $user->CLM_CUST_EMAIL;
        $_SESSION["name"] = $user->CLM_CUST_NAME;
        $_SESSION["surname"] = $user->CLM_CUST_SURNAME;
        $_SESSION["user_id"] = $user->CLM_ACCOUNT_ID;

        // You can customize the response as per your needs
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }
}
