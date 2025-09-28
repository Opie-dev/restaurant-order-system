<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isDisabled ? 'Account Disabled' : 'Account Reactivated' }} - {{ $storeName }}</title>
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
            background-color: {{ $isDisabled ? '#dc2626' : '#059669' }};
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
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .disabled {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .enabled {
            background-color: #f0fdf4;
            color: #059669;
            border: 1px solid #bbf7d0;
        }
        .contact-info {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 15px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $storeName }}</h1>
        <h2>{{ $isDisabled ? 'Account Disabled' : 'Account Reactivated' }}</h2>
    </div>

    <div class="content">
        <p>Dear {{ $user->name }},</p>

        @if($isDisabled)
            <p>We are writing to inform you that your account has been <strong>disabled</strong> by our administration team.</p>
            
            <div class="status-badge disabled">
                Account Status: DISABLED
            </div>

            <p>This means you will not be able to:</p>
            <ul>
                <li>Log into your account</li>
                <li>Place new orders</li>
                <li>Access your order history</li>
                <li>Manage your addresses</li>
            </ul>

            <p>If you believe this action was taken in error, or if you have any questions about your account status, please contact us immediately.</p>
        @else
            <p>Great news! Your account has been <strong>reactivated</strong> and you can now access all features of our service.</p>
            
            <div class="status-badge enabled">
                Account Status: ACTIVE
            </div>

            <p>You can now:</p>
            <ul>
                <li>Log into your account</li>
                <li>Place new orders</li>
                <li>Access your order history</li>
                <li>Manage your addresses</li>
            </ul>

            <p>We apologize for any inconvenience caused during the temporary suspension of your account.</p>
        @endif

        <div class="contact-info">
            <h3>Need Help?</h3>
            <p>If you have any questions or concerns, please don't hesitate to contact us:</p>
            <p><strong>Phone:</strong> {{ $storePhone }}</p>
            <p><strong>Store:</strong> {{ $storeName }}</p>
        </div>

        <p>Thank you for your understanding.</p>

        <p>Best regards,<br>
        The {{ $storeName }} Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.</p>
    </div>
</body>
</html>
