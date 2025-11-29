<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // dd('DashboardController is running');

        $today        = Carbon::today();
        $startOfWeek  = Carbon::now()->startOfWeek();
        $endOfWeek    = Carbon::now()->endOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // --- Arrivals ---
        $todaysArrivals = Schedule::with('invoice')
            ->whereDate('arrival_date', $today)
            ->orderBy('arrival_date')
            ->get();

        $weeksArrivals = Schedule::with('invoice')
            ->whereBetween('arrival_date', [$startOfWeek, $endOfWeek])
            ->orderBy('arrival_date')
            ->get();

        $monthsArrivals = Schedule::with('invoice')
            ->whereBetween('arrival_date', [$startOfMonth, $endOfMonth])
            ->orderBy('arrival_date')
            ->get();

        // --- Quota ---
        $monthlyQuota = 30;

        $closedPaxThisMonth = Invoice::whereBetween('date_issued', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['confirmed', 'paid'])   // treat these as “closed”
            ->sum('number_of_pax');

        $quotaRemaining = max($monthlyQuota - $closedPaxThisMonth, 0);
        $quotaReached   = $closedPaxThisMonth >= $monthlyQuota;

        // --- Invoices by month (sent vs paid) ---
        $monthsBack   = 5;
        $startPeriod  = Carbon::now()->copy()->subMonths($monthsBack)->startOfMonth();
        $endPeriod    = Carbon::now()->copy()->endOfMonth();

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
            $key   = $cursor->format('Y-m');
            $label = $cursor->format('M Y');
            $row   = $stats->firstWhere('ym', $key);

            $invoicesByMonthLabels[] = $label;
            $invoicesByMonthSent[]   = $row ? (int) $row->total_sent : 0;
            $invoicesByMonthPaid[]   = $row ? (int) $row->total_paid : 0;

            $cursor->addMonth();
        }

        // --- Status breakdown this month ---
        $statusCounts = Invoice::whereBetween('date_issued', [$startOfMonth, $endOfMonth])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusPaid      = (int) ($statusCounts['paid'] ?? 0);
        $statusPending   = (int) ($statusCounts['pending'] ?? 0);
        $statusCancelled = (int) ($statusCounts['cancelled'] ?? 0);
        $statusConfirmed = (int) ($statusCounts['confirmed'] ?? 0);

        // --- NEW: Monthly income (paid invoices only) ---
        $monthlyIncomeLabels    = [];
        $monthlyIncomeCollected = [];
        $monthlyIncomeRemaining = [];

        $fromIncome = Carbon::now()->copy()->subMonths($monthsBack)->startOfMonth();

        $paidByMonth = Invoice::selectRaw('
                DATE_FORMAT(date_issued, "%Y-%m") as ym,
                SUM(downpayment) as collected,
                SUM(balance) as remaining
            ')
            ->where('status', 'paid')
            ->whereDate('date_issued', '>=', $fromIncome)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $incomePeriod = CarbonPeriod::create(
            $fromIncome,
            '1 month',
            Carbon::now()->startOfMonth()
        );

        foreach ($incomePeriod as $month) {
            $ym  = $month->format('Y-m');
            $row = $paidByMonth->firstWhere('ym', $ym);

            $monthlyIncomeLabels[]    = $month->format('M Y');
            $monthlyIncomeCollected[] = $row ? (float) $row->collected : 0;
            $monthlyIncomeRemaining[] = $row ? (float) $row->remaining : 0;
        }

        $authUser = Auth::user();

        return view('dashboard', [
            // arrivals
            'todaysArrivals'  => $todaysArrivals,
            'weeksArrivals'   => $weeksArrivals,
            'monthsArrivals'  => $monthsArrivals,

            // quota
            'monthlyQuota'        => $monthlyQuota,
            'closedPaxThisMonth'  => $closedPaxThisMonth,
            'quotaRemaining'      => $quotaRemaining,
            'quotaReached'        => $quotaReached,

            // charts: invoices vs paid
            'invoicesByMonthLabels' => $invoicesByMonthLabels,
            'invoicesByMonthSent'   => $invoicesByMonthSent,
            'invoicesByMonthPaid'   => $invoicesByMonthPaid,

            // charts: status this month
            'statusPaid'      => $statusPaid,
            'statusPending'   => $statusPending,
            'statusCancelled' => $statusCancelled,
            'statusConfirmed' => $statusConfirmed,

            // NEW: income chart
            'monthlyIncomeLabels'    => $monthlyIncomeLabels,
            'monthlyIncomeCollected' => $monthlyIncomeCollected,
            'monthlyIncomeRemaining' => $monthlyIncomeRemaining,

            // current user (for Blade checks)
            'authUser' => $authUser,
        ]);
    }
}
