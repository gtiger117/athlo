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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_method_type_id');
            $table->json('name');
            $table->string('image')->nullable();
            $table->jsonb('description')->nullable();
            $table->decimal('amount')->default(0);
            $table->unsignedBigInteger('tax');
            $table->decimal('amount_with_tax')->default(0);
            $table->integer('sort_order')->default(0);
            $table
                ->boolean('all_countries')
                ->default(1)
                ->nullable();
            $table
                ->boolean('exclude_countries')
                ->default(0)
                ->nullable();
            $table
                ->boolean('all_regions')
                ->default(1)
                ->nullable();
            $table
                ->boolean('exclude_regions')
                ->default(0)
                ->nullable();
            $table
                ->boolean('all_pickups')
                ->default(1)
                ->nullable();
            $table
                ->boolean('exclude_pickups')
                ->default(0)
                ->nullable();
            $table->boolean('active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
