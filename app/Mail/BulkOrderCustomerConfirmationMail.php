<?php

namespace App\Mail;

use App\Models\BulkOrderInquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BulkOrderCustomerConfirmationMail extends Mailable
{
    public function __construct(public BulkOrderInquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your bulk order request'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails/bulk-orders/customer-confirmation');
    }
}
