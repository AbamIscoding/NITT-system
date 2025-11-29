<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

// Route::get('/dashboard', [DashboardController::class, 'index'])
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])   // or ['auth','verified'] if you want
    ->name('dashboard');


    Route::middleware(['auth'])->group(function () {
        Route::redirect('settings', 'settings/profile');

        Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
        Volt::route('settings/password', 'settings.password')->name('user-password.edit');
        Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

        Volt::route('settings/two-factor', 'settings.two-factor')
            ->middleware(
                when(
                    Features::canManageTwoFactorAuthentication()
                        && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                    ['password.confirm'],
                    [],
                ),
            )
            ->name('two-factor.show');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');

        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

        Route::get('/logs', [ActivityLogController::class, 'index'])
            ->name('logs.index');

        Route::get('/register', [RegisteredUserController::class, 'create'])
            ->middleware('can:create-users')
            ->name('register');

        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware('can:create-users');
    });


    Route::middleware(['auth', 'can:manage-users'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

