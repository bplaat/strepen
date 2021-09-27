<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TransactionCreatedModal extends Component
{
    public $transaction;

    public function render()
    {
        return view('components.transaction-created-modal');
    }
}
