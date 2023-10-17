<?php

namespace Gtiger117\Athlo\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'blog_id',
        'name',
        'image',
        'description',
        'publish_date',
        'published',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'publish_date' => 'date',
        'published' => 'boolean',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
