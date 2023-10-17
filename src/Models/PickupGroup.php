<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class PickupGroup extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;
    use SortableTrait;

    public $translatable = ['name', 'description'];

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
        'nova_order_by' => 'DESC',
      ];
    

    protected $fillable = [
        'name',
        'ext_code',
        'image',
        'description',
        'active',
        'sort_order',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'pickup_groups';

    protected $casts = [
        'name' => 'array',
        'active' => 'boolean',
    ];

    public function pickups()
    {
        return $this->hasMany(Pickup::class);
    }
}
