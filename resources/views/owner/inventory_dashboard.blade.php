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
            <table class="w-full text-xs text-left text-gray-700 overflow-x-auto shadow-md">
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
                        <td class="px-4 py-2 ">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="manageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Manage Rental
                                </button>
                                <div class="dropdown-menu" aria-labelledby="manageDropdown">
                                    <button type="button" class="btn btn-primary dropdown-item" data-toggle="modal" data-target="#restockModal">Restock</button>
                                    <button type="button" class="btn btn-primary dropdown-item" onclick="showRemoveStockModal({{ $inventory->id }}, '{{ $inventory->name }}', {{ $inventory->current_quantity }})">Remove Stock</button>
                                </div>
                            </div>
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

    <div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restockModalLabel">Restock Inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventory.restock') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Display item name and price -->
                        <div class="form-group">
                            <label for="itemName">Item Name:</label>
                            <input type="text" class="form-control" id="itemName" name="itemName" value="{{ $inventory->name }}" disabled>
                            <input type="hidden" id="itemName" name="itemName" value="{{ $inventory->name }}">
                        </div>
                        <div class="form-group">
                            <label for="itemPrice">Price:</label>
                            <input type="text" class="form-control" id="itemPrice" name="itemPrice" value="₱{{ number_format($inventory->price, 2) }}" disabled>
                        </div>

                        <!-- Allow editing current quantity (must be larger than initial quantity) -->
                        <div class="form-group">
                            <label for="currentQuantity">Current Quantity:</label>
                            <input type="number" class="form-control" id="currentQuantity" name="currentQuantity" value="{{ $inventory->current_quantity }}" min="0" required>
                            <small class="text-danger">The current quantity must be larger than the initial quantity.</small>
                        </div>

                        <!-- Allow toggling initial quantity editing -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="editInitialQuantityCheckbox">
                            <label class="form-check-label" for="editInitialQuantityCheckbox">Edit Initial Quantity</label>
                        </div>

                        <!-- Alert for confirming initial quantity editing -->
                        <div class="alert alert-warning" role="alert" id="editInitialQuantityAlert" style="display: none;">
                           You are now editing the initial quantity
                        </div>

                        <!-- Allow editing initial quantity -->
                        <div class="form-group" id="initialQuantityFormGroup" style="display: none;">
                            <label for="initialQuantity">Initial Quantity:</label>
                            <input type="number" class="form-control" id="initialQuantity" name="initialQuantity" value="{{ $inventory->initial_quantity }}" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Restock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmRemoveStockModal" tabindex="-1" aria-labelledby="confirmRemoveStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmRemoveStockModalLabel">Confirm Stock Removal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove <span id="removeStockQuantity"></span> stocks from <span id="removeStockItemName"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="removeStockForm" method="POST">
                        @csrf
                        <input type="hidden" name="quantityToRemove" id="quantityToRemove">
                        <button type="submit" class="btn btn-danger">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle the display of the initial quantity form group
        function toggleInitialQuantityEditing() {
            const initialQuantityFormGroup = document.getElementById('initialQuantityFormGroup');
            const editInitialQuantityCheckbox = document.getElementById('editInitialQuantityCheckbox');
            const editInitialQuantityAlert = document.getElementById('editInitialQuantityAlert');

            if (editInitialQuantityCheckbox.checked) {
                initialQuantityFormGroup.style.display = 'block';
                editInitialQuantityAlert.style.display = 'block';
            } else {
                initialQuantityFormGroup.style.display = 'none';
                editInitialQuantityAlert.style.display = 'none';
            }
        }

        // Event listener for the checkbox change event
        document.getElementById('editInitialQuantityCheckbox').addEventListener('change', toggleInitialQuantityEditing);

        function showRemoveStockModal(itemId, itemName, currentQuantity) {
            $('#removeStockItemName').text(itemName);
            $('#removeStockQuantity').text(currentQuantity);
            $('#quantityToRemove').val('');
            $('#confirmRemoveStockModal').modal('show');
            $('#removeStockForm').attr('action', '/owner/dashboard/inventory/' + itemId + '/remove');
        }
    </script>

</x-app-layout>
