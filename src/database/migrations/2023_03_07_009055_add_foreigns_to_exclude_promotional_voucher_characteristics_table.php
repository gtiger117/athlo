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
        Schema::table('exclude_promotional_voucher_characteristics', function (Blueprint $table) {
            $table
                ->foreign('promotional_voucher_id', 'falias02')
                ->references('id')
                ->on('promotional_vouchers')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exclude_promotional_voucher_characteristics', function (Blueprint $table) {
            $table->dropForeign(['pickup_group_id']);
            $table->dropForeign(['payment_method_id']);
        });
    }
};
