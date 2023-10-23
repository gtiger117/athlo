<?php

namespace App\Observers;

use Gtiger117\Athlo\Models\PurchasedVoucher;

class PurchasedVoucherObserver
{
    /**
     * Handle the PurchasedVoucher "created" event.
     */
    public function created(PurchasedVoucher $purchasedVoucher): void
    {
        $number = '1'.$purchasedVoucher->id;
        $missing = 8 - strlen($number);
        $string = "example string";
        $randomNumber = abs(crc32($string) + mt_rand());
        $first_part_of_string = substr($randomNumber,0,$missing);
        $voucher_code = $number.$first_part_of_string;

        PurchasedVoucher::where("id", $purchasedVoucher->id)->update(["voucher_code"=> $voucher_code]);
    }

    /**
     * Handle the PurchasedVoucher "updated" event.
     */
    public function updated(PurchasedVoucher $purchasedVoucher): void
    {
        //
    }

    /**
     * Handle the PurchasedVoucher "deleted" event.
     */
    public function deleted(PurchasedVoucher $purchasedVoucher): void
    {
        //
    }

    /**
     * Handle the PurchasedVoucher "restored" event.
     */
    public function restored(PurchasedVoucher $purchasedVoucher): void
    {
        //
    }

    /**
     * Handle the PurchasedVoucher "force deleted" event.
     */
    public function forceDeleted(PurchasedVoucher $purchasedVoucher): void
    {
        //
    }
}
