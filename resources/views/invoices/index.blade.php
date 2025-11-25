<x-layouts.app>
    <div class="max-w-5xl mx-auto py-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Invoices</h1>

            <a href="{{ route('invoices.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                + New Invoice
            </a>
        </div>

        <form method="GET" action="{{ route('invoices.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold mb-1">Lead Guest / Email / Package</label>
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       class="border rounded px-2 py-1 text-s w-56"
                       placeholder="Search keyword...">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-3 py-2 bg-blue-600 text-white text-sm rounded">
                    Search
                </button>

                <a href="{{ route('invoices.index') }}"
                   class="px-3 py-2 bg-gray-200 text-sm rounded">
                    Clear
                </a>
            </div>
        </form>

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
