<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Owner Statistics Management') }}
        </h2>
    </x-slot>

    <div class="container mt-4">
        <!-- Dropdown for selecting statistical data -->
        <select id="statisticalData" class="border border-gray-300 rounded-md py-2 px-4 mb-4">
            <option value="income">Income</option>
            <!-- Add more options as needed for different statistical data -->
        </select>

        <button id="generateReportBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
            Generate Report
        </button>
    </div>

    <div class="container mx-auto p-8 w-2/4">
        <!-- Canvas for displaying the chart -->
        <canvas id="monthlyIncomeChart" width="400" height="200"></canvas>
    </div>

    <!-- JavaScript to handle chart generation -->
    <script>
        // Dummy data for demonstration
        const ownersData = [
            { name: 'Owner 1', income: 5000 },
            { name: 'Owner 2', income: 7000 },
            { name: 'Owner 3', income: 3000 },
        ];

        // Function to generate chart based on selected statistical data
        function generateChart(dataType) {
            const ctx = document.getElementById('monthlyIncomeChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ownersData.map(owner => owner.name),
                    datasets: [{
                        label: dataType.charAt(0).toUpperCase() + dataType.slice(1), // Capitalize the first letter
                        data: ownersData.map(owner => owner[dataType]),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
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
        }

        // Event listener for button click to generate report
        document.getElementById('generateReportBtn').addEventListener('click', () => {
            const selectedOption = document.getElementById('statisticalData').value;
            generateChart(selectedOption);
        });

        // Generate chart with default option (income) on page load
        generateChart('income');
    </script>
</x-app-layout>
