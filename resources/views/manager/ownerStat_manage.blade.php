<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Owner Statistics Management') }}
        </h2>
    </x-slot>

    <div class="container mt-4">
        <button id="generateReportBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
            Generate Report
        </button>
    </div>

    <div class="container mx-auto p-8 w-2/4">
        <canvas id="monthlyIncomeChart" width="400" height="200"></canvas>
    </div>

</x-app-layout>
