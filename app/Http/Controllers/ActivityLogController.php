<?php

namespace App\Http\Controllers;

use App\Models\InvoiceLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index()
    {
        if (! Auth::user()->is_admin) {
            abort(403); // forbid non-admins
        }

        $logs = InvoiceLog::with(['invoice', 'user'])
            ->latest()
            ->paginate(20);

        return view('logs.index', compact('logs'));
    }
}
