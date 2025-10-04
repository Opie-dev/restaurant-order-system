<?php

namespace App\Livewire\Customer;

use App\Services\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Models\Store;

#[Layout('layouts.customer')]
class Checkout extends Component
{
    public bool $deliver = false;
    public ?int $addressId = null;
    public string $notes = '';
    public ?Store $store = null;
    protected CartService $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    public function mount(Request $request)
    {
        $this->store = $request->store;
    }


    public function updatedDeliver(): void
    {
        if ($this->deliver && Auth::check()) {
            $this->addressId = Auth::user()->defaultAddress?->id;
        }
    }

    public function getCartLinesProperty(): array
    {
        $cart = $this->cartService->current();
        return $this->cartService->getLines($cart);
    }

    public function getCartTotalsProperty(): array
    {
        $cart = $this->cartService->current();
        return $this->cartService->getTotals($cart);
    }

    public function submitOrder()
    {
        $this->validate([
            'deliver' => 'required|boolean',
            'addressId' => 'required_if:deliver,true|nullable|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if cart is not empty
        $cart = $this->cartService->current();
        if ($cart->items()->count() === 0) {
            $this->addError('cart', 'Your cart is empty. Please add items before placing an order.');
            return;
        }

        // Check delivery address if delivery is selected
        if ($this->deliver && !$this->addressId) {
            $this->addError('addressId', 'Please select a delivery address.');
            return;
        }

        try {
            // Get cart totals
            $totals = $this->cartService->getTotals($cart);

            // Resolve address snapshot if delivering
            $address = null;
            if ($this->deliver && $this->addressId) {
                $address = UserAddress::where('user_id', Auth::id())
                    ->where('id', $this->addressId)
                    ->first();
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'address_id' => $address?->id,
                'code' => strtoupper(Str::random(6)),
                'status' => Order::STATUS_PENDING,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                'notes' => $this->notes,
                'ship_recipient_name' => $address?->recipient_name,
                'ship_phone' => $address?->phone,
                'ship_line1' => $address?->line1,
                'ship_line2' => $address?->line2,
                'ship_city' => $address?->city,
                'ship_state' => $address?->state,
                'ship_postal_code' => $address?->postal_code,
                'ship_country' => $address?->country,
            ]);

            // Create order items from cart
            $cartItems = $cart->items()->with('menuItem')->get();
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $cartItem->menu_item_id,
                    'name_snapshot' => $cartItem->menuItem->name,
                    'unit_price' => $cartItem->unit_price,
                    'qty' => $cartItem->qty,
                    'line_total' => $cartItem->qty * $cartItem->unit_price,
                    'selections' => $cartItem->selections,
                ]);
            }

            // Clear the cart after successful order
            $this->cartService->clear();

            // Show success message
            session()->flash('success', 'Order placed successfully! Order code: ' . $order->code);

            // Redirect to order history
            return redirect()->route('orders');
        } catch (\Exception $e) {
            $this->addError('order', 'Failed to place order. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.customer.checkout');
    }
}
