<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Pickup extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Pickup::class;

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
                ->rules('required', 'max:255')
                ->placeholder('Name')
                ->translatable(),

            Text::make('Ext Code')
                ->creationRules(
                    'nullable',
                    'unique:pickups,ext_code',
                    'max:255',
                    'string'
                )
                ->updateRules(
                    'nullable',
                    'unique:pickups,ext_code,{{resourceId}}',
                    'max:255',
                    'string'
                )
                ->placeholder('Ext Code'),

            Image::make('Image')
                ->rules('nullable', 'image', 'max:1024')
                ->placeholder('Image'),

            

            Number::make('Sort Order')
                ->rules('required', 'numeric')
                ->placeholder('Sort Order')
                ->default('0')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromIndex(),

            Trix::make('Description')
                ->rules('nullable', 'max:255')
                ->placeholder('Description')
                ->translatable(),

            Text::make('Label')
                ->rules('nullable', 'max:255', 'string')
                ->placeholder('Label')
                ->translatable(),

            Text::make('Address')
                ->rules('nullable', 'max:255', 'string')
                ->placeholder('Address'),

            Text::make('City')
                ->rules('nullable', 'max:255', 'string')
                ->placeholder('City'),

            Text::make('District')
                ->rules('nullable', 'max:255', 'string')
                ->placeholder('District'),
            
            Country::make('Country')
                ->rules('nullable', 'max:255', 'string')
                ->placeholder('Country')
                ->default('CY'),

            

            BelongsTo::make('PickupGroup', 'pickupGroup')->showCreateRelationButton(),


            Boolean::make('Active')
                ->rules('required', 'boolean')
                ->placeholder('Active')
                ->default('1'),

            // BelongsToMany::make('ShippingMethods', 'shippingMethods'),
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
