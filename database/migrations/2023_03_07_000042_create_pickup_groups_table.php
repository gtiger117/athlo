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
        Schema::create('pickup_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table
                ->string('ext_code')
                ->nullable()
                ->unique();
            $table->string('image')->nullable();
            $table->jsonb('description')->nullable();            
            $table->string('provider')->nullable();
            $table->boolean('active')->default(1);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_groups');
    }
};
