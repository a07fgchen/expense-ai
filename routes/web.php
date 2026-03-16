<?php

use App\Http\Controllers\ExpenseController;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('expenses', function () {
        return Inertia::render('Expenses', [
            'expenses' => Expense::where('user_id', Auth::id())
                ->latest()
                ->take(10)
                ->get()
        ]);
    })->name('expenses');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses');
});

require __DIR__ . '/settings.php';
