<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed Notification</title>
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
            background-color: #7c3aed;
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
        .alert {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 15px;
            border-radius: 6px;
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
        .button {
            display: inline-block;
            background-color: #7c3aed;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Password Security Update</h1>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <p>We're writing to inform you that your account password has been updated by our administrative team.</p>
        
        <div class="alert">
            <strong>Important Security Notice:</strong> If you did not request this password change, please contact our support team immediately.
        </div>
        
        <h3>What this means:</h3>
        <ul>
            <li>Your password has been changed by an administrator</li>
            <li>You will need to use your new password for future logins</li>
            <li>If you have any questions, please contact our support team</li>
        </ul>
        
        <h3>Next Steps:</h3>
        <p>You can now log in to your account using your new password. If you experience any issues accessing your account, please don't hesitate to reach out to us.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('login') }}" class="button">Login to Your Account</a>
        </div>
        
        <p>If you have any concerns about this password change or need assistance, please contact our customer support team.</p>
        
        <p>Thank you for your continued trust in our service.</p>
        
        <p>Best regards,<br>
        The Restaurant Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>If you have any questions, please contact our support team.</p>
    </div>
</body>
</html>
