<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class VoucherOrder extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\PurchasedVoucher>
     */
    public static $model = \App\Models\VoucherOrder::class;

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

            Select::make('Type')
                ->rules('required', 'max:255')
                ->options(['gift_voucher'=>'Gift Voucher', 'order_voucher'=>'Order Voucher'])
                ->placeholder('Type'),

            BelongsTo::make('Gift Voucher', 'giftVouchers')
                        ->nullable()
                        ->dependsOn(
                            ['type'],
                            function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                                $field->readonly(true)->nullable();
                                if ($formData->type == 'gift_voucher') {
                                    $field->readonly(false)->rules(['required']);
                                }
                            }
                        )
                        ->readonly(true),

            BelongsTo::make('Voucher Email Template', 'voucheremailTemplate')
                        ->nullable()
                        ->dependsOn(
                            ['type'],
                            function (BelongsTo $field, NovaRequest $request, FormData $formData) {
                                $field->readonly(true)->nullable();
                                if ($formData->type == 'order_voucher') {
                                    $field->readonly(false)->rules(['required']);
                                }
                            }
                        )
                        ->readonly(true)
                        ->showCreateRelationButton(),

            Number::make('Order')
                ->rules('nullable', 'integer')
                ->dependsOn(
                    ['type'],
                    function (Number $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'order_voucher') {
                            $field->readonly(false)->rules(['required'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Order'),

            Number::make('Amount')
                ->rules('required', 'integer')
                ->placeholder('Amount'),

            Number::make('Quantity')
                ->rules('required', 'integer')
                ->placeholder('Quantity')
                ->default(1),

            Text::make('Payment Reference Number')
                ->rules('nullable', 'max:255')
                ->placeholder('Payment Reference Number'),

            Text::make('Sender Name')
                ->rules('nullable', 'max:255')
                ->dependsOn(
                    ['type'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'gift_voucher') {
                            $field->readonly(false)->rules(['nullable'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Sender Name'),

            Text::make('Sender Email')
                ->rules('nullable', 'email')
                ->dependsOn(
                    ['type'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'gift_voucher') {
                            $field->readonly(false)->rules(['nullable'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Sender Email'),

            Text::make('Sender Phone')
                ->rules('nullable', 'max:255')
                ->dependsOn(
                    ['type'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'gift_voucher') {
                            $field->readonly(false)->rules(['nullable'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Sender Phone'),

            Text::make('Recipient Name')
                ->rules('nullable', 'max:255')
                ->dependsOn(
                    ['type'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'gift_voucher') {
                            $field->readonly(false)->rules(['nullable'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Recipient Name'),

            Text::make('Recipient Email')
                ->rules('nullable', 'email')
                ->dependsOn(
                    ['type'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'gift_voucher') {
                            $field->readonly(false)->rules(['nullable'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Recipient Email'),

            Text::make('Recipient Phone')
                ->rules('nullable', 'max:255')
                ->dependsOn(
                    ['type'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'gift_voucher') {
                            $field->readonly(false)->rules(['nullable'])->show();
                        }
                    }
                )
                ->hide()
                ->placeholder('Recipient Phone'),

            Textarea::make('Message')
                ->rules('nullable')
                ->dependsOn(
                    ['type'],
                    function (Textarea $field, NovaRequest $request, FormData $formData) {
                        $field->readonly(false)->rules(['nullable'])->hide();
                        if ($formData->type == 'gift_voucher') {
                            $field->readonly(false)->rules(['nullable'])->show();
                        }
                    }
                )
                ->placeholder('Message'),

            Select::make('Source')
                ->rules('required', 'max:255')
                ->options(['offline'=>'Offline', 'online'=>'Online'])
                ->placeholder('Source')
                ->default('offline'),

            Select::make('Status')
                ->rules('required', 'max:255')
                ->options(['initiated'=>'Initiated', 'completed'=>'Completed'])
                ->placeholder('Status')
                ->default('completed'),

            Date::make('Expiry Date')
                ->rules('nullable', 'max:255')
                ->placeholder('Expiry Date'),


            Boolean::make('Active')
                ->rules('required', 'boolean')
                ->placeholder('Active')
                ->default('1'),

            HasMany::make('Purchased Voucher', 'purchasedVoucher'),
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
