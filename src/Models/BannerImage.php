<?php

namespace Gtiger117\Athlo\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BannerImage extends Model implements HasMedia
{
    use HasFactory;
    use Searchable;
    use HasTranslations;
    use InteractsWithMedia;

    public $translatable = ['name','description','button_text'];

    protected $fillable = [
        'banner_id',
        'name',
        'image',
        'mobile_image',
        'description',
        'button_text',
        'link',
        'sort_order',
        'active',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'banner_images';

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'button_text' => 'array',
        'active' => 'boolean',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
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
