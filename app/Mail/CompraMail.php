<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompraMail extends Mailable
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

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = config('mail.from.address');
        $filename = 'PUR-'.time().'.pdf';
        return $this->view('emails.GenericMailFormat')
                    ->subject('Envío de Compra')
                    ->from($address, "Compra")
                    ->attachData($this->data['pdf_attachment'], $filename)
                    ->with([
                        'content' => 'Estimado/a ' .$this->data['to_name']. ', informamos que la compra ha sido emitida exitosamente.',
                    ]);
    }
}
