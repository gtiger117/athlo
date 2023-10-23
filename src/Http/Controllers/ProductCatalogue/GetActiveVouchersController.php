<?php

namespace App\Http\Controllers\ProductCatalogue;

use App\Http\Controllers\Controller;
use App\Models\GiftVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetActiveVouchersController extends Controller
{
    public function get_active_vouchers(Request $request)
    {
        // $this->authorize('view-any', Brand::class);

        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:gift_vouchers,id',   
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

        /*
        "id": 646311,
            "ext_code": null,
            "sec_ext_code": "",
            "name": "GIFT VOUCHER",
            "description": "",
            "short_description": "",
            "price": 0,
            "tax": 19,
            "image": "https://administrator.athlokinisi.com.cy/product_catalog/products/646311/giftcards04.jpg",
            */

        $query = GiftVoucher::select('*')->where('active', 1)->orderBy('sort_order', 'asc');

        if ($request->has('id') && $request->id != '') {
            $query = $query->where('id', $request->id);
        }

        $query = $query->paginate($perPage, ['/*'], 'page', $page);

        
        foreach ($query as $key=>$row) {
            // print_r($row->name);
            if($row->image != ''){
                $query[$key]->image = env('APP_URL').'/storage/'.$row->image;
            }
            $name_array =json_decode($row->name);
            $query[$key]->displayname = isset($name_array->en)?$name_array->en:$row->name;
            // $variants_array = DB::table('tbpc_products_variants')
            //                     ->where('CLMPRODUCT_ID', $row->id)
            //                     ->where('CLMPRODVAR_ITEMACTIVE', 1)
            //                     ->select('CLMPRODVAR_ID as product_variant_id', 
            //                             'CLMPRODVAR_OPTVAL1 as name',
            //                             'CLMPRODVAR_WOUTAXPRICE as price')
            //                     ->orderBy('CLMPRODVAR_WOUTAXPRICE', 'asc')
            //                     ->get();
            // foreach ($variants_array as $key1=>$row1) {
            //     $variants_array[$key1]->price = round($row1->price * (100 + $query[$key]->tax) / 100, 2);
            // }
            // $query[$key]->variants = $variants_array;
            // $query[$key]->link = '/page/vouchers-detail?index='.$row->id;
        }
        

        $products = $query;
        
        return response()->json($products);
    }
}
