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
        Schema::create('pickups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table
                ->string('ext_code')
                ->nullable()
                ->unique();
            $table->unsignedBigInteger('pickup_group_id');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->jsonb('description')->nullable();
            $table->string('label')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('district')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->boolean('active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickups');
    }
};
