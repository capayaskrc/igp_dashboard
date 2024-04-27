<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function restock(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'itemName' => 'required|string|max:255',
            'currentQuantity' => 'required|integer|min:0',
            'initialQuantity' => 'nullable|integer|min:0',
        ]);

        // Find the inventory item by name belonging to the authenticated user
        $inventory = Inventory::where('name', $validatedData['itemName'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $quantityChanged = false;

        // Check if the initial quantity has been modified
        if (isset($validatedData['initialQuantity']) && $validatedData['initialQuantity'] !== $inventory->initial_quantity) {
            $inventory->initial_quantity = $validatedData['initialQuantity'];
            $quantityChanged = true;
        }

        // Check if the current quantity is different from the existing current quantity
        if ($validatedData['currentQuantity'] !== $inventory->current_quantity) {
            $inventory->current_quantity = $validatedData['currentQuantity'];
            $quantityChanged = true;
        }

        // Save the changes if the quantity was modified
        if ($quantityChanged) {
            $inventory->save();
            // Redirect back with success message
            return redirect()->back()->with('success', 'Inventory restocked successfully.');
        } else {
            // Redirect back with error message
            return redirect()->back()->with('error', 'No changes were made to the inventory quantity.');
        }
    }


    public function removeStock(Request $request, $id)
    {
        // Find the inventory item by ID belonging to the authenticated user
        $inventory = Inventory::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Validate the request data
        $request->validate([
            'quantityToRemove' => 'required|integer|min:1|max:' . $inventory->current_quantity,
        ]);

        // Update the current quantity
        $inventory->current_quantity -= $request->quantityToRemove;
        $inventory->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Stock removed successfully.');
    }

}
