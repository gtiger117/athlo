<?php

namespace Gtiger117\Athlo\Http\Controllers\Website;

use App\Helpers\Helper;
use Gtiger117\Athlo\Http\Controllers\Controller;
use Gtiger117\Athlo\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GetMenuController extends Controller
{
    public function get_menu(Request $request)
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

        $postData = [
            'id'=> $request->id, 
            'parent'=> $request->parent,
            'visible'=> $request->visible,
            'is_page'=> $request->is_page,
        ];

        $menu = Helper::get_menu($postData);       
        $menu = json_decode($menu);   
        // return true;
        return response()->json($menu);
    }
}
