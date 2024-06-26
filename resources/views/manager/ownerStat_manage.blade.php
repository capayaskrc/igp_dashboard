<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Owner Statistics Management') }}
        </h2>
    </x-slot>

    <div class="container mt-4">
        <select id="userSelection" class="border border-gray-300 rounded-md py-2 px-4 mb-4 w-40">
            <option value="0">Select Owner</option>
            @foreach ($owners as $owner)
                <option value="{{ $owner->id }}">{{ $owner->name }}</option>
            @endforeach
        </select>

{{--        <button id="generateReportBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">--}}
{{--            Generate Report--}}
{{--        </button>--}}
    </div>

    <div class="container mx-auto py-6">
        @if ($dailyIncome || $monthlyIncome || $yearlyIncome || $WeeklyIncomePast4Weeks || $monthlyIncomePast5Months || $yearlyIncomeData || $popularFoods)
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
        @else
            <p class="text-center">No statistical data available for the selected user.</p>
        @endif
    </div>
    <script>
        // Get the context of the canvas element
        var weeklyIncomeCtx = document.getElementById('weeklyIncomeChart').getContext('2d');
        var monthlyIncomeCtx = document.getElementById('monthlyIncomeChart').getContext('2d');
        var yearlyIncomeCtx = document.getElementById('yearlyIncomeChart').getContext('2d');
        var monthlyIncomeLabels = {!! json_encode(array_keys($monthlyIncomePast5Months)) !!};
        var monthlyIncomeData = {!! json_encode(array_values($monthlyIncomePast5Months)) !!};
        // Format date labels
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
                    data: monthlyIncomeData,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
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

        // Get the selected user ID from the URL
        const userIdFromUrl = window.location.pathname.split('/').pop();

        // Set the selected option in the select element
        document.getElementById('userSelection').value = userIdFromUrl || '0';

        // Add event listener to the select element
        document.getElementById('userSelection').addEventListener('change', function() {
            // Get the selected user ID
            const userId = this.value || '0';
            // Construct the URL with the selected user ID
            let url = "/manager/manage-stat/owner/" + userId;
            window.location.href = url;
        });
    </script>

</x-app-layout>
