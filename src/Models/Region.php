<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'ext_code'];

    protected $searchableFields = ['*'];

    public function countries()
    {
        return $this->belongsToMany(Country::class,'region_country','region_id','country_id');
        
    }
}
