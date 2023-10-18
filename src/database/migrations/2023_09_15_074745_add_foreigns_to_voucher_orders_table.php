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
        Schema::table('voucher_orders', function (Blueprint $table) {
            $table
                ->foreign('email_template_id')
                ->references('id')
                ->on('voucher_email_templates')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');

            $table
                ->foreign('gift_vouchers_id')
                ->references('id')
                ->on('gift_vouchers')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_orders', function (Blueprint $table) {
            $table->dropForeign(['email_template_id']);
            $table->dropForeign(['gift_vouchers_id']);
        });
    }
};
