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
        Schema::create('shipping_method_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table
                ->string('ext_code')
                ->nullable()
                ->unique();
            $table->json('name');
            $table->string('image')->nullable();
            $table->string('delivery_type')->nullable();
            $table->boolean('active');
            $table->integer('sort_order')->default(0);
            $table->jsonb('description')->nullable();
            $table
                ->boolean('include_all_payment_gateways')
                ->default(1)
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_method_types');
    }
};
