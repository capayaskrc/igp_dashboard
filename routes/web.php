<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/admin/user_manage', [AdminController::class, 'index'])->name('user.manage');
Route::put('/admin/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.status');

Route::get('/manager/dashboard', [DashboardController::class, 'manager'])->name('manager.dashboard');
Route::get('/manager/owner_manage', [ManagerController::class, 'index'])->name('owner.manage');
Route::get('/manager/rental_manage', [ManagerController::class, 'rental'])->name('rental.manage');
Route::put('/manager/{user}/toggle-status', [ManagerController::class, 'toggleStatus'])->name('owner.status');
Route::post('/manager/owner-update/{id}', [ManagerController::class, 'update'])->name('owners.update');


Route::get('/owner/dashboard', [DashboardController::class, 'owner'])->name('owner.dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
