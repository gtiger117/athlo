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
        Schema::create('include_pickup_group_paymethod', function (
            Blueprint $table
        ) {
            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('pickup_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('include_pickup_group_paymethod');
    }
};
