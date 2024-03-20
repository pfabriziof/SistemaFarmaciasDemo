<?php

namespace App\Console\Commands;

use App\Models\Caja;
use App\Models\CajaDetalle;
use App\Models\MedioPago;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Ramsey\Uuid\v1;

class CerrarCaja extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cerrar-cajas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'En caso exista una caja abierta, se cierra';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Se obtienen las cajas abiertas
        $cajas_abiertas = Caja::whereNull("fecha_cierre")->get();

        // Se obtienen los montos del dia de cada caja
        $cajas_montos = array();
        $medios_pago = MedioPago::all();
        foreach($cajas_abiertas as $caja){
            $montos_dia = array();
            foreach ($medios_pago as $medio_pago){
                $ventas_caja = DB::table('comprobantes')
                    ->where([
                        ["created_at", '>=', $caja->fecha_apertura],
                        ["id_medio_pago",$medio_pago->id_medio_pago],
                        ["id_sucursal",$caja->id_sucursal],
                    ])
                    ->select(DB::raw('SUM(total) as total'))
                    ->first();

                if(!isset($ventas_caja->total)){
                    $medio_pago->monto = 0;
                }else{
                    $medio_pago->monto = (float) $ventas_caja->total;
                }
                // array_push($montos_dia, $medio_pago);
                $montos_dia[] = array(
                    "id_medio_pago" => $medio_pago->id_medio_pago,
                    "monto" => $medio_pago->monto
                );
            }

            $cajas_montos[]=array(
                "id_caja" => $caja->id_caja,
                "monto_apertura" => $caja->monto_apertura,
                "montos_dia" => $montos_dia
            );
        }

        // var_dump($cajas_montos);

        foreach($cajas_montos as $caja_detalle){

            $monto_cierre = (float) $caja_detalle["monto_apertura"];
            foreach($caja_detalle["montos_dia"] as $medio_monto){
                CajaDetalle::create([
                    'id_caja'  => $caja_detalle["id_caja"],
                    'id_medio_pago' => $medio_monto["id_medio_pago"],
                    'monto' =>$medio_monto["monto"]
                ]);

                $monto_cierre += (float) $medio_monto["monto"];
            }

            $tmp_caja = Caja::find($caja_detalle["id_caja"]);
            $tmp_caja->fecha_cierre = date('Y-m-d H:i:s');
            $tmp_caja->monto_cierre = $monto_cierre;
            $tmp_caja->save();
        }

        return Command::SUCCESS;
    }
}
