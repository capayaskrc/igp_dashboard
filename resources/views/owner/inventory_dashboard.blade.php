<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Casher Dashboard') }}
        </h2>
    </x-slot>
    <div class="container mx-auto p-8">
        <div class="mb-4 flex justify-between items-center">
            <!-- Button to trigger modal for adding inventory (assuming you'll add a modal) -->
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" data-toggle="modal" data-target="#addInventoryModal">
                Add Inventory
            </button>
        </div>
        <div class="mb-4 flex justify-between items-center">
            <table class="w-full text-xs text-left text-gray-700 overflow-hidden overflow-x-auto shadow-md">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Price</th>
                    <th class="px-4 py-2">Initial Quantity</th>
                    <th class="px-4 py-2">Current Quantity</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($inventories as $inventory)
                    <tr class="hover:bg-gray-100">
                        <td class="px-4 py-2">{{ $inventory->name }}</td>
                        <td class="px-4 py-2">{{ $inventory->description ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $inventory->category }}</td>
                        <td class="px-4 py-2">₱{{ number_format($inventory->price, 2) }}</td>
                        <td class="px-4 py-2">{{ $inventory->initial_quantity }}</td>
                        <td class="px-4 py-2">{{ $inventory->current_quantity }}</td>
                        <td class="px-4 py-2">
                            <!-- Example place for Edit/Delete buttons -->
                            <button class="btn btn-info">Edit</button>
                            <button class="btn btn-danger">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center">
                            No inventory items found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInventoryModalLabel">Add New Inventory Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('inventory.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Initial Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="initial_quantity" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
