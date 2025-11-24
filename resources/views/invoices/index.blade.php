<x-layouts.app>
    <div class="max-w-5xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Invoices</h1>

        <a href="{{ route('invoices.create') }}"
           class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded">
            + New Invoice
        </a>

        <table class="w-full border text-sm">
            <thead>
                <tr class="bg-green-400">
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">Lead Guest</th>
                    <th class="p-2 border">PAX</th>
                    <th class="p-2 border">Total</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="p-2 border">{{ $invoice->id }}</td>
                        <td class="p-2 border">{{ $invoice->lead_guest_name }}</td>
                        <td class="p-2 border">{{ $invoice->number_of_pax }}</td>
                        <td class="p-2 border">₱{{ number_format($invoice->total_amount) }}</td>
                        <td class="p-2 border">
                            <x-status-badge :status="$invoice->status" />
                        </td>
                        <td class="p-2 border">
                            <!-- View -->
                        <a href="{{ route('invoices.show', $invoice) }}"
                        class="text-blue-600 hover:underline">
                            View
                        </a>

                        <!-- Edit -->
                        <a href="{{ route('invoices.edit', $invoice) }}"
                        class="text-blue-600 hover:underline">
                            Edit
                        </a>

                        <!-- Send Invoice -->
                        <form action="{{ route('invoices.send', $invoice) }}"
                            method="POST"
                            class="inline">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Send this invoice to the client?')"
                                    class="text-green-600 hover:underline">
                                Send
                            </button>
                        </form>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
</x-layouts.app>
