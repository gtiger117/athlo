<?php

namespace App\Http\Controllers\ProductCatalogue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetActiveOptionsController extends Controller
{
    public function get_active_options(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'option_group_id' => [
                'required','integer',
                Rule::exists('option_groups', 'id'),
            ]
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $query = DB::table('options');
        $query = Option::where('name', '!=', "");
        $query = $query->select('options.*')
                ->orderBy('options.order', 'desc')
                ->orderBy('options.name', 'desc')
                ->paginate(1000);
        


        foreach ($query as $key=>$row) {
            $name_array =json_decode($row->name);
            $query[$key]->name = isset($name_array->en)?$name_array->en:'';

            $description_array =json_decode($row->description);
            $query[$key]->description = isset($description_array->en)?$description_array->en:'';
        }

        $options = $query;
        return response()->json($options);
    }
}
