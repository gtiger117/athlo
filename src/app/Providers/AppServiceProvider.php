<?php

namespace App\Providers;

use App\Models\Package;
use App\Models\PurchasedVoucher;
use App\Models\VoucherOrder;
use App\Observers\PackageObserver;
use App\Observers\PurchasedVoucherObserver;
use App\Observers\VoucherOrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PurchasedVoucher::observe(PurchasedVoucherObserver::class);
        VoucherOrder::observe(VoucherOrderObserver::class);
        Package::observe(PackageObserver::class);
    }
}
