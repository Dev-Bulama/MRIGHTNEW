<?php

namespace App\Mail;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $receipt;

    /**
     * Create a new message instance.
     */
    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Digital Receipt - ' . $this->receipt->receipt_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
            with: [
                'receipt' => $this->receipt,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        try {
            // Load ALL relationships that the PDF template needs
            $this->receipt->load([
                'shop', 
                'user', 
                'parentReceipt.shop', 
                'parentReceipt.user'
            ]);
            
            // Generate PDF with proper configuration
            $pdf = Pdf::loadView('receipt.pdf', ['receipt' => $this->receipt])
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'dpi' => 150,
                          'defaultFont' => 'sans-serif',
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => false, // Disable remote content to avoid route issues
                      ]);
            
            return [
                \Illuminate\Mail\Mailables\Attachment::fromData(
                    fn () => $pdf->output(),
                    'receipt-' . $this->receipt->receipt_number . '.pdf'
                )->withMime('application/pdf')
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF attachment: ' . $e->getMessage(), [
                'receipt_id' => $this->receipt->id ?? 'unknown',
                'receipt_number' => $this->receipt->receipt_number ?? 'unknown',
                'error_trace' => $e->getTraceAsString()
            ]);
            // Return empty array so email still sends without PDF
            return [];
        }
    }
}