<?php

namespace Gtiger117\Athlo\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ctessier\NovaAdvancedImageField\AdvancedImage;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Laravel\Nova\Fields\Trix;
use Mostafaznv\NovaVideo\Video;

class BannerImage extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Gtiger117\Athlo\Models\BannerImage::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['name'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('id')->sortable(),

            Text::make('Name')
                ->rules('nullable', 'max:255')
                ->translatable()
                ->placeholder('Name'),

            AdvancedImage::make('Image')
                ->croppable()
                ->resize(1920)
                ->driver('imagick')
                ->croppable(),

            AdvancedImage::make('Mobile Image')
                ->croppable()
                ->resize(1920)
                ->driver('imagick')
                ->croppable(),

            Trix::make('Description')
                ->rules('nullable')
                ->translatable()
                ->placeholder('Description'),

            Text::make('Button Text')
                ->rules('nullable', 'max:255')
                ->translatable()
                ->placeholder('Button Text'),

            Text::make('Link')
                ->rules('nullable', 'max:255')
                ->placeholder('Link'),

            Number::make('Sort Order')
                ->rules('required', 'numeric')
                ->placeholder('Sort Order')
                ->default('0'),

            Images::make('Images', 'images') // second parameter is the media collection name
                ->conversionOnPreview('medium-size') // conversion used to display the "original" image
                ->conversionOnDetailView('thumb') // conversion used on the model's view
                ->conversionOnIndexView('thumb') // conversion used to display the image on the model's index page
                ->conversionOnForm('thumb') // conversion used to display the image on the model's form
                ->fullSize() // full size column
                ->singleImageRules('dimensions:min_width=100')
                ->hideFromIndex(),

            Video::make(trans('Video'), 'video', 'media')
                ->rules('file', 'max:150000', 'mimes:mp4', 'mimetypes:video/mp4')
                ->creationRules('nullable')
                ->updateRules('nullable'),

            Text::make('Youtube Video')
                ->rules('nullable', 'max:255')
                ->placeholder('Youtube Video'),

            Boolean::make('Active')
                ->rules('required', 'boolean')
                ->placeholder('Active')
                ->default('1'),
            
            

            BelongsTo::make('Banner', 'banner'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
