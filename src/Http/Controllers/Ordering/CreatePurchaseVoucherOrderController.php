<?php

namespace App\Http\Controllers\Ordering;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\GiftVoucher;
use App\Models\VoucherOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CreatePurchaseVoucherOrderController extends Controller
{
    public function create_purchase_voucher_order(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'delivery_date' => 'date',
            'customer_id' => [
                'nullable','integer',
                Rule::exists('tbaccounts_user', 'CLM_ACCOUNT_ID'),
            ],
            'sender_name' => 'required',
            'sender_email' => 'required|email',
            'sender_phone' => 'required',
            'recipient_name' => 'required',
            'recipient_email' => 'required|email',
            'recipient_phone' => 'required',
            'voucher_id' => [
                'required','integer',
                Rule::exists('gift_vouchers', 'id'),
            ],
            'amount' => [
                'required','numeric','min:1',
            ],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $gift_voucher = GiftVoucher::where('id', $request->voucher_id)->first();
        $postData = [
                        'type'=> 'gift_voucher', 
                        'sender_name'=> $request->sender_name,
                        'sender_email'=> $request->sender_email,
                        'sender_phone'=> $request->sender_phone,
                        'recipient_name'=> $request->recipient_name,
                        'recipient_email'=> $request->recipient_email,
                        'recipient_phone'=> $request->recipient_phone,
                        'message'=> $request->message,
                        'gift_vouchers_id'=> $request->voucher_id,
                        'email_template_id'=> $gift_voucher->voucheremail_template_id,
                        'amount'=> $request->amount,
                        'quantity'=> 1,
                        'status'=> 'initiated',
                    ];

        $voucher_order = VoucherOrder::create($postData);
        return response()->json([
            'message' => 'Voucher Order created successfully',
            'status' => true,
            'redirect_link' => env('APP_URL').'/voucherpaymentgateway?hash='.$voucher_order->hash,
            'order' => $voucher_order,
        ], 201);
    }
}
