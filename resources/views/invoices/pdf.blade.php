<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>
        {{ $invoice->lead_guest_name }} - Invoice #{{ $invoice->id }}
    </title>
    <style>
        @page {
            margin: 15mm 10mm 20mm 20mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }

        .mb-5 { margin-bottom: 20px; }
        .mb-3 { margin-bottom: 12px; }
        .small { font-size: 9px; }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th,
        .table td {
            border: 1px solid #777;
            padding: 6px 8px;
            vertical-align: top;
        }

        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .no-border td,
        .no-border th {
            border: none;
            padding: 0;
        }

        .header-address {
            font-size: 11px;
            line-height: 1.4;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .important {
            font-size: 9px;
            line-height: 1.4;
        }

        .blue-bar {
            background-color: #0070a8;
            color: #ffffff;
        }

        .blue-bar td {
            border: none;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: bold;
        }

        .footer-text {
            text-align: center;
            font-size: 10px;
            margin-top: 25px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
@php
    $arrival = $invoice->arrival_date ? \Carbon\Carbon::parse($invoice->arrival_date) : null;
    $departure = $invoice->departure_date ? \Carbon\Carbon::parse($invoice->departure_date) : null;
    $dateIssued = $invoice->date_issued ? \Carbon\Carbon::parse($invoice->date_issued) : now();
    $dueDate = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date) : null;

    $adultSubtotal  = $invoice->adult_count  * $invoice->adult_rate;
    $infantSubtotal = $invoice->infant_count * $invoice->infant_rate;
    $seniorSubtotal = $invoice->senior_count * $invoice->senior_rate;
@endphp

{{-- HEADER: address + logo --}}
<table class="no-border mb-5">
    <tr>
        <td class="header-address" style="width: 70%;">
            Agana Street, San Vicente,<br>
            Ivana, Batanes<br>
            Tel: +63918 325 5597
        </td>
        <td class="text-right" style="width: 30%;">
            {{-- Logo disabled for now (GD extension not installed) --}}
            <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="max-height: 100px;">
        </td>
    </tr>
</table>

{{-- INVOICE BAR --}}
<table class="blue-bar mb-3">
    <tr>
        <td style="width: 70%;">
            Invoice {{ $dateIssued->format('Y') }}-{{ $invoice->id }}
        </td>
        <td class="text-right" style="width: 30%;">
            {{ $dateIssued->format('m.d.Y') }}
        </td>
    </tr>
</table>

{{-- BILL TO + TRAVEL DATE --}}
<table class="table mb-3">
    <tr>
        <th style="width: 50%;">Bill To:</th>
        <th style="width: 50%;">Travel Date:</th>
    </tr>
    <tr>
        <td>
          <strong>  {{ $invoice->lead_guest_name }} </strong> <br>
            {{ $invoice->email }}
        </td>
        <td class="text-center">
            @if($arrival && $departure)
                {{ $arrival->format('F d, Y') }} – {{ $departure->format('F d, Y') }}
            @elseif($arrival)
                {{ $arrival->format('F d, Y') }}
            @else
                &nbsp;
            @endif
        </td>
    </tr>
</table>

{{-- INSTRUCTIONS + BANK DETAILS --}}
<table class="table mb-5">
    <tr>
        <th style="width: 50%;">Instructions:</th>
        <th style="width: 50%;">Bank Deposit:</th>
    </tr>
    <tr>
        <td>
            You may pay this invoice through a bank deposit or direct transfer.
        </td>
        <td>
            Bank: Philippine National Bank, Basco, Batanes<br>
            Account Name: Maricel Agana<br>
            Account Number: <span class="bold">2277 7000 3453</span>
        </td>
    </tr>
</table>

{{-- MAIN LINE ITEM TABLE --}}
<table class="table mb-3">
    <tr>
        <th style="width: 15%;">Quantity</th>
        <th style="width: 55%;">Description</th>
        <th style="width: 30%; text-align: right;">Total Amount (PHP)</th>
    </tr>
    <tr>
        <td class="text-center">
            {{ $invoice->number_of_pax }} PAX
        </td>
        <td>
            {{ $invoice->tour_package }}<br>
            @if($invoice->hotel_accommodation)
              <p>Hotel: {{ $invoice->hotel_accommodation }} </p><br>
            @endif

            {{-- Breakdown lines --}}
            @if($invoice->adult_count > 0)
                Php {{ number_format($invoice->adult_rate) }}/per adult
            @endif
            @if($invoice->senior_count > 0)
                <br>Php {{ number_format($invoice->senior_rate) }}/per senior/PWD
            @endif
            @if($invoice->infant_count > 0)
                <br>Php {{ number_format($invoice->infant_rate) }}/per infant
            @endif
        </td>
        <td class="text-center">
            {{ number_format($invoice->total_amount) }}
        </td>
    </tr>
</table>

{{-- DOWNPAYMENT / BALANCE TABLE --}}
<table class="table mb-5">
    <tr>
        {{-- <th style="width: 25%;">Down Payment</th> --}}
        <th style="width: 35%;">Payment Due Date</th>
        <th style="width: 40%; text-align: center;">Amount (PHP)</th>
    </tr>
    <tr>
        {{-- <td>
            @if($invoice->downpayment && $invoice->total_amount > 0)
                {{ round($invoice->downpayment / $invoice->total_amount * 100) }}% Holding Deposit
            @else
                Holding Deposit
            @endif
        </td> --}}
        <td>
            {{ $dueDate ? $dueDate->format('F d, Y') : 'On or before due date' }}
        </td>
        <td class="text-center">
            {{ number_format($invoice->downpayment ?? 0) }}
        </td>
    </tr>
    <tr>
        {{-- <td class="bold">Remaining Balance</td> --}}
        <td>Upon Arrival</td>
        <td class="text-center bold">
            {{ number_format($invoice->balance) }}
        </td>
    </tr>
</table>

{{-- IMPORTANT INFORMATION --}}
<p class="section-title">Important Information:</p>
<p class="important">
    Please read the terms and conditions thoroughly. By paying this invoice, you agree to the
    terms stated in your booking. If you have any questions, kindly contact us.
    Failure to pay by the due date may incur additional charges. Once the initial deposit has
    been made, we will send your service voucher and itinerary.
</p>

{{-- FOOTER --}}
<div class="footer-text">
    <div>Thank you for your business!</div>
    <div style="margin-top: 4px;">Dios Mamajes! Dios Machivan Dinyo!</div>
    <div style="margin-top: 4px; font-size: 9px;">(Thank you, God bless)</div>
</div>

</body>
</html>
