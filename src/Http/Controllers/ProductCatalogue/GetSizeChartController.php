<?php

namespace Gtiger117\Athlo\Http\Controllers\ProductCatalogue;

use App\Helpers\Helper;
use Gtiger117\Athlo\Http\Controllers\Controller;
use App\Services\GetSubcategoriesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GetSizeChartController extends Controller
{
    public function get_size_charts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required|exists:tbchars_values,CLMCHARVALUEID',
            'categories' => 'required|array',
            'categories.*' => 'exists:tbpc_categories,CLMCATEGORY_ID',       
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $customization_sizecharts_array = DB::table('customization_sizecharts')
                        ->orderBy('sequence', 'asc')
                        ->get();
        $sizechart_array = [];
        foreach($customization_sizecharts_array as $row){
            $postData = [
                'categoryarray' => explode(',',$row->categories), 
            ];
            $subcategories_array = Helper::get_sub_categories($postData);

            $brands_array = [];
            if($row->brands != ''){
                $brands_array = explode(',', $row->brands);
            }
            $sizechart_array[] = array('cats' => $subcategories_array, 'brands' => $brands_array, 'image' => env('APP_IMG_URL') . '/product_catalog/sizecharts/' . $row->image);
        }

        $sizechart_image_location = '';
        
        foreach($sizechart_array as $sizechart){
            foreach($request->categories as $category_id){
                if(in_array($category_id, $sizechart['cats']) && in_array($request->brand_id, $sizechart['brands'])){
                    $sizechart_image_location = $sizechart['image'];
                    return $sizechart_image_location;
                }
            }
        } 
        return $sizechart_image_location;
    }
}
