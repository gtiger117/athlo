<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Blog extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;

    protected $fillable = ['name'];

    public $translatable = ['name'];

    protected $searchableFields = ['*'];

    protected $casts = [
        'name' => 'array',
        'active' => 'boolean',
    ];

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }
}
