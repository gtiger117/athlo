<?php

namespace Gtiger117\Athlo\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethodRange extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'from',
        'to',
        'amount',
        'per_order',
        'shipping_method_id',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'shipping_method_ranges';

    protected $casts = [
        'per_order' => 'boolean',
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }
}
