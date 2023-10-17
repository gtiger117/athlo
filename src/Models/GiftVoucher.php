<?php

namespace Gtiger117\Athlo\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class GiftVoucher extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;
    use SortableTrait;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'ext_code',
        'image',
        'description',
        'voucheremail_template_id',
        'tax_id',
        'sort_order',
        'active',
    ];

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
        'nova_order_by' => 'DESC',
      ];

    protected $searchableFields = ['*'];

    protected $table = 'gift_vouchers';

    protected $casts = [
        'name' => 'array',
        'any_amount' => 'boolean',
        'amount_options' => 'boolean',
        'active' => 'boolean',
    ];

    public function voucheremailTemplate()
    {
        return $this->belongsTo(VoucherEmailTemplate::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function purchasedGiftVouchers()
    {
        return $this->hasMany(PurchasedGiftVoucher::class);
    }
}
