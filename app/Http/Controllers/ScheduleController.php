<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
     public function index(Request $request)
    {
        // current month/year as default
        // defaults
        $year  = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $day   = $request->query('day'); // optional

        $query = Schedule::whereYear('arrival_date', $year)
            ->whereMonth('arrival_date', $month)
            ->with('invoice')        // so we can link to invoice without extra queries
            ->orderBy('arrival_date')
            ->orderBy('name');

        if (!empty($day)) {
            $query->whereDay('arrival_date', (int) $day);
        }

        $schedules = $query->get();

        $current = \Illuminate\Support\Carbon::create($year, $month, 1);

        return view('schedules.index', [
            'schedules' => $schedules,
            'current'   => $current,
            'year'      => $year,
            'month'     => $month,
            'day'       => $day,
        ]);
    }
}
