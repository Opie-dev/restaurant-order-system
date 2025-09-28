<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\MenuItem;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function getTodayOrdersProperty()
    {
        return Order::whereDate('created_at', today())->count();
    }

    public function getTodayRevenueProperty()
    {
        return Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getTotalCustomersProperty()
    {
        return User::where('role', 'customer')->count();
    }

    public function getTotalMenuItemsProperty()
    {
        return MenuItem::where('is_active', true)->count();
    }

    public function getPendingOrdersProperty()
    {
        return Order::whereIn('status', ['pending', 'preparing'])->count();
    }

    public function getCompletedOrdersProperty()
    {
        return Order::where('status', 'completed')->count();
    }

    public function getTotalRevenueProperty()
    {
        return Order::where('payment_status', 'paid')->sum('total');
    }

    public function getRecentOrdersProperty()
    {
        return Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getTopSellingItemsProperty()
    {
        return MenuItem::withCount(['orderItems as total_ordered' => function ($query) {
            $query->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.payment_status', 'paid');
        }])
            ->orderBy('total_ordered', 'desc')
            ->limit(5)
            ->get();
    }

    public function getWeeklyRevenueProperty()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getMonthlyRevenueProperty()
    {
        return Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getOrderStatusCountsProperty()
    {
        return [
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'delivering' => Order::where('status', 'delivering')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];
    }

    public function getDailyOrdersDataProperty()
    {
        return Cache::remember('dashboard.daily_orders_data', 300, function () {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = Order::whereDate('created_at', $date)->count();
                $data[] = [
                    'date' => $date->format('M j'),
                    'orders' => $count
                ];
            }
            return $data;
        });
    }

    public function getDailyRevenueDataProperty()
    {
        return Cache::remember('dashboard.daily_revenue_data', 300, function () {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $revenue = Order::whereDate('created_at', $date)
                    ->where('payment_status', 'paid')
                    ->sum('total');
                $data[] = [
                    'date' => $date->format('M j'),
                    'revenue' => $revenue
                ];
            }
            return $data;
        });
    }

    public function refreshDashboard()
    {
        // Clear all dashboard cache
        Cache::forget('dashboard.today_orders');
        Cache::forget('dashboard.today_revenue');
        Cache::forget('dashboard.total_customers');
        Cache::forget('dashboard.total_menu_items');
        Cache::forget('dashboard.pending_orders');
        Cache::forget('dashboard.completed_orders');
        Cache::forget('dashboard.total_revenue');
        Cache::forget('dashboard.recent_orders');
        Cache::forget('dashboard.top_selling_items');
        Cache::forget('dashboard.weekly_revenue');
        Cache::forget('dashboard.monthly_revenue');
        Cache::forget('dashboard.order_status_counts');
        Cache::forget('dashboard.daily_orders_data');
        Cache::forget('dashboard.daily_revenue_data');

        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'navigationBar' => true,
            'pageTitle' => 'Dashboard',
            'breadcrumbs' => [
                ['label' => 'Dashboard']
            ]
        ]);
    }
}
