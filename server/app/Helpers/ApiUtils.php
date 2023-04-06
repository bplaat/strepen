<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class ApiUtils
{
    public static function parseLimit(Request $request): int
    {
        if ($request->has('limit')) {
            $limit = (int) $request->input('limit');
            if ($limit < 1) {
                return 1;
            }
            if ($limit > 50) {
                return 50;
            }
            return $limit;
        }
        return 20;
    }
}
