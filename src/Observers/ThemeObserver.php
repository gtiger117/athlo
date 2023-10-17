<?php

namespace App\Observers;

use App\Models\Theme;
use App\Http\Controllers\SendEmailController;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Config;

class ThemeObserver
{
    /**
     * Handle the Theme "created" event.
     *
     * @param  \App\Models\Theme  $Theme
     * @return void
     */
    public function created(Theme $Theme)
    {
        if($Theme->active){
            Theme::where("id","<>",$Theme->id)->update(array("active"=>0));
            $path = base_path('.env');
            $test = file_get_contents($path);
            if(strpos($test,"Active_Theme")>0){
                if (file_exists($path)) {
                    file_put_contents($path, str_replace('Active_Theme='.env('Active_Theme'), 'Active_Theme='.$Theme->name, $test));
                }
            }else{
                $test .= "\n\r".'Active_Theme=';
                file_put_contents($path, $test);
            }
        }
        $zip = new ZipArchive;
        $res = $zip->open('storage/'.$Theme->zip);
        if ($res === true) {
            if(!file_exists('../public/theme')){
                mkdir('../public/theme', 0777, true);
            }
            $folder = '../public/theme/'.$Theme->name."/";
            if(!file_exists($folder)){
                mkdir($folder, 0777, true);
            }
            $zip->extractTo($folder);
            $zip->close();
        }
    }


    /**
     * Handle the Theme "updated" event.
     *
     * @param  \App\Models\Theme  $Theme
     * @return void
     */
    public function updated(Theme $Theme)
    {
        if($Theme->active){
            Theme::where("id","<>",$Theme->id)->update(array("active"=>0));
            $path = base_path('.env');
            $test = file_get_contents($path);            
            if (file_exists($path)) {
                file_put_contents($path, str_replace('Active_Theme='.Config::get('pagebuilder.theme.active_theme'), 'Active_Theme='.$Theme->name, $test));
            }
        }
    }

    /**
     * Handle the Theme "deleted" event.
     *
     * @param  \App\Models\Theme  $Theme
     * @return void
     */
    public function deleted(Theme $Theme)
    {
        //
    }

    /**
     * Handle the Theme "restored" event.
     *
     * @param  \App\Models\Theme  $Theme
     * @return void
     */
    public function restored(Theme $Theme)
    {
        //
    }

    /**
     * Handle the Theme "force deleted" event.
     *
     * @param  \App\Models\Theme  $Theme
     * @return void
     */
    public function forceDeleted(Theme $Theme)
    {
        //
    }
}
