<?php

namespace Gtiger117\Athlo\Models;

use Gtiger117\Athlo\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Banner extends Model
{
    use HasFactory;
    use Searchable;
	use HasTranslations;
    use InteractsWithMedia;
	
	public $translatable = ['title','description'];

    protected $fillable = [
        'name',
        'image',
        'title',
        'description',
        'active',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'active' => 'boolean',
    ];

    public function bannerImages()
    {
        return $this->hasMany(BannerImage::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public');
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
