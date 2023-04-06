<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use Helpers\ApiUtils;
use Illuminate\Http\Request;

class ApiInventoriesController extends Controller
{
    // Api inventories index route
    public function index(Request $request)
    {
        $inventories = Inventory::search(Inventory::select(), $request->input('query'))
            ->with(['user', 'products'])
            ->orderBy('created_at', 'DESC')
            ->paginate(ApiUtils::parseLimit($request))->withQueryString();
        return InventoryResource::collection($inventories);
    }

    // Api inventories show route
    public function show(Inventory $inventory)
    {
        $inventory->user;
        $inventory->products;
        return new InventoryResource($inventory);
    }
}
