<?php

namespace App\Mail;

use App\Models\ContactEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, string|null>  $details
     */
    public function __construct(public ContactEnquiry $enquiry, public array $details, public ?string $context = null) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'EdHut enrollment request: '.($this->details['Certification'] ?? 'Course').' · '.$this->enquiry->name,
            replyTo: [new Address($this->enquiry->email, $this->enquiry->name)],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'emails.enrollment-request');
    }
}
