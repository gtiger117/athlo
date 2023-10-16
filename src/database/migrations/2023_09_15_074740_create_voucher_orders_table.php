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
        Schema::create('voucher_orders', function (Blueprint $table) {
            $table->id();

            $table->string('payment_reference_number')->nullable();
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->unsignedBigInteger('gift_vouchers_id')->nullable();
            $table->string('hash')->nullable()->unique();
            $table->string('type')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('return_order_id')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->longText('message')->nullable();
            $table->decimal('amount')->default(0);
            $table->integer('quantity')->default(1);
            $table->string('source')->nullable();
            $table->string('status')->default('initiated');
            $table->date('expiry_date')->nullable();
            $table->boolean('active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_orders');
    }
};
