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
        Schema::create('exclude_pickup_paymethod', function (
            Blueprint $table
        ) {
            $table->unsignedBigInteger('pickup_id');
            $table->unsignedBigInteger('payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exclude_pickup_paymethod');
    }
};
