<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Nova\Menu\MenuItem;
use Outl1ne\MenuBuilder\Models\Menu;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        DB::table('nova_menu_menus')->where('name', 'Nova Menu')->delete();
        DB::table('nova_menu_menus')->insert(['name' => 'Nova Menu','slug' => 'nova_menu']);

        $menu_item = Menu::where('slug','nova_menu')->first();

        $menu_items = [];
        $menu_items[] = ['id' => 1, 'menu_id' => $menu_item->id,'name' => 'Product Catalogue', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => null, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 2, 'menu_id' => $menu_item->id,'name' => 'GiftVoucher', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 1, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 3, 'menu_id' => $menu_item->id,'name' => 'Ordering', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => null, 'order' => 2, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 4, 'menu_id' => $menu_item->id,'name' => 'VoucherOrder', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 3, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 5, 'menu_id' => $menu_item->id,'name' => 'Settings', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 3, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 6, 'menu_id' => $menu_item->id,'name' => 'PaymentMethodType', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 5, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 7, 'menu_id' => $menu_item->id,'name' => 'PaymentGateway', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 5, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 8, 'menu_id' => $menu_item->id,'name' => 'Marketing', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => null, 'order' => 3, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 9, 'menu_id' => $menu_item->id,'name' => 'PromotionalVoucher', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 8, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 10, 'menu_id' => $menu_item->id,'name' => 'Shipping', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => null, 'order' => 4, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 11, 'menu_id' => $menu_item->id,'name' => 'ShippingMethod', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 10, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 12, 'menu_id' => $menu_item->id,'name' => 'ShippingMethodType', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 10, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 13, 'menu_id' => $menu_item->id,'name' => 'Settings', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 10, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 14, 'menu_id' => $menu_item->id,'name' => 'Pickup', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 13, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 15, 'menu_id' => $menu_item->id,'name' => 'PickupGroup', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 13, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 16, 'menu_id' => $menu_item->id,'name' => 'Region', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 13, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 17, 'menu_id' => $menu_item->id,'name' => 'Website', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => null, 'order' => 5, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 18, 'menu_id' => $menu_item->id,'name' => 'Package', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 17, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 19, 'menu_id' => $menu_item->id,'name' => 'Page', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemStaticURLType', 'value' => '/page', 'target' => '_self', 'data' => null, 'parent_id' => 17, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 20, 'menu_id' => $menu_item->id,'name' => 'Banner', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 17, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 21, 'menu_id' => $menu_item->id,'name' => 'Blog', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 17, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 22, 'menu_id' => $menu_item->id,'name' => 'Settings', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => null, 'order' => 6, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 23, 'menu_id' => $menu_item->id,'name' => 'Tax', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 22, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 24, 'menu_id' => $menu_item->id,'name' => 'VoucherEmailTemplate', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemTextType', 'value' => null, 'target' => '_self', 'data' => null, 'parent_id' => 22, 'order' => 1, 'enabled' => 1, 'nestable' => 1];
        $menu_items[] = ['id' => 25, 'menu_id' => $menu_item->id,'name' => 'Menu', 'locale' => 'en', 'class' => 'Outl1ne\\MenuBuilder\\MenuItemTypes\\MenuItemStaticURLType', 'value' => '/menus', 'target' => '_self', 'data' => null, 'parent_id' => 17, 'order' => 1, 'enabled' => 1, 'nestable' => 1];

        DB::table('nova_menu_menu_items')->insert($menu_items);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
