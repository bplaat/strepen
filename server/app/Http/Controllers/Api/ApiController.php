<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

abstract class ApiController extends Controller
{
    public function getItems($modelClass, $query, $request)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $items = ($modelClass . '::search')($query, $searchQuery);
        } else {
            $items = $query->where('deleted', false);
        }
        return $items;
    }

    public function getLimit($request)
    {
        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) {
                $limit = 1;
            }
            if ($limit > 50) {
                $limit = 50;
            }
            return $limit;
        } else {
            return 20;
        }
    }
}
