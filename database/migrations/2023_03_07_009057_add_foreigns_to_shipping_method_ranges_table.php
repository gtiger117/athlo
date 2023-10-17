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
        Schema::table('shipping_method_ranges', function (Blueprint $table) {
            $table
                ->foreign('shipping_method_id')
                ->references('id')
                ->on('shipping_methods')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_method_ranges', function (Blueprint $table) {
            $table->dropForeign(['shipping_method_id']);
        });
    }
};
