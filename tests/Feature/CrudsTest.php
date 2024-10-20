<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;

class CrudsTest extends TestCase
{
    public function test_product_index(){
        $user = User::find(2);
        Passport::actingAs($user);
        
        $response = $this->get('/api/producto', [
            'searchTerm' => "",
            'perPage' => 15,
        ]);
        $response->assertStatus(200);
    }
    
    
    public function test_product_create(){
        $user = User::find(2);
        Passport::actingAs($user);

        $response = $this->post('/api/producto', [
            "data" => [
                "id_producto" => 4,
                "codigo_producto" => "PRODTEST",
                "nombreProducto" => "PRODTEST",
                "id_marca" => 2,
                "id_categoria" => 1,
                "id_unidad_medida" => 2,
                "id_laboratorio" => 1,
                "id_condicion_alm" => 1,
                "id_tipo_producto" => 10,
                "stock" => "0",
                "stock_minimo" => "0",
                "ubicacion" => "Almacen",
            ],
            "lotes"=>[],
            "list_detail"=>[
                [
                    "id_lista_precio" => 1,
                    "precio_compra" => 1000,
                    "precio_venta" => 2000,
                    "unidades" => 1000,
                ]
            ]
        ]);
        
        $response->assertStatus(200);
    }

    
    public function test_product_update(){
        $user = User::find(2);
        Passport::actingAs($user);

        $id_producto = 4;
        $response = $this->put('/api/producto/'.$id_producto, [
            "data" => [
                "codigo_producto" => "PRODTEST_UPDATED",
                "nombreProducto" => "PRODTEST_UPDATED",
                "id_marca" => 2,
                "id_categoria" => 1,
                "id_laboratorio" => 1,
                "id_condicion_alm" => 1,
                "id_tipo_producto" => 10,
                "stock" => "0",
                "stock_minimo" => "0",
                "ubicacion" => "Almacen",
            ],
            "lotes"=>[],
            "list_detail"=>[
                [
                    "id_lista_detalle" => 10,
                    "id_lista_precio" => 1,
                    "precio_compra" => 1000,
                    "precio_venta" => 2000,
                    "unidades" => 1000,
                    "estado" => 1,
                ]
            ]
        ]);

        $response->assertStatus(200);
    }

    
    public function test_product_delete()
    {
        $user = User::find(2);
        Passport::actingAs($user);

        $id_producto = 4;
        $response = $this->delete('/api/producto/'.$id_producto);

        $response->assertStatus(200);
    }
}
