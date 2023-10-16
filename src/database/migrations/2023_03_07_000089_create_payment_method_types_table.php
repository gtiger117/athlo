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
        Schema::create('payment_method_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->jsonb('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('payment_gateway_id');
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_types');
    }
};
