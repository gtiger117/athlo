<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;
// use VIPSoft\Unzip\Unzip;

class installPackageController extends Controller
{
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

    // function copyMigrate($request){
    //     $migrate_path = base_path('packages\\'.$request->vender.'\\'.$request->name.'\migrations');
    //     $migrate_to = base_path('database\migrations');
    //     if (is_dir($migrate_path)) {
    //         $files = scandir($migrate_path);
    //         foreach ($files as $file) {
    //             if ($file != '.' && $file != '..') {
    //                 $from = $migrate_path."\\".$file;
    //                 $to = $migrate_to."\\".$file;
    //                 copy($from,$to);
    //             }
    //         }
    //     }
    // }

    // function connectApi($request){
    //     $newLine = "require base_path('packages\\".$request->vender."\\".$request->name."\\routes\\api.php');";
    //     $filePath = base_path('routes/api.php');
    //     $currentContent = file_get_contents($filePath);
    //     if(!strpos($currentContent, $newLine)){
    //         $newContent = $currentContent . "\n" . $newLine;
    //         file_put_contents($filePath, $newContent);
    //     }
    // }

    // function selectFolder($path,$request) {
    //     // if(strpos($path,"routes")){
    //     //     $this->connectApi($request);
    //     // }
    //     // if(strpos($path,"migrations")){
    //     //     $this->copyMigrate($request);
    //     // }
    //     if (is_dir($path)) {
    //         $files = scandir($path);
    //         foreach ($files as $file) {
    //             if ($file != '.' && $file != '..') {
    //                 $file = "$path/$file";
    //                 if (is_dir($file)) {
    //                      $this->selectFolder($file,$request);
    //                 } else {
    //                     // unlink($file); // Delete file
    //                 }
    //             }
    //         }
    //     }
    // }
    public function index(Request $request)
    {
        return view('uploadFile');
    }
    public function install(Request $request)
    {
        if ($request->has('file')) {
            // delete folder
            $directory = base_path('\packages')."\\".$request->vender;
            $this->deleteFolder($directory);

            //extract zip
            $zipFileContent = $request->file;
            $tempZipFilePath = tempnam(sys_get_temp_dir(), 'zip');
            $zip_Array  = explode(";base64,", $zipFileContent);
            $zip_contents = base64_decode($zip_Array[1]);
            file_put_contents($tempZipFilePath, $zip_contents);
            $extractTo = base_path('\packages');
            $zip = new ZipArchive();
            if ($zip->open($tempZipFilePath) === true) {
                $zip->extractTo($extractTo);
                $zip->close();
                // $directory = base_path('\packages')."\\".$request->vender;
                // $this->selectFolder($directory,$request);
                echo 'ZIP file extracted successfully.';
            } else {
                echo 'Failed to open the ZIP file.';
            }

            //composer json
            $composerJsonPath = base_path('composer.json');
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            if (!isset($composerData['autoload']['psr-4'])) {
                $composerData['autoload']['psr-4'] = [];
            }
            $composerData['autoload']['psr-4'][$request->vender.'\\'.$request->name.'\\'] = 'packages/'.$request->vender.'/'.$request->name.'/src/';
            file_put_contents($composerJsonPath, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // config/app.php
            // $appConfig = file_get_contents(config_path('app.php'));
            // $providers = config('app.providers');
            // $newProvider = $request->vender."\\".$request->name."\\".$request->name."Provider";
            // if(!array_search($newProvider,$providers)){      
            //     $matches = $providers[count($providers)-1]."::class";
            //     $newProviders = $providers[count($providers)-1]."::class".",\n      ".$newProvider."::class";
            //     $appConfig = str_replace($matches, $newProviders, $appConfig);
            //     file_put_contents(config_path('app.php'), $appConfig);
            // }

            //run composer dump-autoload
            $output = null;
            $return_var = null;
            $command = 'composer dump-autoload';
            chdir('..');
            exec($command, $output, $return_var);

            //run php artisan migrate:
            $migrate_path = base_path('packages\\'.$request->vender.'\\'.$request->name.'\migrations');
            $migrate_to = base_path('database\migrations');
            if (is_dir($migrate_path)) {
                $files = scandir($migrate_path);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        $command = 'php artisan migrate:refresh --path=/database/migrations/'.$file;
                        exec($command, $output, $return_var);
                    }
                }
            }

            if(!DB::table('nova_menu_menu_items')->where('name',$request->vender)->exists()){
                $menu1 = DB::table('nova_menu_menu_items')->insert(['name'=>$request->vender,
                                                                'locale'=>"en",
                                                                'target'=>"_self",
                                                                'order'=>1,
                                                                'enabled'=>1,
                                                                'nestable'=>1
                                                                ]);
            }
            $menu1 = DB::table('nova_menu_menu_items')->where('name',$request->vender)->first();
            $tempID = $menu1->id;
            $temp = $request->vender.'\\'.$request->name.'\\'.$request->name;
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
    }
}
