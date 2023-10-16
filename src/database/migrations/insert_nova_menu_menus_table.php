<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $nova_menu_menus =[ 
            ['id'=>1,'name'=>'Nova Menu','slug'=>'nova_menu']
        ];
        DB::table('nova_menu_menus')->insert($nova_menu_menus);  
        // $this->insert('pages',$pages);
    }

};
