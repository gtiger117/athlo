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
        Schema::create('promotional_vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->float('amount')->nullable();
            $table->float('percentage')->nullable();
            $table->float('minimum_order_amount')->default(0);
            $table->integer('number_of_redemptions')->default(0);
            $table->string('image')->nullable();
            $table->text('description');
            $table->boolean('can_be_used_on_shipping')->default(1);
            $table->boolean('can_be_used_on_products')->default(1);
            $table->boolean('cannot_be_used_when_product_has_discount')->default(1);
            $table->boolean('can_be_used_once_per_email')->default(1);
            $table->boolean('active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotional_vouchers');
    }
};
