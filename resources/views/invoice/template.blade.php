<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        
        .company-info h1 {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        
        .company-info p {
            margin: 2px 0;
            color: #666;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h2 {
            font-size: 20px;
            color: #000;
            margin-bottom: 10px;
        }
        
        .invoice-info p {
            margin: 2px 0;
        }
        
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .billing-info h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }
        
        .billing-info p {
            margin: 2px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            color: #000;
        }
        
        .items-table td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            vertical-align: top;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .total-section {
            float: right;
            width: 300px;
            margin-bottom: 30px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .total-row.final {
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
            padding-top: 10px;
        }
        
        .payment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            clear: both;
        }
        
        .payment-info h3 {
            color: #000;
            margin-bottom: 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #28a745;
            color: white;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>{{ $company_name }}</h1>
                <p>{{ $company_address }}</p>
                <p>Telp: {{ $company_phone }}</p>
                <p>Email: {{ $company_email }}</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p><strong>No. Invoice:</strong> {{ $order_number }}</p>
                <p><strong>Tanggal:</strong> {{ $invoice_date }}</p>
                <p><strong>Tanggal Bayar:</strong> {{ $due_date }}</p>
                <p><span class="status-badge">PAID</span></p>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-info">
                <h3>Tagihan Kepada:</h3>
                <p><strong>{{ $order->user->name }}</strong></p>
                <p>{{ $order->user->email }}</p>
            </div>
            <div class="billing-info">
                <h3>Detail Pesanan:</h3>
                <p><strong>Order ID:</strong> {{ $order->id }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                <p><strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Produk</th>
                    <th width="20%">Kategori</th>
                    <th width="15%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->product->description)
                        <br><small style="color: #666;">{{ Str::limit($item->product->description, 100) }}</small>
                        @endif
                    </td>
                    <td>{{ $item->product->category->name ?? 'Uncategorized' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Pajak (0%):</span>
                <span>Rp 0</span>
            </div>
            <div class="total-row">
                <span>Ongkir:</span>
                <span>Rp 0</span>
            </div>
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Payment Information -->
        <div class="payment-info">
            <h3>Informasi Pembayaran</h3>
            <p><strong>Metode Pembayaran:</strong> {{ ucfirst($order->payment->method ?? 'N/A') }}</p>
            <p><strong>Status Pembayaran:</strong> <span style="color: #28a745; font-weight: bold;">LUNAS</span></p>
            <p><strong>Tanggal Pembayaran:</strong> {{ $order->payment->paid_at->format('d M Y, H:i') }}</p>
            <p><strong>ID Transaksi:</strong> {{ $order->payment->id }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda berbelanja di {{ $company_name }}!</p>
            <p>Invoice ini dibuat secara otomatis pada {{ now()->format('d M Y H:i:s') }}</p>
            <p>Untuk pertanyaan lebih lanjut, silakan hubungi {{ $company_email }}</p>
        </div>
    </div>
</body>
</html>
