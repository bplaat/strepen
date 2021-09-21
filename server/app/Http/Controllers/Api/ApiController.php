<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    // API home route
    public function home()
    {
        return [
            'message' => 'Strepen REST API documentation: https://github.com/bplaat/strepen/blob/master/docs/api.md'
        ];
    }
}
