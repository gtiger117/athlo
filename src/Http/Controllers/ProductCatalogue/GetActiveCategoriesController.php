<?php

namespace Gtiger117\Athlo\Http\Controllers\ProductCatalogue;

use Gtiger117\Athlo\Http\Controllers\Controller;
use Gtiger117\Athlo\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetActiveCategoriesController extends Controller
{
    public function get_categories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:tbpc_categories,CLMCATEGORY_ID',
            'parent' => 'nullable|array',
            'parent.*' => 'integer',
            'sel_categories' => 'nullable|array',
            'sel_categories.*' => 'exists:tbpc_categories,CLMCATEGORY_ID',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Category::select('tbpc_categories.CLMCATEGORY_ID as id',
                                    'tbpc_categories.CLMCATEGORY_ML_NAME as name', 
                                    'tbpc_categories.CLMCATEGORY_MAIN_PICTURE as image', 
                                    'tbpc_categories.CLMCATEGORY_ISPAGE as is_page', 
                                    'tbpc_categories.CLMCATEGORY_SEONAME as seo_name', 
                                    'tbpc_categories.CLMCATEGORY_SEOTITLE as seo_title', 
                                    'tbpc_categories.CLMCATEGORY_ML_SEODESCRIPTION as seo_description', 
                                    'tbpc_categories.CLMMASTER_CATEGORY_ID as parent_id', 
                                    'tbpc_categories.CLMCATEGORY_REFPAGEID as ref_page');

        if ($request->has('id') && $request->id != '') {
            $query = $query->where('CLMCATEGORY_ID', $request->id);
        }
        if ($request->has('visible') && $request->visible != '') {
            $query = $query->where('CLMCATEGORY_VISIBLE', $request->visible);
        }
        if ($request->has('is_page') && $request->is_page != '') {
            $query = $query->where('CLMCATEGORY_ISPAGE', $request->is_page);
        }
        if ($request->has('parent') && count($request->parent) > 0) {
            $query = $query->whereIn('CLMCATEGORY_ID', function ($subquery) use ($request) {
                $subquery->select('CLMCATEGORY_ID')
                    ->from('tbpc_categories')
                    ->whereIn('CLMMASTER_CATEGORY_ID', $request->parent);
            }); 
        }
        else{
            $query = $query->whereIn('CLMCATEGORY_ID', function ($subquery) use ($request) {
                $subquery->select('CLMCATEGORY_ID')
                    ->from('tbpc_categories')
                    ->where('CLMMASTER_CATEGORY_ID', 0);
            }); 
        }

        $categories_array = [];
        if ($request->has('sel_categories') && count($request->sel_categories) > 0) {
            $categories_array = $request->sel_categories;
        }
        // print_r($categories_array);

        $query = $query
                ->orderBy('tbpc_categories.CLMCATEGORY_SORTING_NUMBER', 'asc')
                ->orderBy('tbpc_categories.CLMCATEGORY_ML_NAME', 'desc')
                ->get();

        foreach ($query as $key=>$row) {
            $query[$key]->name = strip_tags(html_entity_decode($row->name));
            if($row->seo_name == ''){
                $query[$key]->seo_name = $row->name;
            }
            if($row->seo_title == ''){
                $query[$key]->seo_title = $row->name;
            }
            if($row->image != ''){
                $query[$key]->image = env('APP_IMG_URL').'/product_catalog/categories/'.$row->id.'/'.$row->image;
            }
            $query[$key]->link = '/category/'.$row->id.'/'.urlencode(strtolower($row->name));
            if($row->is_page == '1' && is_numeric($row->ref_page)){
                $page_array = DB::table('tbcms_pages')
                ->where('CLMPAGE_ID', $row->ref_page)
                ->select('*')
                ->first();
                if($page_array){
                    if($page_array->CLMMODULE_ID == 'link'){
                        $query[$key]->link = $row->CLMPAGE_LIST_TEMPLATE;
                    }
                    else{
                        $query[$key]->link = '/page/'.$page_array->CLMPAGE_ID.'/'.urlencode(strtolower($page_array->CLMPAGE_ML_NAME));
                    }
                }
            }
            $query[$key]->checked = false;
            if(in_array($row->id, $categories_array)){
                $query[$key]->checked = true;
            }
        }

        $categories = $query;
        return response()->json($categories);
    }
}
