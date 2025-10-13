<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Admin\StoreService;

#[Layout('layouts.admin')]
class PendingOrders extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public ?int $trackingOrderId = null;
    public string $trackingUrl = '';
    public string $deliveryFee = '';

    // Real-time tracking
    public ?string $lastOrderTimestamp = null;
    public int $newOrderCount = 0;

    private $storeService;
    public $store;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function boot()
    {
        $this->storeService = new StoreService();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function updateOrderStatus($orderId, $newStatus, $cancellationRemarks = null, $trackingUrl = null, $deliveryFee = null): void
    {
        $order = Order::findOrFail($orderId);

        if (!$order->canTransitionTo($newStatus)) {
            $this->dispatch('flash', type: 'error', message: 'Invalid status transition from ' . $order->status . ' to ' . $newStatus);
            return;
        }

        // Require cancellation reason when cancelling
        if ($newStatus === Order::STATUS_CANCELLED && (!is_string($cancellationRemarks) || trim($cancellationRemarks) === '')) {
            $this->dispatch('flash', type: 'error', message: 'Cancellation reason is required.');
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

        $this->dispatch('flash', type: 'success', message: $message);
        if ($newStatus === Order::STATUS_DELIVERING) {
            $this->dispatch('close-tracking-modal');
        }
    }

    public function confirmDelivering(): void
    {
        $this->validate([
            'trackingUrl' => ['required', 'url'],
            'deliveryFee' => ['required', 'numeric', 'min:0'],
            'trackingOrderId' => ['required', 'integer'],
        ]);

        $order = Order::findOrFail($this->trackingOrderId);

        if (!$order->canTransitionTo(Order::STATUS_DELIVERING)) {
            $this->dispatch('flash', type: 'error', message: 'Invalid status transition from ' . $order->status . ' to delivering');
            return;
        }

        // Add delivery fee to existing total
        $newTotal = $order->total + $this->deliveryFee;

        $order->update([
            'status' => Order::STATUS_DELIVERING,
            'tracking_url' => $this->trackingUrl,
            'delivery_fee' => $this->deliveryFee,
            'total' => $newTotal,
        ]);

        $this->dispatch('flash', type: 'success', message: 'Order status updated to Delivering');
        $this->dispatch('close-tracking-modal');

        // Reset form state
        $this->trackingOrderId = null;
        $this->trackingUrl = '';
        $this->deliveryFee = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function clearTrackingValidation(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function getPendingOrdersProperty()
    {
        return Order::with(['user', 'items', 'table'])
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PREPARING, Order::STATUS_DELIVERING])
            ->where('store_id', $this->store->id)
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where('code', 'like', '%' . trim($this->search) . '%')
                    ->orWhere('table_number', 'like', '%' . trim($this->search) . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getPendingCountProperty(): int
    {
        return Order::where('status', Order::STATUS_PENDING)->where('store_id', $this->store->id)->count();
    }

    public function getPreparingCountProperty(): int
    {
        return Order::where('status', Order::STATUS_PREPARING)->where('store_id', $this->store->id)->count();
    }

    public function getDeliveringCountProperty(): int
    {
        return Order::where('status', Order::STATUS_DELIVERING)->where('store_id', $this->store->id)->count();
    }

    // Real-time methods
    public function checkForNewOrders(): void
    {
        if ($this->lastOrderTimestamp) {
            $newOrders = Order::whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PREPARING, Order::STATUS_DELIVERING])
                ->where('store_id', $this->store->id)
                ->where('created_at', '>', $this->lastOrderTimestamp)
                ->count();

            if ($newOrders > 0) {
                $this->newOrderCount += $newOrders;
                $this->dispatch('new-orders-notification', [
                    'count' => $newOrders,
                    'message' => $newOrders === 1 ? 'New order received!' : "{$newOrders} new orders received!"
                ]);
            }
        }

        // Update timestamp to current time
        $this->lastOrderTimestamp = now()->toDateTimeString();
    }

    public function markOrdersAsSeen(): void
    {
        $this->newOrderCount = 0;
    }

    public function mount(): void
    {
        // Initialize with current timestamp
        $this->lastOrderTimestamp = now()->toDateTimeString();
        $this->store = $this->storeService->getCurrentStore();
    }

    // Removed ready/delivered count from pending page per new flow

    public function render()
    {
        return view('livewire.admin.orders.pending-orders');
    }
}
