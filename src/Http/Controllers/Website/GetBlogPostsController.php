<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetBlogPostsController extends Controller
{
    public function get_blog_posts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:blog_posts,id',
            'blog_id' => 'nullable|integer|exists:blogs,id',
            'perpage' => 'nullable|integer',
            'page' => 'nullable|integer',
        ]);

        $perPage = 20;
        if ($request->has('perpage') && $request->perpage != '') {
            $perPage = $request->perpage;
        }
        $page = 1;
        if ($request->has('page') && $request->page != '') {
            $page = $request->page;
        }

        $query = BlogPost::where('published', '=', 1);

        
       
        if ($request->has('id') && $request->id != '') {
            $query = $query->where('id', $request->id);
        }
        if ($request->has('blog_id') && $request->blog_id != '') {
            $query = $query->where('blog_id', $request->blog_id);
        }
        if ($request->has('featured') && $request->featured == 1) {
            $query = $query->where('featured', $request->featured);
        }

        $query = $query
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['/*'], 'page', $page);
        
        foreach ($query as $key => $row) {
            if($row->image != ''){
                $query[$key]->image = env('APP_IMG_URL') . '/storage/' . $row->image;
            }
            $name_array = $row->name;
            $query[$key]->displayname = $name_array;
            $description_array = $row->description;
            $query[$key]->displaydescription = $description_array;
            $short_description_array = $row->short_description;
            $query[$key]->short_description = isset($short_description_array->en)?$short_description_array->en:'';
            $query[$key]->displayshortdescription = $short_description_array;
            $query[$key]->link = '/page/blogpost-detail/'.$row->id;
        }

        $result = $query;

        return response()->json($result);
    }
}
