<!DOCTYPE html>
<html>
<head>
    <title>Balance Sheet</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row td {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Balance Sheet</h2>

<table>
    <thead>
    <tr>
        <th>Name of the Renter</th>
        <th>Item that is being rented</th>
        <th>Date rented and until</th>
        <th>Paid</th>
        <th>Unpaid</th>
    </tr>
    </thead>
    <tbody>
    @foreach($balanceSheet as $item)
        <tr>
            <td>{{ $item['Name of the Renter'] }}</td>
            <td>{{ $item['Item that is being rented'] }}</td>
            <td>{{ $item['Date rented and until'] }}</td>
            <td>P {{ $item['Paid'] }}</td>
            <td>P {{ $item['Unpaid'] }}</td>
        </tr>
    @endforeach
    <!-- Total row for paid and unpaid -->
    <tr class="total-row">
        <td colspan="3">Total</td>
        <td>P {{ $totalPaid }}</td>
        <td>P {{ $totalUnpaid }}</td>
    </tr>
    </tbody>
</table>

</body>
</html>
