<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class PendingOrders extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->resetPage();
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

    public function getPendingOrdersProperty()
    {
        return Order::with(['user', 'items'])
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PREPARING])
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where('code', 'like', '%' . trim($this->search) . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getPendingCountProperty(): int
    {
        return Order::where('status', Order::STATUS_PENDING)->count();
    }

    public function getPreparingCountProperty(): int
    {
        return Order::where('status', Order::STATUS_PREPARING)->count();
    }

    public function render()
    {
        return view('livewire.admin.orders.pending-orders');
    }
}
