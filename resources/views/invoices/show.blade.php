<x-layouts.app>
    <div class="max-w-3xl mx-auto py-8">
        <a href="{{ route('invoices.index') }}" class="mt-4 inline-block text-blue-600 underline">
            ← Back to invoices
        </a>
        <h1 class="text-2xl font-bold mb-4 mt-5">Invoice #{{ $invoice->id }}</h1>

        <div class="mb-6 space-y-1 text-m">
            <p><strong>Lead Guest: </strong> {{ $invoice->lead_guest_name }}</p>
            <p><strong>Email: </strong> {{ $invoice->email }}</p>
            <p><strong>PAX: </strong> {{ $invoice->number_of_pax }}</p>
            <p><strong>Tour Package: </strong> {{ $invoice->tour_package }}</p>
            <p><strong>Hotel: </strong> {{ $invoice->hotel_accommodation }}</p>
            <p>
                <strong>Arrival: </strong>
                {{ $invoice->arrival_date ? \Carbon\Carbon::parse($invoice->arrival_date)->format('F d, Y') : '—' }}
            </p>
            <p>
                <strong>Departure: </strong>
                {{ $invoice->departure_date ? \Carbon\Carbon::parse($invoice->departure_date)->format('F d, Y') : '—' }}
            </p>
        </div>

        {{-- Guest breakdown --}}
        <h2 class="text-lg font-semibold mb-2">Guest Breakdown</h2>

        <table class="w-full text-sm border mb-6">
            <thead>
                <tr class="bg-gray-400">
                    <th class="p-2 border text-left">Type</th>
                    <th class="p-2 border text-right">Count</th>
                    <th class="p-2 border text-right">Rate per Person</th>
                    <th class="p-2 border text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $adultSubtotal  = $invoice->adult_count  * $invoice->adult_rate;
                    $infantSubtotal = $invoice->infant_count * $invoice->infant_rate;
                    $seniorSubtotal = $invoice->senior_count * $invoice->senior_rate;
                @endphp

                @if ($invoice->adult_count > 0)
                    <tr>
                        <td class="p-2 border">Adult</td>
                        <td class="p-2 border text-right">{{ $invoice->adult_count }}</td>
                        <td class="p-2 border text-right">
                            ₱{{ number_format($invoice->adult_rate) }}
                        </td>
                        <td class="p-2 border text-right">
                            ₱{{ number_format($adultSubtotal) }}
                        </td>
                    </tr>
                @endif

                @if ($invoice->infant_count > 0)
                    <tr>
                        <td class="p-2 border">Infant</td>
                        <td class="p-2 border text-right">{{ $invoice->infant_count }}</td>
                        <td class="p-2 border text-right">
                            ₱{{ number_format($invoice->infant_rate) }}
                        </td>
                        <td class="p-2 border text-right">
                            ₱{{ number_format($infantSubtotal) }}
                        </td>
                    </tr>
                @endif

                @if ($invoice->senior_count > 0)
                    <tr>
                        <td class="p-2 border">Senior / PWD</td>
                        <td class="p-2 border text-right">{{ $invoice->senior_count }}</td>
                        <td class="p-2 border text-right">
                            ₱{{ number_format($invoice->senior_rate) }}
                        </td>
                        <td class="p-2 border text-right">
                            ₱{{ number_format($seniorSubtotal) }}
                        </td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="bg-gray-500">
                    <th class="p-2 border text-left" colspan="3">Total</th>
                    <th class="p-2 border text-right">
                        ₱{{ number_format($invoice->total_amount) }}
                    </th>
                </tr>
            </tfoot>
        </table>

        <div class="mb-4 text-sm">
            <p><strong>Total:</strong> ₱{{ number_format($invoice->total_amount, 2) }}</p>
            <p><strong>Balance:</strong> ₱{{ number_format($invoice->balance, 2) }}</p>

            <form method="POST" action="{{ route('invoices.updateStatus', $invoice) }}" class="mt-2 flex items-center gap-2">
                @csrf
                @method('PATCH')

                <label class="text-sm font-semibold">Status:</label>

                <select name="status" class="border rounded p-1 text-sm">
                    <option value="pending"   @selected($invoice->status === 'pending')>Pending</option>
                    <option value="paid"      @selected($invoice->status === 'paid')>Paid</option>
                    <option value="cancelled" @selected($invoice->status === 'cancelled')>Cancelled</option>
                </select>

                <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-xs rounded">
                    Update
                </button>
            </form>
        </div>
            @if(auth()->check() && auth()->user()->is_admin)
                <div class="mt-8">
                    <h2 class="text-lg font-semibold mb-2">Audit Logs</h2>

                    @if ($invoice->logs->isEmpty())
                        <p class="text-sm text-gray-500">No activity recorded yet.</p>
                    @else
                        <table class="w-full text-sm border">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="p-2 border text-left">Date</th>
                                    <th class="p-2 border text-left">User</th>
                                    <th class="p-2 border text-left">Action</th>
                                    <th class="p-2 border text-left">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->logs as $log)
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
                                            @if($log->action === 'status_updated')
                                                Status: {{ ucfirst($log->old_status) }} → {{ ucfirst($log->new_status) }}
                                            @else
                                                {{ $log->note ?? '—' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif


    </div>
</x-layouts.app>
