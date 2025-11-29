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
        // 👉 1. YOUR EXISTING DASHBOARD STATS
        // Move whatever logic you currently use (for quota, arrivals, etc.)
        // from your /dashboard route into here.

        // Example only – adjust if your code is different:
        $today = Carbon::today();

        // Today's arrivals
        $todaysArrivals = Schedule::with('invoice')
            ->whereDate('arrival_date', $today)
            ->orderBy('arrival_date')
            ->get();

        // Week arrivals
        $weeksArrivals = Schedule::with('invoice')
            ->whereBetween('arrival_date', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek(),
            ])
            ->orderBy('arrival_date')
            ->get();

        // Month arrivals
        $monthsArrivals = Schedule::with('invoice')
            ->whereBetween('arrival_date', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
            ])
            ->orderBy('arrival_date')
            ->get();

        // Example quota logic – use your real values here:
        $monthlyQuota        = 30;
        $closedPaxThisMonth  = $monthsArrivals->sum('number_of_pax');
        $quotaRemaining      = max(0, $monthlyQuota - $closedPaxThisMonth);
        $quotaReached        = $quotaRemaining <= 0;

        // 👉 2. EXISTING CHART DATA (sent vs paid, status)
        // (Assuming you already have these somewhere – keep your versions,
        // or use these as a base.)

        // Last 6 months for invoices sent/paid
        $monthsBack = 5;
        $from = Carbon::now()->startOfMonth()->subMonths($monthsBack);

        $byMonth = Invoice::selectRaw('
                DATE_FORMAT(date_issued, "%Y-%m") as ym,
                COUNT(*) as sent_count,
                SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_count
            ')
            ->whereDate('date_issued', '>=', $from)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $period = CarbonPeriod::create(
            $from,
            '1 month',
            Carbon::now()->startOfMonth()
        );

        $invoicesByMonthLabels = [];
        $invoicesByMonthSent   = [];
        $invoicesByMonthPaid   = [];

        foreach ($period as $month) {
            $ym  = $month->format('Y-m');
            $row = $byMonth->firstWhere('ym', $ym);

            $invoicesByMonthLabels[] = $month->format('M Y');
            $invoicesByMonthSent[]   = $row ? (int) $row->sent_count : 0;
            $invoicesByMonthPaid[]   = $row ? (int) $row->paid_count : 0;
        }

        // Status breakdown THIS month
        $statusPaid      = Invoice::where('status', 'paid')
            ->whereBetween('date_issued', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->count();

        $statusPending   = Invoice::where('status', 'pending')
            ->whereBetween('date_issued', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->count();

        $statusCancelled = Invoice::where('status', 'cancelled')
            ->whereBetween('date_issued', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->count();

        // 👉 3. NEW: Monthly income (admin only)
        $monthlyIncomeLabels    = [];
        $monthlyIncomeCollected = [];
        $monthlyIncomeRemaining = [];

        if (Auth::user()->is_admin) {
            $fromIncome = Carbon::now()->startOfMonth()->subMonths($monthsBack);

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
        }

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

            // NEW: income chart
            'monthlyIncomeLabels'    => $monthlyIncomeLabels,
            'monthlyIncomeCollected' => $monthlyIncomeCollected,
            'monthlyIncomeRemaining' => $monthlyIncomeRemaining,
        ]);
    }
}
