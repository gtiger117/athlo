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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table
                ->string('ext_code')
                ->nullable()
                ->unique();
            $table->string('name');
            $table->string('delivery_type');
            $table->string('method_type');
            $table->boolean('all_countries')->default(1);
            $table->boolean('exclude_countries')->default(0);
            $table->boolean('all_pickups')->default(1);
            $table->boolean('exclude_pickups')->default(0);
            $table->boolean('active')->default(1);
            $table->boolean('public')->default(1);
            $table->unsignedBigInteger('tax_id');
            $table->decimal('amount')->nullable();
            $table->decimal('amount_with_tax')->nullable();
            $table->decimal('order_amount')->nullable();
            $table->decimal('order_amount_with_tax')->nullable();
            $table->string('own_charges_type')->nullable();
            $table->string('own_charges_range_type')->nullable();
            $table->unsignedBigInteger('shipping_method_type_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
