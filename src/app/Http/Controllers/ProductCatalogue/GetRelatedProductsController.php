<?php

namespace App\Http\Controllers\ProductCatalogue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class GetRelatedProductsController extends Controller
{
    public function get_related_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
               'errors' => $validator->errors(),
            ], 422);
        }

        $related_products_array = DB::table('tbpc_products_rel_prods')
                        ->whereIn('CLM_REL1_PROD', $request->ids)
                        ->where('CLM_REL1_TYPE', 'g')
                        ->where('CLM_REL2_TYPE', 'g')
                        ->select('CLM_REL1_PROD', 'CLM_REL2_PROD')
                        ->get();
        $related_products = [];
        foreach($related_products_array as $related_product){
            if(!in_array($related_product->CLM_REL1_PROD, $related_products)){
                $related_products[] = $related_product->CLM_REL1_PROD;
            }
            if(!in_array($related_product->CLM_REL2_PROD, $related_products)){
                $related_products[] = $related_product->CLM_REL2_PROD;
            }
        }
        $resultProducts = array();
        foreach($related_products as $product){
            $query = Product::where('CLMPRODGROUP_ACTIVE', 1)->where('CLMPRODGROUP_ID', $product);
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
            $query = $query->first();
            
            $images = [];
            if($query->image != ''){
                $query->image = env('APP_IMG_URL').'/product_catalog/groups/'.$query->id.'/'.$query->image;
                $images[] = $query->image;
            }
            $images_array = DB::table('tbcms_galleries')->where('CLMGALLERY_IMG_PRODUCTGROUP_ID', $query->id)->orderBy('CLMGALLERY_IMG_NAME', 'asc')->get();
            foreach($images_array as $image){
                $images[] = env('APP_IMG_URL').'/product_catalog/groups/'.$query->id.'/pics/'.$image->CLMGALLERY_IMG_NAME;
            }
            unset($query->image);
            $query->images = $images;

            $brand_array = DB::table('tbchars_values')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMCHAR_ID', env('BRAND_ID'))
                                ->where('CLMPRCHAR_PGROUP', $query->id)
                                ->select('CLMCHARVALUEID as id', 'CLMCHARVALUE_ML_NAME as name')
                                ->first();
            $query->brand_id = '';
            $query->brand_name = '';
            if($brand_array){
                $query->brand_id = $brand_array->id;
                $query->brand_name = $brand_array->name;
            }
            $color_array = DB::table('tbchars_values')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMCHAR_ID', env('COLOR_ID'))
                                ->where('CLMPRCHAR_PGROUP', $query->id)
                                ->select('CLMCHARVALUEID as id', 'CLMCHARVALUE_ML_NAME as name')
                                ->first();
            $query->color_id = '';
            $query->color_name = '';
            if($color_array){
                $query->color_id = $color_array->id;
                $query->color_name = $color_array->name;
            }
            $variants_array = DB::table('tbpc_products')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.CLMPRODUCTID', '=', 'tbpc_products.CLMPRODUCT_ID')
                                ->join('tbchars_values', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMPRODUCT_GROUPID', $query->id)
                                ->where('CLMPRODUCT_ACTIVE', 1)
                                ->where('CLMCHAR_ID', env('SIZE_ID'))
                                ->select('CLMPRODUCT_ID as product_variant_id', 'CLMCHARVALUE_ML_NAME as size_name')
                                ->orderBy('CLMCHARVALUEPRIORITY', 'asc')
                                ->get();
            $query->variants = $variants_array;

            $query->is_wishlist = 0;
            if ($request->has('customer_id') && $request->customer_id != '') {
                $is_favorite_array = DB::table('tbpc_basket')
                                        ->where('CLMGROUPID', $query->id)
                                        ->where('CLMPRODUCTTYPE', 'w')
                                        ->first();
                if($is_favorite_array){
                    $query->is_wishlist = 1;
                }
            }

            $categories_array = DB::table('tbpc_prod_pgroups_cat_rel')
                                ->where('CLMPRCAT_PGROUP', $query->id)
                                ->select('CLMPRCAT_CATEGORY as category_id')
                                ->get();
            $categories = [];
            foreach($categories_array as $category){
                $categories[] = $category->category_id;
            }
            $query->categories = $categories;
            $query->link = '/product/'.$query->id.'/'.strtolower(urlencode($query->name));
            array_push($resultProducts,$query);
        }
        return response()->json($resultProducts);
    }
}
