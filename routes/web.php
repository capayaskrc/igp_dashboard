<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    $user = Auth::user(); // Get the authenticated user

    // Ensure the user is not null
    if (!$user) {
        return abort(403, 'No authenticated user.');
    }

    // Check user role and return appropriate view
    switch ($user->role) {
        case 'admin':
            return view('admin.admin_dashboard');
        case 'manager':
            return view('manager.manager_dashboard');
        case 'owner':
            return view('owner.owner_dashboard');
        default:
            return abort(403, 'Unauthorized access'); // Or handle another way
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/admin/user_manage', [AdminController::class, 'index'])->name('user.manage');
Route::put('/admin/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.status');

Route::get('/manager/dashboard', [DashboardController::class, 'manager'])->name('manager.dashboard');
Route::get('/manager/owner_manage', [ManagerController::class, 'index'])->name('owner.manage');
Route::get('/manager/rental_manage', [ManagerController::class, 'rental'])->name('rental.manage');
Route::post('/manager/rentals', [ManagerController::class, 'store'])->name('rentals.store');
Route::put('/manager/{user}/toggle-status', [ManagerController::class, 'toggleStatus'])->name('owner.status');
Route::post('/manager/owner-update/{id}', [ManagerController::class, 'update'])->name('owners.update');
Route::get('/manager/manage/rentals', [RentalController::class, 'index'])->name('rentals.view');

Route::get('/owner/dashboard', [DashboardController::class, 'owner'])->name('owner.dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
