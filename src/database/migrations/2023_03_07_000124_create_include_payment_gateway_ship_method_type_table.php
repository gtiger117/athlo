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
        Schema::create('include_payment_gateway_ship_method_type', function (
            Blueprint $table
        ) {
            $table->unsignedBigInteger('payment_gateway_id');
            $table->unsignedBigInteger('shipping_method_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('include_payment_gateway_ship_method_type');
    }
};
