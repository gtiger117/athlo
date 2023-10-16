<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetLanguagesController extends Controller
{
    public function get_languages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:tbpc_categories,CLMCATEGORY_ID',
            'parent' => 'nullable|array',
            'parent.*' => 'integer',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = DB::table('tbcms_languages')
        ->where('CLMLANGUAGE_ACTIVE', 1)
        ->select('CLMLANGUAGE_ID as id', 'CLM_LANGUAGE_NAME as name')
        ->orderBy('CLMLANGUAGE_STATUS', 'desc')
        ->orderBy('CLMLANGUAGE_PRIORITY', 'asc')
        ->get();;


        $categories = $query;
        return response()->json($categories);
    }
}
