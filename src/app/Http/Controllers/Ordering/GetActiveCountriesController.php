<?php

namespace App\Http\Controllers\Ordering;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetActiveCountriesController extends Controller
{
    public function get_active_countries(Request $request)
    {
        // $this->authorize('view-any', Country::class);

        $active_countries = [];
        $all_countries = [];
        $all_countries_script = Country::select('id')->get();
        foreach($all_countries_script as $pickup){
            $all_countries[] = $pickup->id;
        }

        $query = ShippingMethod::join('shipping_method_types', 'shipping_method_types.id', '=', 'shipping_methods.shipping_method_type_id')
                                ->select('shipping_methods.*')
                                ->where('delivery_type', 'customer_address')
                                ->where('shipping_method_types.active', '1')
                                ->where('shipping_methods.active', '1')
                                ->get();

        foreach($query as $row){
            $countries_to_include = [];
            $countries_to_exclude = [];
            if($row->all_countries == true){
                $countries_to_include = array_merge($countries_to_include, $all_countries);
            }
            else{
                $countries_to_include_query = DB::table('country_shipping_method')
                                ->select('country_id as id')
                                ->where('shipping_method_id', $row->id)
                                ->get();
                if($countries_to_include_query){
                    foreach($countries_to_include_query as $pickup){
                        $countries_to_include[] = $pickup->id;
                    }
                }
            }
            if($row->exclude_countries == true){
                $countries_to_exclude_query = DB::table('exclude_country_shipping_method')
                                ->select('country_id as id')
                                ->where('shipping_method_id', $row->id)
                                ->get();
                if($countries_to_exclude_query){
                    foreach($countries_to_exclude_query as $pickup){
                        $countries_to_exclude[] = $pickup->id;
                    }
                }
            }
            $countries_to_include_query = DB::table('region_shipping_method')
                                ->select('region_country.country_id as id')
                                ->join('region_country', 'region_shipping_method.region_id', '=', 'region_country.region_id')
                                ->where('shipping_method_id', $row->id)
                                ->get();
            if($countries_to_include_query){
                foreach($countries_to_include_query as $pickup){
                    $countries_to_include[] = $pickup->id;
                }
            }
            $countries_to_include = array_unique($countries_to_include);

            if(count($countries_to_exclude) > 0){
                $collection = collect($countries_to_include);
                $elementsToRemove = $countries_to_exclude;
    
                $filteredCollection = $collection->reject(function ($value) use ($elementsToRemove) {
                    return in_array($value, $elementsToRemove);
                });
                
                // $active_countries = array_unique(array_merge($active_countries, $filteredCollection));
            }
            else{
                $active_countries = array_unique(array_merge($active_countries, $countries_to_include));
            }
        }

        $orderby = 'name';
        $orderbytype = 'asc';

        $countries = Country::orderBy($orderby, $orderbytype)->whereIn('id', $active_countries)
            ->paginate(1000)
            ->withQueryString();

        return response()->json($countries);
    }
}
