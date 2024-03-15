<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComprobanteMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $address = config('mail.from.address');
        $filename = 'PUR-'.time().'.pdf';
        return $this->view('emails.GenericMailFormat')
                    ->subject('Demo Farmacias - Envío de Comprobante')
                    ->from($address, "Admin Farmacias")
                    ->attachData($this->data['pdf_attachment'], $filename)
                    ->with([
                        'content' => 'Estimado/a ' .$this->data['to_name']. ', informamos que la venta ha sido emitida satisfactoriamente.',
                    ]);
    }
}
