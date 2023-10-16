<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetBlogsController extends Controller
{
    public function get_blogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:blogs,id',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            // Return the validation errors
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
       
        if ($request->has('id') && $request->id != '') {
            $query = Blog::where('blogs.id', $request->id)->get();
        }else{
            $query = Blog::get();
        }
        
        foreach ($query as $key => $row) {
            $name = $row->name;
            $query[$key]->displayname = $name;
            $subquery = BlogPost::where('blog_id', $row->id)->get();
            foreach ($subquery as $subkey => $subrow) {
                $subquery[$subkey]->imageUrl = env('APP_IMG_URL') . '/storage/' . $subrow->image;
                $subname = $subrow->name;
                $subquery[$subkey]->displayname = $subname;
                $subdescription = $subrow->description;
                $subquery[$subkey]->displaydescription = $subdescription;
            }
            $query[$key]->blogpost = $subquery;
        }

        $result = $query;

        return response()->json($result);
    }
}
