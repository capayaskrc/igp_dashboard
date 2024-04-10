<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-8">
        <div class="mb-4 flex justify-between items-center">
            <table class="w-full text-xs text-left text-black overflow-x-auto shadow-lg">
                <thead class="text-xs text-black-700 uppercase border">
                <tr>
                    <th class="px-4 py-2 ">ID</th>
                    <th class="px-4 py-2 ">Name</th>
                    <th class="px-4 py-2 ">Email</th>
                    <th class="px-4 py-2 ">Role</th>
                    <th class="px-4 py-2 ">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="px-4 py-2 ">{{ $user->id }}</td>
                        <td class="px-4 py-2 ">{{ $user->name }}</td>
                        <td class="px-4 py-2 ">{{ $user->email }}</td>
                        <td class="px-4 py-2 ">{{ $user->role }}</td>
                        <td class="px-4 py-2 ">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                            <button
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded {{ $user->active ? 'bg-red-500 hover:bg-red-700' : 'bg-green-500 hover:bg-green-700' }}"
                                onclick="toggleUserStatus({{ $user->id }}, {{ $user->active ? '0' : '1' }})">
                                {{ $user->active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <script>
        function toggleUserStatus(userId, isActive) {
            const url = `/admin/users/${userId}/toggle-status`;
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
