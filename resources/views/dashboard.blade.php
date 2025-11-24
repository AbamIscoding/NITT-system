<x-layouts.app :title="__('Dashboard')">

    {{-- Top Action Button --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('invoices.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
            + New Invoice
        </a>
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

</x-layouts.app>
