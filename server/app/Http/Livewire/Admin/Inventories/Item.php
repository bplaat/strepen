<?php

namespace App\Http\Livewire\Admin\Inventories;

use Livewire\Component;

class Item extends Component
{
    public $inventory;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'inventory.name' => 'required|min:2|max:48'
    ];

    public function editInventory()
    {
        $this->validate();
        $this->isEditing = false;
        $this->inventory->save();
    }

    public function deleteInventory()
    {
        $this->isDeleting = false;
        $this->inventory->delete();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.inventories.item');
    }
}
