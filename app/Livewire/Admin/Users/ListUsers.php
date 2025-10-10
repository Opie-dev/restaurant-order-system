<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;
use App\Services\Admin\StoreService;

#[Layout('layouts.admin')]
class ListUsers extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    public $currentStore;
    private $storeService;

    public function boot()
    {
        $this->storeService = new StoreService();
    }

    public function mount()
    {
        $this->currentStore = $this->storeService->getCurrentStore();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getUsersProperty()
    {
        return User::query()
            ->where('role', 'customer')
            ->where('store_id', $this->currentStore->id)
            ->with(['defaultAddress'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.users.list-users');
    }
}
