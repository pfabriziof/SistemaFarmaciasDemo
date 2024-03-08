<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CrudsTest extends TestCase
{
    public function test_product_index()
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
    public function test_product_create()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/api/productos', [
            'searchTerm' => "",
            'perPage' => 15,
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
