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
        Schema::create('exclude_promotional_voucher_characteristics', function (Blueprint $table) {
            $table->unsignedBigInteger('promotional_voucher_id')->nullable();
            $table->unsignedBigInteger('characteristic_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exclude_promotional_voucher_characteristics');
    }
};
