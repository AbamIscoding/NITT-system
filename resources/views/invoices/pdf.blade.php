<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->lead_guest_name }} - Invoice #{{ $invoice->id }}</title>

    <style>
        @page { margin: 20mm 15mm; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header-address {
            font-size: 11px;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Cleaner table look */
        .table th,
        .table td {
            border: 1px solid #cfcfcf;
            padding: 8px;
            vertical-align: top;
        }

        .table th {
            background: #e8f3fb;
            font-weight: bold;
        }

        .no-border td { border: none; }

        /* Blue invoice bar */
        .invoice-bar td {
            background: #0a7cc7;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            border: none;
        }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .important {
            font-size: 10px;
            line-height: 1.5;
        }

        .footer-text {
            margin-top: 25px;
            text-align: center;
            line-height: 1.6;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .bold { font-weight: bold; }

        /* More modern spacing */
        .mb-5 { margin-bottom: 20px; }
        .mb-3 { margin-bottom: 12px; }

        .only-top-bottom td,
        .only-top-bottom th {
            border-left: none !important;
            border-right: none !important;
        }

        .only-top-bottom tr:first-child th {
            border-top: 1px solid #cfcfcf !important;
        }

        .only-top-bottom tr:last-child td {
            border-bottom: 1px solid #cfcfcf !important;
        }
        .section-box {
            border-top: 1px solid #666;
            border-bottom: 1px solid #666;
            padding: 12px 0; /* adds spacing above & below */
        }

        .section-box table td,
        .section-box table th {
            border: none !important; /* removes all inside borders */
            padding: 3px 0; /* cleaner spacing inside */
        }

    </style>
</head>

<body>

@php
    $arrival = $invoice->arrival_date ? \Carbon\Carbon::parse($invoice->arrival_date) : null;
    $departure = $invoice->departure_date ? \Carbon\Carbon::parse($invoice->departure_date) : null;
    $dateIssued = $invoice->date_issued ? \Carbon\Carbon::parse($invoice->date_issued) : now();
    $dueDate = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date) : null;
@endphp

<!-- HEADER -->
<table class="no-border mb-5">
    <tr>
        <td class="header-address" style="width: 70%;">
            Agana Street, San Vicente,<br>
            Ivana, Batanes<br>
            Tel: +63918 325 5597
        </td>

        <td class="text-right" style="width: 30%;">
            <img src="{{ public_path('images/logo.png') }}" style="height: 100px;">
        </td>
    </tr>
</table>

<!-- BLUE BAR -->
<table class="invoice-bar mb-3">
    <tr>
        <td style="width: 70%;">Invoice {{ $dateIssued->format('Y') }}-{{ $invoice->id }}</td>
        <td class="text-right" style="width: 30%;">{{ $dateIssued->format('m.d.Y') }}</td>
    </tr>
</table>

<!-- BILL TO -->
<table class="section-box mb-5 only-top-bottom">
    <tr>
        <th>Bill To:</th>
        <th>Travel Date:</th>
    </tr>
    <tr>
        <td>
            <span class="bold">{{ $invoice->lead_guest_name }}</span><br>
            {{ $invoice->email }}
        </td>
        <td class="text-center">
            @if($arrival && $departure)
                {{ $arrival->format('F d, Y') }} – {{ $departure->format('F d, Y') }}
            @endif
        </td>
    </tr>
</table>

<!-- INSTRUCTIONS & BANK DETAILS -->
<div class="section-box mb-5">
    <table width="100%">
        <tr>
            <th style="text-align:left;">Instructions:</th>
            <th style="text-align:left;">Bank Deposit:</th>
        </tr>
        <tr>
            <td>
                You may pay this invoice through a bank deposit or direct transfer.
            </td>
            <td>
                Bank: Philippine National Bank, Basco, Batanes<br>
                Account Name: <strong>Maricel Agana</strong><br>
                Account Number: <strong>2277 7000 3453</strong>
            </td>
        </tr>
    </table>
</div>


<!-- PACKAGE TABLE -->
<table class="table mb-5">
    <tr>
        <th style="width: 15%;">Quantity</th>
        <th style="width: 55%;">Description</th>
        <th style="width: 30%;" class="text-center">Total Amount (PHP)</th>
    </tr>

    <tr>
        <td class="text-center">{{ $invoice->number_of_pax }} PAX</td>

        <td>
            {{ $invoice->tour_package }}

            @if($invoice->hotel_accommodation)
                <br>Hotel: <strong>{{ $invoice->hotel_accommodation }}</strong>
            @endif

            @if($invoice->adult_rate)
                <br>Php {{ number_format($invoice->adult_rate) }}/per adult
            @endif
        </td>

        <td class="text-center bold">
            {{ number_format($invoice->total_amount) }}
        </td>
    </tr>
</table>

<!-- DOWNPAYMENT TABLE -->
<table class="table mb-5">
    <tr>
        <th>Payment Due Date</th>
        <th class="text-center">Amount (PHP)</th>
    </tr>

    <tr>
        <td>{{ $dueDate ? $dueDate->format('F d, Y') : 'On or before due date' }}</td>
        <td class="text-center">{{ number_format($invoice->downpayment ?? 0) }}</td>
    </tr>

    <tr>
        <td>Upon Arrival</td>
        <td class="text-center bold">{{ number_format($invoice->balance) }}</td>
    </tr>
</table>

<!-- IMPORTANT INFO -->
<p class="section-title">Important Information:</p>
<p class="important">
    Please read the terms and conditions thoroughly. By paying this invoice, you agree to the
    terms stated in your booking. If you have any questions, kindly contact us. Failure to pay
    by the due date may incur additional charges. Once the initial deposit has been made, we
    will send your service voucher and itinerary.
</p>

<!-- FOOTER -->
<div class="footer-text">
    Thank you for your business!<br>
    Dios Mamajes! Dios Machivan Dinyo!<br>
    <span style="font-size: 9px;">(Thank you, God bless)</span>
</div>

</body>
</html>
