<p>Dear {{ $invoice->lead_guest_name }},</p>

<p>Thank you for booking with Northern Island Travel & Tours.</p>

<p>
    Please find your invoice attached as a PDF.<br>
    Tour Package: {{ $invoice->tour_package }}<br>
    Total Amount: ₱{{ number_format($invoice->total_amount, 2) }}<br>
    Balance: ₱{{ number_format($invoice->balance, 2) }}
</p>

<p>
    Arrival: {{ $invoice->arrival_date }}<br>
    Departure: {{ $invoice->departure_date }}
</p>

<p>If you have any questions, feel free to reply to this email.</p>

<p>Best regards,<br>
Northern Island Travel & Tours</p>
