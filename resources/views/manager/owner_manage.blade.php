<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Owner Management') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <div class="mb-4">
            <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Register</a>
        </div>
        <div class="mb-4 flex justify-between items-center">
            <table class="w-full text-xs text-left text-black overflow-x-auto shadow-lg">
                <thead class="text-xs text-black-700 uppercase border">
                <tr>
                    <th class="px-4 py-2 ">Name</th>
                    <th class="px-4 py-2 ">Email</th>
                    <th class="px-4 py-2 ">Actions</th>
                </tr>
                </thead>
                <tbody>
                @if($owners)
                    @foreach ($owners as $owner)
                        <tr>
                            <td class="px-4 py-2 ">{{ $owner->name }}</td>
                            <td class="px-4 py-2 ">{{ $owner->email }}</td>
                            <td class="px-4 py-2 ">
{{--                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" data-toggle="modal" data-target="#editOwnerModal{{ $owner->id }}">Edit</button>--}}
                                <button
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded {{ $owner->active ? 'bg-red-500 hover:bg-red-700' : 'bg-green-500 hover:bg-green-700' }}"
                                    onclick="toggleUserStatus({{ $owner->id }}, {{ $owner->active ? '0' : '1' }})">
                                    {{ $owner->active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="p-6">
                            <div class="text-center p-5">
                                     No owners found.
                            </div>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="editOwnerModal{{ $owner->id }}" tabindex="-1" role="dialog" aria-labelledby="editOwnerModalLabel{{ $owner->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOwnerModalLabel{{ $owner->id }}">Edit Owner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('owners.update', $owner->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $owner->name }}">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $owner->email }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        function toggleUserStatus(userId, isActive) {
            const url = `/manager/${userId}/toggle-status`;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // CSRF token for Laravel

            fetch(url, {
                method: 'PUT', // Or 'PUT', depending on your route definition
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    active: isActive, // Send the desired status as part of the request
                }),
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    location.reload(); // Reload the page to reflect the change
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        }
    </script>
</x-app-layout>
