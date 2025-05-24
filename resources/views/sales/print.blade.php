<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta #{{ $sale->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --dark-color: #333;
            --light-color: #f8f9fa;
            --border-color: #ddd;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            width: 80mm; /* Ancho estándar para tickets */
            background-color: white;
        }
        
        .ticket-container {
            padding: 10px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .ticket-header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px dashed var(--border-color);
            margin-bottom: 10px;
        }
        
        .logo {
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: var(--primary-color);
            margin: 0;
            padding: 5px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .store-info {
            font-size: 11px;
            color: #666;
            margin: 5px 0;
        }
        
        .ticket-info {
            background-color: var(--light-color);
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 15px;
        }
        
        .ticket-info p {
            margin: 3px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .ticket-info p span:first-child {
            font-weight: 500;
        }
        
        .divider {
            height: 1px;
            background: repeating-linear-gradient(
                to right,
                var(--border-color),
                var(--border-color) 5px,
                transparent 5px,
                transparent 10px
            );
            margin: 10px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        thead {
            background-color: var(--light-color);
        }
        
        th {
            font-weight: 500;
            text-transform: uppercase;
            font-size: 10px;
            padding: 5px 3px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        td {
            padding: 6px 3px;
            border-bottom: 1px solid var(--border-color);
        }
        
        td:nth-child(2), th:nth-child(2) {
            text-align: center;
        }
        
        td:nth-child(3), th:nth-child(3),
        td:nth-child(4), th:nth-child(4) {
            text-align: right;
        }
        
        .product-name {
            max-width: 30mm;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .totals {
            margin-top: 10px;
            text-align: right;
        }
        
        .totals p {
            margin: 3px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .total-line {
            font-weight: 700;
            font-size: 14px;
            padding: 5px 0;
            border-top: 1px solid var(--dark-color);
            border-bottom: 1px solid var(--dark-color);
            margin-top: 5px;
        }
        
        .payment-method {
            margin: 10px 0;
            padding: 5px;
            background-color: var(--light-color);
            border-radius: 4px;
            text-align: center;
            font-weight: 500;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            padding-top: 10px;
            border-top: 1px dashed var(--border-color);
        }
        
        .footer p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .thank-you {
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            font-size: 14px;
            margin: 10px 0;
            color: var(--primary-color);
        }
        
        .website {
            font-size: 10px;
            color: #666;
        }
        
        .barcode {
            margin: 10px 0;
            text-align: center;
        }
        
        .barcode img {
            max-width: 90%;
            height: auto;
        }
        
        .no-print {
            margin-top: 20px;
            text-align: center;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background: var(--primary-color);
            color: white;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            margin: 0 5px;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: var(--secondary-color);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        @media print {
            body {
                width: 100%;
            }
            .ticket-container {
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1 class="logo">SOFTWARE PARA PC</h1>
            <p class="store-info">Av. Principal #123, Ciudad</p>
            <p class="store-info">Tel: (123) 456-7890</p>
        </div>
        
        <div class="ticket-info">
            <p>
                <span>Ticket:</span>
                <span>{{ $sale->invoice_number }}</span>
            </p>
            <p>
                <span>Fecha:</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            </p>
            <p>
                <span>Vendedor:</span>
                <span>{{ $sale->user->name ?? 'N/A' }}</span>
            </p>
            @if($sale->customer)
            <p>
                <span>Cliente:</span>
                <span>{{ $sale->customer->name }}</span>
            </p>
            @endif
        </div>
        
        <div class="divider"></div>
        
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant</th>
                    <th>Precio</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleDetails as $detail)
                <tr>
                    <td class="product-name">{{ $detail->product->name ?? 'Producto desconocido' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>${{ number_format($detail->price, 2) }}</td>
                    <td>${{ number_format($detail->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totals">
            <p>
                <span>Subtotal:</span>
                <span>${{ number_format($sale->subtotal, 2) }}</span>
            </p>
            <p>
                <span>Impuesto ({{ $sale->tax_rate ?? 19 }}%):</span>
                <span>${{ number_format($sale->tax, 2) }}</span>
            </p>
            <p class="total-line">
                <span>TOTAL:</span>
                <span>${{ number_format($sale->total, 2) }}</span>
            </p>
        </div>
        
        <div class="payment-method">
            Método de Pago: {{ ucfirst($sale->payment_method ?? 'Efectivo') }}
        </div>
        
        <div class="barcode">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOAAAABACAIAAAADh1nxAAAACXBIWXMAAA7EAAAOxAGVKw4bAAADR0lEQVR4nO3c22sTQRjG4beb02aTpmlNUm2r1ioieLA0YL0QRYWKiBfqnX+1P9ILQaoo1YKCKFYvbG3Fmsa0TbObHdbEWIoYcsisJvt73jBk9pvZfdnM7LJGJQiCPwAGTUK3AMi+UvRANJo1GARBEIiEb3wZCAB5JmQkaZVQIwCJ8QASMA9gwTyABcwDWMA8gAXMA1jAPIAFzANYwDyABcwDWMA8gAXMA1jAPIAFzANYwDyABcwDWMA8gAXMA1gwM9MFIPOKxd8iMf9DIhUAqmwmzxJu1+9VK6kAUFWr9bnZ3UjMcXszTOtcuO4c/BZdolJoLCy82tzcc/1ZV9dEyD7t+ub4+JViMaqVt7t2PPLnb4xOXO3u/hyJnew/22r3ZHPzycmRc6Ojo/PzF2u13+uMjtNVKs0tLj5vNqPtXSjMbGw8bjb3Gg1PVSPtpAg2BwYe1uvTIrTtfmWs0Wj/4NCVcvl9e/vA0YWFi0bjV6TdsRtVz3vi+ZORdmeOn/f9ByIl9p8lSXZ2rvj+e5GIRr1lDSwvX221FttO+/p0pNX6EI1aNl9SWy0n0qXtPXO9x6bZG2k1jbPVamd7jTa2Rr2OWdu76+lpiB1G08J7rNGDgpHnGiPsmmYsKQeGMRRpdVjPRsM9KEGvSCRjq6iIWNaSiEuiS8QNY8pxvogsxvaQpVKvzM52u/CQzpD/SdJWu+xEImk0FWVLZc1EuiQxoeQcETPNMcdZECnFdyLTnIxYWv0pSZLhLn7++wLVeCAa3U8lEb3fQm1fVZKkb3Fvv+96qrpnW89ENnb63L3eRZH1eDGu6zWbP7zSx97eHzMz55rNb5FWx+5RLW9QxKuUi+XyO9d93t//fXr6TLX6LNLu2I1aebd7aqXyPtLuOJ7vP4q0k5mZ9/3HGxvXPO9Zd/cHkcQPa8kxzX7DGBWRRCJ6ZV2W5UT4vLTtYc9bCPeu+/6TiBvbNI64rjc7e7tafRFpZxpTleR1sI/T/2dmkrKnABkWbC8pWs4hm5t3dCsA+hR+KwI0YB7AgnkAC5gHsIB5AAuYB7CAeQALmAewgHkAC5gHsIB5AAuYB7CAeQALmAewgHkAC5gHsIB5AAuYB7CAeQALfwFEe2bgizHwJwAAAABJRU5ErkJggg==" alt="Barcode">
        </div>
        
        <div class="footer">
            <p class="thank-you">¡GRACIAS POR SU COMPRA!</p>
            <p class="website">www.softwareparapc.com</p>
        </div>
    </div>
    
    <div class="no-print">
        <button class="btn" onclick="window.print();">Imprimir Ticket</button>
        <button class="btn btn-secondary" onclick="window.close();">Cerrar</button>
    </div>
    
    <script>
        // Abrir automáticamente el diálogo de impresión cuando se carga la página
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
