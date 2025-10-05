<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use App\Services\Admin\StoreService;

#[Layout('layouts.admin')]
class ListOrders extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public string $status = 'all';
    public ?int $user = null;
    private $storeService;
    public $storeId;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'pending'],
    ];

    public function boot()
    {
        $this->storeService = app(StoreService::class);
    }

    public function mount()
    {
        $this->storeId = $this->storeService->getCurrentStore()->id;
    }

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
        return ['all', 'pending', 'preparing', 'delivering', 'completed', 'cancelled'];
    }

    public function updateOrderStatus($orderId, $newStatus, $cancellationRemarks = null, $trackingUrl = null, $deliveryFee = null): void
    {
        $order = Order::where('store_id', $this->storeId)->findOrFail($orderId);

        if (!$order->canTransitionTo($newStatus)) {
            session()->flash('error', 'Invalid status transition from ' . $order->status . ' to ' . $newStatus);
            $this->dispatch('flash', 'Invalid status transition from ' . $order->status . ' to ' . $newStatus);
            return;
        }

        $updateData = ['status' => $newStatus];

        // Add cancellation remarks if cancelling
        if ($newStatus === Order::STATUS_CANCELLED && $cancellationRemarks) {
            $updateData['cancellation_remarks'] = $cancellationRemarks;
        }

        if ($newStatus === Order::STATUS_DELIVERING) {
            $updateData['tracking_url'] = $trackingUrl;
            $updateData['delivery_fee'] = $deliveryFee;
            // Add delivery fee to existing total
            $updateData['total'] = $order->total + $deliveryFee;
        }
        $order->update($updateData);

        $message = $newStatus === Order::STATUS_CANCELLED
            ? 'Order cancelled successfully'
            : 'Order status updated to ' . ucfirst($newStatus);

        session()->flash('success', $message);
        $this->dispatch('flash', $message);
    }

    public function getOrdersProperty()
    {
        return Order::with(['user', 'items'])
            ->where('store_id', $this->storeId)
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

    public function getOrderStatusCountsProperty()
    {
        return Cache::remember('orders.status_counts', 120, function () {
            return [
                'pending' => Order::where('status', 'pending')->where('store_id', $this->storeId)->count(),
                'preparing' => Order::where('status', 'preparing')->where('store_id', $this->storeId)->count(),
                'delivering' => Order::where('status', 'delivering')->where('store_id', $this->storeId)->count(),
                'completed' => Order::where('status', 'completed')->where('store_id', $this->storeId)->count(),
                'cancelled' => Order::where('status', 'cancelled')->where('store_id', $this->storeId)->count(),
            ];
        });
    }

    public function render()
    {
        return view('livewire.admin.orders.list-orders');
    }
}
