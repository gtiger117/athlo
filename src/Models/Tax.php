<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Tax extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = ['ext_code', 'name', 'percentage'];

    protected $searchableFields = ['*'];
}
