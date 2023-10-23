<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LoginCustomerController extends Controller
{
    public function login_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = Accounts::where('CLM_CUST_EMAIL', $request->email)->first();
        if($customer){
            if (md5($request->password) == $customer->CLM_CUST_PWD) {
                $_SESSION["email"] = $customer->CLM_CUST_EMAIL;
                $_SESSION["name"] = $customer->CLM_CUST_NAME;
                $_SESSION["surname"] = $customer->CLM_CUST_SURNAME;
                $_SESSION["user_id"] = $customer->CLM_ACCOUNT_ID;

                return response()->json([
                    'message' => 'User logged in successfully',
                    'user' => $customer,
                ], 201);
            } 
        }
        
        
        // Authentication failed
        return response()->json([
            'message' => 'Invalid login credentials',
        ], 401);
    }
}
