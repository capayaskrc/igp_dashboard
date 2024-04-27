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
                    <th class="px-4 py-2 ">Rent Name</th>
                    <th class="px-4 py-2 ">Category</th>
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
                        @if ($rental->category)
                            <td class="px-4 py-2">{{ $rental->category->rent_name }}</td>
                            <td class="px-4 py-2 ">  {{ $rental->category->name }}</td>
                        @else
                            <td class="px-4 py-2 ">  No rent</td>
                            <td class="px-4 py-2 ">  no category</td>
                        @endif
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
                                    <button type="button" class="btn btn-primary dropdown-item" onclick="confirmMarkAsPaid({{ $rental->id }})">Mark as Paid</button>
                                    <button class="btn btn-primary dropdown-item" onclick="deleteRental({{ $rental->id }})">
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
                        <div class="form-group" id="ownerSelection">
                            <label for="owner_id">Owner:</label>
                            <select class="form-control" id="owner_id" name="owner_id">
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Guest name input (initially hidden) -->
                        <div class="form-group" id="guestNameInput" style="display: none;">
                            <label for="guest_name">Guest Name:</label>
                            <input type="text" class="form-control" id="guest_name" name="guest_name">
                        </div>

                        <!-- Radio button to toggle between owner and guest -->
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="owner_guest_toggle" id="ownerToggle" value="owner" checked>
                            <label class="form-check-label" for="ownerToggle">
                                Owner
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="owner_guest_toggle" id="guestToggle" value="guest">
                            <label class="form-check-label" for="guestToggle">
                                Guest
                            </label>
                        </div>

                        <!-- Category selection -->
                        <div class="form-group">
                            <label for="category_name">Category:</label>
                            <select class="form-control" id="category_name" name="category_name">
                                @foreach($groupedCategories as $categoryName => $rentNames)
                                    <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rental item -->
                        <div class="form-group">
                            <label for="rent_name">Rent Name:</label>
                            <select class="form-control" id="rent_name" name="rent_name">
                                <!-- This option will be dynamically populated based on the selected category -->
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
        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                if ($(this).attr('id') == 'guestToggle') {
                    $('#ownerSelection').hide();
                    $('#guestNameInput').show();
                } else {
                    $('#ownerSelection').show();
                    $('#guestNameInput').hide();
                }
            });
        });
        // Get the category select dropdown
        const categorySelect = document.getElementById('category_name');

        // Get the rent name select dropdown
        const rentNameSelect = document.getElementById('rent_name');

        // Function to fetch and populate rent names based on the selected category
        function populateRentNames() {
            // Clear existing options
            rentNameSelect.innerHTML = '';

            // Get the selected category value
            const selectedCategory = categorySelect.value;

            // Find the rent names associated with the selected category
            const rentNames = {!! json_encode($groupedCategories) !!}[selectedCategory];

            // Populate rent name options
            if (rentNames && rentNames.length > 0) {
                rentNames.forEach(rentName => {
                    const option = document.createElement('option');
                    option.value = rentName;
                    option.textContent = rentName;
                    rentNameSelect.appendChild(option);
                });
            } else {
                // Add a default option if no rent names found
                const option = document.createElement('option');
                option.textContent = 'No rent names found';
                rentNameSelect.appendChild(option);
            }
        }

        // Add event listener for category select dropdown change
        categorySelect.addEventListener('change', populateRentNames);

        // Initial population of rent names based on default selected category
        populateRentNames();
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

        function deleteRental(rentalId) {
            if (confirm('Are you sure you want to delete this rental?')) {
                fetch(`/manager/rental/delete/${rentalId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            console.log(response);
                            location.reload();
                        } else {
                            // Handle error response
                            console.error('Failed to delete rental');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        }
    </script>
</x-app-layout>
