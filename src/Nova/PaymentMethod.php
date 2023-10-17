<?php

namespace Gtiger117\Athlo\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use ZiffMedia\NovaSelectPlus\SelectPlus;

class PaymentMethod extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Gtiger117\Athlo\Models\PaymentMethod::class;

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

            Image::make('Image')
                ->rules('nullable', 'image', 'max:1024')
                ->placeholder('Image'),

            Trix::make('Description')
                ->rules('nullable')
                ->placeholder('Description')
                ->translatable(),

            Number::make('Amount')
                ->rules('required', 'numeric')
                ->placeholder('Amount')
                ->step('0.01')
                ->default('0'),

            BelongsTo::make('Tax', 'tax')->showCreateRelationButton(),

            Number::make('Amount With Tax')
                ->rules('required', 'numeric')
                ->placeholder('Amount With Tax')
                ->step('0.01')
                ->default('0'),

            
            Boolean::make('All Countries')->default(true)->hideFromIndex(),
                
            SelectPlus::make('Include Countries', 'includeCountries', Country::class)->hideFromIndex(),

            Boolean::make('Exclude Countries')->default(false)->hideFromIndex(),
            
            SelectPlus::make('Exclude Countries', 'excludeCountries', Country::class)->hideFromIndex(),            

            SelectPlus::make('Regions', 'regions', Region::class)->hideFromIndex(),

            Boolean::make('All Pickups')->default(true)->hideFromIndex(),

            SelectPlus::make('Include Pickup', 'includePickup', Pickup::class)->hideFromIndex(),

            Boolean::make('Exclude Pickups')->default(false)->hideFromIndex(),

            SelectPlus::make('Exclude Pickup', 'excludePickup', Pickup::class)->hideFromIndex(),
            
            SelectPlus::make('Include Pickup Groups', 'includePickupGroups', PickupGroup::class)->hideFromIndex(),

            BelongsTo::make('Payment Method Type', 'paymentMethodType')->showCreateRelationButton(),
            

            Boolean::make('Active')
                ->rules('nullable', 'boolean')
                ->placeholder('Active')
                ->default('1'),

            // SelectPlus::make('Included Countries', 'country', Country::class),

            // BelongsToMany::make('Countries', 'countries_include'),

            // BelongsToMany::make('Countries', 'countries_exclude'),
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
