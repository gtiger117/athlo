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
        Schema::table('include_country_shipmeth_type', function (
            Blueprint $table
        ) {
            $table
                ->foreign('country_id')
                ->references('id')
                ->on('countries')
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
        Schema::table('include_country_shipmeth_type', function (
            Blueprint $table
        ) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['shipping_method_type_id']);
        });
    }
};
