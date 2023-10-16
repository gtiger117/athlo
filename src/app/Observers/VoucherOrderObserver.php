<?php

namespace App\Observers;

use App\Models\PurchasedVoucher;
use App\Models\VoucherOrder;

class VoucherOrderObserver
{
    /**
     * Handle the VoucherOrder "created" event.
     */
    public function created(VoucherOrder $voucherOrder): void
    {
        $hash = md5($voucherOrder->id . date('Y-m-d'));
        $voucherOrder->hash = $hash;
        $voucherOrder->save();


        $data = [            
            'hash' =>  $hash,
            'voucher_order_id' =>  $voucherOrder->id,
            'amount' =>  $voucherOrder->amount,
        ];
        for($i =0; $i<$voucherOrder->quantity; $i++){
            PurchasedVoucher::create($data);
        }        
    }

    /**
     * Handle the VoucherOrder "updated" event.
     */
    public function updated(VoucherOrder $voucherOrder): void
    {
        //
    }

    /**
     * Handle the VoucherOrder "deleted" event.
     */
    public function deleted(VoucherOrder $voucherOrder): void
    {
        //
    }

    /**
     * Handle the VoucherOrder "restored" event.
     */
    public function restored(VoucherOrder $voucherOrder): void
    {
        //
    }

    /**
     * Handle the VoucherOrder "force deleted" event.
     */
    public function forceDeleted(VoucherOrder $voucherOrder): void
    {
        //
    }
}
