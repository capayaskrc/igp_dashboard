<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    //
    public function index()
    {
        $rentals = Rental::with('owner')->get();
        return view('manager.rental_manage', compact('rentals'));
    }
}
