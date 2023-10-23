<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use Gtiger117\Athlo\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class PaymentMethod extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;
   

    public $translatable = ['name','description'];

    protected $fillable = [
        'payment_method_type_id',
        'name',
        'image',
        'description',
        'amount',
        'tax_id',
        'amount_with_tax',
        'sort_order',
        'all_countries',
        'exclude_countries',
        'active',
    ];

    

    protected $searchableFields = ['*'];

    protected $table = 'payment_methods';

    protected $casts = [
        'name' => 'array',
        'all_countries' => 'boolean',
        'exclude_countries' => 'boolean',
        'active' => 'boolean',
    ];

    public function paymentMethodType()
    {
        return $this->belongsTo(PaymentMethodType::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function countries_include()
    {
        return $this->belongsToMany(Country::class,'include_country_paymethod');
    }

    public function includeCountries(){
        return $this->belongsToMany(Country::class,'include_country_paymethod','payment_method_id','country_id');
    }

    public function excludeCountries(){
        return $this->belongsToMany(Country::class,'exclude_country_paymethod','payment_method_id','country_id');
    }

    public function includePickup(){
        return $this->belongsToMany(Pickup::class,'include_pickup_paymethod','payment_method_id','pickup_id');
    }

    public function excludePickup(){
        return $this->belongsToMany(Pickup::class,'exclude_pickup_paymethod','payment_method_id','pickup_id');
    }

    public function includePickupGroups(){
        return $this->belongsToMany(PickupGroup::class,'include_pickup_group_paymethod','payment_method_id','pickup_group_id');
    }

    public function excludePickupGroups(){
        return $this->belongsToMany(PickupGroup::class,'exclude_pickup_group_paymethod','payment_method_id','pickup_group_id');
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class,'region_payment_method','payment_method_id','region_id');
        
    }

    public function countries_exclude()
    {
        return $this->belongsToMany(
            Country::class,
            'exclude_country_paymethod'
        );
    }
}
