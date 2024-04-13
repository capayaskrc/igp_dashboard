<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="container mx-auto p-8">
        <div class="mb-4 flex justify-between items-center">
            <table class="w-full text-xs text-left text-black overflow-x-auto shadow-lg">
                <thead class="text-xs text-black-700 uppercase border">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @if ($owners)
                @foreach ($owners as $owner)
                    <tr>
                        <td>{{ $owner->name }}</td>
                        <td>{{ $owner->email }}</td>
                        <td>
                            <a href="#" class="btn btn-info">Edit</a>
                            <a href="#" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">No owners found.</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

</x-app-layout>
