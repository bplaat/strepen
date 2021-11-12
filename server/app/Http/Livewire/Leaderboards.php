<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Leaderboards extends Component
{
    public $range;
    public $startDate;

    public $queryString = ['range' => ['except' => '']];

    public function mount()
    {
        if (
            $this->range != 'month_to_date' && $this->range != 'month' && $this->range != 'half_year' && $this->range != 'year' &&
            $this->range != 'two_year' && $this->range != 'five_year' && $this->range != 'everything'
        ) {
            $this->range = null;
        }

        if ($this->range == 'month_to_date') {
            $this->startDate = date('Y-m-01');
        }
        if ($this->range == 'month') {
            $this->startDate = date('Y-m-d', time() - 30 * 24 * 60 * 60);
        }
        if ($this->range == 'half_year') {
            $this->startDate = date('Y-m-d', time() - 132 * 24 * 60 * 60);
        }
        if ($this->range == null) {
            $this->startDate = date('Y-01-01');
        }
        if ($this->range == 'year') {
            $this->startDate = date('Y-m-d', time() - 356 * 24 * 60 * 60);
        }
        if ($this->range == 'two_year') {
            $this->startDate = date('Y-m-d', time() - 2 * 356 * 24 * 60 * 60);
        }
        if ($this->range == 'five_year') {
            $this->startDate = date('Y-m-d', time() - 5 * 356 * 24 * 60 * 60);
        }
        if ($this->range == 'everything') {
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
