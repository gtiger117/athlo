<?php

namespace Gtiger117\Athlo;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Dashboards\Main;
use Illuminate\Support\Facades\DB;

class Athlo extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    function get_menus($parent){
        $submenu = DB::table('nova_menu_menu_items')
                        // ->join('nova_menu_menus', 'nova_menu_menus.id', '=', 'nova_menu_menu_items.menu_id')
                        // ->where('slug','nova_menu')
                        ->where('parent_id',$parent)->orderBy('order', 'asc')->get();
        $menus = [];
        for($k=0;$k<count($submenu);$k++){
            if(DB::table('nova_menu_menu_items')->where('parent_id',$submenu[$k]->id)->exists()){
                $tempMenu =  MenuGroup::make($submenu[$k]->name, $this->get_menus($submenu[$k]->id))->collapsable()->collapsedByDefault();
            }else if($submenu[$k]->value == null || $submenu[$k]->value == ""){
                if(strpos($submenu[$k]->name,"\\")){
                    $tempMenu =  MenuItem::resource($submenu[$k]->name);
                }else{
                    $tempMenu =  MenuItem::resource('\\Gtiger117\\Athlo\\Nova\\'.$submenu[$k]->name);
                }
                
            }else{
                $tempMenu =  MenuItem::link($submenu[$k]->name, $submenu[$k]->value);
            }
            array_push($menus,$tempMenu);
        }
        return $menus;
    }

    public function boot()
    {
        Nova::resources([
            \Gtiger117\Athlo\Nova\GiftVoucher::class,
            \Gtiger117\Athlo\Nova\VoucherOrder::class,
            \Gtiger117\Athlo\Nova\PaymentMethodType::class,
            \Gtiger117\Athlo\Nova\PaymentGateway::class,
            \Gtiger117\Athlo\Nova\VoucherEmailTemplate::class,
            \Gtiger117\Athlo\Nova\ShippingMethod::class,
            \Gtiger117\Athlo\Nova\ShippingMethodType::class,
            \Gtiger117\Athlo\Nova\PromotionalVoucher::class,
            \Gtiger117\Athlo\Nova\Pickup::class,
            \Gtiger117\Athlo\Nova\PickupGroup::class,
            \Gtiger117\Athlo\Nova\Region::class,
            \Gtiger117\Athlo\Nova\Banner::class,
            \Gtiger117\Athlo\Nova\Blog::class,
            \Gtiger117\Athlo\Nova\Tax::class,
            \Gtiger117\Athlo\Nova\Theme::class,
            \Gtiger117\Athlo\Nova\Package::class,
        ]);
        Nova::script('athlo', __DIR__.'/../dist/js/tool.js');
        Nova::style('athlo', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        $menus = [];
        array_push($menus,MenuSection::dashboard(Main::class)->icon('chart-bar'));
        $result = DB::table('nova_menu_menu_items')->where('parent_id',null)->orderBy('order', 'asc')->get();
        for($i=0;$i<count($result);$i++){
            if(DB::table('nova_menu_menu_items')->where('parent_id',$result[$i]->id)->exists()){
                $tempMenu =  MenuSection::make($result[$i]->name, $this->get_menus($result[$i]->id))->collapsable()->collapsedByDefault();
            }else if($result[$i]->value == null || $result[$i]->value == ""){
                $tempMenu =  MenuItem::resource('\\Gtiger117\\Athlo\\Nova\\'.$result[$i]->name);
            }else{
                $tempMenu =  MenuItem::link($result[$i]->name, $result[$i]->value);
            }
            array_push($menus,$tempMenu);
        }
        return $menus;
        // $menus = [
        //     MenuSection::make('Product Catalogue', [
        //         MenuItem::resource(\Gtiger117\Athlo\Nova\GiftVoucher::class),
        //         MenuGroup::make('Settings', [
        //             // MenuItem::resource(PaymentMethod::class),
        //             // MenuItem::resource(PaymentMethodType::class),
        //             // MenuItem::resource(PaymentGateway::class),
        //         ])->collapsable()->collapsedByDefault(),
        //     ])->icon('briefcase')->collapsable()->collapsedByDefault(),

        //     MenuSection::make('Ordering', [
        //         MenuItem::resource(\Gtiger117\Athlo\Nova\VoucherOrder::class),
        //         MenuGroup::make('Settings', [
        //             // MenuItem::resource(PaymentMethod::class),
        //             MenuItem::resource(\Gtiger117\Athlo\Nova\PaymentMethodType::class),
        //             MenuItem::resource(\Gtiger117\Athlo\Nova\PaymentGateway::class),
        //             MenuItem::resource(\Gtiger117\Athlo\Nova\VoucherEmailTemplate::class),
        //         ])->collapsable()->collapsedByDefault(),
        //     ])->icon('briefcase')->collapsable()->collapsedByDefault(),

        //     MenuSection::make('Marketing', [
        //         MenuItem::resource(\Gtiger117\Athlo\Nova\PromotionalVoucher::class),
        //         // MenuItem::resource(ShippingMethodType::class),
        //         MenuGroup::make('Settings', [
        //             // MenuItem::resource(Pickup::class),
        //             // MenuItem::resource(PickupGroup::class),
        //             // MenuItem::resource(Region::class),
        //         ])->collapsable()->collapsedByDefault(),
        //     ])->icon('briefcase')->collapsable()->collapsedByDefault(),

        //     MenuSection::make('Shipping', [
        //         MenuItem::resource(\Gtiger117\Athlo\Nova\ShippingMethod::class),
        //         MenuItem::resource(\Gtiger117\Athlo\Nova\ShippingMethodType::class),
        //         MenuGroup::make('Settings', [
        //             MenuItem::resource(\Gtiger117\Athlo\Nova\Pickup::class),
        //             MenuItem::resource(\Gtiger117\Athlo\Nova\PickupGroup::class),
        //             MenuItem::resource(\Gtiger117\Athlo\Nova\Region::class),
        //         ])->collapsable()->collapsedByDefault(),
        //     ])->icon('briefcase')->collapsable()->collapsedByDefault(),

        //     MenuSection::make('Website', [
        //         // MenuItem::resource(Page::class),
        //          MenuItem::link('Page', '/page'),  
        //          MenuItem::resource(\Gtiger117\Athlo\Nova\Banner::class),                  
        //          MenuItem::resource(\Gtiger117\Athlo\Nova\Blog::class),                  
        //      ])->icon('trash')->collapsable()->collapsedByDefault(),

        //      MenuSection::make('Settings', [
        //         MenuItem::resource(\Gtiger117\Athlo\Nova\Tax::class),
        //     ])->icon('cog')->collapsable()->collapsedByDefault(),
        // ];
        return $menus;
    }
    
}
