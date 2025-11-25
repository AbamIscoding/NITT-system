<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
     public function index(Request $request)
    {
        $month = $request->input('month');
        $year  = $request->input('year');

        if ($month && $year) {
            $current = Carbon::createFromDate($year, $month, 1);
        } else {
            $current = Carbon::now()->startOfMonth();
        }

        $startOfMonth = $current->copy()->startOfMonth();
        $endOfMonth   = $current->copy()->endOfMonth();

        $search = $request->input('search');
        $date   = $request->input('date');

        $query = Schedule::query()->with('invoice');

        if ($date) {
            $query->whereDate('arrival_date', $date);
        } else {
            $query->whereBetween('arrival_date', [$startOfMonth, $endOfMonth]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('hotel_accommodation', 'like', '%' . $search . '%')
                ->orWhere('tours', 'like', '%' . $search . '%');
            });
        }

        $schedules = $query->orderBy('arrival_date')->get();

        return view('schedules.index', [
            'schedules' => $schedules,
            'current'   => $current,
            'search'    => $search,
            'date'      => $date,
        ]);
    }
}
