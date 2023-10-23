<?php

namespace App\Http\Controllers\ProductCatalogue;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CharValue;
use App\Services\GetSubcategoriesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetActiveColorsController extends Controller
{
    public function get_active_colours(Request $request)
    {
        // $this->authorize('view-any', Brand::class);

        $main_char_id = env('COLOR_GROUP_ID');
        
        $validator = Validator::make($request->all(), [
            'categories' => 'nullable|array',
            'categories.*' => 'exists:tbpc_categories,CLMCATEGORY_ID',
            'sub_categories' => 'nullable|array',
            'sub_categories.*' => 'exists:tbpc_categories,CLMCATEGORY_ID',
            'sub_sub_categories' => 'nullable|array',
            'sub_sub_categories.*' => 'exists:tbpc_categories,CLMCATEGORY_ID',            
            'brands' => 'nullable|array',
            'brands.*' => 'exists:tbchars_values,CLMCHARVALUEID',
            'colors' => 'nullable|array',
            'colors.*' => 'exists:tbchars_values,CLMCHARVALUEID',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:tbchars_values,CLMCHARVALUEID',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $perPage = 1000;
        $page = 1;

        $colors_array = [];
        if ($request->has('colors') && count($request->colors) > 0) {
            $colors_array = $request->colors;
        }

        $subcategories_array = [];
        if ($request->has('sub_sub_categories') && count($request->sub_sub_categories) > 0) {
            $postData = [
                'categoryarray' => $request->sub_sub_categories, 
            ];
            $subcategories_array = Helper::get_sub_categories($postData);
        }
        else if ($request->has('sub_categories') && count($request->sub_categories) > 0) {
            $postData = [
                'categoryarray' => $request->sub_categories, 
            ];
            $subcategories_array = Helper::get_sub_categories($postData);
        }
        else if ($request->has('categories') && count($request->categories) > 0) {
            $postData = [
                'categoryarray' => $request->categories, 
            ];
            $subcategories_array = Helper::get_sub_categories($postData);
        }
        else if ($request->has('category_id') && $request->category_id > 0) {
            $postData = [
                'categoryarray' => explode(",", $request->category_id), 
            ];
            $subcategories_array = Helper::get_sub_categories($postData);
        }
        
        $query = CharValue::where('CLMCHAR_ID', $main_char_id);

        $query = $query->whereIn('CLMCHARVALUEID', function ($subquery)  use ($subcategories_array, $request) {
            $subquery->select('CLMSRCH_CHARVAL_ID')
                ->from('tbpc_srch_cats_chars');
            if(count($subcategories_array) > 0){
                $subquery = $subquery->whereIn('CLMSRCH_CATID', $subcategories_array);
            }
        });

        if ($request->has('brands') && count($request->brands) > 0) {
            $query = $query->whereIn('CLMCHARVALUEID', function ($subquery)  use ($request) {
                $subquery->select('EXTCHARVALUEID')
                    ->from('tbchars_values_prods_group_rel')
                    ->join('tbpc_products', 'tbchars_values_prods_group_rel.CLMPRODUCTID', '=', 'tbpc_products.CLMPRODUCT_ID')
                    ->where('CLMPRODUCT_ACTIVE', 1)
                    ->where('CLMPRODUCT_SOLDSEPARETELY', 1)
                    ->whereIn('CLMPRODUCTID', function ($subsubquery)  use ($request) {
                        $subsubquery->select('CLMPRODUCTID')
                            ->from('tbchars_values_prods_group_rel')
                            ->whereIn('EXTCHARVALUEID', $request->brands);
                    });
            });
        }

        if ($request->has('sizes') && count($request->brands) > 0) {
            $query = $query->whereIn('CLMCHARVALUEID', function ($subquery)  use ($request) {
                $subquery->select('EXTCHARVALUEID')
                    ->from('tbchars_values_prods_group_rel')
                    ->join('tbpc_products', 'tbchars_values_prods_group_rel.CLMPRODUCTID', '=', 'tbpc_products.CLMPRODUCT_ID')
                    ->where('CLMPRODUCT_ACTIVE', 1)
                    ->where('CLMPRODUCT_SOLDSEPARETELY', 1)
                    ->whereIn('CLMPRODUCTID', function ($subsubquery)  use ($request) {
                        $subsubquery->select('CLMPRODUCTID')
                            ->from('tbchars_values_prods_group_rel')
                            ->whereIn('EXTCHARVALUEID', $request->sizes);
                    });
            });
        }

        

        


        $query = $query->select('CLMCHARVALUEID as id', 'CLMCHARVALUE_ML_NAME as name', 'CLMCHARVALUE_PICTURE as image')
                ->orderBy('CLMCHARVALUEPRIORITY', 'asc')
                ->orderBy('CLMCHARVALUE_ML_NAME', 'asc')
                ->paginate($perPage, ['/*'], 'page', $page);

        foreach ($query as $key=>$row) {
            $query[$key]->checked = false;
            if(in_array($row->id, $colors_array)){
                $query[$key]->checked = true;
            }
            if($row->image != ''){
                $query[$key]->image = env('APP_IMG_URL').'/product_catalog/characteristics/chars_values/images/'.$row->id.'/'.$row->image;
            }
        }

        $brands = $query;
        
        return response()->json($brands);
    }
}
