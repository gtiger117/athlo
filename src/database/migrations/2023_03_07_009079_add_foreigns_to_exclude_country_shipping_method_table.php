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
        Schema::table('include_pickup_group_paymethod', function (Blueprint $table) {
            $table
                ->foreign('product_group_id')
                ->references('id')
                ->on('product_groups')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('include_pickup_group_paymethod', function (Blueprint $table) {
            $table->dropForeign(['product_group_id']);
            $table->dropForeign(['payment_method_id']);
        });
    }
};
