<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1a1a1a; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 1px solid #f0f0f0; }
        .logo { text-align: center; color: #C5A021; font-size: 24px; font-weight: bold; letter-spacing: 4px; margin-bottom: 40px; }
        .header { border-bottom: 2px solid #C5A021; padding-bottom: 20px; margin-bottom: 30px; }
        .item-table { w-full; border-collapse: collapse; margin: 20px 0; }
        .item-row td { padding: 15px 0; border-bottom: 1px solid #eee; }
        .total-section { text-align: right; margin-top: 30px; font-size: 18px; font-weight: bold; }
        .footer { font-size: 10px; color: #999; text-align: center; margin-top: 50px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">WANDR JEWELRY</div>
        
        <div class="header">
            <h3>Invoice #WNDR-{{ $order->id }}</h3>
            <p>Date: {{ $order->created_at->format('M d, Y') }}<br>
               Customer: {{ $order->full_name }}</p>
        </div>

        <table class="item-table" width="100%">
            <thead>
                <tr style="text-align: left; font-size: 12px; color: #999;">
                    <th>TREASURE</th>
                    <th>QTY</th>
                    <th style="text-align: right;">PRICE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr class="item-row">
                    <td style="font-size: 14px; font-weight: bold;">{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <span style="color: #999; font-size: 14px;">Total Amount:</span>
            <span style="color: #C5A021;">${{ number_format($order->total_price, 2) }}</span>
        </div>

        <div class="footer">
            Handcrafted Excellence. Delivered to your hands.<br>
            © 2026 Wandr Jewelry Heritage.
        </div>
    </div>
</body>
</html>