<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Menu\ListItems;
use App\Livewire\Admin\Menu\EditItem;
use App\Livewire\Admin\Menu\CreateItem;
use App\Livewire\Admin\Categories\ListCategories;
use App\Livewire\Admin\Orders\ListOrders;
use App\Livewire\Auth\Login as LoginPage;
use App\Livewire\Auth\Register as RegisterPage;
use App\Livewire\Customer\Menu as CustomerMenu;
use App\Livewire\Customer\Cart as CustomerCart;
use App\Livewire\Customer\Checkout as CustomerCheckout;
use App\Livewire\Customer\Addresses as CustomerAddresses;

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
    Route::redirect('/', '/admin/menu');

    Route::get('/menu', ListItems::class)->name('menu.index');
    Route::get('/menu/create', CreateItem::class)->name('menu.create');
    Route::get('/menu/{menuItem}/edit', EditItem::class)->whereNumber('menuItem')->name('menu.edit');

    Route::get('/categories', ListCategories::class)->name('categories.index');
    Route::get('/orders', ListOrders::class)->name('orders.index');
});

// Customer-facing pages
Route::get('/menu', CustomerMenu::class)->name('menu');
Route::get('/cart', CustomerCart::class)->name('cart');
Route::get('/checkout', CustomerCheckout::class)->name('checkout');
Route::get('/addresses', CustomerAddresses::class)->middleware('auth')->name('addresses');
Route::get('/orders', function () {
    return view('customer.orders');
})->middleware('auth')->name('orders');
