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
        Schema::table('region_payment_method', function (Blueprint $table) {
            $table
                ->foreign('region_id')
                ->references('id')
                ->on('regions')
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
        Schema::table('region_payment_method', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['payment_method_id']);
        });
    }
};
