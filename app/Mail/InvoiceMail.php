<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        // 1) Generate the invoice PDF
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
        ]);

        return $this->subject('Batanes Trip Invoice')
            ->view('emails.invoice')
            // dynamic invoice PDF
            ->attachData(
                $pdf->output(),
                'invoice-' . $this->invoice->id . '.pdf',
                ['mime' => 'application/pdf']
            )
            // static Terms & Conditions
            ->attach(public_path('attachments/terms-and-conditions.pdf'), [
                'as'   => 'Terms-and-Conditions.pdf',
                'mime' => 'application/pdf',
            ])
            // DOT accreditation image
            ->attach(public_path('attachments/dot-accreditation.jpg'), [
                'as'   => 'DOT-Accreditation.jpg',
                'mime' => 'image/jpeg', // change to image/png if PNG
            ])
            ->with([
                'invoice' => $this->invoice,
            ]);
    }
}
