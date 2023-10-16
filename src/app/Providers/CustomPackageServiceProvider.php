<?php

        // app/Providers/CustomServiceProvider.php

        namespace App\Providers;

        use Illuminate\Support\ServiceProvider;
        use Illuminate\Support\Facades\DB;

        class CustomPackageServiceProvider extends ServiceProvider
        {
            /**
             * Register services.
             *
             * @return void
             */
            public function register()
            {
                $result = DB::table('nova_menu_menu_items')->where('parent_id',"!=",null)->get();
                for($i=0;$i<count($result);$i++){
                    if(strpos($result[$i]->name,"\\")){
                        $temp = explode("\\",$result[$i]->name);
                        $classPath = "\\".$temp[0]."\\".$temp[1]."\\PackageProvider";
                        if (class_exists($classPath)) {
                            $this->app->register($classPath);
                        } else {
                            throw new \Exception("Class $classPath does not exist.");
                        }
                    }
                }
            }

            /**
             * Bootstrap services.
             *
             * @return void
             */
            public function boot()
            {
                
            }
        }

?>
