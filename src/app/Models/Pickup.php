<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Pickup extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'ext_code',
        'pickup_group_id',
        'image',
        'active',
        'sort_order',
        'description',
        'label',
        'address', 
        'district', 
        'city', 
        'country',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'name' => 'array',
        'active' => 'boolean',
    ];

    public function pickupGroup()
    {
        return $this->belongsTo(PickupGroup::class);
    }
}
