<?php

namespace App\Http\Livewire\Admin\ApiKeys;

use App\Http\Livewire\PaginationComponent;
use App\Models\ApiKey;

class Crud extends PaginationComponent
{
    public $apiKey;
    public $isCreating;

    public $rules = [
        'apiKey.name' => 'required|min:2|max:48',
        'apiKey.level' => 'required|integer|digits_between:' . ApiKey::LEVEL_REQUIRE_AUTH . ',' . ApiKey::LEVEL_NO_AUTH
    ];

    public function mount()
    {
        $this->apiKey = new ApiKey();
        $this->apiKey->level = ApiKey::LEVEL_REQUIRE_AUTH;
        $this->isCreating = false;
    }

    public function createApiKey()
    {
        $this->validate();
        $this->apiKey->key = ApiKey::generateKey();
        $this->apiKey->save();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.api_keys.crud', [
            'apiKeys' => ApiKey::search(ApiKey::select(), $this->query)
                ->orderByRaw('LOWER(name)')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/api_keys.crud.title')]);
    }
}
