<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        ALTER TABLE `shipping_method_types`
        DROP `include_all_countries`,
        DROP `exclude_countries`,
        DROP `include_all_pickups`,
        DROP `exclude_pickups`;


        ALTER TABLE `shipping_method_types` CHANGE `include_all_payment_methods` `include_all_payment_gateways` TINYINT(1) NULL DEFAULT '0', CHANGE `exclude_payment_methods` `exclude_payment_gateways` TINYINT(1) NULL DEFAULT '0';

        DROP TABLE include_country_shipping_method_type;
        DROP TABLE exclude_country_shipping_method_type;
        DROP TABLE include_pickup_ship_method_type;
        DROP TABLE exclude_pickup_ship_method_type;

        ALTER TABLE `shipping_methods` ADD `all_pickups` TINYINT(1) NULL DEFAULT '1' AFTER `shipping_method_type_id`, ADD `allcountries` TINYINT(1) NULL DEFAULT '1' AFTER `all_pickups`;
        ALTER TABLE `shipping_methods` ADD `all_regions` TINYINT(1) NOT NULL DEFAULT '0' AFTER `all_countries`;
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
