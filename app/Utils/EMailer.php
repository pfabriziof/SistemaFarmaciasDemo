<?php

namespace App\Utils;

use App\Mail\CompraMail;
use App\Mail\CotizacionMail;
use App\Mail\InvoiceMail;
use App\Mail\OrdenCompraMail;
use Illuminate\Support\Facades\Mail;

class EMailer
{

    private static function getMailInstance($type, $data)
    {
        switch ($type) {
            case 'compra_mail':
                return new CompraMail($data);
                break;

            case 'cotizacion_mail':
                return new CotizacionMail($data);
                break;

            case 'orden_compra_mail':
                return new OrdenCompraMail($data);
                break;

            default:
                return null;
        }
    }

    public static function send($type, $to, $data)
    {
        try {
            $instance = self::getMailInstance($type, $data);
            $response = Mail::to($to)->send($instance);

            return $response;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
}