<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Report</title>
    <style>
        /* Add your CSS styles here */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .total {
            font-weight: bold;
            color: #4CAF50;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<!-- Table for Paid Owners -->
<h2>Paid Owners This Month</h2>
<table>
    <thead>
    <tr>
        <th>Rental ID</th>
        <th>Owner Name</th>
        <th>Rental Name</th>
        <th>Rent Price</th>
        <!-- Add more columns as needed -->
    </tr>
    </thead>
    <tbody>
    @foreach ($paidRentals as $rental)
        <tr>
            <td>{{ $rental->id }}</td>
            <td>{{ $rental->owner_name }}</td>
            <td>{{ $rental->rental_name }}</td>
            <td>P {{ $rental->rent_price }}</td>
            <!-- Add more columns as needed -->
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Table for Unpaid Owners -->
<h2>Unpaid Owners This Month</h2>
<table>
    <thead>
    <tr>
        <th>Rental ID</th>
        <th>Owner Name</th>
        <th>Rental Name</th>
        <th>Rent Price</th>
        <!-- Add more columns as needed -->
    </tr>
    </thead>
    <tbody>
    @foreach ($unpaidRentals as $rental)
        <tr>
            <td>{{ $rental->id }}</td>
            <td>{{ $rental->owner_name }}</td>
            <td>{{ $rental->rental_name }}</td>
            <td>P {{ $rental->rent_price }}</td>
            <!-- Add more columns as needed -->
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Total income section -->
<div class="total">
    <p>Total Income from Paid Rentals: P {{ $totalIncomePaid }}</p>
    <p>Total Potential Income from Unpaid Rentals: P {{ $totalPotentialIncomeUnpaid }}</p>
</div>

</body>
</html>
