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
        Schema::create('purchased_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_order_id');
            $table->string('voucher_code')->nullable()->unique();
            $table->decimal('amount')->default(0);
            $table->boolean('active')->default(1);
            $table->boolean('is_used')->default(0);
            $table->date('used_date')->nullable();
            $table->integer('order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchased_vouchers');
    }
};
