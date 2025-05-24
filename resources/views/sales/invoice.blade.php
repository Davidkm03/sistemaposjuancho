<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .company-info h2, .company-info h3 {
            margin: 0;
        }
        .company-info p {
            margin: 5px 0;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h1 {
            color: #2e64fe;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .customer-info h3 {
            margin: 0 0 10px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        table th {
            background-color: #f8f8f8;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
        }
        .totals table {
            width: 300px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 16px;
            background-color: #f8f8f8;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #777;
            font-size: 11px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .payment-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: 5px;
        }
        @media print {
            .invoice-box {
                box-shadow: none;
                border: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-info">
                <h2>{{ config('app.name', 'Sistema POS') }}</h2>
                <p>Dirección de la Empresa<br>
                Ciudad, Código Postal<br>
                Teléfono: (123) 456-7890<br>
                Email: info@sistemaspos.com</p>
            </div>
            <div class="invoice-details">
                <h1>FACTURA</h1>
                <p><strong>Factura Nº:</strong> {{ $sale->invoice_number }}</p>
                <p><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($sale->status) }}</p>
            </div>
        </div>
        
        <div class="customer-info">
            <h3>Datos del Cliente</h3>
            @if($sale->customer)
                <p><strong>Cliente:</strong> {{ $sale->customer->name }}</p>
                <p><strong>{{ $sale->customer->document_type }}:</strong> {{ $sale->customer->document_number }}</p>
                @if($sale->customer->address)
                    <p><strong>Dirección:</strong> {{ $sale->customer->address }}</p>
                @endif
                @if($sale->customer->phone)
                    <p><strong>Teléfono:</strong> {{ $sale->customer->phone }}</p>
                @endif
                @if($sale->customer->email)
                    <p><strong>Email:</strong> {{ $sale->customer->email }}</p>
                @endif
            @else
                <p>Cliente Casual</p>
            @endif
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="40%">Descripción</th>
                    <th width="15%">Precio Unit.</th>
                    <th width="10%">Cant.</th>
                    <th width="15%">Descuento</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleDetails as $detail)
                    <tr>
                        <td>{{ $detail->product->name }}</td>
                        <td>${{ number_format($detail->price, 2) }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>${{ number_format($detail->discount ?? 0, 2) }}</td>
                        <td class="text-right">${{ number_format($detail->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totals">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">${{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Impuesto ({{ $sale->tax_rate }}%):</strong></td>
                    <td class="text-right">${{ number_format($sale->tax, 2) }}</td>
                </tr>
                @if($sale->discount > 0)
                <tr>
                    <td><strong>Descuento:</strong></td>
                    <td class="text-right">${{ number_format($sale->discount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td><strong>TOTAL:</strong></td>
                    <td class="text-right">${{ number_format($sale->total, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <div class="payment-info">
            <p><strong>Método de Pago:</strong> {{ ucfirst($sale->payment_method) }}</p>
            @if($sale->payment_reference)
                <p><strong>Referencia de Pago:</strong> {{ $sale->payment_reference }}</p>
            @endif
        </div>
        
        <div class="footer">
            <p>¡Gracias por su compra!</p>
            <p>Esta factura fue generada automáticamente y es válida sin firma ni sello.</p>
            <p>Para cualquier consulta sobre esta factura, por favor comuníquese con nosotros.</p>
        </div>
        
        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-print"></i> Imprimir Factura
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                Cerrar
            </button>
        </div>
    </div>
</body>
</html>
