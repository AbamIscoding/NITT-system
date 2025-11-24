<x-layouts.app>
    <div class="max-w-5xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">
            Arrivals – {{ $current->format('F Y') }}
        </h1>

        @php
            $prev = $current->copy()->subMonth();
            $next = $current->copy()->addMonth();
        @endphp

        {{-- Month navigation --}}
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('schedules.index', ['month' => $prev->month, 'year' => $prev->year]) }}"
               class="px-3 py-1 border rounded">
                ‹ Previous
            </a>

            <span class="text-sm text-gray-600">
                {{ $current->format('F Y') }}
            </span>

            <a href="{{ route('schedules.index', ['month' => $next->month, 'year' => $next->year]) }}"
               class="px-3 py-1 border rounded">
                Next ›
            </a>
        </div>

        {{-- Day filter within current month --}}
        <form method="GET" action="{{ route('schedules.index') }}" class="flex items-end gap-2 mb-6">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div>
                <label class="block text-sm font-medium mb-1">Select a Day</label>
                <select name="day" class="border rounded p-1 bg-gray-500 text-sm">
                    <option value="">All days</option>
                    @for ($d = 1; $d <= $current->daysInMonth; $d++)
                        <option value="{{ $d }}" {{ (int)($day ?? 0) === $d ? 'selected' : '' }}>
                            {{ $d }}
                        </option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="px-3 py-1 border rounded text-sm">
                Search
            </button>
        </form>

        @if ($schedules->isEmpty())
            <p>No arrivals scheduled for this selection.</p>
        @else
            <table class="w-full border text-sm">
                <thead>
                    <tr class="bg-gray-500">
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Name / Group</th>
                        <th class="p-2 border">PAX</th>
                        <th class="p-2 border">Hotel</th>
                        <th class="p-2 border">Tour</th>
                        <th class="p-2 border">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedules as $schedule)
                        <tr>
                            <td class="p-2 border">
                                {{ \Illuminate\Support\Carbon::parse($schedule->arrival_date)->format('M d, Y') }}
                            </td>
                            <td class="p-2 border">{{ $schedule->name }}</td>
                            <td class="p-2 border">{{ $schedule->number_of_pax }}</td>
                            <td class="p-2 border">{{ $schedule->hotel_accommodation }}</td>
                            <td class="p-2 border">{{ $schedule->tours }}</td>
                            <td class="p-2 border text-center">
                                @if ($schedule->invoice)
                                    <a href="{{ route('invoices.show', $schedule->invoice) }}"
                                       class="text-blue-600 underline">
                                        View
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layouts.app>
