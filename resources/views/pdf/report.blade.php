<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tfoot td {
            border-top: 2px solid #000;
            font-weight: bold;
        }
    </style>
</head>
<body>
<h1>Sales Report from {{ $startDate }} to {{ $endDate }}</h1>
<table>
    <thead>
    <tr>
        <th>Product</th>
        <th>Quantity Sold</th>
        <th>Total Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->product->name ?? 'N/A' }}</td>
            <td>{{ $sale->quantity_sold }}</td>
            <td>P{{ number_format($sale->total_amount, 2) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="2"><strong>Total Amount</strong></td>
        <td><strong>P{{ number_format($totalAmount, 2) }}</strong></td>
    </tr>
    </tbody>
</table>
</body>
</html>
