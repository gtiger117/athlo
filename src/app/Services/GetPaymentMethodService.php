<?php

namespace App\Services;

use App\Models\PaymentMethodType;

class GetPaymentMethodService
{
    public function get_active_payment_methods($post_data = [])
    {
        $perPage = 1000;
        $page = 1;
        
        $query = PaymentMethodType::select('*');
        
        if (isset($post_data['payment_method_type_id']) && $post_data['payment_method_type_id'] != '') {
            $query = $query->where('id', $post_data['payment_method_type_id']);
        }
        
        $query = $query->where('active', 1)
                ->orderBy('sort_order', 'desc')
                ->orderBy('name', 'desc')
                ->paginate($perPage, ['/*'], 'page', $page);

        foreach ($query as $key=>$row) {
            $query[$key]->payment_gateway_code = 'del';
            $query[$key]->displayname = $row->name;
            $query[$key]->amount = 10;
            $query[$key]->payment_method_id = 1;
            $query[$key]->tax = 1.19;
        }

        $paymentmethods = $query;
        
        return response()->json($paymentmethods);
    }
}

?>