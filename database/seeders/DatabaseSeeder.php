<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TiposCambioSeeder::class);
        $this->call(TiposComprobantesSeeder::class);
        $this->call(TiposDocumentoSeeder::class);
        $this->call(TiposEgresoSeeder::class);
        $this->call(TiposMovimientoSeeder::class);
        $this->call(CompraEstadosSeeder::class);
        $this->call(EgresosMotivosSeeder::class);
        $this->call(CondicionesAlmacenamientoSeeder::class);

        $this->call(RolesPermissionsSeeder::class);
        $this->call(EmpresasSeeder::class);
        $this->call(SucursalesSeeder::class);
        $this->call(ListasPreciosSeeder::class);
        $this->call(LaboratoriosSeeder::class);
        $this->call(MarcasSeeder::class);
        $this->call(MedioPagoSeeder::class);
        $this->call(MonedasSeeder::class);
        $this->call(UnidadesMedidaSeeder::class);
        $this->call(CompressedTablesSeeder::class);

        $this->call(UsersSeeder::class);
        $this->call(ProveedoresSeeder::class);
        $this->call(SeriesInvoiceSeeder::class);

        $this->call(ProductoCategoriasSeeder::class);
        $this->call(ProductosTiposSeeder::class);
        $this->call(ProductosSeeder::class);

        $this->call(LotesProductosSeeder::class);
    }
}
