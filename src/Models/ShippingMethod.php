<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethod extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'ext_code',
        'name',
        'method_type',
        'active',
        'public',
        'tax_id',
        'amount',
        'amount_with_tax',
        'order_amount',
        'order_amount_with_tax',
        'own_charges_type',
        'own_charges_range_type',
        'shipping_method_type_id',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'shipping_methods';

    protected $casts = [
        'active' => 'boolean',
        'public' => 'boolean',
        'order_amount_with_tax' => 'boolean',
    ];

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function shippingMethodRanges()
    {
        return $this->hasMany(ShippingMethodRange::class);
    }

    public function shippingMethodType()
    {
        return $this->belongsTo(ShippingMethodType::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class,'country_shipping_method','shipping_method_id','country_id');
        
    }
    public function excludecountries()
    {
        return $this->belongsToMany(Country::class,'exclude_country_shipping_method','shipping_method_id','country_id');
        
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class,'region_shipping_method','shipping_method_id','region_id');
        
    }

    public function pickups()
    {
        return $this->belongsToMany(Pickup::class,'pickup_shipping_method','shipping_method_id','pickup_id');
    }

    public function excludepickups()
    {
        return $this->belongsToMany(Pickup::class,'exclude_pickup_shipping_method','shipping_method_id','pickup_id');
    }

    public function region()
    {
        return $this->belongsToMany(Region::class,'region_shipping_method','shipping_method_id','region_id');
    }

    public function pickupgroups()
    {
        return $this->belongsToMany(PickupGroup::class,'pickup_groups_shipping_method','shipping_method_id', 'pickup_group_id');
    }

    
}
