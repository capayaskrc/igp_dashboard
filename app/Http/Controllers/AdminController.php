<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    //
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        // Retrieve all users
        $users = User::all();
        return view('admin.user_manage', ['users' => $users]);
    }


    public function toggleStatus(Request $request, User $user)
    {
        $newStatus = $request->input('active', false); // Default to false if not provided
        $user->active = $newStatus;
        $user->save();

        return response()->json(['message' => 'User status updated successfully.']);
    }

}
