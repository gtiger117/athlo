<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('include_payment_gateway_ship_method_type', function (
            Blueprint $table
        ) {
            $table
                ->foreign('payment_gateway_id', 'foreign_alias_0000004')
                ->references('id')
                ->on('payment_gateways')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('shipping_method_type_id', 'foreign_alias_0000005')
                ->references('id')
                ->on('shipping_method_types')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('include_payment_gateway_ship_method_type', function (
            Blueprint $table
        ) {
            $table->dropForeign(['payment_gateway_id']);
            $table->dropForeign(['shipping_method_type_id']);
        });
    }
};
