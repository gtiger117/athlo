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
        Schema::table('payment_ship_method_type', function (Blueprint $table) {
            $table
                ->foreign('payment_method_type_id')
                ->references('id')
                ->on('payment_method_types')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('shipping_method_type_id')
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
        Schema::table('payment_ship_method_type', function (Blueprint $table) {
            $table->dropForeign(['payment_method_type_id']);
            $table->dropForeign(['shipping_method_type_id']);
        });
    }
};
