<?php

namespace App\Console\Commands;

use App\Models\TipoCambio;
use Illuminate\Console\Command;

class UpdateTipoCambio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-tipocambio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene el valor actual del tipo de cambio USD a PEN';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://www.sunat.gob.pe/a/txt/tipoCambio.txt',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            $response = explode('|', $response);
            // dd($response [2]);

            $cambio_usd = TipoCambio::find(1);
            $cambio_usd->cambio = (float) $response[2];
            $cambio_usd->save();

        curl_close($curl);

        return Command::SUCCESS;
    }
}
