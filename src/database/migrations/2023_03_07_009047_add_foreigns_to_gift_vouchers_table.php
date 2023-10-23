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
        Schema::table('gift_vouchers', function (Blueprint $table) {
            $table
                ->foreign('tax_id')
                ->references('id')
                ->on('taxes')
                ->onUpdate('CASCADE')
                ->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gift_vouchers', function (Blueprint $table) {
            $table->dropForeign(['voucheremail_template_id']);
            $table->dropForeign(['tax_id']);
        });
    }
};
