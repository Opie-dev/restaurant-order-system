<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ListOrders extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public string $status = 'all';
    public ?int $user = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->resetPage();
    }

    public function getStatusesProperty(): array
    {
        return ['all', 'unpaid', 'processing', 'paid', 'refunded', 'failed'];
    }

    public function getOrdersProperty()
    {
        return Order::with(['user', 'items'])
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where('code', 'like', '%' . trim($this->search) . '%');
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('payment_status', $this->status);
            })
            ->when($this->user !== null, function ($q) {
                $q->where('user_id', $this->user);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.orders.list-orders');
    }
}
