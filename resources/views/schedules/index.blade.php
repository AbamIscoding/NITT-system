<x-layouts.app :title="__('Schedules')">
    <div class="max-w-6xl mx-auto py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-50">
                    Arrivals – {{ $current->format('F Y') }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-300 mt-1">
                    Monthly overview of confirmed guest arrivals, hotels, and tours.
                </p>
            </div>

            @php
                $prev = $current->copy()->subMonth();
                $next = $current->copy()->addMonth();
            @endphp

            {{-- Month navigation --}}
            <div class="inline-flex items-center gap-2">
                <a href="{{ route('schedules.index', ['month' => $prev->month, 'year' => $prev->year]) }}"
                   class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium
                          text-slate-700 hover:bg-slate-50
                          dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">
                    ‹ Previous
                </a>

                <span class="text-xs text-slate-500 dark:text-slate-300">
                    {{ $current->format('F Y') }}
                </span>

                <a href="{{ route('schedules.index', ['month' => $next->month, 'year' => $next->year]) }}"
                   class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium
                          text-slate-700 hover:bg-slate-50
                          dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">
                    Next ›
                </a>
            </div>
        </div>

        {{-- Search / filters --}}
        <div class="mb-5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm p-4">
            <form method="GET"
                  action="{{ route('schedules.index') }}"
                  class="flex flex-wrap gap-3 items-end">

                {{-- keep month/year when searching --}}
                <input type="hidden" name="month" value="{{ $current->month }}">
                <input type="hidden" name="year" value="{{ $current->year }}">

                <div class="flex-1 min-w-[220px]">
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Name / Hotel / Tour
                    </label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? request('search') }}"
                        class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm
                               text-slate-900 placeholder:text-slate-400
                               focus:border-sky-400 focus:outline-none focus:ring-1 focus:ring-sky-400
                               dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100"
                        placeholder="Search keyword..."
                    >
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white
                                   hover:bg-sky-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400">
                        Search
                    </button>

                    <a href="{{ route('schedules.index', ['month' => $current->month, 'year' => $current->year]) }}"
                       class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm
                              text-slate-700 hover:bg-slate-50
                              dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Schedules table --}}
        @if ($schedules->isEmpty())
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm p-6">
                <p class="text-sm text-slate-500 dark:text-slate-300">
                    No arrivals scheduled for this selection.
                </p>
            </div>
        @else
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-sky-50 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                            <th class="p-2 px-3 text-left">Arrival Date</th>
                            <th class="p-2 px-3 text-left">Name / Group</th>
                            <th class="p-2 px-3 text-left">PAX</th>
                            <th class="p-2 px-3 text-left">Hotel</th>
                            <th class="p-2 px-3 text-left">Tour</th>
                            <th class="p-2 px-3 text-left">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($schedules as $schedule)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    {{ \Illuminate\Support\Carbon::parse($schedule->arrival_date)->format('M d, Y') }}
                                </td>
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    {{ $schedule->name }}
                                </td>
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    {{ $schedule->number_of_pax }}
                                </td>
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    {{ $schedule->hotel_accommodation }}
                                </td>
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    {{ $schedule->tours }}
                                </td>
                                <td class="p-2 px-3 text-center">
                                    @if ($schedule->invoice)
                                        <a href="{{ route('invoices.show', $schedule->invoice) }}"
                                           class="text-sky-600 dark:text-sky-300 hover:underline text-xs">
                                            View Invoice
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
