<?php

namespace Gtiger117\Athlo\Nova;


use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Http\Requests\NovaRequest;
use ZiffMedia\NovaSelectPlus\SelectPlus;

use function Laravel\Prompts\text;

class Templates extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\PromotionalVoucher>
     */
    public static $model = \Gtiger117\Athlo\Models\PromotionalVoucher::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')->required()->default('Promotional Voucher'),

            Text::make('Code')
                ->creationRules(
                    'required',
                    'unique:promotional_vouchers,code',
                    'max:255',
                    'string'
                )
                ->updateRules(
                    'required',
                    'unique:promotional_vouchers,code,{{resourceId}}',
                    'max:255',
                    'string'
                )
                ->placeholder('Code'),

            Image::make('Image')->nullable(),

            Text::make('Description')->nullable(),

            Select::make('Type')
                ->rules('required', 'max:255')
                ->options(['general'=>'General', 'other'=>'Categories/Characteristics'])
                ->placeholder('Select Type')
                ->default('general'),

            Boolean::make('Can be used on shipping')->default(true)->sortable()->hideFromIndex(),
        
            Boolean::make('Can be used on products')->default(true)->sortable()->hideFromIndex(),

            Select::make('Discount Type')
                ->rules('required', 'max:255')
                ->options(['amount'=>'Amount', 'percentage'=>'Percentage'])
                ->dependsOn(
                    ['type'],
                    function (Select $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'general') {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Select Discount Type'),

            Number::make('Amount')
                ->rules('nullable', 'numeric')
                ->placeholder('Amount')
                ->dependsOn(
                    ['discount_type', 'type'],
                    function (Number $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->discount_type == 'amount' && $formData->type == 'general') {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->hideFromIndex()
                ->sortable(),

            Number::make('Percentage')
                ->rules('nullable', 'numeric', 'min:0', 'max:100')
                ->placeholder('Percentage')
                ->dependsOn(
                    ['discount_type', 'type'],
                    function (Number $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->discount_type == 'percentage' && $formData->type == 'general') {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->hideFromIndex()
                ->sortable(),

            Number::make('Minimum Order Amount')
                ->rules('required', 'numeric')
                ->placeholder('Minimum Order Amount')
                ->hideFromIndex()
                ->default('0'),
                

            Number::make('Number Of Redemptions')
                ->rules('required', 'numeric')
                ->placeholder('Number Of Redemptions')
                ->hideFromIndex()
                ->default('0'),

            SelectPlus::make('Exclude Categories', 'excludecategories', Category::class)->label('CLMCATEGORY_ML_NAME'),
            
            SelectPlus::make('Exclude characteristics', 'excludecharacteristics', CharValue::class)->label('CLMCHARVALUE_ML_NAME'),
            
            Boolean::make('Cannot be used when product has discount')->default(true)->sortable()->hideFromIndex(),
            
            Boolean::make('Can be used once per email')->default(false)->sortable()->hideFromIndex(),
            
            Date::make('Expiry Date')->nullable(),
            
            Boolean::make('Active')->default(true)->sortable()->hideFromIndex(),

            HasMany::make('IncludePromotionalVoucherCategory', 'includePromotionalVoucherCategory'),
            // HasMany::make('IncludePromotionalVoucherCharacteristic', 'includePromotionalVoucherCharacteristic'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
