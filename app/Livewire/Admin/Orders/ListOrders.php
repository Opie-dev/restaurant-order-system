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
    public string $orderType = 'all';
    public ?int $user = null;
    private $storeService;
    public $storeId;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'pending'],
        'orderType' => ['except' => 'all'],
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

    public function updatingOrderType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->orderType = 'all';
        $this->resetPage();
    }

    public function getStatusesProperty(): array
    {
        return ['all', 'pending', 'preparing', 'delivering', 'completed', 'cancelled'];
    }

    public function getOrderTypesProperty(): array
    {
        return ['all', 'table', 'delivery', 'pickup'];
    }

    public function getOrdersProperty()
    {
        return Order::with(['user', 'items', 'table'])
            ->where('store_id', $this->storeId)
            ->when(strlen($this->search) > 0, function ($q) {
                $q->where('code', 'like', '%' . trim($this->search) . '%')
                    ->orWhere('table_number', 'like', '%' . trim($this->search) . '%');
            })
            ->when($this->status !== 'all', function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->orderType !== 'all', function ($q) {
                if ($this->orderType === 'table') {
                    $q->whereNotNull('table_id');
                } elseif ($this->orderType === 'delivery') {
                    $q->where('delivery_fee', '>', 0);
                } elseif ($this->orderType === 'pickup') {
                    $q->whereNull('table_id')->where('delivery_fee', 0);
                }
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
