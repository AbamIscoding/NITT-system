<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\Invoice;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $today = Carbon::today();
    $startOfWeek  = Carbon::now()->startOfWeek();
    $endOfWeek    = Carbon::now()->endOfWeek();
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth   = Carbon::now()->endOfMonth();

    $todaysArrivals = Schedule::whereDate('arrival_date', $today)->get();

    $weeksArrivals = Schedule::whereBetween('arrival_date', [
        $startOfWeek, $endOfWeek
    ])->orderBy('arrival_date')->get();

    $monthsArrivals = Schedule::whereBetween('arrival_date', [
        $startOfMonth, $endOfMonth
    ])->orderBy('arrival_date')->get();

    $monthlyQuota = 20;

    $closedPaxThisMonth = Invoice::whereBetween('arrival_date', [$startOfMonth, $endOfMonth])
        ->whereIn('status', ['confirmed', 'paid'])
        ->sum('number_of_pax');

    $quotaRemaining = max($monthlyQuota - $closedPaxThisMonth, 0);
    $quotaReached   = $closedPaxThisMonth >= $monthlyQuota;

    $monthsBack = 5;
    $startPeriod = Carbon::now()->copy()->subMonths($monthsBack)->startOfMonth();
    $endPeriod   = Carbon::now()->copy()->endOfMonth();

    $stats = Invoice::selectRaw('DATE_FORMAT(date_issued, "%Y-%m") as ym')
        ->selectRaw('COUNT(*) as total_sent')
        ->selectRaw('SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as total_paid')
        ->whereBetween('date_issued', [$startPeriod, $endPeriod])
        ->groupBy('ym')
        ->orderBy('ym')
        ->get();

    $invoicesByMonthLabels = [];
    $invoicesByMonthSent   = [];
    $invoicesByMonthPaid   = [];

    $cursor = $startPeriod->copy();
    while ($cursor <= $endPeriod) {
        $key = $cursor->format('Y-m');
        $label = $cursor->format('M Y');

        $row = $stats->firstWhere('ym', $key);

        $invoicesByMonthLabels[] = $label;
        $invoicesByMonthSent[]   = $row ? (int) $row->total_sent : 0;
        $invoicesByMonthPaid[]   = $row ? (int) $row->total_paid : 0;

        $cursor->addMonth();
    }

    $statusCounts = Invoice::whereBetween('date_issued', [$startOfMonth, $endOfMonth])
        ->selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

    $statusPaid      = (int) ($statusCounts['paid'] ?? 0);
    $statusPending   = (int) ($statusCounts['pending'] ?? 0);
    $statusCancelled = (int) ($statusCounts['cancelled'] ?? 0);
    $statusConfirmed = (int) ($statusCounts['confirmed'] ?? 0);

    return view('dashboard', [
        'todaysArrivals'         => $todaysArrivals,
        'weeksArrivals'          => $weeksArrivals,
        'monthsArrivals'         => $monthsArrivals,
        'monthlyQuota'           => $monthlyQuota,
        'closedPaxThisMonth'     => $closedPaxThisMonth,
        'quotaRemaining'         => $quotaRemaining,
        'quotaReached'           => $quotaReached,
        'invoicesByMonthLabels'  => $invoicesByMonthLabels,
        'invoicesByMonthSent'    => $invoicesByMonthSent,
        'invoicesByMonthPaid'    => $invoicesByMonthPaid,
        'statusPaid'             => $statusPaid,
        'statusPending'          => $statusPending,
        'statusCancelled'        => $statusCancelled,
        'statusConfirmed'        => $statusConfirmed,
    ]);
    })->middleware(['auth'])->name('dashboard');

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
});
