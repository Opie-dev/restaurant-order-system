<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Menu\ListItems;
use App\Livewire\Admin\Menu\EditItem;
use App\Livewire\Admin\Menu\CreateItem;
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
use App\Livewire\Admin\Stores\CreateStore;
use App\Livewire\Auth\Login as LoginPage;
use App\Livewire\Auth\Register as RegisterPage;
use App\Livewire\Auth\MerchantLogin as MerchantLoginPage;
use App\Livewire\Auth\MerchantRegister as MerchantRegisterPage;
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
use App\Livewire\Welcome as WelcomePage;

Route::get('/', WelcomePage::class)->name('home');

// Merchant authentication routes
Route::get('/merchant/login', MerchantLoginPage::class)->name('merchant.login');
Route::get('/merchant/register', MerchantRegisterPage::class)->name('merchant.register');

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
    Route::get('/stores/create', CreateStore::class)->name('stores.create');

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
        // Preview route removed

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

Route::group([
    'domain' => config('app.url'),
    'prefix' => 'menu/{store:slug}',
    'middleware' => 'resolve.store',
    'as' => 'menu.store.'
], function (): void {
    Route::get('/', CustomerMenu::class)->name('index');
    Route::get('/checkout', CustomerCheckout::class)->name('checkout');
    Route::get('/orders', CustomerOrderHistory::class)->name('orders');
    Route::get('/cart', CustomerCart::class)->name('cart');
    Route::get('/addresses', CustomerAddresses::class)->name('addresses');
    Route::get('/login', LoginPage::class)->name('login');
});

// Early access subscription page
Route::get('/subscribe', Subscribe::class)->name('subscribe');
Route::get('/stores', StoresShowcase::class)->name('stores.index');
