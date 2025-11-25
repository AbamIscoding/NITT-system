<x-layouts.app :title="__('Activity Logs')">
    <div class="max-w-5xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Activity Logs</h1>

        @if($logs->isEmpty())
            <p class="text-sm text-gray-500">No activity recorded yet.</p>
        @else
            <table class="w-full text-sm border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 border text-left">Date</th>
                        <th class="p-2 border text-left">User</th>
                        <th class="p-2 border text-left">Action</th>
                        <th class="p-2 border text-left">Invoice</th>
                        <th class="p-2 border text-left">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="p-2 border">
                                {{ $log->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="p-2 border">
                                {{ $log->user?->name ?? 'System' }}
                            </td>
                            <td class="p-2 border">
                                {{ str_replace('_', ' ', ucfirst($log->action)) }}
                            </td>
                            <td class="p-2 border">
                                @if($log->invoice)
                                    <a href="{{ route('invoices.show', $log->invoice) }}"
                                       class="text-blue-600 hover:underline">
                                        #{{ $log->invoice->id }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-2 border">
                                @if($log->action === 'status_updated')
                                    Status: {{ ucfirst($log->old_status) }} → {{ ucfirst($log->new_status) }}
                                @elseif($log->action === 'invoice_created')
                                    {{ $log->note ?? 'Invoice created' }}
                                @else
                                    {{ $log->note ?? '—' }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
