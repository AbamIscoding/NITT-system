<x-layouts.app :title="__('Dashboard')">

    @php
        $authUser = auth()->user();
    @endphp

    @php
        // Make sure income arrays always exist, even if the controller didn't pass them
        $monthlyIncomeLabels    = $monthlyIncomeLabels    ?? [];
        $monthlyIncomeCollected = $monthlyIncomeCollected ?? [];
        $monthlyIncomeRemaining = $monthlyIncomeRemaining ?? [];
    @endphp

    {{-- Header / Sea vibe --}}
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-[14px] font-medium
                          bg-sky-100 text-sky-800
                          dark:bg-sky-900/40 dark:text-sky-200">
                Northern Island Travel &amp; Tours • Operations
            </span>

            <h1 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-slate-50">
                Dashboard
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-300 mt-1">
                Overview of arrivals, invoices, and monthly performance.
            </p>
        </div>

        <a href="{{ route('invoices.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow-sm
                  hover:bg-sky-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400">
            <span class="text-lg leading-none">＋</span>
            <span>New Invoice</span>
        </a>
    </div>

    @php
        $authUser = auth()->user();
    @endphp

    {{-- KPI row: Quota + Charts --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-4">

        {{-- Monthly Quota --}}
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4
                    bg-white dark:bg-slate-900/80 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="font-semibold text-sm text-slate-900 dark:text-slate-50 mb-1">Monthly Quota</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-300 mb-3">
                    Target vs closed bookings for the current month.
                </p>

                <div class="space-y-1.5 text-xs text-slate-700 dark:text-slate-200">
                    <p>Target: <span class="font-semibold">{{ $monthlyQuota }}</span> PAX</p>
                    <p>Closed this month: <span class="font-semibold">{{ $closedPaxThisMonth }}</span> PAX</p>
                    <p>Remaining: <span class="font-semibold">{{ $quotaRemaining }}</span> PAX</p>
                </div>
            </div>

            <div class="mt-4">
                @if($quotaReached)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-[11px] font-medium
                                dark:bg-emerald-900/40 dark:text-emerald-200">
                        🎉 Quota reached – amazing work!
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-800 px-3 py-1 text-[11px] font-medium
                                dark:bg-amber-900/40 dark:text-amber-200">
                        Keep going — {{ $quotaRemaining }} PAX to go
                    </span>
                @endif
            </div>
        </div>

        {{-- Bar chart: invoices sent vs paid by month --}}
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4
                    bg-white dark:bg-slate-900/80 shadow-sm">
            <h2 class="font-semibold text-sm text-slate-900 dark:text-slate-50 mb-1">Invoices by Month</h2>
            <p class="text-[11px] text-slate-500 dark:text-slate-300 mb-2">Sent vs paid (last few months)</p>
            <div class="mt-2 h-52">
                <canvas id="invoicesByMonthChart" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Pie chart: status breakdown this month --}}
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4
                    bg-white dark:bg-slate-900/80 shadow-sm">
            <h2 class="font-semibold text-sm text-slate-900 dark:text-slate-50 mb-1">Status This Month</h2>
            <p class="text-[11px] text-slate-500 dark:text-slate-300 mb-2">Paid, pending, cancelled</p>
            <div class="mt-2 h-52">
                <canvas id="statusPieChart" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- NEW: Monthly Income (admin only) --}}
        @if($authUser && $authUser->is_admin)
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4
                        bg-white dark:bg-slate-900/80 shadow-sm">
                <h2 class="font-semibold text-sm text-slate-900 dark:text-slate-50 mb-1">Monthly Income</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-300 mb-2">
                    Downpayments collected vs remaining balances (paid invoices)
                </p>
                <div class="mt-2 h-52">
                    <canvas id="monthlyIncomeChart" class="w-full h-full"></canvas>
                </div>
            </div>
        @endif
    </div>



    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- TODAY'S ARRIVALS --}}
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4
                    bg-white dark:bg-slate-900/80 shadow-sm">
            <h2 class="font-semibold text-sm text-slate-900 dark:text-slate-50 mb-2">Today's Arrivals</h2>

            @if($todaysArrivals->count() == 0)
                <p class="text-xs text-slate-500 dark:text-slate-300">No arrivals today.</p>
            @else
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300">
                            <th class="py-2 text-left">Date</th>
                            <th class="py-2 text-left">Guest</th>
                            <th class="py-2 text-left">PAX</th>
                            <th class="py-2 text-left">Status</th>
                            <th class="py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-800 dark:text-slate-100">
                        @foreach ($todaysArrivals as $arr)
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <td class="py-2">{{ \Carbon\Carbon::parse($arr->arrival_date)->format('M d, Y') }}</td>
                                <td>{{ $arr->name }}</td>
                                <td>{{ $arr->number_of_pax }}</td>
                                <td><x-status-badge :status="$arr->invoice->status" /></td>
                                <td>
                                    <a href="{{ route('invoices.show', $arr->invoice_id) }}"
                                       class="text-sky-600 dark:text-sky-300 hover:underline">
                                        View Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- ARRIVALS THIS WEEK --}}
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4
                    bg-white dark:bg-slate-900/80 shadow-sm">
            <h2 class="font-semibold text-sm text-slate-900 dark:text-slate-50 mb-2">Arrivals This Week</h2>

            @if($weeksArrivals->count() == 0)
                <p class="text-xs text-slate-500 dark:text-slate-300">No arrivals this week.</p>
            @else
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300">
                            <th class="py-2 text-left">Date</th>
                            <th class="py-2 text-left">Guest</th>
                            <th class="py-2 text-left">PAX</th>
                            <th class="py-2 text-left">Status</th>
                            <th class="py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-800 dark:text-slate-100">
                        @foreach ($weeksArrivals as $arr)
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <td class="py-2">{{ \Carbon\Carbon::parse($arr->arrival_date)->format('M d, Y') }}</td>
                                <td>{{ $arr->name }}</td>
                                <td>{{ $arr->number_of_pax }}</td>
                                <td><x-status-badge :status="$arr->invoice->status" /></td>
                                <td>
                                    <a href="{{ route('invoices.show', $arr->invoice_id) }}"
                                       class="text-sky-600 dark:text-sky-300 hover:underline">
                                        View Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- ARRIVALS THIS MONTH --}}
    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4
                bg-white dark:bg-slate-900/80 shadow-sm mt-6">
        <h2 class="font-semibold text-sm text-slate-900 dark:text-slate-50 mb-2">Arrivals This Month</h2>

        @if($monthsArrivals->count() == 0)
            <p class="text-xs text-slate-500 dark:text-slate-300">No arrivals this month.</p>
        @else
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300">
                        <th class="py-2 text-left">Date</th>
                        <th class="py-2 text-left">Guest</th>
                        <th class="py-2 text-left">PAX</th>
                        <th class="py-2 text-left">Status</th>
                        <th class="py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-slate-800 dark:text-slate-100">
                    @foreach ($monthsArrivals as $arr)
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <td class="py-2">{{ \Carbon\Carbon::parse($arr->arrival_date)->format('M d, Y') }}</td>
                            <td>{{ $arr->name }}</td>
                            <td>{{ $arr->number_of_pax }}</td>
                            <td><x-status-badge :status="$arr->invoice->status" /></td>
                            <td>
                                <a href="{{ route('invoices.show', $arr->invoice_id) }}"
                                   class="text-sky-600 dark:text-sky-300 hover:underline">
                                    View Invoice
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Charts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const invoicesCanvas      = document.getElementById('invoicesByMonthChart');
            const statusCanvas        = document.getElementById('statusPieChart');
            const monthlyIncomeCanvas = document.getElementById('monthlyIncomeChart');

            // Invoices by month
            const labels   = @json($invoicesByMonthLabels);
            const sentData = @json($invoicesByMonthSent);
            const paidData = @json($invoicesByMonthPaid);

            if (invoicesCanvas && typeof Chart !== 'undefined') {
                new Chart(invoicesCanvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Sent',
                                data: sentData,
                                backgroundColor: 'rgba(56, 189, 248, 0.6)',
                                borderRadius: 4,
                            },
                            {
                                label: 'Paid',
                                data: paidData,
                                backgroundColor: 'rgba(45, 212, 191, 0.7)',
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        },
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }

            // Monthly income (admin only)
            @if($authUser && $authUser->is_admin)
                const incomeLabels    = @json($monthlyIncomeLabels);
                const incomeCollected = @json($monthlyIncomeCollected);
                const incomeRemaining = @json($monthlyIncomeRemaining);

                if (monthlyIncomeCanvas && typeof Chart !== 'undefined') {
                    new Chart(monthlyIncomeCanvas, {
                        type: 'bar',
                        data: {
                            labels: incomeLabels,
                            datasets: [
                                {
                                    label: 'Collected (downpayments)',
                                    data: incomeCollected,
                                    backgroundColor: 'rgba(56, 189, 248, 0.8)',
                                    borderRadius: 4,
                                },
                                {
                                    label: 'Remaining (balances)',
                                    data: incomeRemaining,
                                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                    borderRadius: 4,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback(value) {
                                            return '₱' + value.toLocaleString('en-PH');
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: { position: 'bottom' },
                            },
                        },
                    });
                }
            @endif

            // Status pie chart
            const statusPaid      = {{ $statusPaid }};
            const statusPending   = {{ $statusPending }};
            const statusCancelled = {{ $statusCancelled }};

            if (statusCanvas && typeof Chart !== 'undefined') {
                new Chart(statusCanvas, {
                    type: 'pie',
                    data: {
                        labels: ['Paid', 'Pending', 'Cancelled'],
                        datasets: [{
                            data: [statusPaid, statusPending, statusCancelled],
                            backgroundColor: [
                                'rgba(56, 189, 248, 0.9)',
                                'rgba(251, 191, 36, 0.9)',
                                'rgba(248, 113, 113, 0.9)',
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>



</x-layouts.app>
