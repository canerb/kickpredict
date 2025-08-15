<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Welcome;
use App\Livewire\ManageMatches;
use App\Livewire\ManageResults;
use App\Livewire\AdminLogin;

// Public routes
Route::get('/', Welcome::class)->name('home');

// Special admin login route (no visible link)
Route::get('/admin-access', AdminLogin::class)->name('admin.login');

// Admin logout
Route::post('/admin/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('home')->with('success', 'Logged out successfully.');
})->name('admin.logout');

// Protected admin routes
Route::middleware(['admin'])->group(function () {
    Route::get('/manage-matches', ManageMatches::class)->name('manage-matches');
    Route::get('/manage-results', ManageResults::class)->name('manage-results');
});
