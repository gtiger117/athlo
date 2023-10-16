<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ValidateVoucherController extends Controller
{

    public function validate_voucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_code' => 'required',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $postData = [
            'voucher_code'=> $request->voucher_code
        ];
        $validate_voucher_response = Helper::validate_voucher_method($postData);
        // return true;
        return response()->json($validate_voucher_response);
    }
}
