<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Menu\ListItems;
use App\Livewire\Admin\Menu\EditItem;
use App\Livewire\Admin\Menu\CreateItem;
use App\Livewire\Admin\Menu\PreviewMenu;
use App\Livewire\Admin\Categories\ListCategories;
use App\Livewire\Admin\Categories\CreateCategory;
use App\Livewire\Admin\Orders\ListOrders;
use App\Livewire\Admin\Orders\PendingOrders;
use App\Livewire\Admin\Users\ListUsers;
use App\Livewire\Admin\Users\CreateUser;
use App\Livewire\Admin\Users\ManageCustomer;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Settings\StoreDetails;
use App\Livewire\Admin\Settings\Security;
use App\Livewire\Admin\Stores\StoreSelector;
use App\Livewire\Auth\Login as LoginPage;
use App\Livewire\Auth\Register as RegisterPage;
use App\Livewire\Customer\Menu as CustomerMenu;
use App\Livewire\Customer\StoresShowcase;
use App\Livewire\Customer\Cart as CustomerCart;
use App\Livewire\Customer\Checkout as CustomerCheckout;
use App\Livewire\Customer\Addresses as CustomerAddresses;
use App\Livewire\Subscribe;
use App\Livewire\Customer\OrderHistory as CustomerOrderHistory;
use App\Livewire\Admin\Settings\StoreMedia as AdminStoreMedia;
use App\Livewire\Admin\Settings\StoreAddress as AdminStoreAddress;
use App\Livewire\Admin\Settings\StoreHours as AdminStoreHours;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
});

Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::redirect('/', '/admin/dashboard');

    // Store management routes (no store selection required)
    Route::get('/stores/select', StoreSelector::class)->name('stores.select');

    // Store selection handler
    Route::post('/stores/select', function () {
        $storeId = request('store_id');
        $store = \Illuminate\Support\Facades\Auth::user()->stores()->find($storeId);

        if ($store) {
            session(['current_store_id' => $store->id]);
            return redirect()->route('admin.dashboard')
                ->with('success', "Switched to {$store->name}");
        }

        return redirect()->route('admin.stores.select')
            ->with('error', 'Invalid store selected');
    })->name('stores.select.post');

    // All other admin routes require store selection
    Route::middleware('store.selected')->group(function (): void {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        Route::get('/menu', ListItems::class)->name('menu.index');
        Route::get('/menu/create', CreateItem::class)->name('menu.create');
        Route::get('/menu/{menuItem}/edit', EditItem::class)->whereNumber('menuItem')->name('menu.edit');
        Route::get('/menu/preview', PreviewMenu::class)->name('menu.preview');

        Route::get('/categories', ListCategories::class)->name('categories.index');
        Route::get('/categories/create', CreateCategory::class)->name('categories.create');
        Route::get('/orders', ListOrders::class)->name('orders.index');
        Route::get('/orders/pending', PendingOrders::class)->name('orders.pending');
        Route::get('/customers', ListUsers::class)->name('customers.index');
        Route::get('/customers/create', CreateUser::class)->name('customers.create');
        Route::get('/customers/{customer}/manage', ManageCustomer::class)->name('customers.manage');

        Route::get('/settings/store-details', StoreDetails::class)->name('settings.store-details');
        Route::get('/settings/store-media', AdminStoreMedia::class)->name('settings.store-media');
        Route::get('/settings/store-address', AdminStoreAddress::class)->name('settings.store-address');
        Route::get('/settings/store-hours', AdminStoreHours::class)->name('settings.store-hours');
        Route::get('/settings/security', Security::class)->name('settings.security');
    });
});

// Customer-facing pages (admins can view but not interact)
Route::get('/menu', CustomerMenu::class)->name('menu');
Route::get('/stores', StoresShowcase::class)->name('stores.index');

Route::get('/menu/{store:slug}', CustomerMenu::class)->name('menu.store');

Route::middleware(['session.store', 'auth'])->group(function () {
    Route::get('/checkout', CustomerCheckout::class)->name('checkout');
    Route::get('/orders', CustomerOrderHistory::class)->name('orders');
});

Route::middleware('auth')->group(function () {
    Route::get('/cart', CustomerCart::class)->name('cart');
    Route::get('/addresses', CustomerAddresses::class)->name('addresses');
});

// Early access subscription page
Route::get('/subscribe', Subscribe::class)->name('subscribe');
