<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    //
    function casher()
    {
        return view('owner.casher_dashboard');
    }

    public function inventory()
    {
        // Get the current authenticated user's ID
        $userId = auth()->id();

        // Fetch all inventory items belonging to the logged-in user
        $inventories = Inventory::where('user_id', $userId)->get();

        // Return the view with the inventory data
        return view('owner.inventory_dashboard', compact('inventories'));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'initial_quantity' => 'required|integer|min:1'
        ]);

        // Create the new inventory item using the validated data
        $inventory = new Inventory();
        $inventory->user_id = auth()->id();  // Assuming the user needs to be authenticated to add inventory
        $inventory->name = $validatedData['name'];
        $inventory->description = $validatedData['description'];
        $inventory->category = $validatedData['category'];
        $inventory->price = $validatedData['price'];
        $inventory->initial_quantity = $validatedData['initial_quantity'];
        $inventory->current_quantity = $validatedData['initial_quantity']; // Initialize current quantity to the initial quantity
        $inventory->save();

        // Redirect back to the inventory list with a success message
        return redirect()->back()->with('success', 'Inventory item added successfully!');
    }
}
