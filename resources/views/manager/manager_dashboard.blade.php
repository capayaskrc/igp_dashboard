<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="container-fluid mt-5 mx-auto max-w-screen-lg">
        <div class="md:col-span-10 mx-0 px-0">
            <div class="md:flex md:justify-between md:items-center md:space-x-4 mt-4">
                <div class="md:w-1/2 mt-4 md:mt-0">
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="p-4">
                            <h5 class="text-xl font-semibold mb-2">Owner Management</h5>
                            <hr class="my-2">
                            <p class="text-sm text-gray-700">Manage new and old owner</p>
                            <a href="{{ route('owner.manage') }}" class="inline-block mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Go to Owner Management</a>
                        </div>
                    </div>
                </div>

                <div class="md:w-1/2 mt-4 md:mt-0">
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="p-4">
                            <h5 class="text-xl font-semibold mb-2">Rental Management</h5>
                            <hr class="my-2">
                            <p class="text-sm text-gray-700">View and Manage your rentals</p>
                            <a href="{{ route('rental.manage') }}" class="inline-block mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Go to Rental Management</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
