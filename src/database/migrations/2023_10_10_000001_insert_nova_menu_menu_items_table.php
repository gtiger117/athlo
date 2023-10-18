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
        $nova_menu_menu_items =[ 
            ['id'=>1,'menu_id'=>1,'name'=>'Product Catalogue','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>null,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>2,'menu_id'=>1,'name'=>'GiftVoucher','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>1,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>3,'menu_id'=>1,'name'=>'Ordering','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>null,'order'=>2,'enabled'=>'1','nestable'=>'1'],
            ['id'=>4,'menu_id'=>1,'name'=>'VoucherOrder','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>3,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>5,'menu_id'=>1,'name'=>'Settings','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>3,'order'=>2,'enabled'=>'1','nestable'=>'1'],
            ['id'=>6,'menu_id'=>1,'name'=>'PaymentMethodType','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>5,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>7,'menu_id'=>1,'name'=>'PaymentGateway','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>5,'order'=>2,'enabled'=>'1','nestable'=>'1'],
            ['id'=>8,'menu_id'=>1,'name'=>'Marketing','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>null,'order'=>3,'enabled'=>'1','nestable'=>'1'],
            ['id'=>9,'menu_id'=>1,'name'=>'PromotionalVoucher','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>8,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>10,'menu_id'=>1,'name'=>'Shipping','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>null,'order'=>4,'enabled'=>'1','nestable'=>'1'],
            ['id'=>11,'menu_id'=>1,'name'=>'ShippingMethod','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>10,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>12,'menu_id'=>1,'name'=>'ShippingMethodType','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>10,'order'=>2,'enabled'=>'1','nestable'=>'1'],
            ['id'=>13,'menu_id'=>1,'name'=>'Settings','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>10,'order'=>3,'enabled'=>'1','nestable'=>'1'],
            ['id'=>14,'menu_id'=>1,'name'=>'Pickup','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>13,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>15,'menu_id'=>1,'name'=>'PickupGroup','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>13,'order'=>2,'enabled'=>'1','nestable'=>'1'],
            ['id'=>16,'menu_id'=>1,'name'=>'Region','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>13,'order'=>3,'enabled'=>'1','nestable'=>'1'],
            ['id'=>17,'menu_id'=>1,'name'=>'Website','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>null,'order'=>5,'enabled'=>'1','nestable'=>'1'],
            ['id'=>18,'menu_id'=>1,'name'=>'Package','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>22,'order'=>1,'enabled'=>'1','nestable'=>'1'],
            ['id'=>19,'menu_id'=>1,'name'=>'Page','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemStaticURLType','value'=>'/page','target'=>'_self','data'=>null,'parent_id'=>17,'order'=>2,'enabled'=>'1','nestable'=>'1'],
            ['id'=>20,'menu_id'=>1,'name'=>'Banner','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>17,'order'=>3,'enabled'=>'1','nestable'=>'1'],
            ['id'=>21,'menu_id'=>1,'name'=>'Blog','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>17,'order'=>4,'enabled'=>'1','nestable'=>'1'],
            ['id'=>22,'menu_id'=>1,'name'=>'Settings','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>null,'order'=>6,'enabled'=>'1','nestable'=>'1'],
            ['id'=>23,'menu_id'=>1,'name'=>'Tax','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>22,'order'=>3,'enabled'=>'1','nestable'=>'1'],
            ['id'=>24,'menu_id'=>1,'name'=>'VoucherEmailTemplate','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType','value'=>null,'target'=>'_self','data'=>null,'parent_id'=>22,'order'=>4,'enabled'=>'1','nestable'=>'1'],
            ['id'=>25,'menu_id'=>1,'name'=>'Menu','locale'=>'en','class'=>'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemStaticURLType','value'=>'/menus','target'=>'_self','data'=>null,'parent_id'=>22,'order'=>2,'enabled'=>'1','nestable'=>'1']
        ];
        DB::table('nova_menu_menu_items')->insert($nova_menu_menu_items);  
        // $this->insert('pages',$pages);
    }

};
