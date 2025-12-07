<x-layouts.app>
    <div class="max-w-4xl mx-auto py-8">

        {{-- Back link --}}
        <a href="{{ route('invoices.index') }}"
           id="back-button"
           class="inline-flex items-center gap-1 text-sky-400 hover:text-sky-300 hover:underline mb-4 text-m">
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10.5 6 4.5 12m0 0 6 6m-6-6h15" />
            </svg>
            Back
        </a>

        {{-- Card wrapper --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900/80 shadow-md p-6 md:p-8">

            <div class="flex items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-50">Create Invoice</h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Fill out guest details, travel dates, and pricing. Totals are calculated automatically.
                    </p>
                </div>
            </div>

            {{-- Flash + errors --}}
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-900/30 px-3 py-2 text-xs text-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-500/40 bg-rose-900/30 px-3 py-2 text-xs text-rose-100">
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="invoice-form" method="POST" action="{{ route('invoices.store') }}" class="space-y-6">
                @csrf

                {{-- Lead guest + email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Lead Guest Name</label>
                        <input type="text" name="lead_guest_name"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('lead_guest_name') }}" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Email</label>
                        <input type="email" name="email"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('email') }}" required>
                    </div>
                </div>

                {{-- Guest breakdown --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-100">Guest Breakdown</h2>
                            <p class="text-[11px] text-slate-400">
                                Separate pricing for adults, infants, and seniors/PWD.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Adults --}}
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 space-y-2">
                            <p class="font-medium text-slate-100 text-sm">Adults</p>

                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1">Number of Adults</label>
                                <input type="number" min="0" name="adult_count"
                                       class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                       value="{{ old('adult_count', 0) }}"
                                       oninput="recalcTotals()">
                            </div>

                            <div>
                                <label class="block text-[11px] text-slate-400 mb-1 mt-1.5">Rate per Adult</label>
                                <input type="number" step="0.01" min="0" name="adult_rate"
                                       class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                       value="{{ old('adult_rate', 0) }}"
                                       oninput="recalcTotals()">
                            </div>
                        </div>

                        {{-- Infants --}}
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 space-y-2">
                            <label class="font-medium text-slate-100 text-sm flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="has_infants"
                                       class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-sky-500 focus:ring-sky-500"
                                       onclick="toggleGuestBox('infant_box', this)">
                                Infants
                            </label>

                            <div id="infant_box" class="mt-1 space-y-2 hidden">
                                <div>
                                    <label class="block text-[11px] text-slate-400 mb-1">Number of Infants</label>
                                    <input type="number" min="0" name="infant_count"
                                           class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                           value="{{ old('infant_count', 0) }}"
                                           oninput="recalcTotals()">
                                </div>

                                <div>
                                    <label class="block text-[11px] text-slate-400 mb-1 mt-1.5">Rate per Infant</label>
                                    <input type="number" step="0.01" min="0" name="infant_rate"
                                           class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                           value="{{ old('infant_rate', 0) }}"
                                           oninput="recalcTotals()">
                                </div>
                            </div>
                        </div>

                        {{-- Seniors / PWD --}}
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 space-y-2">
                            <label class="font-medium text-slate-100 text-sm flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="has_seniors"
                                       class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-sky-500 focus:ring-sky-500"
                                       onclick="toggleGuestBox('senior_box', this)">
                                Seniors / PWD
                            </label>

                            <div id="senior_box" class="mt-1 space-y-2 hidden">
                                <div>
                                    <label class="block text-[11px] text-slate-400 mb-1">Number of Seniors / PWD</label>
                                    <input type="number" min="0" name="senior_count"
                                           class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                           value="{{ old('senior_count', 0) }}"
                                           oninput="recalcTotals()">
                                </div>

                                <div>
                                    <label class="block text-[11px] text-slate-400 mb-1 mt-1.5">Rate per Senior / PWD</label>
                                    <input type="number" step="0.01" min="0" name="senior_rate"
                                           class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-xs text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                                           value="{{ old('senior_rate', 0) }}"
                                           oninput="recalcTotals()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pricing + dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Downpayment</label>
                        <input type="number" step="0.01" name="downpayment"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('downpayment') }}"
                               oninput="recalcTotals()">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Hotel Accommodation</label>
                        <input type="text" name="hotel_accommodation"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('hotel_accommodation') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Tour Package</label>
                        <input type="text" name="tour_package"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('tour_package') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Arrival Date</label>
                        <input type="date" name="arrival_date"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('arrival_date') }}" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Departure Date</label>
                        <input type="date" name="departure_date"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('departure_date') }}" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Due Date (Payment)</label>
                        <input type="date" name="due_date"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-sky-500 focus:ring-1 focus:ring-sky-500"
                               value="{{ old('due_date') }}">
                    </div>
                </div>

                {{-- Totals preview --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Total (preview)</label>
                        <input type="text"
                               id="total_preview"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-200"
                               value="₱0.00"
                               readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Balance (preview)</label>
                        <input type="text"
                               id="balance_preview"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-200"
                               value="₱0.00"
                               readonly>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-sky-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400">
                        Save Invoice
                    </button>
                </div>
            </form>
        </div>

        {{-- Unsaved changes modal --}}
        <div id="unsaved-modal"
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">
            <div class="bg-slate-900 rounded-lg border border-slate-700 shadow-xl max-w-sm w-full p-4">
                <h2 class="text-base font-semibold text-slate-50 mb-1">Discard changes?</h2>
                <p class="text-xs text-slate-400 mb-4">
                    You have unsaved changes. Are you sure you want to leave this page?
                </p>
                <div class="flex justify-end gap-2">
                    <button id="cancel-leave"
                            class="px-3 py-1 text-xs rounded border border-slate-600 text-slate-100 hover:bg-slate-800">
                        Stay
                    </button>
                    <button id="confirm-leave"
                            class="px-3 py-1 text-xs rounded bg-rose-600 text-white hover:bg-rose-500">
                        Leave without saving
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Small helpers + totals --}}
    <script>
        function toggleGuestBox(id, checkbox) {
            const box = document.getElementById(id);
            if (!box) return;
            box.classList.toggle('hidden', !checkbox.checked);
        }

        function recalcTotals() {
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

        document.addEventListener('DOMContentLoaded', function () {
            recalcTotals();

            const form         = document.getElementById('invoice-form');
            if (!form) return;

            const inputs       = form.querySelectorAll('input, textarea, select');
            const backButton   = document.getElementById('back-button');
            const modal        = document.getElementById('unsaved-modal');
            const cancelLeave  = document.getElementById('cancel-leave');
            const confirmLeave = document.getElementById('confirm-leave');

            let isDirty     = false;
            let pendingHref = null;

            inputs.forEach(el => {
                if (el.type === 'hidden') return;
                el.addEventListener('input',  () => isDirty = true);
                el.addEventListener('change', () => isDirty = true);
            });

            form.addEventListener('submit', () => { isDirty = false; });

            function openModal() {
                if (modal) modal.classList.remove('hidden');
            }
            function closeModal() {
                if (modal) modal.classList.add('hidden');
            }

            if (backButton) {
                backButton.addEventListener('click', function (e) {
                    if (!isDirty) return;
                    e.preventDefault();
                    pendingHref = backButton.href;
                    openModal();
                });
            }

            if (cancelLeave) {
                cancelLeave.addEventListener('click', function () {
                    pendingHref = null;
                    closeModal();
                });
            }

            if (confirmLeave) {
                confirmLeave.addEventListener('click', function () {
                    const href = pendingHref;
                    pendingHref = null;
                    isDirty = false;
                    closeModal();
                    if (href) window.location.href = href;
                });
            }

            window.addEventListener('beforeunload', function (e) {
                if (!isDirty) return;
                e.preventDefault();
                e.returnValue = '';
            });
        });
    </script>
</x-layouts.app>
