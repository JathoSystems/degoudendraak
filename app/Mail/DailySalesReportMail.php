<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\DailySalesReport;

class DailySalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public DailySalesReport $report
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dagelijks Verkooprappport - ' . $this->report->report_date->format('d-m-Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-sales-report',
            with: [
                'report' => $this->report,
                'priceExVat' => ($this->report->total_sales / 106) * 100,
                'vatAmount' => $this->report->total_sales - (($this->report->total_sales / 106) * 100),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->report->getFileExists()) {
            $attachments[] = Attachment::fromStorageDisk('public', $this->report->file_path)
                ->as('dagelijks-verkooprappport-' . $this->report->report_date->format('Y-m-d') . '.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }
        
        return $attachments;
    }
}
