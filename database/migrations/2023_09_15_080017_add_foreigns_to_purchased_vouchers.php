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
        Schema::table('purchased_vouchers', function (Blueprint $table) {
            $table
                ->foreign('voucher_order_id')
                ->references('id')
                ->on('voucher_orders')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchased_vouchers', function (Blueprint $table) {
            $table->dropForeign(['voucher_order_id']);
        });
    }
};
