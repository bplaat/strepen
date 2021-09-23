<?php

namespace App\Http\Controllers\Api;

use App\Models\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiInventoriesController extends Controller
{
    // Api inventories index route
    public function index(Request $request)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $inventories = Inventory::search(Inventory::select(), $searchQuery);
        } else {
            $inventories = Inventory::where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $inventories = $inventories->orderBy('created_at', 'DESC')
            ->paginate($limit)->withQueryString();
        foreach ($inventories as $inventory) {
            $inventory->forApi($request->user());
        }
        return $inventories;
    }

    // Api inventories show route
    public function show(Request $request, Inventory $inventory)
    {
        $inventory->forApi($request->user());
        return $inventory;
    }
}
