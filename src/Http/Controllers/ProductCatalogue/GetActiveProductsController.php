<?php

namespace App\Http\Controllers\ProductCatalogue;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\GetSubcategoriesService;
use Hamcrest\Description;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetActiveProductsController extends Controller
{
    // protected $getsubcategories;

    // public function __construct(GetSubcategoriesService $GetSubcategoriesService)
    // {
    //     $this->getsubcategories = $GetSubcategoriesService;
    // }
    public function get_active_products(Request $request)
    {
        // $this->authorize('view-any', Brand::class);

        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:tbpc_products_groups,CLMPRODGROUP_ID',
            'customer_id' => 'nullable|exists:tbaccounts_user,CLM_ACCOUNT_ID',
            'category_id' => 'nullable|exists:tbpc_categories,CLMCATEGORY_ID',
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
            'exclude_ids' => 'nullable|array',
            'exclude_ids.*' => 'exists:tbpc_products_groups,CLMPRODGROUP_ID',            
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $perPage = 20;
        $page = 1;

        if ($request->has('page') && is_numeric($request->page)) {
            $page = $request->page;
        }
        if ($request->has('per_page') && is_numeric($request->per_page)) {
            $perPage = $request->per_page;
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
        
        $query = Product::where('CLMPRODGROUP_ACTIVE', 1)->where('CLMPRODGROUP_SOLD_SEPARATELY', 1);

        
        
        if ($request->has('id') && $request->id != '') {
            $query = $query->where('CLMPRODGROUP_ID', $request->id);
        }
        if ($request->has('ids') && count($request->ids) > 1) {
            $query = $query->wherein('whereIn', $request->ids);
        }
        if ($request->has('exclude_ids') && count($request->exclude_ids) > 0) {
            $query = $query->wherein('whereNotIn', $request->exclude_ids);
        }
        if ($request->has('ext_code') && $request->ext_code != '') {
            $query = $query->where('CLMPRODGROUP_EXTID', $request->ext_code);
        }
        
        if ($request->has('best_sellers') && $request->best_sellers != ''){
            $product_groups_array = [];
            $results  = DB::select('SELECT t1.CLMPRODGROUP_ID   
                                        FROM tbpc_products_groups t1 
                                        INNER JOIN (SELECT t3.CLMPRODGROUP_ID, Count(*)  
                                                        FROM tbordering_closed_order_items t1 
                                                        INNER JOIN tbpc_products t2 ON t1.CLMCLORDIT_PRID = t2.CLMPRODUCT_ID 
                                                        INNER JOIN tbpc_products_groups t3 ON t2.CLMPRODUCT_GROUPID = t3.CLMPRODGROUP_ID  
                                                        WHERE 
                                                            t3.CLMPRODGROUP_ACTIVE = 1 AND 
                                                            t3.CLMPRODGROUP_SOLD_SEPARATELY = 1 
                                                        GROUP BY t3.CLMPRODGROUP_ID 
                                                        ORDER BY Count(*) DESC 
                                                        LIMIT 40) as t2 ON t1.CLMPRODGROUP_ID = t2.CLMPRODGROUP_ID 
                                        ORDER BY RAND()');
            foreach($results as $row){
                $product_groups_array[] = $row->CLMPRODGROUP_ID;
            }
            if(count($product_groups_array) > 0){
                $query = $query->whereIn('CLMPRODGROUP_ID', $product_groups_array);
            }
        }
        if ($request->has('search_term') && trim($request->search_term) != ''){
            $search_term = '%'.str_replace(" ", "%", $request->search_term).'%';
            $query = $query->where(function ($subquery) use($search_term) {
                $subquery->where('CLMPRODGROUP_ID', 'LIKE', $search_term)
                      ->orWhere('CLMPRODGROUP_ML_NAME', 'LIKE', $search_term)
                      ->orWhere('CLMPRODGROUP_EXTID', 'LIKE', $search_term);
            });
        }
        if ($request->has('new') && $request->new != '') {
            $query = $query->where('CLMPRODGROUP_ISNEW', $request->new);
        }
        if ($request->has('hot') && $request->hot != '') {
            $query = $query->where('CLMPRODGROUP_ISHOT', $request->hot);
        }
        if ($request->has('comingsoon') && $request->comingsoon != '') {
            $query = $query->where('CLMPRODGROUP_COMINGSOON', $request->comingsoon);
        }
        if ($request->has('featured') && $request->featured != '') {
            $query = $query->where('CLMPRODGROUP_FEATURED', $request->featured);
        }
        if ($request->has('sellable') && $request->sellable != '') {
            $query = $query->where('CLMPRODGROUP_ISSELLABLE', $request->sellable);
        }
        if ($request->has('name') && $request->name != '') {
            $query = $query->where('CLMPRODGROUP_ML_NAME', $request->name);
        }
        if (count($subcategories_array) > 0) {
            $query = $query->whereIn('CLMPRODGROUP_ID', function ($subquery)  use ($subcategories_array) {
                $subquery->select('CLMPRCAT_PGROUP')
                    ->from('tbpc_prod_pgroups_cat_rel')
                    ->whereIn('CLMPRCAT_CATEGORY', $subcategories_array);
            });
        }
        if ($request->has('brands') && count($request->brands) > 0) {
            $query = $query->whereIn('CLMPRODGROUP_ID', function ($subquery)  use ($request) {
                $subquery->select('CLMPRCHAR_PGROUP')
                    ->from('tbchars_values_prods_group_rel')
                    ->join('tbpc_products', 'tbpc_products.CLMPRODUCT_GROUPID', '=', 'tbchars_values_prods_group_rel.CLMPRCHAR_PGROUP')
                    ->where('CLMPRODUCT_ACTIVE', 1)
                    ->whereIn('EXTCHARVALUEID', $request->brands);
            });
        }
        if ($request->has('sizes') && count($request->sizes) > 0) {
            $query = $query->whereIn('CLMPRODGROUP_ID', function ($subquery)  use ($request) {
                $subquery->select('CLMPRCHAR_PGROUP')
                    ->from('tbchars_values_prods_group_rel')
                    ->join('tbpc_products', 'tbpc_products.CLMPRODUCT_GROUPID', '=', 'tbchars_values_prods_group_rel.CLMPRCHAR_PGROUP')
                    ->where('CLMPRODUCT_ACTIVE', 1)
                    ->whereIn('EXTCHARVALUEID', $request->sizes);
            });
        }
        if ($request->has('colors') && count($request->colors) > 0) {
            $query = $query->whereIn('CLMPRODGROUP_ID', function ($subquery)  use ($request) {
                $subquery->select('CLMPRCHAR_PGROUP')
                    ->from('tbchars_values_prods_group_rel')
                    ->join('tbpc_products', 'tbpc_products.CLMPRODUCT_GROUPID', '=', 'tbchars_values_prods_group_rel.CLMPRCHAR_PGROUP')
                    ->where('CLMPRODUCT_ACTIVE', 1)
                    ->whereIn('EXTCHARVALUEID', $request->colors);
            });
        }

        
                
        $query = $query->select('CLMPRODGROUP_ID as id', 
                                'CLMPRODGROUP_EXTID as ext_code', 
                                'CLMPRODGROUP_ML_NAME as name', 
                                'CLMPRODGROUP_ML_DESCRIPTION as description', 
                                'CLMPRODGROUP_PICTURE as image', 
                                'CLMPRODGROUP_FEATURED as featured', 
                                DB::raw('CASE 
                                            WHEN CLMPRODGROUP_OFF_PRICE > 0 THEN ROUND(CLMPRODGROUP_OFF_PRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2) 
                                            WHEN CLMPRODGROUP_OFFERPRICE > 0 THEN ROUND(CLMPRODGROUP_OFFERPRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2) 
                                            ELSE ROUND(CLMPRODGROUP_PRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2)
                                        END as pricesold'), 
                                DB::raw('ROUND(100 - (CASE 
                                            WHEN CLMPRODGROUP_OFF_PRICE > 0 AND CLMPRODGROUP_OFF_PRICE < CLMPRODGROUP_PRICE THEN CLMPRODGROUP_OFF_PRICE 
                                            WHEN CLMPRODGROUP_OFFERPRICE IS NULL OR CLMPRODGROUP_OFFERPRICE = 0 THEN CLMPRODGROUP_PRICE 
                                            ELSE CLMPRODGROUP_OFFERPRICE 
                                        END * 100 / CLMPRODGROUP_PRICE)) as offerpercentage'), 
                                DB::raw('CASE 
                                            WHEN CLMPRODGROUP_OFF_PRICE > 0 AND CLMPRODGROUP_OFF_PRICE < CLMPRODGROUP_PRICE THEN 1 
                                            WHEN CLMPRODGROUP_OFFERPRICE > 0 AND CLMPRODGROUP_OFFERPRICE < CLMPRODGROUP_PRICE THEN 1 
                                            ELSE 0 
                                        END as hasdiscount'), 
                                'CLMPRODGROUP_PRICE_WITHTAX as price', 
                                DB::raw('ROUND(CASE 
                                            WHEN CLMPRODGROUP_OFF_PRICE > 0 THEN CLMPRODGROUP_OFF_PRICE  * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100
                                            ELSE CLMPRODGROUP_OFFERPRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100 
                                        END, 2) as offerprice'), 
                                'CLMPRODGROUP_TAX_PERCENTAGE as tax',
                                'CLMPRODGROUP_ACTIVE as active',
                                'CLMPRODGROUP_APPROVED as approved',
                                'CLMPRODGROUP_ISHOT as hot',
                                'CLMPRODGROUP_COMINGSOON as comingsoon',
                                'CLMPRODGROUP_ISNEW as new',
                                'CLMPRODGROUP_ISSELLABLE as sellable',
                                'CLMPRODGROUP_SOLD_SEPARATELY as soldseparetely',
                                'CLMPRODGROUP_OFF_PRICE as offer_price',
                                'CLMPRODGROUP_CRE_DATETIME as create_datetime',
                                'CLMPRODGROUP_LAST_UPD_DATETIME as upd_datetime');
        $order_by = '';
        if ($request->has('order_by') && $request->order_by != '') {
            $order_by = $request->order_by;
        }
        switch($order_by){
            case 'rand':
                $query = $query->orderByRaw('RAND()');
                break;
            case 'price_low_to_high':
                $query = $query->orderBy('pricesold', 'asc');
                break;
            case 'price_high_to_low':
                $query = $query->orderBy('pricesold', 'desc');
                break;
            case 'highest_discounts':
                $query = $query->orderBy('offerpercentage', 'desc');
                $query = $query->orderBy('pricesold', 'asc');
                break;
            case 'name_asc':
                $query = $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query = $query->orderBy('name', 'desc');
                break;
            default:
                $query = $query->orderBy('featured', 'desc');
                $query = $query->orderBy('create_datetime', 'desc');
                break;
        }

        if ($request->has('min_price') && $request->min_price != ''){
            $query = $query->having('pricesold', '>=', $request->min_price);
        } 
        if ($request->has('max_price') && $request->max_price != '') {
            $query = $query->having('pricesold', '<=', $request->max_price);            
        }
        if ($request->has('hasdiscount') && $request->hasdiscount != ''){
            $query = $query->having('hasdiscount', '=', 1);            
        }

        $query = $query->paginate($perPage, ['/*'], 'page', $page);

        foreach ($query as $key=>$row) {
            $images = [];
            $thumb_images = [];
            if($row->image != ''){
                $images[] = env('APP_IMG_URL').'/product_catalog/groups/'.$row->id.'/'.$row->image;
                $thumb_images[] = env('APP_IMG_URL').'/product_catalog/groups/'.$row->id.'/t_'.$row->image;
            }
            $images_array = DB::table('tbcms_galleries')->where('CLMGALLERY_IMG_PRODUCTGROUP_ID', $row->id)->orderBy('CLMGALLERY_IMG_NAME', 'asc')->get();
            foreach($images_array as $image){
                $images[] = env('APP_IMG_URL').'/product_catalog/groups/'.$row->id.'/pics/'.$image->CLMGALLERY_IMG_NAME;
                $thumb_images[] = env('APP_IMG_URL').'/product_catalog/groups/'.$row->id.'/pics/t_'.$image->CLMGALLERY_IMG_NAME;
            }
            
            unset($query[$key]->image);
            $query[$key]->images = $images;
            $query[$key]->thumb_images = $thumb_images;

            $brand_array = DB::table('tbchars_values')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMCHAR_ID', env('BRAND_ID'))
                                ->where('CLMPRCHAR_PGROUP', $row->id)
                                ->select('CLMCHARVALUEID as id', 'CLMCHARVALUE_ML_NAME as name')
                                ->first();
            $query[$key]->brand_id = '';
            $query[$key]->brand_name = '';
            if($brand_array){
                $query[$key]->brand_id = $brand_array->id;
                $query[$key]->brand_name = $brand_array->name;
            }
            $color_array = DB::table('tbchars_values')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMCHAR_ID', env('COLOR_ID'))
                                ->where('CLMPRCHAR_PGROUP', $row->id)
                                ->select('CLMCHARVALUEID as id', 'CLMCHARVALUE_ML_NAME as name')
                                ->first();
            $query[$key]->color_id = '';
            $query[$key]->color_name = '';
            if($color_array){
                $query[$key]->color_id = $color_array->id;
                $query[$key]->color_name = $color_array->name;
            }
            $variants_array = DB::table('tbpc_products')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.CLMPRODUCTID', '=', 'tbpc_products.CLMPRODUCT_ID')
                                ->join('tbchars_values', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMPRODUCT_GROUPID', $row->id)
                                ->where('CLMPRODUCT_ACTIVE', 1)
                                ->where('CLMCHAR_ID', env('SIZE_ID'))
                                ->select('CLMPRODUCT_ID as product_variant_id', 'CLMCHARVALUE_ML_NAME as size_name')
                                ->orderBy('CLMCHARVALUEPRIORITY', 'asc')
                                ->get();
            $query[$key]->variants = $variants_array;

            $query[$key]->is_wishlist = 0;
            if ($request->has('customer_id') && $request->customer_id != '') {
                $is_favorite_array = DB::table('tbpc_basket')
                                        ->where('CLMGROUPID', $row->id)
                                        ->where('CLMPRODUCTTYPE', 'w')
                                        ->first();
                if($is_favorite_array){
                    $query[$key]->is_wishlist = 1;
                }
            }

            $categories_array = DB::table('tbpc_prod_pgroups_cat_rel')
                                ->where('CLMPRCAT_PGROUP', $row->id)
                                ->select('CLMPRCAT_CATEGORY as category_id')
                                ->get();
            $categories = [];
            foreach($categories_array as $category){
                $categories[] = $category->category_id;
            }
            $query[$key]->categories = $categories;
            $query[$key]->link = '/product/'.$row->id.'/'.strtolower(urlencode($row->name));
        }

        $products = $query;
        
        return response()->json($products);
    }
}
