<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rental Management') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <div class="mb-4 flex justify-between items-center">
            <!-- Button to trigger modal -->
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" data-toggle="modal" data-target="#addRentalModal">
                Add Rental
            </button>
        </div>
        <div class="mb-4 flex justify-between items-center">
            <table class="w-full text-xs text-left text-black overflow-x-auto shadow-lg">
                <thead class="text-xs text-black-700 uppercase border">
                <tr>
                    <th class="px-4 py-2 ">Owner</th>
                    <th class="px-4 py-2 ">Rent Price</th>
                    <th class="px-4 py-2 ">Start Date</th>
                    <th class="px-4 py-2 ">Due Date</th>
                    <th class="px-4 py-2 ">Paid This Month</th>
                    <th class="px-4 py-2 ">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($rentals as $rental)
                    <tr>
                        <td class="px-4 py-2 ">{{ $rental->owner->name }}</td>
                        <td class="px-4 py-2 ">₱{{ number_format($rental->rent_price, 2) }}</td>
                        <td class="px-4 py-2 ">{{ $rental->start_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 ">{{ $rental->due_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 ">{{ $rental->paid_for_this_month ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-2 ">
                            <!-- Dropdown for managing rental -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="manageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Manage Rental
                                </button>
                                <div class="dropdown-menu" aria-labelledby="manageDropdown">
                                    <button type="button" class="btn btn-primary dropdown-item" onclick="manageRental()">Manage</button>
                                    <button type="button" class="btn btn-primary dropdown-item" onclick="confirmMarkAsPaid({{ $rental->id }})">Mark as Paid<</button>
                                    <button
                                        class="btn btn-primary dropdown-item"
                                        onclick="deleteRental({{ $rental->id }})">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6">
                            <div class="text-center p-5">
                                No rentals found.
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="addRentalModal" tabindex="-1" role="dialog" aria-labelledby="addRentalModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRentalModalLabel">Add Rental</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form for adding rental -->
                    <form action="{{ route('rentals.store') }}" method="POST">
                        @csrf
                        <!-- Owner selection -->
                        <div class="form-group">
                            <label for="owner_id">Owner:</label>
                            <select class="form-control" id="owner_id" name="owner_id">
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Start date -->
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <!-- End date -->
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <!-- Rent price -->
                        <div class="form-group">
                            <label for="rent_price">Rent Price:</label>
                            <input type="text" class="form-control" id="rent_price" name="rent_price">
                        </div>
                        <!-- Submit button -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("start_date").setAttribute('min', today);
            document.getElementById("end_date").setAttribute('min', today);
        });

        function confirmMarkAsPaid(userId) {
            if (confirm("Are you sure you want to mark this rental as paid?")) {
                // Send a POST request using Axios
                axios.post("/manager/rentals/" + userId + "/mark-as-paid")
                    .then(function (response) {
                        console.log(response.data);
                        alert("Rental marked as paid.");
                        location.reload();
                    })
                    .catch(function (error) {
                        // Handle error response
                        console.error(error);
                        alert("An error occurred while marking the rental as paid.");
                    });
            } else {
            }
        }
    </script>
</x-app-layout>
