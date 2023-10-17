<?php

namespace Gtiger117\Athlo\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Computed;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\FormData;
use ZiffMedia\NovaSelectPlus\SelectPlus;

class ShippingMethod extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Gtiger117\Athlo\Models\ShippingMethod::class;

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
                ->rules('required', 'max:255', 'string')
                ->placeholder('Name'),

            Text::make('Ext Code')
                ->creationRules(
                    'nullable',
                    'unique:product_variants,ext_code',
                    'max:255',
                    'string'
                )
                ->updateRules(
                    'nullable',
                    'unique:product_variants,ext_code,{{resourceId}}',
                    'max:255',
                    'string'
                )
                ->placeholder('Ext Code')
                ->hideFromIndex(),

            Select::make('Method Type')->options([
                'one_price' => 'One Price for All',
                'own_shipping' => 'Enter My Own Shipping Charges',
                'free_order_greater' => 'Free Shipping for Orders Greater than',
            ])->required()->sortable(),

            Number::make('Amount')
                ->rules('nullable', 'numeric')
                // ->symbol('€')
                ->placeholder('Amount')
                ->default('0')
                ->dependsOn(
                    ['method_type'],
                    function (Number $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->method_type === 'one_price') {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->step(0.01)
                ->hide()
                ->hideFromIndex()
                ->sortable(),

            Number::make('Amount with Tax')
                ->rules('nullable', 'numeric')
                // ->symbol('€')
                ->placeholder('Amount with Tax')
                ->default('0')
                ->step(0.01)
                ->dependsOn(
                    ['method_type'],
                    function (Number $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->method_type === 'one_price') {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->hideFromIndex()
                ->sortable(),

            Number::make('Order Amount')
                ->rules('required', 'numeric')
                // ->symbol('€')
                ->placeholder('Order Amount')
                ->default('0')
                ->step(0.01)
                ->dependsOn(
                    ['method_type'],
                    function (Number $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->method_type === 'free_order_greater') {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->sortable()
                ->hideFromIndex(),

            Boolean::make('All Pickups')->default(true)->sortable(),

            SelectPlus::make('Pickups', 'pickups', Pickup::class)->hideFromIndex(),

            Boolean::make('Exclude Pickups')->default(true)->sortable(),

            SelectPlus::make('Exclude Pickups', 'excludepickups', Pickup::class)->hideFromIndex(),

            SelectPlus::make('Pickup Groups', 'pickupgroups', PickupGroup::class)->hideFromIndex(),

            Boolean::make('All Countries')->default(false)->sortable(),

            SelectPlus::make('Countries', 'countries', Country::class)->hideFromIndex(),

            SelectPlus::make('Exclude Countries', 'excludecountries', Country::class)->hideFromIndex(),

            SelectPlus::make('Regions', 'region', Region::class)->hideFromIndex(),

            BelongsTo::make('Tax', 'tax')->showCreateRelationButton()->sortable(),

            HasMany::make('ShippingMethodRanges', 'shippingMethodRanges'),

            BelongsTo::make('Shipping Method Type', 'shippingMethodType')->sortable()->showCreateRelationButton(),

            Boolean::make('Public')->default(true)->sortable(),

            Boolean::make('Active')->default(true)->sortable(),

            // BelongsToMany::make('Countries', 'countries'),

            // BelongsToMany::make('Pickups', 'pickups'),
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
