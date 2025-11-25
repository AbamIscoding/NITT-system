<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;


class InvoiceController extends Controller
{
    // show the form
    public function create()
    {
        return view('invoices.create');
    }

    // handle form submit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_guest_name'     => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email'],

            'adult_count'         => ['nullable', 'integer', 'min:0'],
            'adult_rate'          => ['nullable', 'numeric', 'min:0'],
            'infant_count'        => ['nullable', 'integer', 'min:0'],
            'infant_rate'         => ['nullable', 'numeric', 'min:0'],
            'senior_count'        => ['nullable', 'integer', 'min:0'],
            'senior_rate'         => ['nullable', 'numeric', 'min:0'],

            'hotel_accommodation' => ['nullable', 'string', 'max:255'],
            'tour_package'        => ['nullable', 'string', 'max:255'],

            'downpayment'         => ['nullable', 'numeric', 'min:0'],
            'arrival_date'        => ['required', 'date'],
            'departure_date'      => ['required', 'date', 'after_or_equal:arrival_date'],
            'due_date'            => ['nullable', 'date'],
        ]);

        // calculate totals
        $adultCount  = (int) ($validated['adult_count']  ?? 0);
        $infantCount = (int) ($validated['infant_count'] ?? 0);
        $seniorCount = (int) ($validated['senior_count'] ?? 0);

        $adultRate   = (float) ($validated['adult_rate']  ?? 0);
        $infantRate  = (float) ($validated['infant_rate'] ?? 0);
        $seniorRate  = (float) ($validated['senior_rate'] ?? 0);

        // total PAX
        $totalPax = $adultCount + $infantCount + $seniorCount;

        // compute total based on each group
        $total =
            $adultCount  * $adultRate +
            $infantCount * $infantRate +
            $seniorCount * $seniorRate;

        $down    = (float) ($validated['downpayment'] ?? 0);
        $balance = $total - $down;

        // create invoice
        $invoice = Invoice::create([
           'lead_guest_name'     => $validated['lead_guest_name'],
            'email'               => $validated['email'],

            'adult_count'         => $adultCount,
            'adult_rate'          => $adultRate,
            'infant_count'        => $infantCount,
            'infant_rate'         => $infantRate,
            'senior_count'        => $seniorCount,
            'senior_rate'         => $seniorRate,

            'number_of_pax'       => $totalPax,
            'hotel_accommodation' => $validated['hotel_accommodation'] ?? null,
            'tour_package'        => $validated['tour_package'] ?? null,

            'total_amount'        => $total,
            'downpayment'         => $down,
            'balance'             => $balance,
            'status'              => 'pending',

            'arrival_date'        => $validated['arrival_date'],
            'departure_date'      => $validated['departure_date'],
            'due_date'            => $validated['due_date'] ?? null,
            'date_issued'         => now()->toDateString(),
        ]);

        // create linked schedule row
        $invoice->schedule()->create([
            'name'                => $invoice->lead_guest_name,
            'number_of_pax'       => $invoice->number_of_pax,
            'arrival_date'        => $invoice->arrival_date,
            'departure_date'      => $invoice->departure_date,
            'hotel_accommodation' => $invoice->hotel_accommodation,
            'tours'               => $invoice->tour_package,
            'notes'               => null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Invoice created and schedule added!');
    }

    public function index()
    {
        $invoices = Invoice::latest()->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'lead_guest_name'     => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email'],

            'adult_count'         => ['nullable', 'integer', 'min:0'],
            'adult_rate'          => ['nullable', 'numeric', 'min:0'],
            'infant_count'        => ['nullable', 'integer', 'min:0'],
            'infant_rate'         => ['nullable', 'numeric', 'min:0'],
            'senior_count'        => ['nullable', 'integer', 'min:0'],
            'senior_rate'         => ['nullable', 'numeric', 'min:0'],

            'hotel_accommodation' => ['nullable', 'string', 'max:255'],
            'tour_package'        => ['nullable', 'string', 'max:255'],

            'downpayment'         => ['nullable', 'numeric', 'min:0'],

            'arrival_date'        => ['required', 'date'],
            'departure_date'      => ['required', 'date', 'after_or_equal:arrival_date'],
            'due_date'            => ['nullable', 'date'],
        ]);

        // Compute totals again
        $adultCount  = (int) ($validated['adult_count']  ?? 0);
        $infantCount = (int) ($validated['infant_count'] ?? 0);
        $seniorCount = (int) ($validated['senior_count'] ?? 0);

        $adultRate   = (float) ($validated['adult_rate']  ?? 0);
        $infantRate  = (float) ($validated['infant_rate'] ?? 0);
        $seniorRate  = (float) ($validated['senior_rate'] ?? 0);

        $totalPax = $adultCount + $infantCount + $seniorCount;

        $total =
            $adultCount  * $adultRate +
            $infantCount * $infantRate +
            $seniorCount * $seniorRate;

        $down    = (float) ($validated['downpayment'] ?? 0);
        $balance = $total - $down;

        // Update invoice
        $invoice->update([
            'lead_guest_name'     => $validated['lead_guest_name'],
            'email'               => $validated['email'],

            'adult_count'         => $adultCount,
            'adult_rate'          => $adultRate,
            'infant_count'        => $infantCount,
            'infant_rate'         => $infantRate,
            'senior_count'        => $seniorCount,
            'senior_rate'         => $seniorRate,

            'number_of_pax'       => $totalPax,
            'hotel_accommodation' => $validated['hotel_accommodation'] ?? null,
            'tour_package'        => $validated['tour_package'] ?? null,

            'total_amount'        => $total,
            'downpayment'         => $down,
            'balance'             => $balance,

            'arrival_date'        => $validated['arrival_date'],
            'departure_date'      => $validated['departure_date'],
            'due_date'            => $validated['due_date'] ?? null,
        ]);

        // Update linked schedule
        if ($invoice->schedule) {
            $invoice->schedule->update([
                'name'                => $invoice->lead_guest_name,
                'number_of_pax'       => $invoice->number_of_pax,
                'arrival_date'        => $invoice->arrival_date,
                'departure_date'      => $invoice->departure_date,
                'hotel_accommodation' => $invoice->hotel_accommodation,
                'tours'               => $invoice->tour_package,
            ]);
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }
    public function send(Invoice $invoice)
    {
        // Debug check (optional while testing)
        // dd($invoice->id);

        Mail::to($invoice->email)->send(new InvoiceMail($invoice));

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice emailed to client.');
    }
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'status' => ['required', 'in:confirmed,paid,cancelled'],
        ]);

        $invoice->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Invoice status updated.');
    }


}
