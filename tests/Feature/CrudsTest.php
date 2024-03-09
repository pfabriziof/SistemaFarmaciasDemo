<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;

class CrudsTest extends TestCase
{
    public function test_product_index()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        
        $response = $this->get('/api/producto', [
            'searchTerm' => "",
            'perPage' => 15,
        ]);
        // dd($response);
        $response->assertStatus(200);
    }
    
    
    public function test_product_create()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->post('/api/producto', [
            "data" => [
                "codigo_producto" => "PR00TEST",
                "nombreProducto" => "PRODTEST",
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
                    "id_lista_precio" => 1,
                    "precio_compra" => 1000,
                    "precio_venta" => 2000,
                    "unidades" => 1000,
                ]
            ]
        ]);
        
        $response->assertStatus(200);
    }

    
    public function test_product_update()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/api/productos', [
            'searchTerm' => "",
            'perPage' => 15,
        ]);

        $response->assertStatus(200);
    }

    /*
    public function test_product_delete()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/api/productos', [
            'searchTerm' => "",
            'perPage' => 15,
        ]);

        $response->assertStatus(200);
    }*/
}
