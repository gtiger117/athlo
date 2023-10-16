<?php

namespace App\Observers;

use App\Models\Package;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class PackageObserver
{
    /**
     * Handle the Package "created" event.
     */
    function deleteFolder($path) {
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $file = "$path/$file";
                    if (is_dir($file)) {
                        $this->deleteFolder($file); // Recursively delete subfolder
                    } else {
                        unlink($file); // Delete file
                    }
                }
            }
            rmdir($path); // Delete empty folder
        }
    }
    
    public function created(Package $package): void
    {
        $package_count = Package::where('vendor', $package->vendor)->where('name', $package->name)->where('id', '!=',$package->id)->get();
        for($i=0; $i< count($package_count); $i++){
            $temp = Package::where('id',$package_count[0]->id)->delete();
            if (file_exists('../packageszip/'.$package_count[0]->file)) {
                unlink('../packageszip/'.$package_count[0]->file);
            }
            if (is_dir('../custom-packages/'.$package_count[0]->vendor.'/'.$package_count[0]->name)) {
                $this->deleteFolder('../custom-packages/'.$package_count[0]->vendor.'/'.$package_count[0]->name);
            }
        }

        $zip = new ZipArchive;
        $res = $zip->open('storage/'.$package->file);
        if ($res === true) {
            if(!file_exists('../custom-packages/'.$package->vendor)){
                mkdir('../custom-packages/'.$package->vendor, 0777, true);
            }
            $folder = '../custom-packages/'.$package->vendor.'/'.$package->name."/";
            if(!file_exists($folder)){
                mkdir($folder, 0777, true);
            }
            $zip->extractTo($folder);
            $zip->close();
            $zipFrom = 'storage/'.$package->file;
            $zipTo = '../packageszip/'.$package->file;
            copy($zipFrom,$zipTo);
        }

        //composer json
        $composerJsonPath = base_path('composer.json');
        $composerData = json_decode(file_get_contents($composerJsonPath), true);
        if (!isset($composerData['autoload']['psr-4'])) {
            $composerData['autoload']['psr-4'] = [];
        }
        $composerData['autoload']['psr-4'][$package->vendor.'\\'.$package->name.'\\'] = 'custom-packages/'.$package->vendor.'/'.$package->name.'/src/';
        file_put_contents($composerJsonPath, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

         //run composer dump-autoload
         $output = null;
         $return_var = null;
         $command = 'composer dump-autoload';
         chdir('..');
         exec($command, $output, $return_var);

         //run php artisan migrate:
         $migrate_path = base_path('custom-packages\\'.$package->vendor.'\\'.$package->name.'\migrations');
         $migrate_to = base_path('database\migrations');
         if (is_dir($migrate_path)) {
             $files = scandir($migrate_path);
             foreach ($files as $file) {
                 if ($file != '.' && $file != '..') {
                     $command = 'php artisan migrate:refresh --path=//custom-packages//'.$package->vendor.'//'.$package->name.'//migrations//'.$file;
                     exec($command, $output, $return_var);
                 }
             }
         }
         if(!DB::table('nova_menu_menu_items')->where('name',$package->vendor)->exists()){
            $menu1 = DB::table('nova_menu_menu_items')->insert(['name'=>$package->vendor,
                                                            'locale'=>"en",
                                                            'target'=>"_self",
                                                            'order'=>1,
                                                            'enabled'=>1,
                                                            'nestable'=>1
                                                            ]);
        }
        $menu1 = DB::table('nova_menu_menu_items')->where('name',$package->vendor)->first();
        $tempID = $menu1->id;
        $temp = $package->vendor.'\\'.$package->name.'\\'.$package->name;
        if(!DB::table('nova_menu_menu_items')->where('name',$temp)->exists()){
            
            $menu2 = DB::table('nova_menu_menu_items')->insert(['name'=>$temp,
                                                            'locale'=>"en",
                                                            'target'=>"_self",
                                                            'order'=>1,
                                                            'enabled'=>1,
                                                            'nestable'=>1,
                                                            'parent_id'=>$tempID
                                                            ]);
        }
    }

    /**
     * Handle the Package "updated" event.
     */
    public function updated(Package $package): void
    {
        if (file_exists('../packageszip/'.$package->file)) {
            unlink('../packageszip/'.$package->file);
        }
        if (is_dir('../custom-packages/'.$package->vendor.'/'.$package->name)) {
            $this->deleteFolder('../custom-packages/'.$package->vendor.'/'.$package->name);
        }
        $zip = new ZipArchive;
        $res = $zip->open('storage/'.$package->file);
        if ($res === true) {
            if(!file_exists('../custom-packages/'.$package->vendor)){
                mkdir('../custom-packages/'.$package->vendor, 0777, true);
            }
            $folder = '../custom-packages/'.$package->vendor.'/'.$package->name."/";
            if(!file_exists($folder)){
                mkdir($folder, 0777, true);
            }
            $zip->extractTo($folder);
            $zip->close();
            $zipFrom = 'storage/'.$package->file;
            $zipTo = '../packageszip/'.$package->file;
            copy($zipFrom,$zipTo);
            // $this->selectFolder($folder,$package);
        }
    }

    /**
     * Handle the Package "deleted" event.
     */
    public function deleted(Package $package): void
    {
        if (file_exists('../packageszip/'.$package->file)) {
            unlink('../packageszip/'.$package->file);
        }
        if (is_dir('../custom-packages/'.$package->vendor.'/'.$package->name)) {
            $this->deleteFolder('../custom-packages/'.$package->vendor.'/'.$package->name);
        }

        //composer json
        $composerJsonPath = base_path('composer.json');
        $composerData = json_decode(file_get_contents($composerJsonPath), true);
        if (isset($composerData['autoload']['psr-4'][$package->vendor.'\\'.$package->name.'\\'])) {
            unset($composerData['autoload']['psr-4'][$package->vendor.'\\'.$package->name.'\\']);
        }
        file_put_contents($composerJsonPath, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        
        $temp = $package->vendor.'\\'.$package->name.'\\'.$package->name;
        if(DB::table('nova_menu_menu_items')->where('name',$temp)->exists()){
            $menu2 = DB::table('nova_menu_menu_items')->where('name',$temp)->delete();
        }
        if(DB::table('nova_menu_menu_items')->where('name',$package->vendor)->exists()){
            $menu1 = DB::table('nova_menu_menu_items')->where('name',$package->vendor)->first();
            $tempID = $menu1->id;
            $menu1 = DB::table('nova_menu_menu_items')->where('parent_id',$tempID)->get();
            if(count($menu1)==0){
                $menu1 = DB::table('nova_menu_menu_items')->where('id',$tempID)->delete();
            }
        }
    }

    /**
     * Handle the Package "restored" event.
     */
    public function restored(Package $package): void
    {
        //
    }

    /**
     * Handle the Package "force deleted" event.
     */
    public function forceDeleted(Package $package): void
    {
        //
    }
}
