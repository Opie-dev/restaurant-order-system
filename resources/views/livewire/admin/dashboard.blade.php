<div class="w-full px-6 py-8" x-data="{ 
    refreshing: false,
    showNotification: false,
    notificationMessage: ''
}" 
x-init="
    $wire.on('dashboard-refreshed', () => {
        refreshing = false;
        showNotification = true;
        notificationMessage = 'Dashboard refreshed successfully!';
        setTimeout(() => showNotification = false, 3000);
    });
">
    <!-- Header with Refresh Button -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome back! Here's what's happening with your restaurant today.</p>
        </div>
        <button 
            wire:click="refreshDashboard" 
            @click="refreshing = true"
            :disabled="refreshing"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" :class="{ 'animate-spin': refreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span x-text="refreshing ? 'Refreshing...' : 'Refresh'"></span>
        </button>
    </div>

    <!-- Success Notification -->
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span x-text="notificationMessage"></span>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Orders -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">Today's Orders</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $this->todayOrders }}</p>
                    <p class="text-xs text-blue-500 mt-1">Orders placed today</p>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-600">Today's Revenue</p>
                    <p class="text-3xl font-bold text-green-900">RM{{ number_format($this->todayRevenue, 2) }}</p>
                    <p class="text-xs text-green-500 mt-1">Revenue from paid orders</p>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-600">Total Customers</p>
                    <p class="text-3xl font-bold text-purple-900">{{ $this->totalCustomers }}</p>
                    <p class="text-xs text-purple-500 mt-1">Registered customers</p>
                </div>
            </div>
        </div>

        <!-- Active Menu Items -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-orange-600">Active Menu Items</p>
                    <p class="text-3xl font-bold text-orange-900">{{ $this->totalMenuItems }}</p>
                    <p class="text-xs text-orange-500 mt-1">Items available for order</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Weekly Revenue -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Week {{ $this->selectedWeek }} Revenue</h3>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <select wire:model.live="selectedWeek" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 pr-8 bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors appearance-none">
                            @foreach($this->availableWeeks as $weekNum => $weekName)
                                <option value="{{ $weekNum }}">{{ $weekName }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <p class="text-3xl font-bold text-green-600 mb-2">RM{{ number_format($this->weeklyRevenue, 2) }}</p>
            <p class="text-sm text-gray-500">Selected week's total revenue</p>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ $this->availableMonths[$this->selectedMonth] }} Revenue</h3>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <div class="relative">
                            <select wire:model.live="selectedMonth" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 pr-8 bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors appearance-none">
                                @foreach($this->availableMonths as $monthNum => $monthName)
                                    <option value="{{ $monthNum }}">{{ $monthName }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <p class="text-3xl font-bold text-blue-600 mb-2">RM{{ number_format($this->monthlyRevenue, 2) }}</p>
            <p class="text-sm text-gray-500">Selected month's total revenue</p>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Total Revenue for {{ $this->selectedYear }}</h3>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <select wire:model.live="selectedYear" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 pr-8 bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors appearance-none">
                            @foreach($this->availableYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-600 mb-2">RM{{ number_format($this->totalRevenue, 2) }}</p>
            <p class="text-sm text-gray-500">All-time total revenue for {{ $this->selectedYear }}</p>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Status Overview</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
            @foreach($this->orderStatusCounts as $status => $count)
                <div class="text-center p-4 rounded-lg {{ $status === 'pending' ? 'bg-yellow-50 border border-yellow-200' : ($status === 'preparing' ? 'bg-blue-50 border border-blue-200' : ($status === 'delivering' ? 'bg-indigo-50 border border-indigo-200' : ($status === 'completed' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'))) }}">
                    <div class="text-2xl font-bold {{ $status === 'pending' ? 'text-yellow-600' : ($status === 'preparing' ? 'text-blue-600' : ($status === 'delivering' ? 'text-indigo-600' : ($status === 'completed' ? 'text-green-600' : 'text-red-600'))) }}">{{ $count }}</div>
                    <div class="text-sm font-medium {{ $status === 'pending' ? 'text-yellow-700' : ($status === 'preparing' ? 'text-blue-700' : ($status === 'delivering' ? 'text-indigo-700' : ($status === 'completed' ? 'text-green-700' : 'text-red-700'))) }} capitalize">{{ $status }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Orders Chart -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Daily Orders</h3>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select wire:model.live="selectedOrdersPeriod" class="text-sm border border-gray-300 rounded-lg px-3 py-1 pr-8 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none">
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="60">Last 60 Days</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="relative">
                <!-- Y-axis labels -->
                <div class="absolute left-0 top-0 h-64 w-8 flex flex-col justify-between text-xs text-gray-500">
                    @php
                        $maxOrders = max(array_column($this->dailyOrdersData, 'orders'));
                        $yAxisValues = [];
                        if ($maxOrders > 0) {
                            $yAxisValues = [
                                $maxOrders,
                                round($maxOrders * 0.75),
                                round($maxOrders * 0.5),
                                round($maxOrders * 0.25),
                                0
                            ];
                        } else {
                            $yAxisValues = [0, 0, 0, 0, 0];
                        }
                    @endphp
                    @foreach($yAxisValues as $value)
                        <span class="text-right pr-2">{{ $value }}</span>
                    @endforeach
                </div>
                
                <!-- Chart area -->
                <div class="overflow-x-auto">
                    <div class="h-64 flex items-end space-x-2 ml-10 {{ $this->selectedOrdersPeriod == 7 ? 'justify-between' : 'min-w-max' }}">
                        @foreach($this->dailyOrdersData as $data)
                            <div class="flex flex-col items-center {{ $this->selectedOrdersPeriod == 7 ? 'flex-1' : 'flex-shrink-0' }}" style="{{ $this->selectedOrdersPeriod == 7 ? '' : 'min-width: 40px;' }}">
                                <div class="w-full bg-gray-100 rounded-t-lg relative group">
                                    <div 
                                        class="bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg transition-all duration-500 hover:from-blue-600 hover:to-blue-500" 
                                        style="height: {{ $maxOrders > 0 ? ($data['orders'] / $maxOrders) * 200 : 0 }}px;"
                                    ></div>
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                                        {{ $data['orders'] }} orders
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 mt-2 font-medium whitespace-nowrap">{{ $data['date'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Chart -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Daily Revenue</h3>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select wire:model.live="selectedRevenuePeriod" class="text-sm border border-gray-300 rounded-lg px-3 py-1 pr-8 bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors appearance-none">
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="60">Last 60 Days</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="relative">
                <!-- Y-axis labels -->
                <div class="absolute left-0 top-0 h-64 w-12 flex flex-col justify-between text-xs text-gray-500">
                    @php
                        $maxRevenue = max(array_column($this->dailyRevenueData, 'revenue'));
                        $yAxisValues = [];
                        if ($maxRevenue > 0) {
                            $yAxisValues = [
                                'RM' . number_format($maxRevenue, 0),
                                'RM' . number_format($maxRevenue * 0.75, 0),
                                'RM' . number_format($maxRevenue * 0.5, 0),
                                'RM' . number_format($maxRevenue * 0.25, 0),
                                'RM0'
                            ];
                        } else {
                            $yAxisValues = ['RM0', 'RM0', 'RM0', 'RM0', 'RM0'];
                        }
                    @endphp
                    @foreach($yAxisValues as $value)
                        <span class="text-right pr-2">{{ $value }}</span>
                    @endforeach
                </div>
                
                <!-- Chart area -->
                <div class="overflow-x-auto">
                    <div class="h-64 flex items-end space-x-2 ml-14 {{ $this->selectedRevenuePeriod == 7 ? 'justify-between' : 'min-w-max' }}">
                        @foreach($this->dailyRevenueData as $data)
                            <div class="flex flex-col items-center {{ $this->selectedRevenuePeriod == 7 ? 'flex-1' : 'flex-shrink-0' }}" style="{{ $this->selectedRevenuePeriod == 7 ? '' : 'min-width: 40px;' }}">
                                <div class="w-full bg-gray-100 rounded-t-lg relative group">
                                    <div 
                                        class="bg-gradient-to-t from-green-500 to-green-400 rounded-t-lg transition-all duration-500 hover:from-green-600 hover:to-green-500" 
                                        style="height: {{ $maxRevenue > 0 ? ($data['revenue'] / $maxRevenue) * 200 : 0 }}px;"
                                    ></div>
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                                        RM{{ number_format($data['revenue'], 0) }}
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 mt-2 font-medium whitespace-nowrap">{{ $data['date'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Items and Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Items -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Top Selling Items</h3>
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-4">
                @forelse($this->topSellingItems as $index => $item)
                    <div class="flex items-center justify-between p-3 rounded-lg {{ $index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }}">
                        <div class="flex items-center">
                            @if($index === 0)
                                <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-xs font-bold text-gray-600">{{ $index + 1 }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500">RM{{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">{{ $item->total_ordered ?? 0 }}</p>
                            <p class="text-xs text-gray-500">orders</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="space-y-4">
                @forelse($this->recentOrders as $order)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $order->code }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user?->name ?? 'Guest' }} • {{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">RM{{ number_format($order->total, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusColorClass() }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm text-gray-500">No recent orders</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>