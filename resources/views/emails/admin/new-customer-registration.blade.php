<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Customer Registration - {{ $store->name }}</title>
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
            background-color: #059669;
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
        .customer-info {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .registration-details {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .stats-section {
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .action-button {
            display: inline-block;
            background-color: #059669;
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
        .badge-new {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        .info-value {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $store->name }}</h1>
        <h2>New Customer Registration!</h2>
        <p>Welcome a new customer to your store</p>
    </div>

    <div class="content">
        <p>Dear Store Administrator,</p>

        <p>A new customer has registered and is now part of your customer base.</p>

        <div class="customer-info">
            <h3>Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $customer->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $customer->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $customer->phone ?? 'Not provided' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Registration Date:</span>
                <span class="info-value">{{ $customer->created_at->format('M d, Y \a\t g:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="badge badge-new">New Customer</span>
                </span>
            </div>
        </div>

        <div class="registration-details">
            <h3>Registration Details</h3>
            <p><strong>Customer ID:</strong> #{{ $customer->id }}</p>
            <p><strong>Email Verified:</strong> {{ $customer->email_verified_at ? 'Yes' : 'No' }}</p>
            <p><strong>Last Login:</strong> {{ $customer->last_login_at ? $customer->last_login_at->format('M d, Y \a\t g:i A') : 'Never' }}</p>
            
            @if($customer->addresses && $customer->addresses->count() > 0)
                <p><strong>Addresses:</strong> {{ $customer->addresses->count() }} address(es) saved</p>
            @else
                <p><strong>Addresses:</strong> No addresses saved yet</p>
            @endif
        </div>

        <div class="stats-section">
            <h3>Customer Insights</h3>
            <p>This customer has:</p>
            <ul>
                <li>Just registered - no order history yet</li>
                <li>Potential for first-time customer promotions</li>
                <li>Opportunity to provide excellent onboarding experience</li>
            </ul>
        </div>

        <p>Consider reaching out to welcome this new customer and encourage their first order!</p>

        <p>Best regards,<br>
        Restaurant Order System</p>
    </div>

    <div class="footer">
        <p>This is an automated notification. You can manage customers in your admin dashboard.</p>
        <p>&copy; {{ date('Y') }} {{ $store->name }}. All rights reserved.</p>
    </div>
</body>
</html>
