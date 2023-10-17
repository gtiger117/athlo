<?php

namespace Gtiger117\Athlo\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Category extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Gtiger117\Athlo\Models\Category::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'CLMCATEGORY_ML_NAME';

    /**asd
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['CLMCATEGORY_ML_NAME'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('CLMCATEGORY_ID','CLMCATEGORY_ID')->sortable(),

            // Text::make('Code')
            //     ->creationRules(
            //         'required',
            //         'unique:countries,code',
            //         'max:255',
            //         'string'
            //     )
            //     ->updateRules(
            //         'required',
            //         'unique:countries,code,{{resourceId}}',
            //         'max:255',
            //         'string'
            //     )
            //     ->placeholder('Code'),

            Text::make('CLMCATEGORY_ML_NAME','CLMCATEGORY_ML_NAME')
                ->rules('required', 'max:255', 'string')
                ->placeholder('Name'),

            // Image::make('Image')
            //     ->rules('nullable', 'image', 'max:1024')
            //     ->placeholder('Image'),
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
