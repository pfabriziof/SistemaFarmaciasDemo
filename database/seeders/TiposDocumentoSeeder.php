<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposDocumentoSeeder extends Seeder
{
    public function run()
    {
        TipoDocumento::create([
            'id_tipo_doc' => 1,
            'tipo_documento' => 'RUC',
            'codigo_sunat' => '6',
        ]);
        TipoDocumento::create([
            'id_tipo_doc' => 2,
            'tipo_documento' => 'DNI',
            'codigo_sunat' => '1',
        ]);
        TipoDocumento::create([
            'id_tipo_doc' => 4,
            'tipo_documento' => 'CARNET DE EXTRANJERIA',
            'codigo_sunat' => '4',
        ]);
        TipoDocumento::create([
            'id_tipo_doc' => 7,
            'tipo_documento' => 'PASAPORTE',
            'codigo_sunat' => '7',
        ]);
    }
}
