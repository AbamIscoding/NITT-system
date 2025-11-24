<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Models\Invoice;
use App\Models\Schedule;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
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

    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    Route::get('/invoices', [InvoiceController::class, 'index'])
        ->name('invoices.index');

    Route::get('/invoices/create', [InvoiceController::class, 'create'])
        ->name('invoices.create');

    Route::post('/invoices', [InvoiceController::class, 'store'])
        ->name('invoices.store');

    Route::get('/schedules', [ScheduleController::class, 'index'])
        ->name('schedules.index');

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');

    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])
        ->name('invoices.send');

});

// Route::get('/invoices', [InvoiceController::class, 'index'])
//     ->middleware('auth')
//     ->name('invoices.index');

Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])
    ->middleware('auth')
    ->name('invoices.edit');

Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])
    ->middleware('auth')
    ->name('invoices.update');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
});

Route::get('/dashboard', function () {

    $today = Carbon::today();
    $startOfWeek = Carbon::now()->startOfWeek();
    $endOfWeek   = Carbon::now()->endOfWeek();
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth   = Carbon::now()->endOfMonth();

    // Today’s arrivals
    $todaysArrivals = Schedule::whereDate('arrival_date', $today)->get();

    // This week’s arrivals
    $weeksArrivals = Schedule::whereBetween('arrival_date', [
        $startOfWeek, $endOfWeek
    ])->orderBy('arrival_date')->get();

    // This month’s arrivals
    $monthsArrivals = Schedule::whereBetween('arrival_date', [
        $startOfMonth, $endOfMonth
    ])->orderBy('arrival_date')->get();

    return view('dashboard', [
        'todaysArrivals' => $todaysArrivals,
        'weeksArrivals' => $weeksArrivals,
        'monthsArrivals' => $monthsArrivals,
    ]);
})->middleware(['auth'])->name('dashboard');
