<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Store;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;

#[Layout('layouts.customer')]
class OrderHistory extends Component
{
    public string $status = 'all';
    public string $search = '';
    public ?Store $store = null;

    // Table context (from QR code)
    public ?int $tableId = null;
    public ?string $tableNumber = null;
    public ?string $qrCode = null;

    protected $queryString = [
        'status' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function mount(Request $request)
    {
        $this->store = $request->store;

        // Check for table context from QR code
        $this->tableId = session('current_table_id');
        $this->tableNumber = session('current_table_number');
        $this->qrCode = session('current_qr_code');
    }

    public function getStatusesProperty(): array
    {
        return ['all', 'pending', 'preparing', 'delivering', 'completed', 'cancelled'];
    }

    public function getOrdersProperty()
    {
        return Order::where('user_id', Auth::id())
            ->when($this->store, function ($q) {
                $q->where('store_id', $this->store->id);
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
            })
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where('code', 'like', '%' . trim($this->search) . '%');
            })
            ->where('store_id', $this->store->id)
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
