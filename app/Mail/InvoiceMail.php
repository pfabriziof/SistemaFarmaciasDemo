<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = config('mail.from.address');
        $pdf_filename = 'INV-'.time().'.pdf';
        $xml_filename = 'XML-'.time().'.xml';
        return $this->view('emails.GenericMailFormat')
                    ->subject('Envío de Comprobante')
                    ->from($address, "Comprobante")
                    ->attachData($this->data['pdf_attachment'], $pdf_filename)
                    ->attachData($this->data['xml_attachment'], $xml_filename)
                    ->with([
                        'content' => 'Estimado/a ' .$this->data['to_name']. ', informamos que su comprobante ha sido emitido exitosamente.',
                    ]);
    }
}
