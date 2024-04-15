<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    //
    public function index()
    {
        $owners = User::where('role', 'owner')->get();
        // You can pass data to the view if necessary
        return view('manager.owner_manage', compact('owners'));
    }

    public function rental()
    {
        $owners = User::where('role', 'owner')->get();
        $rentals = Rental::with('owner')->get();
        // You can pass data to the view if necessary
        return view('manager.rental_manage', compact('rentals','owners'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'owner_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'rent_price' => 'required|numeric|min:0',
        ]);

        // Create a new Rental instance
        $rental = new Rental();

        $rental->owner_id = $validatedData['owner_id'];
        $rental->start_date = $validatedData['start_date'];
        $rental->due_date = $validatedData['due_date'];
        $rental->rent_price = $validatedData['rent_price'];
        $rental->save();
        dd($rental);
        // Redirect the user to a confirmation page or elsewhere
//        return response()->json(['success' => true, 'message' => 'Rental added successfully']);
    }

    public function toggleStatus(Request $request, User $user)
    {
        $newStatus = $request->input('active', false); // Default to false if not provided
        $user->active = $newStatus;
        $user->save();

        return response()->json(['message' => 'Owner status updated successfully.']);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        // Find the owner by ID
        $owner = User::findOrFail($id);

        // Update the owner's name and email
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Owner details updated successfully');
    }
}
