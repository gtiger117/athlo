<?php

namespace Gtiger117\Athlo\Models;

use App\Models\Scopes\Searchable;
use Ebess\AdvancedNovaMediaLibrary\Fields\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BlogPost extends Model
{
    use HasFactory;
    use Searchable;
    use HasTranslations;

    protected $fillable = [
        'blog_id',
        'name',
        'image',
        'description',
        'short_description',
        'publish_on',
        'priority',
        'published',
    ];

    protected $searchableFields = ['*'];

    public $translatable = ['name','description','short_description'];

    protected $table = 'blog_posts';

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'short_description' => 'array',
        'publish_on' => 'date',
        'published' => 'boolean',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('large-size')
        ->width(1024)
        ->height(768);
        
        $this->addMediaConversion('medium-size')
        ->width(768)
        ->height(576);

        $this->addMediaConversion('thumb')
        ->width(368)
        ->height(232);
    }
}
