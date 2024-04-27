<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistical Management') }}
        </h2>
    </x-slot>

    <div class="container mt-4">
        <button id="generateReportBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
            Generate Report
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

        document.getElementById('generateReportBtn').addEventListener('click', function() {
            // Make a POST request to the Laravel route
            axios.post('{{ route("rental.report") }}')
                .then(function(response) {
                    // Handle the success response, e.g., display a success message
                    alert('PDF report generated successfully!');
                })
                .catch(function(error) {
                    // Handle errors, e.g., display an error message
                    alert('Error generating PDF report: ' + error.message);
                });
        });
    </script>
</x-app-layout>