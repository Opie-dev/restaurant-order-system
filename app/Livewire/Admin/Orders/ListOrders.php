<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ListOrders extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public string $status = 'all';
    public ?int $user = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'pending'],
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
        return ['all', 'pending', 'preparing', 'ready', 'completed', 'cancelled'];
    }

    public function updateOrderStatus($orderId, $newStatus): void
    {
        $order = Order::findOrFail($orderId);

        if (!$order->canTransitionTo($newStatus)) {
            session()->flash('error', 'Invalid status transition from ' . $order->status . ' to ' . $newStatus);
            $this->dispatch('flash', 'Invalid status transition from ' . $order->status . ' to ' . $newStatus);
            return;
        }

        $order->update(['status' => $newStatus]);

        session()->flash('success', 'Order status updated to ' . ucfirst($newStatus));

        // Dispatch flash event for Alpine.js
        $this->dispatch('flash', 'Order status updated to ' . ucfirst($newStatus));
    }

    public function getOrdersProperty()
    {
        return Order::with(['user', 'items'])
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where('code', 'like', '%' . trim($this->search) . '%');
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
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
