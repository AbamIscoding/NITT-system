<x-layouts.app :title="__('Dashboard')">

    {{-- Top Action Button --}}
    {{-- <div class="flex justify mb-4">
        <a href="{{ route('invoices.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
            + New Invoice
        </a>
    </div> --}}

   {{-- KPI row: Quota + Charts --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Monthly Quota --}}
        <div class="border rounded-xl p-4 bg-white dark:bg-zinc-800 shadow flex flex-col justify-between">
            <div>
                <h2 class="font-bold text-lg mb-1">Monthly Quota</h2>
                <p class="text-sm text-gray-600">
                    Target: <span class="font-semibold">{{ $monthlyQuota }}</span> PAX
                </p>
                <p class="text-sm text-gray-600">
                    Closed this month: <span class="font-semibold">{{ $closedPaxThisMonth }}</span> PAX
                </p>
                <p class="text-sm text-gray-600">
                    Remaining: <span class="font-semibold">{{ $quotaRemaining }}</span> PAX
                </p>
            </div>

            <div class="mt-3">
                @if($quotaReached)
                    <span class="inline-block px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                        🎉 Quota reached!
                    </span>
                @else
                    <span class="inline-block px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                        Keep going — {{ $quotaRemaining }} PAX to go
                    </span>
                @endif
            </div>
        </div>

        {{-- Bar chart: invoices sent vs paid by month --}}
        <div class="border rounded-xl p-4 bg-white dark:bg-zinc-800 shadow">
            <h2 class="font-bold text-lg mb-2">Invoices by Month</h2>
            <p class="text-xs text-gray-500 mb-2">Sent vs paid (last few months)</p>
            <div class="mt-2 h-52">
                <canvas id="invoicesByMonthChart" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Pie chart: status breakdown this month --}}
        <div class="border rounded-xl p-4 bg-white dark:bg-zinc-800 shadow">
            <h2 class="font-bold text-lg mb-2">Status This Month</h2>
            <p class="text-xs text-gray-500 mb-2">Paid, pending, cancelled</p>
            <div class="mt-2 h-52">
                <canvas id="statusPieChart" class="w-full h-full"></canvas>
            </div>
        </div>

    </div>



    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- TODAY'S ARRIVALS --}}
        <div class="border rounded-xl p-4 bg-white dark:bg-zinc-800 shadow">
            <h2 class="font-bold text-xl mb-3">Today's Arrivals</h2>

            @if($todaysArrivals->count() == 0)
                <p class="text-sm text-gray-500">No arrivals today.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 text-left">Date</th>
                            <th class="py-2 text-left">Guest</th>
                            <th class="py-2 text-left">PAX</th>
                            <th class="py-2 text-left">Status</th>
                            <th class="py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($todaysArrivals as $arr)
                            <tr class="border-b">
                                <td class="py-2">{{ \Carbon\Carbon::parse($arr->arrival_date)->format('M d, Y') }}</td>
                                <td>{{ $arr->name }}</td>
                                <td>{{ $arr->number_of_pax }}</td>
                                <td><x-status-badge :status="$arr->invoice->status" /></td>
                                <td>
                                    <a href="{{ route('invoices.show', $arr->invoice_id) }}"
                                       class="text-blue-600 hover:underline">
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
        <div class="border rounded-xl p-4 bg-white dark:bg-zinc-800 shadow">
            <h2 class="font-bold text-xl mb-3">Arrivals This Week</h2>

            @if($weeksArrivals->count() == 0)
                <p class="text-sm text-gray-500">No arrivals this week.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 text-left">Date</th>
                            <th class="py-2 text-left">Guest</th>
                            <th class="py-2 text-left">PAX</th>
                            <th class="py-2 text-left">Status</th>
                            <th class="py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weeksArrivals as $arr)
                            <tr class="border-b">
                                <td class="py-2">{{ \Carbon\Carbon::parse($arr->arrival_date)->format('M d, Y') }}</td>
                                <td>{{ $arr->name }}</td>
                                <td>{{ $arr->number_of_pax }}</td>
                                <td><x-status-badge :status="$arr->invoice->status" /></td>
                                <td>
                                    <a href="{{ route('invoices.show', $arr->invoice_id) }}"
                                       class="text-blue-600 hover:underline">
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
    <div class="border rounded-xl p-4 bg-white dark:bg-zinc-800 shadow mt-6">
        <h2 class="font-bold text-xl mb-3">Arrivals This Month</h2>

        @if($monthsArrivals->count() == 0)
            <p class="text-sm text-gray-500">No arrivals this month.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="py-2 text-left">Date</th>
                        <th class="py-2 text-left">Guest</th>
                        <th class="py-2 text-left">PAX</th>
                        <th class="py-2 text-left">Status</th>
                        <th class="py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monthsArrivals as $arr)
                        <tr class="border-b">
                            <td class="py-2">{{ \Carbon\Carbon::parse($arr->arrival_date)->format('M d, Y') }}</td>
                            <td>{{ $arr->name }}</td>
                            <td>{{ $arr->number_of_pax }}</td>
                            <td><x-status-badge :status="$arr->invoice->status" /></td>
                            <td>
                                <a href="{{ route('invoices.show', $arr->invoice_id) }}"
                                   class="text-blue-600 hover:underline">
                                    View Invoice
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const invoicesCanvas = document.getElementById('invoicesByMonthChart');
        const statusCanvas = document.getElementById('statusPieChart');

        const labels = @json($invoicesByMonthLabels);
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
                        },
                        {
                            label: 'Paid',
                            data: paidData,
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
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        const statusPaid = {{ $statusPaid }};
        const statusPending = {{ $statusPending }};
        const statusCancelled = {{ $statusCancelled }};


        if (statusCanvas && typeof Chart !== 'undefined') {
            new Chart(statusCanvas, {
                type: 'pie',
                data: {
                    labels: ['Paid', 'Pending', 'Cancelled'],
                    datasets: [{
                        data: [statusPaid, statusPending, statusCancelled],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>

</x-layouts.app>
