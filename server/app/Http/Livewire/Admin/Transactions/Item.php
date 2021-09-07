<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Models\Transaction;
use Livewire\Component;

class Item extends Component
{
    public $transaction;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'transaction.name' => 'required|min:2|max:48'
    ];

    public function editTransaction()
    {
        $this->validate();
        $this->isEditing = false;
        $this->transaction->save();
    }

    public function deleteTransaction()
    {
        $this->isDeleting = false;
        $this->transaction->delete();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.transactions.item');
    }
}
