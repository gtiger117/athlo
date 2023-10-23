<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetWishListController extends Controller
{
    public function get_wish_list(Request $request)
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
                ->where('CLMPRODUCTTYPE', 'w')
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
        }

        $cart = $query;

        return response()->json($cart);
    }
}
