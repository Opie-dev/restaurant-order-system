<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code - Table {{ $table->table_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 20px;
        }
        .store-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .table-info {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .qr-code {
            margin: 20px 0;
        }
        .qr-code img {
            width: 300px;
            height: 300px;
        }
        .table-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-top: 15px;
            padding: 10px 20px;
            border: 2px solid #333;
            border-radius: 8px;
            display: inline-block;
            background-color: #f8f9fa;
        }
        .instructions {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
            line-height: 1.5;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="store-name">{{ $store->name }}</div>
        <div class="table-info">Table {{ $table->table_number }}</div>
    </div>

    <div class="qr-code">
        <img src="{{ $qrCodePath }}" alt="QR Code for Table {{ $table->table_number }}">
        <div class="table-number">Table {{ $table->table_number }}</div>
    </div>

    <div class="instructions">
        <p><strong>Scan this QR code to order from your table</strong></p>
        <p>Point your camera at the QR code above to access our menu and place your order.</p>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y g:i A') }}</p>
        <p>{{ $store->name }} - {{ $store->address }}</p>
    </div>
</body>
</html>
