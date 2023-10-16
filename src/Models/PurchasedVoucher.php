<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Searchable;

class PurchasedVoucher extends Model
{
    use HasFactory;
    use Searchable;

    protected $searchableFields = ['*'];

    protected $casts = [
        'active' => 'boolean',
        'is_used' => 'boolean',
        'used_date' => 'date',
    ];

    protected $fillable = [
        'voucher_code',
        'voucher_order_id',
        'amount',
        'active',
        'is_used',
        'used_date',
        'order_id',
    ];

    public function voucherOrder()
    {
        return $this->belongsTo(VoucherOrder::class);
    }
}
