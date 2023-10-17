<?php

namespace Gtiger117\Athlo;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Illuminate\Http\Request;
use Laravel\Nova\Dashboards\Main;
use Illuminate\Support\Facades\DB;
// use Athlo\PackageVoucher\PackageVoucher;
// use App\Nova\PackageVoucher;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
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
                    $tempMenu =  MenuItem::resource("App\\Nova\\".$submenu[$k]->name);
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
        parent::boot();
        //$resourceDirectory = base_path('packages/Athlo/PackageVoucher/src/Nova');
        // $resourceDirectory = base_path('app/Nova/Athlo/');
        // Nova::resourcesIn($resourceDirectory);
        // Nova::resources([
        //    PackageVoucher::class,
        // ]);
        
        Nova::mainMenu(function (Request $request) {
            $menus = [];
            array_push($menus,MenuSection::dashboard(Main::class)->icon('chart-bar'));
            $result = DB::table('nova_menu_menu_items')->where('parent_id',null)->orderBy('order', 'asc')->get();
            for($i=0;$i<count($result);$i++){
                if(DB::table('nova_menu_menu_items')->where('parent_id',$result[$i]->id)->exists()){
                    $tempMenu =  MenuSection::make($result[$i]->name, $this->get_menus($result[$i]->id))->collapsable()->collapsedByDefault();
                }else if($result[$i]->value == null || $result[$i]->value == ""){
                    $tempMenu =  MenuItem::resource($result[$i]->name);
                }else{
                    $tempMenu =  MenuItem::link($result[$i]->name, $result[$i]->value);
                }
                array_push($menus,$tempMenu);
            }
            return $menus;
            
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        
        return [
            \Outl1ne\MenuBuilder\MenuBuilder::make(),
            // new \Outl1ne\NovaSettings\NovaSettings
            // (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
            //     return $request->user()->isSuperAdmin();
            // }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        
    }
}
