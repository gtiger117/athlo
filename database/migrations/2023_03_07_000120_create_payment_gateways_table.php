<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('payment_gateways');
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type');
            $table
                ->string('ext_code')
                ->nullable()
                ->unique();

            $table->timestamps();
        });

        $payment_gateways = [
            ['name' => 'pay on delivery', 'type' => 'visa'],
            ['name' => 'pay with jcc', 'type' => 'visa'],
            ['name' => 'pay with vivawallet', 'type' => 'visa'],
            ['name' => 'pay with skash', 'type' => 'visa'],
        ];
          
        DB::table('payment_gateways')->insert($payment_gateways);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
