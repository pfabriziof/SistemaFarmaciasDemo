<?php

namespace Database\Seeders;

use App\Models\ProductoTipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosTiposSeeder extends Seeder
{
    public function run()
    {
        ProductoTipo::create([
            'id_producto_tipo' => 1,
            'tipo' => 'Gravado - Operación Onerosa',
            'impuesto' => 1,
            'icbper' => null,
        ]);

        ProductoTipo::create([
            'id_producto_tipo' => 2,
            'tipo' => 'Gravado - Retiro por premio',
            'impuesto' => 1,
            'icbper' => null,
        ]);

        ProductoTipo::create([
            'id_producto_tipo' => 3,
            'tipo' => 'Gravado - Retiro por donación',
            'impuesto' => 1,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 4,
            'tipo' => 'Gravado - Retiro',
            'impuesto' => 1,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 5,
            'tipo' => 'Gravado - Retiro por publicidad',
            'impuesto' => 1,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 6,
            'tipo' => 'Gravado - Bonificaciones',
            'impuesto' => 1,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 7,
            'tipo' => 'Gravado - Retiro por entrega a trabajadores',
            'impuesto' => 1,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 8,
            'tipo' => 'Exonerado - Operación Onerosa',
            'impuesto' => 3,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 9,
            'tipo' => 'Exonerado - Transferencia Gratuita',
            'impuesto' => 3,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 10,
            'tipo' => 'Inafecto - Operación Onerosa',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 11,
            'tipo' => 'Inafecto - Retiro por Bonificación',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 12,
            'tipo' => 'Inafecto - Retiro',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 13,
            'tipo' => 'Inafecto - Retiro por Muestras Médicas',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 14,
            'tipo' => 'Inafecto - Retiro por Convenio Colectivo',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 15,
            'tipo' => 'Inafecto - Retiro por premio',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 16,
            'tipo' => 'Inafecto - Retiro por publicidad',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 17,
            'tipo' => 'Inafecto - Transferencia gratuita',
            'impuesto' => 2,
            'icbper' => null,
        ]);
        
        ProductoTipo::create([
            'id_producto_tipo' => 18,
            'tipo' => 'Impuesto al Consumo de las bolsas de Plástico',
            'impuesto' => 2,
            'icbper' => null,
        ]);
    }
}