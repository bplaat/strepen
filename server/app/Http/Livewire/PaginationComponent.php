<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class PaginationComponent extends Component
{
    use WithPagination;

    public $query;
    public $queryString = ['query' => ['except' => '']];

    public $listeners = [ 'refresh' => '$refresh' ];

    public function search()
    {
        $this->resetPage();
    }

    public function paginationView()
    {
        return 'layouts.pagination';
    }

    public function _previousPage($disabled)
    {
        if (!$disabled) $this->previousPage();
    }

    public function _nextPage($disabled)
    {
        if (!$disabled) $this->nextPage();
    }
}
