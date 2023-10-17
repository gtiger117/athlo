<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class PaymentMethodType extends Model implements Sortable
{
    use SortableTrait;
    use HasFactory;
    use Searchable;
    use HasTranslations;

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
        'nova_order_by' => 'DESC',
      ];
      
    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'image',
        'payment_gateway_id',
        'active',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'payment_method_types';

    protected $casts = [
        'name' => 'array',
        'active' => 'boolean',
    ];

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function shippingMethodTypes()
    {
        return $this->belongsToMany(
            ShippingMethodType::class,
            'payment_ship_method_type'
        );
    }
}
