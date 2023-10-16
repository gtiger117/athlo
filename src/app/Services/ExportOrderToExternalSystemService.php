<?php

namespace App\Services;

use App\Models\PaymentMethodType;

class ExportOrderToExternalSystemService
{
    public function export_order_to_external_systems($post_data = [])
    {
        echo 'we are adding payment gateway';

        // $perPage = 1000;
        // $page = 1;
        
        // $query = PaymentMethodType::select('*');
        
        // if (isset($post_data['payment_method_type_id']) && $post_data['payment_method_type_id'] != '') {
        //     $query = $query->where('id', $post_data['payment_method_type_id']);
        // }
        
        // $query = $query->where('active', 1)
        //         ->orderBy('sort_order', 'desc')
        //         ->orderBy('name', 'desc')
        //         ->paginate($perPage, ['/*'], 'page', $page);

        // foreach ($query as $key=>$row) {
        //     $query[$key]->payment_gateway_code = 'jcc';
        //     $query[$key]->displayname = $row->name;
        //     $query[$key]->amount = 10;
        //     $query[$key]->payment_method_id = 1;
        //     $query[$key]->tax = 1.19;
        // }

        // $paymentmethods = $query;

        $payment_gateway = [];
        
        return response()->json($payment_gateway);
        
    }
    
}

?>