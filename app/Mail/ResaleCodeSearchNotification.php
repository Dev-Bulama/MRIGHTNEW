<?php

namespace App\Mail;

use App\Models\Receipt;
use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResaleCodeSearchNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Receipt $originalReceipt,
        public Shop $searchingShop
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Your Phone Resale Code is Being Searched - M-right Digital',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.resale-code-search',
            with: [
                'originalReceipt' => $this->originalReceipt,
                'searchingShop' => $this->searchingShop,
                'phoneDetails' => [
                    'name' => $this->originalReceipt->phone_name,
                    'color' => $this->originalReceipt->phone_color,
                    'serial' => $this->originalReceipt->phone_serial_number,
                ],
                'customerName' => $this->originalReceipt->customer_name,
                'originalDate' => $this->originalReceipt->created_at->format('M d, Y'),
                'shopName' => $this->searchingShop->shop_name ?? 'Unknown Shop',
                'shopLocation' => $this->searchingShop->shop_address ?? 'Location not provided',
                'shopPhone' => $this->searchingShop->phone_number ?? 'Contact not provided',
                'searchTime' => now()->format('M d, Y \a\t g:i A'),
                'supportUrl' => route('help.contact'),
                'verifyUrl' => route('public.verify', $this->originalReceipt->receipt_number),
            ]
        );
    }
}