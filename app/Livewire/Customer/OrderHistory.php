<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderHistory extends Component
{
    public string $status = 'all';
    public string $search = '';

    protected $queryString = [
        'status' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function getStatusesProperty(): array
    {
        return ['all', 'unpaid', 'processing', 'paid', 'refunded', 'failed'];
    }

    public function updatedSearch(): void
    {
        // This method ensures the component re-renders when search changes
    }

    public function getOrdersProperty()
    {
        return Order::where('user_id', Auth::id())
            ->when($this->status !== 'all', function ($q) {
                $q->where('payment_status', $this->status);
            })
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where('code', 'like', '%' . trim($this->search) . '%');
            })
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.order-history');
    }
}
