<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistical Management') }}
        </h2>
    </x-slot>

    <div class="container mt-4">
        <button onclick="window.location='{{ route('rental.report') }}'" class="bg-blue-500 hover:bg-blue-700 text-white mb-4 font-bold py-2 px-4 rounded">
            Generate PDF Report
        </button>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Paid Rentals</h5>
                        <p class="card-text">{{ $paidCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Income</h5>
                        <p class="card-text">₱{{ $totalIncome }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Number of Owners Paid</h5>
                        <p class="card-text">{{ $uniqueOwnersPaidCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Unpaid Rentals</h5>
                        <p class="card-text">{{ $unpaidCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Potential Income from Unpaid Rentals</h5>
                        <p class="card-text">₱{{ $potentialIncome }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container mx-auto p-8 w-2/4">
        <canvas id="monthlyIncomeChart" width="400" height="200"></canvas>
    </div>


    <!-- JavaScript to create the chart -->
    <script>
        const monthlyIncomeData = {!! json_encode($monthlyIncomeData) !!};
        const labels = Object.keys(monthlyIncomeData);
        const data = Object.values(monthlyIncomeData);
        const ctx = document.getElementById('monthlyIncomeChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Income',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                }
            }
        });

    </script>
</x-app-layout>
