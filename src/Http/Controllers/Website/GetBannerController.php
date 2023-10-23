<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetBannerController extends Controller
{
    public function get_banner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'type' => 'required|in:pages,banners',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        switch($request->type){
            case 'banners':
                $location = env('APP_IMG_URL') . '/downloads/uploads/banners/'.$request->id.'/slideshow';
                $column_name = 'CLMGALLERY_IMG_BANNER_ID';
                break;
            default:
                $location = env('APP_IMG_URL') . '/product_catalog/pages/'.$request->id;
                $column_name = 'CLMGALLERY_IMG_PAGE_ID';
                break;
        }

        $banner_array = DB::table('tbcms_galleries')
                    ->where('CLMGALLERY_IMG_FILE_TYPE', 'image')
                    ->where('CLMGALLERY_IMG_ACTIVE', 1)
                    ->where($column_name, $request->id)
                    ->select('*')
                    ->orderBy('CLMGALLERY_IMG_PRIORITY', 'desc')
                    ->get();
        $slideshow_array = [];
        foreach($banner_array as $banner){
            $slideshow_array[] = array(
                'title' => $banner->CLMGALLERY_IMG_TITLE, 
                'description' => $banner->CLMGALLERY_IMG_DESCRIPTION, 
                'link' => $banner->CLMGALLERY_IMG_LINK, 
                'button_text' => $banner->CLMGALLERY_IMG_BUTTON_TEXT, 
                'image' => $location.'/pics/'.$banner->CLMGALLERY_IMG_NAME, 
                'thumb_image' => $location.'/pics/t_'.$banner->CLMGALLERY_IMG_NAME, 
                'mobile_image' => $location.'/pics/m_'.$banner->CLMGALLERY_IMG_NAME, 
            );
        }
        $banner = [];
        $banner['slideshow'] = $slideshow_array;
        return response()->json($banner);
    }
}
