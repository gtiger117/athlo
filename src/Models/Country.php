<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['code', 'name', 'image'];

    protected $table = 'countries';

    protected $searchableFields = ['*'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function paymentMethods()
    {
        return $this->belongsToMany(
            PaymentMethod::class,
            'include_country_paymethod'
        );
    }

    public function paymentMethods2()
    {
        return $this->belongsToMany(
            PaymentMethod::class,
            'exclude_country_paymethod'
        );
    }

    public function shippingMethods()
    {
        return $this->belongsToMany(ShippingMethod::class);
    }
}
