<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-4 flex items-end">
                    <form method="GET" action="{{ route('income.manage') }}" class="flex space-x-4 items-end">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                            <input type="date" id="date" name="date" value="{{ $date ?? old('date') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-700">End Date:</label>
                            <input type="date" id="endDate" name="endDate" value="{{ $endDate ?? old('endDate') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Fetch Sales
                            </button>
                        </div>
                    </form>
                        <button onclick="window.location='{{ route('generate.pdf', ['startDate' => $date, 'endDate' => $endDate]) }}'" class="inline-flex px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 ml-3">
                            Generate PDF Report
                        </button>

                </div>

                <h3 class="text-lg leading-6 font-medium text-gray-900">Sales from {{ $date }} to {{ $endDate }}</h3>

                <div class="mt-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity Sold
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Amount
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($sales as $sale)
                            <tr>
                                <td class="px-6 py-2  whitespace-nowrap">
                                    {{ $sale->product->name ?? 'N/A' }}
                                </td>
                                <td class="px-6  py-2 whitespace-nowrap">
                                    {{ $sale->quantity_sold }}
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap">
                                    ₱{{ number_format($sale->total_amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="2" class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Income
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ₱{{ number_format($totalIncome, 2) }}
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
