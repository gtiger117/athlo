<?php

namespace Gtiger117\Athlo\Nova;

use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Trix;

class BlogPost extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Gtiger117\Athlo\Models\BlogPost::class;

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
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

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
                ->rules('required', 'max:255')
                ->placeholder('Name')
                ->translatable(),

            Image::make('Image')
                ->rules('nullable', 'image', 'max:1024')
                ->placeholder('Image'),

            Trix::make('Description')
                ->rules('nullable')
                ->placeholder('Description')
                ->translatable(),


            Trix::make('Short Description')
                ->rules('nullable')
                ->placeholder('Short Description')
                ->translatable(),

            Date::make('Publish On')
                ->rules('nullable', 'date')
                ->placeholder('Publish On'),

            Number::make('Priority')
                ->rules('nullable', 'numeric')
                ->placeholder('Priority')
                ->default('0')
                ->sortable()
                ->hide(),

			// Images::make('Images', 'images') // second parameter is the media collection name
			// 		->conversionOnPreview('medium-size') // conversion used to display the "original" image
			// 		->conversionOnDetailView('thumb') // conversion used on the model's view
			// 		->conversionOnIndexView('thumb') // conversion used to display the image on the model's index page
			// 		->conversionOnForm('thumb') // conversion used to display the image on the model's form
			// 		->fullSize() // full size column
			// 		// ->rules('required', 'size:3') // validation rules for the collection of images
			// 		// validation rules for the collection of images
			// 		->singleImageRules('dimensions:min_width=100')
			// 		->hideFromIndex(),

            Boolean::make('Featured')
                ->rules('nullable', 'boolean')
                ->placeholder('Featured')
                ->default(false),

            Boolean::make('Published')
                ->rules('nullable', 'boolean')
                ->placeholder('Published')
                ->default('1'),

            BelongsTo::make('Blog', 'blog'),
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
