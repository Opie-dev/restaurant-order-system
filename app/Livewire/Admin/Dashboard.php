<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\MenuItem;
use App\Services\Admin\StoreService;
use App\Services\Admin\OnboardingService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public int $selectedMonth;
    public int $selectedWeek;
    public int $selectedYear;
    public int $selectedOrdersPeriod = 7;
    public int $selectedRevenuePeriod = 7;
    public $currentStore;
    private $storeService;
    private $onboardingService;

    public function boot()
    {
        $this->storeService = new StoreService();
        // Initialize onboarding service

        $this->currentStore = $this->storeService->getCurrentStore();
        $this->onboardingService = new OnboardingService($this->currentStore);
    }

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
        $this->selectedWeek = $this->getCurrentWeekOfMonth();
    }

    public function getCurrentWeekOfMonth()
    {
        $now = now();
        $firstDayOfMonth = $now->copy()->startOfMonth();
        $currentDay = $now->day;

        // Calculate which week of the month (1-4)
        return ceil($currentDay / 7);
    }

    public function updatedSelectedMonth()
    {
        $this->selectedWeek = 1; // Reset to week 1 when month changes
    }

    public function updatedSelectedYear()
    {
        $this->selectedWeek = 1; // Reset to week 1 when year changes
    }

    public function getTodayOrdersProperty()
    {
        return Order::where('store_id', $this->currentStore->id)
            ->whereDate('created_at', today())->count();
    }

    public function getTodayRevenueProperty()
    {
        return Order::where('store_id', $this->currentStore->id)
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getTotalCustomersProperty()
    {
        return User::where('role', 'customer')->count();
    }

    public function getTotalMenuItemsProperty()
    {
        return MenuItem::where('store_id', $this->currentStore->id)
            ->where('is_active', true)->count();
    }

    public function getPendingOrdersProperty()
    {
        return Order::where('store_id', $this->currentStore->id)
            ->whereIn('status', ['pending', 'preparing'])->count();
    }

    public function getCompletedOrdersProperty()
    {
        return Order::where('store_id', $this->currentStore->id)
            ->where('status', 'completed')->count();
    }

    public function getTotalRevenueProperty()
    {
        return Order::where('store_id', $this->currentStore->id)
            ->where('payment_status', 'paid')
            ->whereYear('created_at', $this->selectedYear)
            ->sum('total');
    }

    public function getRecentOrdersProperty()
    {
        return Order::where('store_id', $this->currentStore->id)
            ->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getTopSellingItemsProperty()
    {
        return MenuItem::where('store_id', $this->currentStore->id)
            ->withCount(['orderItems as total_ordered' => function ($query) {
                $query->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.payment_status', 'paid')
                    ->where('orders.store_id', $this->currentStore->id);
            }])
            ->orderBy('total_ordered', 'desc')
            ->limit(5)
            ->get();
    }

    public function getWeeklyRevenueProperty()
    {
        return Cache::remember("dashboard.weekly_revenue.{$this->currentStore->id}.{$this->selectedYear}.{$this->selectedMonth}.{$this->selectedWeek}", 300, function () {
            $startOfWeek = $this->getWeekStartDate();
            $endOfWeek = $this->getWeekEndDate();

            return Order::where('store_id', $this->currentStore->id)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->where('payment_status', 'paid')
                ->sum('total');
        });
    }

    public function getMonthlyRevenueProperty()
    {
        return Cache::remember("dashboard.monthly_revenue.{$this->currentStore->id}.{$this->selectedYear}.{$this->selectedMonth}", 600, function () {
            return Order::where('store_id', $this->currentStore->id)
                ->whereMonth('created_at', $this->selectedMonth)
                ->whereYear('created_at', $this->selectedYear)
                ->where('payment_status', 'paid')
                ->sum('total');
        });
    }

    public function getWeekStartDate()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $weekStart = $date->copy()->addWeeks($this->selectedWeek - 1)->startOfWeek();

        // If the week start is in the previous month, use the 1st of the current month
        if ($weekStart->month !== $this->selectedMonth) {
            $weekStart = $date->copy();
        }

        return $weekStart;
    }

    public function getWeekEndDate()
    {
        $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $weekEnd = $date->copy()->addWeeks($this->selectedWeek - 1)->endOfWeek();

        // If the week end is in the next month, use the last day of the current month
        if ($weekEnd->month !== $this->selectedMonth) {
            $weekEnd = $date->copy()->endOfMonth();
        }

        return $weekEnd;
    }

    public function getAvailableYearsProperty()
    {
        $currentYear = now()->year;
        $years = [];
        for ($i = $currentYear - 2; $i <= $currentYear; $i++) {
            $years[] = $i;
        }
        return $years;
    }

    public function getAvailableMonthsProperty()
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    public function getAvailableWeeksProperty()
    {
        return [1 => 'Week 1', 2 => 'Week 2', 3 => 'Week 3', 4 => 'Week 4'];
    }

    public function getOnboardingStepsProperty()
    {
        return $this->onboardingService->getSteps();
    }

    public function getOnboardingCompleteProperty()
    {
        return $this->onboardingService->isOnboardingComplete();
    }

    public function getCompletionPercentageProperty()
    {
        return $this->onboardingService->getCompletionPercentage();
    }

    public function getNextStepProperty()
    {
        return $this->onboardingService->getNextStep();
    }

    public function completeOnboarding()
    {
        $this->onboardingService->completeOnboarding();
        $this->currentStore->refresh();
        $this->onboardingService = new OnboardingService($this->currentStore);

        $this->dispatch('flash', type: 'success', message: 'Onboarding completed! Welcome to your dashboard.');
    }

    public function getOrderStatusCountsProperty()
    {
        return [
            'pending' => Order::where('store_id', $this->currentStore->id)->where('status', 'pending')->count(),
            'preparing' => Order::where('store_id', $this->currentStore->id)->where('status', 'preparing')->count(),
            'delivering' => Order::where('store_id', $this->currentStore->id)->where('status', 'delivering')->count(),
            'completed' => Order::where('store_id', $this->currentStore->id)->where('status', 'completed')->count(),
            'cancelled' => Order::where('store_id', $this->currentStore->id)->where('status', 'cancelled')->count(),
        ];
    }

    public function getDailyOrdersDataProperty()
    {
        return Cache::remember("dashboard.daily_orders_data.{$this->currentStore->id}.{$this->selectedOrdersPeriod}", 300, function () {
            $data = [];
            for ($i = $this->selectedOrdersPeriod - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $count = Order::where('store_id', $this->currentStore->id)
                    ->whereDate('created_at', $date)->count();
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
        return Cache::remember("dashboard.daily_revenue_data.{$this->currentStore->id}.{$this->selectedRevenuePeriod}", 300, function () {
            $data = [];
            for ($i = $this->selectedRevenuePeriod - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $revenue = Order::where('store_id', $this->currentStore->id)
                    ->whereDate('created_at', $date)
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
        Cache::forget('dashboard.order_status_counts');
        Cache::forget("dashboard.daily_orders_data.{$this->selectedOrdersPeriod}");
        Cache::forget("dashboard.daily_revenue_data.{$this->selectedRevenuePeriod}");

        // Clear dynamic cache keys
        Cache::forget("dashboard.weekly_revenue.{$this->currentStore->id}.{$this->selectedYear}.{$this->selectedMonth}.{$this->selectedWeek}");
        Cache::forget("dashboard.monthly_revenue.{$this->currentStore->id}.{$this->selectedYear}.{$this->selectedMonth}");
        Cache::forget("dashboard.daily_orders_data.{$this->currentStore->id}.{$this->selectedOrdersPeriod}");
        Cache::forget("dashboard.daily_revenue_data.{$this->currentStore->id}.{$this->selectedRevenuePeriod}");

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
