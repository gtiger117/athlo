<?php

namespace Gtiger117\Athlo\Http\Controllers\Accounts;

use Gtiger117\Athlo\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ChangePasswordController extends Controller
{
    public function changepassword_user(Request $request)
    {
        // $this->authorize('create', Customer::class);
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:4',
            'confirm_password' => 'required|same:password',
            'id' => 'required'
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
            'CLM_CUST_PWD' => md5($request->password),
        ]);

        // You can customize the response as per your needs
        return response()->json([
            'message' => 'Password updated successfully'
        ], 201);
    }
}
