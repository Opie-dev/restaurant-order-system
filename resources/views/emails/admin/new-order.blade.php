<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order #{{ $order->code }} - {{ $store->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .order-info {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .order-items {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .item-name {
            font-weight: 600;
            color: #1f2937;
        }
        .item-details {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }
        .item-price {
            font-weight: 600;
            color: #059669;
        }
        .total-section {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-final {
            font-weight: bold;
            font-size: 18px;
            color: #059669;
            border-top: 2px solid #bbf7d0;
            padding-top: 10px;
            margin-top: 10px;
        }
        .customer-info {
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .delivery-info {
            background-color: #f3e8ff;
            border: 1px solid #d8b4fe;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .action-button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-delivery {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-pickup {
            background-color: #f0fdf4;
            color: #166534;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $store->name }}</h1>
        <h2>New Order Received!</h2>
        <p>Order #{{ $order->code }}</p>
    </div>

    <div class="content">
        <p>Dear Store Administrator,</p>

        <p>A new order has been placed and requires your attention.</p>

        <div class="order-info">
            <h3>Order Details</h3>
            <p><strong>Order Code:</strong> {{ $order->code }}</p>
            <p><strong>Order Time:</strong> {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            <p><strong>Status:</strong> 
                <span class="badge badge-pending">{{ ucfirst($order->status) }}</span>
            </p>
            <p><strong>Payment Status:</strong> 
                <span class="badge badge-pending">{{ ucfirst($order->payment_status) }}</span>
            </p>
            @if($order->notes)
                <p><strong>Special Instructions:</strong> {{ $order->notes }}</p>
            @endif
        </div>

        <div class="customer-info">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> {{ $order->user->name }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
            <p><strong>Phone:</strong> {{ $order->user->phone ?? 'Not provided' }}</p>
        </div>

        @if($order->ship_recipient_name)
            <div class="delivery-info">
                <h3>Delivery Information</h3>
                <p><strong>Recipient:</strong> {{ $order->ship_recipient_name }}</p>
                <p><strong>Phone:</strong> {{ $order->ship_phone }}</p>
                <p><strong>Address:</strong></p>
                <p>{{ $order->ship_line1 }}@if($order->ship_line2), {{ $order->ship_line2 }}@endif<br>
                {{ $order->ship_city }}, {{ $order->ship_state }} {{ $order->ship_postal_code }}<br>
                {{ $order->ship_country }}</p>
                <span class="badge badge-delivery">Delivery</span>
            </div>
        @else
            <div class="delivery-info">
                <h3>Pickup Information</h3>
                <p>This is a pickup order. Customer will collect from store.</p>
                <span class="badge badge-pickup">Self Pickup</span>
            </div>
        @endif

        <div class="order-items">
            <h3>Order Items</h3>
            @foreach($order->items as $item)
                <div class="item-row">
                    <div>
                        <div class="item-name">{{ $item->name_snapshot }}</div>
                        <div class="item-details">
                            Quantity: {{ $item->qty }} × RM{{ number_format($item->unit_price, 2) }}
                            @if($item->selections)
                                <br>
                                @foreach($item->selections as $type => $selections)
                                    @if(is_array($selections) && !empty($selections))
                                        <strong>{{ ucfirst($type) }}:</strong> 
                                        @foreach($selections as $selection)
                                            @if(is_array($selection) && isset($selection['name']))
                                                {{ $selection['name'] }}@if(isset($selection['price']) && $selection['price'] > 0) (+RM{{ number_format($selection['price'], 2) }})@endif
                                            @else
                                                {{ $selection }}
                                            @endif
                                            @if(!$loop->last), @endif
                                        @endforeach
                                        @if(!$loop->last)<br>@endif
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="item-price">RM{{ number_format($item->line_total, 2) }}</div>
                </div>
            @endforeach
        </div>

        <div class="total-section">
            <h3>Order Summary</h3>
            <div class="total-row">
                <span>Subtotal:</span>
                <span>RM{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Tax (8%):</span>
                <span>RM{{ number_format($order->tax, 2) }}</span>
            </div>
            <div class="total-row total-final">
                <span>Total:</span>
                <span>RM{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <p>Please process this order as soon as possible to ensure customer satisfaction.</p>

        <p>Best regards,<br>
        Restaurant Order System</p>
    </div>

    <div class="footer">
        <p>This is an automated notification. Please check your admin dashboard for order management.</p>
        <p>&copy; {{ date('Y') }} {{ $store->name }}. All rights reserved.</p>
    </div>
</body>
</html>
