<x-layouts.app :title="__('Invoices')">
    <div class="max-w-6xl mx-auto py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-50">
                    Invoices
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-300 mt-1">
                    Manage client invoices, statuses, and email sending.
                </p>
            </div>

            <a href="{{ route('invoices.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow-sm
                      hover:bg-sky-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400">
                <span class="text-lg leading-none">＋</span>
                <span>New Invoice</span>
            </a>
        </div>

        {{-- Search / Filters --}}
        <div class="mb-5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm p-4">
            <form method="GET"
                  action="{{ route('invoices.index') }}"
                  class="flex flex-wrap gap-3 items-end">

                <div class="flex-1 min-w-[220px]">
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-300 mb-1">
                        Lead Guest / Email / Package
                    </label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
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

                    <a href="{{ route('invoices.index') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm
                              text-slate-700 hover:bg-slate-50
                              dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-sky-50 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-3 py-2 text-left">#</th>
                        <th class="px-3 py-2 text-left">Lead Guest</th>
                        <th class="px-3 py-2 text-left">PAX</th>
                        <th class="px-3 py-2 text-left">Total</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                            <td class="px-3 py-2 text-slate-600 dark:text-slate-300">
                                {{ $invoice->id }}
                            </td>
                            <td class="px-3 py-2 text-slate-900 dark:text-slate-50">
                                {{ $invoice->lead_guest_name }}
                            </td>
                            <td class="px-3 py-2 text-slate-900 dark:text-slate-50">
                                {{ $invoice->number_of_pax }}
                            </td>
                            <td class="px-3 py-2 text-slate-900 dark:text-slate-50">
                                ₱{{ number_format($invoice->total_amount, 2) }}
                            </td>
                            <td class="px-3 py-2">
                                <x-status-badge :status="$invoice->status" />
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <a href="{{ route('invoices.show', $invoice) }}"
                                       class="text-sky-600 dark:text-sky-300 hover:underline">
                                        View
                                    </a>

                                    <a href="{{ route('invoices.edit', $invoice) }}"
                                       class="text-sky-600 dark:text-sky-300 hover:underline">
                                        Edit
                                    </a>

                                    <form action="{{ route('invoices.send', $invoice) }}"
                                          method="POST"
                                          class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Send this invoice to the client?')"
                                                class="text-emerald-600 dark:text-emerald-300 hover:underline">
                                            Send
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-xs text-slate-500 dark:text-slate-300">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
</x-layouts.app>
