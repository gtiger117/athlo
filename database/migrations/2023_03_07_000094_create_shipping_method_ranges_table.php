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
        Schema::create('shipping_method_ranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('from');
            $table->decimal('to')->nullable();
            $table->decimal('amount');
            $table->boolean('per_order')->default(1);
            $table->unsignedBigInteger('shipping_method_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_method_ranges');
    }
};
