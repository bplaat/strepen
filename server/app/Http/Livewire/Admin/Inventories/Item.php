<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Models\User;
use Livewire\Component;

class Item extends Component
{
    public $inventory;
    public $users;
    public $inventoryCreatedAtDate;
    public $inventoryCreatedAtTime;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'inventory.user_id' => 'required|integer|exists:users,id',
        'inventory.name' => 'required|min:2|max:48',
        'inventoryCreatedAtDate' => 'required|date_format:Y-m-d',
        'inventoryCreatedAtTime' => 'required|date_format:H:i:s'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->inventoryCreatedAtDate = $this->inventory->created_at->format('Y-m-d');
        $this->inventoryCreatedAtTime = $this->inventory->created_at->format('H:i:s');
    }

    public function editInventory()
    {
        $this->validate();
        $this->inventory->created_at = $this->inventoryCreatedAtDate . ' ' . $this->inventoryCreatedAtTime;
        $this->inventory->save();
        $this->isEditing = false;
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
