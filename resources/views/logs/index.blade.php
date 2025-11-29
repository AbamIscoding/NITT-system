<x-layouts.app :title="__('Activity Logs')">
    <div class="max-w-6xl mx-auto py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-50">
                    Activity Logs
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-300 mt-1">
                    Trail of changes to invoices, including status updates and creations.
                </p>
            </div>
        </div>

        @if($logs->isEmpty())
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm p-6">
                <p class="text-sm text-slate-500 dark:text-slate-300">
                    No activity recorded yet.
                </p>
            </div>
        @else
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/80 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-sky-50 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                            <th class="p-2 px-3 text-left">Date</th>
                            <th class="p-2 px-3 text-left">User</th>
                            <th class="p-2 px-3 text-left">Action</th>
                            <th class="p-2 px-3 text-left">Invoice</th>
                            <th class="p-2 px-3 text-left">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60">
                                {{-- Date --}}
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50 whitespace-nowrap">
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </td>

                                {{-- User --}}
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    {{ $log->user?->name ?? 'System' }}
                                </td>

                                {{-- Action --}}
                                <td class="p-2 px-3">
                                    @php
                                        $actionLabel = str_replace('_', ' ', ucfirst($log->action));
                                    @endphp

                                    @if($log->action === 'status_updated')
                                        <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-medium text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                                            {{ $actionLabel }}
                                        </span>
                                    @elseif($log->action === 'invoice_created')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                            {{ $actionLabel }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            {{ $actionLabel }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Invoice link --}}
                                <td class="p-2 px-3">
                                    @if($log->invoice)
                                        <a href="{{ route('invoices.show', $log->invoice) }}"
                                           class="text-sky-600 dark:text-sky-300 hover:underline text-xs">
                                            #{{ $log->invoice->id }}
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                    @endif
                                </td>

                                {{-- Details --}}
                                <td class="p-2 px-3 text-slate-900 dark:text-slate-50">
                                    @if($log->action === 'status_updated')
                                        <span class="text-xs">
                                            Status:
                                            <span class="font-medium">{{ ucfirst($log->old_status) }}</span>
                                            →
                                            <span class="font-medium">{{ ucfirst($log->new_status) }}</span>
                                        </span>
                                    @elseif($log->action === 'invoice_created')
                                        <span class="text-xs">
                                            {{ $log->note ?? 'Invoice created' }}
                                        </span>
                                    @else
                                        <span class="text-xs">
                                            {{ $log->note ?? '—' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
