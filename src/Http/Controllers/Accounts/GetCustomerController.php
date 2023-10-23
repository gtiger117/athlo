<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetCustomerController extends Controller
{
    public function get_users(Request $request)
    {
        // $this->authorize('view-any', Customer::class);

        $perPage = 20;
        $page = 1;
        $orderby = 'tbaccounts_user.CLM_ACCOUNT_ID';
        $orderbytype = 'desc';

        if ($request->has('page') && is_numeric($request->page)) {
            $page = $request->page;
        }
        if ($request->has('per_page') && is_numeric($request->per_page)) {
            $perPage = $request->per_page;
        }

        $query = DB::table('tbaccounts_user');

        if ($request->has('id') && $request->id != '') {
            $query = $query->where('CLM_ACCOUNT_ID', $request->id);
        }

        $query = $query
                    ->select('CLM_CUST_NAME as name',
                                'CLM_CUST_SURNAME as surname',
                                'CLM_CUST_EMAIL as email',
                                'CLM_CUST_PHONE as phone',
                                'CLM_ADR_POSTCODE as postcode',
                                'CLM_ADR_CITY as city',
                                'CLM_ADR_STATE as district',
                                'CLM_ADR_COUNTRY as country')                
                    ->orderBy($orderby, $orderbytype)
                    ->paginate($perPage, ['/*'], 'page', $page);
        foreach ($query as $key=>$row) {
            unset($query[$key]->password);
        }
        
        $customers = $query; 

        return response()->json($customers);

    }
}
