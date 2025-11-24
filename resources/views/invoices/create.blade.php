<x-layouts.app>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Create Invoice</h1>

        @if(session('success'))
            <div class="mb-4 p-3 border border-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Lead Guest Name</label>
                    <input type="text" name="lead_guest_name" class="w-full border rounded p-2"
                           value="{{ old('lead_guest_name') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" class="w-full border rounded p-2"
                           value="{{ old('email') }}" required>
                </div>

                {{-- Guest breakdown --}}
                <div class="md:col-span-2 mt-4">
                    <h2 class="font-semibold mb-2">Guest Breakdown</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Adults --}}
                        <div class="border rounded p-3">
                            <p class="font-medium mb-2">Adults</p>
                            <label class="block text-xs mb-1">Number of Adults</label>
                            <input type="number" min="0" name="adult_count"
                                class="w-full border rounded p-2 text-sm"
                                value="{{ old('adult_count', 0) }}"
                                oninput="recalcTotals()">

                            <label class="block text-xs mt-2 mb-1">Rate per Adult</label>
                            <input type="number" step="0.01" min="0" name="adult_rate"
                                class="w-full border rounded p-2 text-sm"
                                value="{{ old('adult_rate', 0) }}"
                                oninput="recalcTotals()">
                        </div>

                        {{-- Infants --}}
                        <div class="border rounded p-3">
                            <p class="font-medium mb-2 flex items-center gap-2">
                                <input type="checkbox" id="has_infants"
                                    onclick="toggleGuestBox('infant_box', this)">
                                <label for="has_infants">Infants</label>
                            </p>

                            <div id="infant_box" style="display: none;">
                                <label class="block text-xs mb-1">Number of Infants</label>
                                <input type="number" min="0" name="infant_count"
                                    class="w-full border rounded p-2 text-sm"
                                    value="{{ old('infant_count', 0) }}"
                                    oninput="recalcTotals()">

                                <label class="block text-xs mt-2 mb-1">Rate per Infant</label>
                                <input type="number" step="0.01" min="0" name="infant_rate"
                                    class="w-full border rounded p-2 text-sm"
                                    value="{{ old('infant_rate', 0) }}"
                                    oninput="recalcTotals()">
                            </div>
                        </div>

                        {{-- Seniors / PWD --}}
                        <div class="border rounded p-3">
                            <p class="font-medium mb-2 flex items-center gap-2">
                                <input type="checkbox" id="has_seniors"
                                    onclick="toggleGuestBox('senior_box', this)">
                                <label for="has_seniors">Seniors / PWD</label>
                            </p>

                            <div id="senior_box" style="display: none;">
                                <label class="block text-xs mb-1">Number of Seniors/PWD</label>
                                <input type="number" min="0" name="senior_count"
                                    class="w-full border rounded p-2 text-sm"
                                    value="{{ old('senior_count', 0) }}"
                                    oninput="recalcTotals()">

                                <label class="block text-xs mt-2 mb-1">Rate per Senior/PWD</label>
                                <input type="number" step="0.01" min="0" name="senior_rate"
                                    class="w-full border rounded p-2 text-sm"
                                    value="{{ old('senior_rate', 0) }}"
                                    oninput="recalcTotals()">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- tiny JS helper at the bottom of the file --}}
                <script>
                    function toggleGuestBox(id, checkbox) {
                        document.getElementById(id).style.display = checkbox.checked ? 'block' : 'none';
                    }
                </script>


                <div>
                    <label class="block text-sm font-medium">Downpayment</label>
                    <input type="number" step="0.01" name="downpayment" class="w-full border rounded p-2"
                           value="{{ old('downpayment') }}"
                           oninput="recalcTotals()">
                </div>

                <div>
                    <label class="block text-sm font-medium">Hotel Accommodation</label>
                    <input type="text" name="hotel_accommodation" class="w-full border rounded p-2"
                           value="{{ old('hotel_accommodation') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium">Tour Package</label>
                    <input type="text" name="tour_package" class="w-full border rounded p-2"
                           value="{{ old('tour_package') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium">Arrival Date</label>
                    <input type="date" name="arrival_date" class="w-full border rounded p-2"
                           value="{{ old('arrival_date') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Departure Date</label>
                    <input type="date" name="departure_date" class="w-full border rounded p-2"
                           value="{{ old('departure_date') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Due Date (Payment)</label>
                    <input type="date" name="due_date" class="w-full border rounded p-2"
                           value="{{ old('due_date') }}">
                </div>
            </div>

            {{-- Totals preview (view only) --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Total (preview)</label>
                    <input type="text"
                        id="total_preview"
                        class="w-full border rounded p-2 bg-gray-400"
                        value="₱0.00"
                        readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium">Balance (preview)</label>
                    <input type="text"
                        id="balance_preview"
                        class="w-full border rounded p-2 bg-gray-400"
                        value="₱0.00"
                        readonly>
                </div>
            </div>

            <button type="submit"
                    class="mt-6 px-4 py-2 bg-blue-600 text-white rounded">
                Save Invoice
            </button>
        </form>
                <script>
                    function recalcTotals() {
                        // Helper: read numeric value safely
                        function num(name) {
                            const el = document.querySelector('[name="' + name + '"]');
                            if (!el) return 0;
                            const v = parseFloat(el.value);
                            return isNaN(v) ? 0 : v;
                        }

                        const adultCount  = num('adult_count');
                        const adultRate   = num('adult_rate');
                        const infantCount = num('infant_count');
                        const infantRate  = num('infant_rate');
                        const seniorCount = num('senior_count');
                        const seniorRate  = num('senior_rate');
                        const downpayment = num('downpayment');

                        const adultSubtotal  = adultCount  * adultRate;
                        const infantSubtotal = infantCount * infantRate;
                        const seniorSubtotal = seniorCount * seniorRate;

                        const total   = adultSubtotal + infantSubtotal + seniorSubtotal;
                        const balance = total - downpayment;

                        // Format as ₱X,XXX.XX (simple formatting)
                        function formatMoney(amount) {
                            return '₱' + (amount || 0).toLocaleString('en-PH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }

                        const totalField   = document.getElementById('total_preview');
                        const balanceField = document.getElementById('balance_preview');

                        if (totalField)   totalField.value   = formatMoney(total);
                        if (balanceField) balanceField.value = formatMoney(balance);
                    }

                    // Run once on page load to initialize with default values
                    document.addEventListener('DOMContentLoaded', recalcTotals);
                </script>
    </div>
</x-layouts.app>
