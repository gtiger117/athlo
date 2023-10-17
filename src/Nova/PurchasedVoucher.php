<?php

namespace Gtiger117\Athlo\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class PurchasedVoucher extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\PurchasedVoucher>
     */
    public static $model = \Gtiger117\Athlo\Models\PurchasedVoucher::class;

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

            Text::make('Voucher Code')
                ->creationRules(
                    'nullable',
                    'unique:purchased_vouchers,voucher_code',
                    'max:255',
                    'string'
                )
                ->updateRules(
                    'nullable',
                    'unique:purchased_vouchers,voucher_code,{{resourceId}}',
                    'max:255',
                    'string'
                )
                ->placeholder('Voucher Code')
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            BelongsTo::make('Voucher Order', 'voucherOrder'),

            Number::make('Amount')
                ->rules('required', 'integer')
                ->placeholder('Amount'),

            Boolean::make('Active')
                ->rules('nullable', 'boolean')
                ->placeholder('Active')
                ->default('1'),

            Boolean::make('Is Used')
                ->rules('nullable', 'boolean')
                ->placeholder('Is Used')
                ->default(false),

            Date::make('Used Date')
                ->rules('nullable', 'max:255')
                ->dependsOn(
                    ['is_used'],
                    function (Date $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->is_used == 1) {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Used Date'),

            Number::make('Order Id')
                ->rules('nullable', 'max:255')
                ->dependsOn(
                    ['is_used'],
                    function (Number $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->is_used == 1) {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Order Id'),
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
