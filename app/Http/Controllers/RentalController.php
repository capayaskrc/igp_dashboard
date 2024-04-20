<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Tests\Database\EloquentRelationshipsTest\Owner;

class RentalController extends Controller
{
    //
    public function index()
    {
        $rentals = Rental::with('owner')->get();
        return view('manager.rental_manage', compact('rentals'));
    }

    public function markAsPaid($id): \Illuminate\Http\RedirectResponse
    {
        $rental = Rental::findOrFail($id);
        if ($rental->id == $id) {
            $rental->paid_for_this_month = true;
            $rental->save();
            return redirect()->back()->with('success', 'Rental marked as paid.');
        } else {
            return redirect()->back()->with('error', 'Owner ID does not match the user ID.');
        }
    }

}
