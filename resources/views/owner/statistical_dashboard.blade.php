<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistical Data Dashboard') }}
        </h2>
    </x-slot>
    <div class="container mx-auto py-6">
        <div class="flex flex-wrap gap-4">
            <!-- Card for Daily Income -->
            <div class="bg-white rounded-lg shadow-md p-4 flex-grow">
                <h3 class="text-lg font-semibold mb-2">Daily Income</h3>
                <p class="text-3xl font-bold text-green-600">P {{ number_format($dailyIncome, 2) }}</p>
            </div>

            <!-- Card for Monthly Income -->
            <div class="bg-white rounded-lg shadow-md p-4 flex-grow" style="min-height: 150px;">
                <h3 class="text-lg font-semibold mb-2">Monthly Income</h3>
                <p class="text-3xl font-bold text-green-600">P {{ number_format($monthlyIncome, 2) }}</p>
            </div>

            <!-- Card for Yearly Income -->
            <div class="bg-white rounded-lg shadow-md p-4 flex-grow" style="min-height: 150px;">
                <h3 class="text-lg font-semibold mb-2">Yearly Income</h3>
                <p class="text-3xl font-bold text-green-600">P {{ number_format($yearlyIncome, 2) }}</p>
            </div>
        </div>
        <!-- Card for Popular Food Items -->
        <div class="bg-white rounded-lg shadow-md p-4 flex-grow">
            <h2 class="text-2xl font-semibold mb-4">Popular Food Items</h2>
            <div class="grid grid-cols-1 gap-2">
                @foreach ($popularFoods as $food)
                    <div class="border p-2 rounded-md">
                        <p class="text-lg font-semibold">{{ $food->name }}</p>
                        <p class="text-gray-600">Quantity Sold: {{ $food->total_quantity }}</p>
                    </div>
                @endforeach
            </div>
        </div>


        <!-- Graph Section -->
        <div class="flex flex-col md:flex-row gap-4 mt-6">
            <!-- Graphs Section -->
            <div class="flex flex-col md:flex-row gap-4 flex-grow">
                <div class="card p-4 flex-grow">
                    <h3 class="text-lg font-semibold mb-2">Weekly Income</h3>
                    <canvas id="weeklyIncomeChart" width="400" height="200"></canvas>
                </div>
                <div class="card p-4 flex-grow">
                    <h3 class="text-lg font-semibold mb-2">Monthly Income</h3>
                    <canvas id="monthlyIncomeChart" width="400" height="200"></canvas>
                </div>
                <div class="card p-4 flex-grow">
                    <h3 class="text-lg font-semibold mb-2">Yearly Income</h3>
                    <canvas id="yearlyIncomeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

    </div>
    <script>
        // Get the context of the canvas element
        var weeklyIncomeCtx = document.getElementById('weeklyIncomeChart').getContext('2d');
        var monthlyIncomeCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
        var yearlyIncomeCtx = document.getElementById('yearlyIncomeChart').getContext('2d');

        // Format date labels
        var monthlyIncomeLabels = {!! json_encode(array_map(function($key) { return Carbon\Carbon::parse($key)->format('Y-m'); }, array_keys($monthlyIncomePast3Months->toArray()))) !!};

        var currentDate = new Date();
        var currentWeek = Math.ceil((((currentDate - new Date(currentDate.getFullYear(), 0, 1)) / 86400000) + 1) / 7);

        // Adjust week labels based on the current week
        var weekLabels = [];
        for (var i = 0; i < 4; i++) {
            weekLabels.push('Week ' + ((currentWeek + i - 1) % 4 + 1));
        }
        // Format date labels for weekly income chart
        var weeklyIncomeLabels = weekLabels;
        // Replace missing values with zero
        var weeklyIncomeData = {!! json_encode(array_values($WeeklyIncomePast4Weeks)) !!};
        console.log(weeklyIncomeData);
        for (var i = 0; i < weeklyIncomeData.length; i++) {
            if (weeklyIncomeData[i] === null || weeklyIncomeData[i] === undefined) {
                weeklyIncomeData[i] = 0;
            }
        }

        // Create the weekly income chart
        var weeklyIncomeChart = new Chart(weeklyIncomeCtx, {
            type: 'line',
            data: {
                labels: weeklyIncomeLabels,
                datasets: [{
                    label: 'Weekly Income',
                    data: weeklyIncomeData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: false
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

        // Create the monthly income chart
        var monthlyIncomeChart = new Chart(monthlyIncomeCtx, {
            type: 'line',
            data: {
                labels: monthlyIncomeLabels,
                datasets: [{
                    label: 'Monthly Income',
                    data: {!! json_encode(array_values($monthlyIncomePast3Months->toArray())) !!},
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: false
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

        // Create the yearly income chart
        var yearlyIncomeChart = new Chart(yearlyIncomeCtx, {
            type: 'line',
            data: {
                labels: ['Yearly Income'],
                datasets: [{
                    label: 'Yearly Income',
                    data: {!! json_encode($yearlyIncomeData) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
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
