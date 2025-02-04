<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice {$this->invoice->number} from " . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'paymentLink' => route('invoice.pay', $this->invoice),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->generatePDF(), 
                "Invoice-{$this->invoice->number}.pdf"
            )
            ->withMime('application/pdf'),
        ];
    }

    protected function generatePDF()
    {
        // We'll use your existing print view for the PDF
        return \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'invoices.print', 
            ['invoice' => $this->invoice->load(['customer', 'items.productVariation.product', 'paymentTerms'])]
        )->output();
    }
} 