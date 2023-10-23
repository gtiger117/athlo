<?php

namespace Gtiger117\Athlo\Http\Controllers\Accounts;

use Gtiger117\Athlo\Http\Controllers\Controller;
use Gtiger117\Athlo\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetCartController extends Controller
{
    public function get_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => [
                'required','integer',
                Rule::exists('tbaccounts_user', 'CLM_ACCOUNT_ID'),
            ]
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = DB::table('tbpc_basket')
                ->where('CLMACCOUNT_ID', $request->customer_id)
                ->where('CLMPRODUCTTYPE', 'b')
                ->select('CLMBASKET_ID as id', 
                        'CLMGROUPID as product_id', 
                        'CLMPRODUCTID as product_variant_id', 
                        'CLMACCOUNT_ID as customer_id',
                        'CLMPRODUCTQTY as quantity')
                ->paginate(1000);

        foreach ($query as $key=>$row) {
            $product = Product::select('CLMPRODGROUP_PICTURE as image',
                                        'CLMPRODGROUP_ML_NAME as name',
                                        DB::raw('CASE 
                                                    WHEN CLMPRODGROUP_OFF_PRICE > 0 THEN ROUND(CLMPRODGROUP_OFF_PRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2) 
                                                    WHEN CLMPRODGROUP_OFFERPRICE > 0 THEN ROUND(CLMPRODGROUP_OFFERPRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2) 
                                                    ELSE ROUND(CLMPRODGROUP_PRICE * (100 + CLMPRODGROUP_TAX_PERCENTAGE) / 100, 2)
                                                END as pricesold'))
                                                ->where('CLMPRODGROUP_ID', $row->product_id)->first();
            $query[$key]->price = $product->pricesold;
            $query[$key]->name = $product->name;
            $query[$key]->link = '/product/'.$row->product_id.'/'.strtolower(urlencode($product->name));
            
            $images = [];
            if($product->image != ''){
                $query[$key]->image = env('APP_IMG_URL').'/product_catalog/groups/'.$row->product_id.'/'.$product->image;
                $images[] = env('APP_IMG_URL').'/product_catalog/groups/'.$row->product_id.'/'.$product->image;
            }
            $images_array = DB::table('tbcms_galleries')->where('CLMGALLERY_IMG_PRODUCTGROUP_ID', $row->product_id)->orderBy('CLMGALLERY_IMG_NAME', 'asc')->get();
            foreach($images_array as $image){
                $images[] = env('APP_IMG_URL').'/product_catalog/groups/'.$row->product_id.'/pics/'.$image->CLMGALLERY_IMG_NAME;
            }
            unset($query[$key]->image);
            $query[$key]->images = $images;
            

            // $name_array =json_decode($product->name);
            // $productData->productName = isset($name_array->en)?$name_array->en:'';

            
            $color_array = DB::table('tbchars_values')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMCHAR_ID', env('COLOR_ID'))
                                ->where('CLMPRODUCTID', $row->product_variant_id)
                                ->select('CLMCHARVALUE_ML_NAME as name')
                                ->first();
            $query[$key]->color_name = '';
            if($color_array){
                $query[$key]->color_name = $color_array->name;
            }

            $size_array = DB::table('tbchars_values')
                                ->join('tbchars_values_prods_group_rel', 'tbchars_values_prods_group_rel.EXTCHARVALUEID', '=', 'tbchars_values.CLMCHARVALUEID')
                                ->where('CLMCHAR_ID', env('SIZE_ID'))
                                ->where('CLMPRODUCTID', $row->product_variant_id)
                                ->select('CLMCHARVALUE_ML_NAME as name')
                                ->first();
            $query[$key]->size_name = '';
            if($size_array){
                $query[$key]->size_name = $size_array->name;
            }
            
        }

        $cart = $query;

        return response()->json($cart);
    }
}
