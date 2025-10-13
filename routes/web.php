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
use Illuminate\Support\Facades\Auth;
use App\Livewire\Welcome as WelcomePage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QrCodeController;
use App\Livewire\Admin\Tables\ListTables;
use App\Livewire\Admin\QrCodes\QrGenerator;
use App\Livewire\Admin\Tables\TableForm;
use App\Http\Controllers\Admin\AdminQrController;

Route::get('/', WelcomePage::class)->name('home');

// Merchant authentication routes
Route::get('/merchant/login', MerchantLoginPage::class)->name('merchant.login');
Route::get('/merchant/register', MerchantRegisterPage::class)->name('merchant.register');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::redirect('/', '/admin/dashboard');

    // Store management routes (no store selection required)
    Route::prefix('stores')->name('stores.')->group(function (): void {
        Route::get('/select', StoreSelector::class)->name('select');
        Route::get('/create', CreateStore::class)->name('create');
    });

    // All other admin routes require store selection
    Route::middleware('store.selected')->group(function (): void {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        Route::prefix('menu')->name('menu.')->group(function (): void {
            Route::get('/', ListItems::class)->name('index');
            Route::get('/create', CreateItem::class)->name('create');
            Route::get('/{menuItem}/edit', EditItem::class)->whereNumber('menuItem')->name('edit');
        });
        // Preview route removed

        Route::prefix('categories')->name('categories.')->group(function (): void {
            Route::get('/', ListCategories::class)->name('index');
            Route::get('/create', CreateCategory::class)->name('create');
        });

        Route::prefix('orders')->name('orders.')->group(function (): void {
            Route::get('/', ListOrders::class)->name('index');
            Route::get('/pending', PendingOrders::class)->name('pending');
        });
        // Kitchen display removed

        Route::prefix('customers')->name('customers.')->group(function (): void {
            Route::get('/', ListUsers::class)->name('index');
            Route::get('/create', CreateUser::class)->name('create');
            Route::get('/{customer}/manage', ManageCustomer::class)->name('manage');
        });

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/store-details', StoreDetails::class)->name('store-details');
            Route::get('/store-media', AdminStoreMedia::class)->name('store-media');
            Route::get('/store-address', AdminStoreAddress::class)->name('store-address');
            Route::get('/store-hours', AdminStoreHours::class)->name('store-hours');
            Route::get('/security', Security::class)->name('security');
        });

        // Table management routes
        // Route::prefix('tables')->name('tables.')->group(function (): void {
        //     Route::get('/', ListTables::class)->name('index');
        //     Route::get('/create', TableForm::class)->name('create');
        //     Route::get('/{table}/edit', TableForm::class)->name('edit');
        // });

        // QR code management routes (essentials only)
        // Route::prefix('qr-codes')->name('qr-codes.')->group(function (): void {
        //     Route::get('/create', QrGenerator::class)->name('create');
        //     Route::post('/generate-all', [AdminQrController::class, 'generateAll'])->name('generate-all');
        // });

        Route::post('/logout', [AuthController::class, 'merchantLogout'])->name('logout');
    });
});

Route::group([
    'domain' => config('app.url'),
    'prefix' => 'menu/{store:slug}',
    'middleware' => 'resolve.store',
    'as' => 'menu.store.'
], function (): void {
    Route::get('/', CustomerMenu::class)->name('index');
    Route::get('/cart', CustomerCart::class)->name('cart');
    Route::get('/login', LoginPage::class)->name('login');

    Route::middleware('auth')->group(function (): void {
        Route::get('/checkout', CustomerCheckout::class)->name('checkout');
        Route::get('/orders', CustomerOrderHistory::class)->name('orders');
        Route::get('/addresses', CustomerAddresses::class)->name('addresses');
        Route::post('/logout', [AuthController::class, 'customerLogout'])->name('logout');
    });
});

// Table QR code routes removed
Route::get('/table/{qrCode}', [QrCodeController::class, 'redirect'])->name('table.menu');
// Early access subscription page
Route::get('/subscribe', Subscribe::class)->name('subscribe');
Route::get('/stores', StoresShowcase::class)->name('stores.index');
