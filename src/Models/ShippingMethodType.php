<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class ShippingMethodType extends Model implements Sortable
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
        'ext_code',
        'name',
        'delivery_type',
        'image',
        'active',
        'description',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'shipping_method_types';

    protected $casts = [
        'name' => 'array',
        'active' => 'boolean',
    ];

    public function shippingMethods()
    {
        return $this->hasMany(ShippingMethod::class);
    }

    public function paymentMethodTypes()
    {
        return $this->belongsToMany(
            PaymentMethodType::class,
            'payment_ship_method_type'
        );
    }

    


    

    public function includePaymentGateways()
    {
        return $this->belongsToMany(PaymentGateway::class,'include_payment_gateway_ship_method_type','shipping_method_type_id','payment_gateway_id');   
    }

    public function excludePaymentGateways()
    {
        return $this->belongsToMany(PaymentGateway::class,'exclude_payment_gateway_ship_method_type','shipping_method_type_id','payment_gateway_id');
    }
}
