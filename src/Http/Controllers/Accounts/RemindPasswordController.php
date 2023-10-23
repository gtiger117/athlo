<?php

namespace Gtiger117\Athlo\Http\Controllers\Accounts;

use Gtiger117\Athlo\Http\Controllers\Controller;
use Gtiger117\Athlo\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RemindPasswordController extends Controller
{
    public function remind_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:tbaccounts_user,CLM_CUST_EMAIL',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
// echo $request->email.'<br>';

        $user = Accounts::where('CLM_CUST_EMAIL', $request->email)->first();

        
        if($user){
            $data = '1234567890';
            $code = substr(str_shuffle($data), 0, 8);

            DB::table('tbaccounts_user')
            ->where('CLM_ACCOUNT_ID', $user->CLM_ACCOUNT_ID)
            ->update(['CLM_CUST_PWD' => $code]);

            // Accounts::where('CLM_CUST_EMAIL', $request->email)->update(['CLM_CUST_PWD' => $code]);
            
            $email_message = 'Dear ' . $user->CLM_CUST_NAME . ' ' . $user->CLM_CUST_SURNAME . ',<br><br>
                            Your password is:<br>
                            <b>Password:</b> '.$code.'<br><br>
                            In order to login please visit '.env('APP_URL').' and login.<br><br>
                            This is an automatic generated e-mail. Please do not reply to this e-mail.';
            $to = $request->email;
            $subject = 'Password reminder';
            $headers  = "From: ".env('MAIL_FROM_ADDRESS')."\r\n";
            $headers .= "Reply-To: ".env('MAIL_FROM_ADDRESS')."\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            mail($to, $subject, $email_message, $headers);

            // You can customize the response as per your needs
            return response()->json([
                'message' => 'User password reminder sent successfully',
            ], 201);
            
        }
    }
}
