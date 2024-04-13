<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    //
    public function index()
    {
        $owners = User::where('role', 'owner')->first();

        // Check if $owners is null, if so, set it to an empty array
        if (!$owners) {
            $owners = [];
        } else {
            $owners = $owners->users;
        }

        // You can pass data to the view if necessary
        return view('manager.owner_manage', compact('owners'));
    }
}
