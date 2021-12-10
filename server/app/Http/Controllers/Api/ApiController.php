<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

abstract class ApiController extends Controller
{
    public function getItems($modelClass, $query, $request)
    {
        if ($request->has('query')) {
            $items = ($modelClass . '::search')($query, $request->input('query'));
        } else {
            $items = $query->where('deleted', false);
        }
        return $items;
    }

    public function getLimit($request)
    {
        if ($request->has('limit')) {
            $limit = (int)$request->input('limit');
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
