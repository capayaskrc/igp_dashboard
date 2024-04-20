<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
//        dd($request);
            // Create a new rental instance
            $rental = new Rental();
            $rental->owner_id = $request->owner_id;
            $rental->start_date = $request->start_date;
            $rental->due_date = $request->end_date;
            $rental->rent_price = $request->rent_price;
            $rental->save();

            // Return success response
        return redirect()->back()->with('success', 'Rental added successfully');
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

    public function statistical()
    {
        // Get the count of rentals that are paid
        $paidCount = Rental::where('paid_for_this_month', true)->count();

        // Get the total rent price of all paid rentals
        $totalIncome = Rental::where('paid_for_this_month', true)->sum('rent_price');

        // Get the number of unique owners who have paid
        $uniqueOwnersPaidCount = Rental::where('paid_for_this_month', true)->distinct('owner_id')->count('owner_id');

        // Get the count of rentals that are unpaid
        $unpaidCount = Rental::where('paid_for_this_month', false)->count();

        // Get the total potential income from unpaid rentals
        $potentialIncome = Rental::where('paid_for_this_month', false)->sum('rent_price');

        $monthlyIncomeData = Rental::where('paid_for_this_month', true)
            ->whereYear('start_date', Carbon::now()->year)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->start_date)->format('F');
            })
            ->map(function($month) {
                return $month->sum('rent_price');
            });

        // Return the statistical data to the view
        return view('manager.statistical_manage', [
            'paidCount' => $paidCount,
            'totalIncome' => $totalIncome,
            'uniqueOwnersPaidCount' => $uniqueOwnersPaidCount,
            'unpaidCount' => $unpaidCount,
            'potentialIncome' => $potentialIncome,
            'monthlyIncomeData' => $monthlyIncomeData
        ]);
    }

}
