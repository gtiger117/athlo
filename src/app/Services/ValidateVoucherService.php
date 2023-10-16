<?php

namespace App\Services;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ValidateVoucherService
{
    public function validate_voucher_method($post_data = [])
    {
        $voucher_array = ['id' => '', 'type' => '', 'amount' => '', 'tax' => '', 'active'=>false];
        if(isset($post_data['voucher_code']) && $post_data['voucher_code'] == '1234'){
            $voucher_array = ['id' => '1', 'type' => 'promotional', 'amount' => '10', 'tax' => '1.19', 'active' => true];
        }
        
        return response()->json($voucher_array);
    }
}

?>