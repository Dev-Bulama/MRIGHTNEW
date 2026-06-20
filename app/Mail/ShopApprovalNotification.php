<?php

namespace App\Mail;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShopApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Shop $shop,
        public string $status, // 'approved' or 'rejected'
        public string $message = ''
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? 'Shop Approved - Start Generating Receipts!'
            : 'Shop Registration Update';
            
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shop-approval',
            with: [
                'shop' => $this->shop,
                'status' => $this->status,
                'message' => $this->message,
                'dashboardUrl' => route('dashboard'),
                'shopUrl' => route('shop.show', $this->shop),
            ]
        );
    }
}