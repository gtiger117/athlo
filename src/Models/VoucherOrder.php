<?php

namespace Gtiger117\Athlo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Searchable;

class VoucherOrder extends Model
{
    use HasFactory;
    use Searchable;

    protected $searchableFields = ['*'];

    protected $casts = [
        'active' => 'boolean',
        'expiry_date' => 'date',
    ];

    protected $fillable = [
        'name',
        'hash',
        'payment_reference_number',
        'order_id',
        'return_order_id',
        'email_template_id',
        'sender_name',
        'sender_email',
        'sender_phone',
        'recipient_name',
        'recipient_email',
        'recipient_phone',
        'message',
        'gift_vouchers_id',
        'amount',
        'quantity',
        'active',
        'expiry_date',
        'type',
        'status',
        'voucher_id',
        'source',
    ];

    public function giftVouchers()
    {
        return $this->belongsTo(GiftVoucher::class);
    }

    public function purchasedVoucher()
    {
        return $this->hasMany(PurchasedVoucher::class);
    }

    public function voucheremailTemplate()
    {
        return $this->belongsTo(VoucherEmailTemplate::class);
    }
}
