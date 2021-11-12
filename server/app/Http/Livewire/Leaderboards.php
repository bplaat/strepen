<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Leaderboards extends Component
{
    public $type;
    public $startDate;

    public $queryString = ['type' => ['except' => '']];

    public function mount()
    {
        if (
            $this->type != 'month' && $this->type != 'half_year' && $this->type != 'year' &&
            $this->type != 'two_year' && $this->type != 'five_year' && $this->type != 'everything'
        ) {
            $this->type = null;
        }

        if ($this->type == 'month') {
            $this->startDate = date('Y-m-01');
        }
        if ($this->type == 'half_year') {
            $this->startDate = date('Y-m-d', time() - ceil(356 / 2) * 24 * 60 * 60);
        }
        if ($this->type == null) {
            $this->startDate = date('Y-01-01');
        }
        if ($this->type == 'year') {
            $this->startDate = date('Y-m-d', time() - 356 * 24 * 60 * 60);
        }
        if ($this->type == 'two_year') {
            $this->startDate = date('Y-m-d', time() - 2 * 356 * 24 * 60 * 60);
        }
        if ($this->type == 'five_year') {
            $this->startDate = date('Y-m-d', time() - 5 * 356 * 24 * 60 * 60);
        }
        if ($this->type == 'everything') {
            $this->startDate = date('Y-m-d', 0);
        }
    }

    public function select()
    {
        $this->mount();
    }

    public function render()
    {
        return view('livewire.leaderboards')->layout('layouts.app', ['title' => __('leaderboards.title')]);
    }
}
