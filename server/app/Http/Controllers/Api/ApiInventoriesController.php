<?php

namespace App\Http\Controllers\Api;

use App\Models\Inventory;
use Illuminate\Http\Request;

class ApiInventoriesController extends ApiController
{
    // Api inventories index route
    public function index(Request $request)
    {
        $inventories = $this->getItems(Inventory::class, Inventory::select(), $request)
            ->orderBy('created_at', 'DESC')
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $inventories->count(); $i++) {
            $inventories[$i] = $inventories[$i]->toApiData($request->user(), ['user', 'products']);
        }
        return $inventories;
    }

    // Api inventories show route
    public function show(Request $request, Inventory $inventory)
    {
        return $inventory->toApiData($request->user(), ['user', 'products']);
    }
}
