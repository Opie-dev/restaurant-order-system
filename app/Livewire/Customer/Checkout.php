<?php

namespace App\Livewire\Customer;

use App\Services\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\NewOrderNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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
    public int $timeRemaining = 0;
    public bool $cartExpired = false;

    // Table context (from QR code)
    public ?int $tableId = null;
    public ?string $tableNumber = null;
    public ?string $qrCode = null;

    protected CartService $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }


    public function mount(Request $request)
    {
        $this->store = $request->store;

        // Check for table context from QR code
        $this->tableId = session('current_table_id');
        $this->tableNumber = session('current_table_number');
        $this->qrCode = session('current_qr_code');

        // Check if cart is expired and clear if necessary
        if ($this->cartService->checkAndClearExpiredCart($this->store?->id)) {
            $this->cartExpired = true;
            return;
        }

        // Restart timer when navigating to checkout page
        $this->cartService->restartTimer($this->store?->id);

        // Update timer
        $this->updateTimer();
    }

    public function updateTimer(): void
    {
        $this->timeRemaining = $this->cartService->getCartTimeRemaining($this->store?->id);
        $this->cartExpired = $this->cartService->isCartExpired($this->store?->id);
    }

    #[On('timer-tick')]
    public function onTimerTick(): void
    {
        $this->updateTimer();

        if ($this->cartExpired) {
            // Cart has expired, dispatch event to refresh the page
            $this->dispatch('cart-expired');
        }
    }


    public function updatedDeliver(): void
    {
        if ($this->deliver && Auth::check()) {
            // Auto-select default address if available
            $defaultAddress = Auth::user()->defaultAddress;
            if ($defaultAddress) {
                $this->addressId = $defaultAddress->id;
            } else {
                // If no default, select the first available address
                $firstAddress = $this->userAddresses->first();
                if ($firstAddress) {
                    $this->addressId = $firstAddress->id;
                }
            }
        } else {
            $this->addressId = null;
        }
    }

    public function getCartLinesProperty(): array
    {
        return $this->cartService->getLines($this->store?->id);
    }

    public function getCartTotalsProperty(): array
    {
        return $this->cartService->getTotals($this->store?->id);
    }

    public function getUserAddressesProperty()
    {
        if (!Auth::check()) {
            return collect();
        }

        return UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function submitOrder()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            $this->addError('auth', 'You must be logged in to place an order.');
            return;
        }

        // Check if store exists
        if (!$this->store) {
            $this->addError('store', 'Store not found. Please try again.');
            return;
        }

        $this->validate([
            'deliver' => 'required|boolean',
            'addressId' => 'required_if:deliver,true|nullable|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if cart is not empty
        $cartLines = $this->cartService->getLines($this->store?->id);
        if (empty($cartLines)) {
            $this->addError('cart', 'Your cart is empty. Please add items before placing an order.');
            return;
        }

        // Check delivery address if delivery is selected
        if ($this->deliver) {
            // Check if user has any addresses at all
            $userAddresses = UserAddress::where('user_id', Auth::id())->count();
            if ($userAddresses === 0) {
                $this->addError('address', 'You need to create a delivery address before placing a delivery order. Please click "Create Delivery Address" above or choose "Self-pickup" instead.');
                return;
            }

            // Check if a specific address is selected
            if (!$this->addressId) {
                $this->addError('addressId', 'Please select a delivery address from the list above.');
                return;
            }

            // Verify the selected address belongs to the user
            $selectedAddress = UserAddress::where('user_id', Auth::id())
                ->where('id', $this->addressId)
                ->first();
            if (!$selectedAddress) {
                $this->addError('addressId', 'The selected address is invalid. Please select a valid delivery address.');
                return;
            }
        }

        try {
            // Get cart totals
            $totals = $this->cartService->getTotals($this->store?->id);

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
                'store_id' => $this->store?->id,
                'table_id' => $this->tableId,
                'table_number' => $this->tableNumber,
                'address_id' => $address?->id,
                'code' => strtoupper(Str::random(6)),
                'status' => Order::STATUS_PENDING,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'tax_rate' => $totals['tax_rate'],
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
            foreach ($cartLines as $cartLine) {
                // Validate cart line structure
                if (!isset($cartLine['item']) || !isset($cartLine['unit_price']) || !isset($cartLine['qty']) || !isset($cartLine['line_total'])) {
                    throw new \Exception('Invalid cart line structure: ' . json_encode($cartLine));
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $cartLine['item']->id,
                    'name_snapshot' => $cartLine['item']->name,
                    'unit_price' => $cartLine['unit_price'],
                    'qty' => $cartLine['qty'],
                    'line_total' => $cartLine['line_total'],
                    'selections' => $cartLine['selections'],
                ]);
            }

            // Clear the cart after successful order
            $this->cartService->clear($this->store?->id);

            // Send notification email to store admin
            try {
                if ($this->store->admin && $this->store->admin->email) {
                    Mail::to($this->store->admin->email)->send(new NewOrderNotification($order, $this->store));
                }
            } catch (\Exception $emailException) {
                // Log email error but don't fail the order
                Log::warning('Failed to send new order notification email', [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'admin_email' => $this->store->admin?->email,
                    'error' => $emailException->getMessage()
                ]);
            }

            // Clear table context after successful order
            if ($this->tableId) {
                session()->forget(['current_table_id', 'current_table_number', 'current_qr_code']);
            }

            // Show success message
            session()->flash('success', 'Order placed successfully! Order code: ' . $order->code);

            // Redirect to order history
            return redirect()->route('menu.store.orders', ['store' => $this->store->slug]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Order placement failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'store_id' => $this->store?->id,
                'cart_lines' => $cartLines,
                'exception' => $e
            ]);

            $this->addError('order', 'Failed to place order. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.customer.checkout');
    }
}
