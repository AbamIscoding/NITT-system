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

        {{-- add an search bar here for searching of dates and name --}}
        <form method="GET" action="{{ route('schedules.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
            <input type="hidden" name="month" value="{{ $current->month }}">
            <input type="hidden" name="year" value="{{ $current->year }}">

            <div>
                <label class="block text-xs font-semibold mb-1">Name / Hotel / Tour</label>
                <input type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    class="border rounded px-2 py-1 text-sm w-56"
                    placeholder="Search keyword...">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-3 py-2 bg-blue-600 text-white text-sm rounded">
                    Search
                </button>

                <a href="{{ route('schedules.index', ['month' => $current->month, 'year' => $current->year]) }}"
                class="px-3 py-2 bg-gray-200 text-sm rounded">
                    Clear
                </a>
            </div>
        </form>

        {{-- Schedules table --}}
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
