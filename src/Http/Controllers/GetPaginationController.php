<?php

namespace Gtiger117\Athlo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPaginationController extends Controller
{
    public function get_pagination(Request $request)
    {
        // Simulating a collection of items
        $items = Collection::make(range(1, $request->total));

        // Current page number
        $page = $request->current_page;

        // Number of items per page
        $perPage = $request->per_page;

        // Create a paginator instance
        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => url('/')] // Replace with your desired URL path
        );

        // Set the number of pages to show before and after the current page
        $paginator->withPath('');
        
        $paginator1 = $paginator->onEachSide(1);


        // Get the pagination array
        $paginationArray = $paginator1->toArray();
        foreach($paginationArray['links'] as $key => $link){
            if($link['label'] == 'pagination.previous'){
                $paginationArray['links'][$key]['label'] = 'Previous';
            }
            if($link['label'] == 'pagination.next'){
                $paginationArray['links'][$key]['label'] = 'Next';
            }
        }
        return response()->json($paginationArray);
    }
}
