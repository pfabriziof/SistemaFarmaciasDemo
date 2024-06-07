<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //--- Roles ---
        $role_superadmin = Role::create([
            'name' => 'superadmin',
            'title' => 'Superadmin',
        ]);
        $role_admin = Role::create([
            'name' => 'admin',
            'title' => 'Administrador',
        ]);
        $role_accounting = Role::create([
            'name' => 'accounting',
            'title' => 'Contabilidad',
        ]);
        $role_warehouse = Role::create([
            'name' => 'warehouse',
            'title' => 'Almacén',
        ]);
        $role_cashier = Role::create([
            'name' => 'cashier',
            'title' => 'Cajero',
        ]);
        //--- End ---

        //--- Permissions ---
        Permission::create([
            'name' => 'users_create',
            'title' => 'Crear usuarios'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'users_index',
            'title' => 'Leer usuarios'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'users_update',
            'title' => 'Actualizar usuarios'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'users_delete',
            'title' => 'Eliminar usuarios'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'roles_create',
            'title' => 'Crear roles'
        ])->syncRoles([$role_superadmin]);
        Permission::create([
            'name' => 'roles_index',
            'title' => 'Leer roles'
        ])->syncRoles([$role_superadmin]);
        Permission::create([
            'name' => 'roles_update',
            'title' => 'Actualizar roles'
        ])->syncRoles([$role_superadmin]);
        Permission::create([
            'name' => 'roles_delete',
            'title' => 'Eliminar roles'
        ])->syncRoles([$role_superadmin]);

        Permission::create([
            'name' => 'permissions_create',
            'title' => 'Crear permisos'
        ])->syncRoles([$role_superadmin]);
        Permission::create([
            'name' => 'permissions_index',
            'title' => 'Leer permisos'
        ])->syncRoles([$role_superadmin]);
        Permission::create([
            'name' => 'permissions_update',
            'title' => 'Actualizar permisos'
        ])->syncRoles([$role_superadmin]);
        Permission::create([
            'name' => 'permissions_delete',
            'title' => 'Eliminar permisos'
        ])->syncRoles([$role_superadmin]);
        Permission::create([
            'name' => 'assign_permissions',
            'title' => 'Asignar permisos'
        ])->syncRoles([$role_superadmin]);

        Permission::create([
            'name' => 'seriesinv_create',
            'title' => 'Crear series comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'seriesinv_index',
            'title' => 'Leer series comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'seriesinv_update',
            'title' => 'Actualizar series comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'seriesinv_delete',
            'title' => 'Eliminar series comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'proveedores_create',
            'title' => 'Crear proveedores'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'proveedores_index',
            'title' => 'Leer proveedores'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'proveedores_update',
            'title' => 'Actualizar proveedores'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'proveedores_delete',
            'title' => 'Eliminar proveedores'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'empresas_create',
            'title' => 'Crear empresas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'empresas_index',
            'title' => 'Leer empresas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'empresas_update',
            'title' => 'Actualizar empresas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'empresas_delete',
            'title' => 'Eliminar empresas'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'products_create',
            'title' => 'Crear productos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'products_index',
            'title' => 'Leer productos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'products_update',
            'title' => 'Actualizar productos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'products_delete',
            'title' => 'Eliminar productos'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'categories_create',
            'title' => 'Crear categorias'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'categories_index',
            'title' => 'Leer categorias'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'categories_update',
            'title' => 'Actualizar categorias'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'categories_delete',
            'title' => 'Eliminar categorias'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'marcas_create',
            'title' => 'Crear marcas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'marcas_index',
            'title' => 'Leer marcas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'marcas_update',
            'title' => 'Actualizar marcas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'marcas_delete',
            'title' => 'Eliminar marcas'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'labs_create',
            'title' => 'Crear laboratorios'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'labs_index',
            'title' => 'Leer laboratorios'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'labs_update',
            'title' => 'Actualizar laboratorios'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'labs_delete',
            'title' => 'Eliminar laboratorios'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'condicionesalm_create',
            'title' => 'Crear condiciones alm.'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'condicionesalm_index',
            'title' => 'Leer condiciones alm.'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'condicionesalm_update',
            'title' => 'Actualizar condiciones alm.'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'condicionesalm_delete',
            'title' => 'Eliminar condiciones alm.'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'unidadesmed_create',
            'title' => 'Crear unidades medida'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'unidadesmed_index',
            'title' => 'Leer unidades medida'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'unidadesmed_update',
            'title' => 'Actualizar unidades medida'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'unidadesmed_delete',
            'title' => 'Eliminar unidades medida'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'listsprice_create',
            'title' => 'Crear listas precio'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'listsprice_index',
            'title' => 'Leer listas precio'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'listsprice_update',
            'title' => 'Actualizar listas precio'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'listsprice_delete',
            'title' => 'Eliminar listas precio'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'historicing_create',
            'title' => 'Crear historico ingresos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'historicing_index',
            'title' => 'Leer historico ingresos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'historicsal_create',
            'title' => 'Crear historico salidas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'historicsal_index',
            'title' => 'Leer historico salidas'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'almacen_index',
            'title' => 'Leer almacen'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'cuentascobrar_create',
            'title' => 'Crear cuentas cobrar'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cuentascobrar_index',
            'title' => 'Leer cuentas cobrar'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cuentascobrar_update',
            'title' => 'Actualizar cuentas cobrar'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cuentascobrar_delete',
            'title' => 'Eliminar cuentas cobrar'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'cuentaspagar_create',
            'title' => 'Crear cuentas pagar'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cuentaspagar_index',
            'title' => 'Leer cuentas pagar'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cuentaspagar_update',
            'title' => 'Actualizar cuentas pagar'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cuentaspagar_delete',
            'title' => 'Eliminar cuentas pagar'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'compras_create',
            'title' => 'Crear compras'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'compras_index',
            'title' => 'Leer compras'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'compras_update',
            'title' => 'Actualizar compras'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'compras_delete',
            'title' => 'Eliminar compras'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'ordcomp_create',
            'title' => 'Crear ordenes compra'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'ordcomp_index',
            'title' => 'Leer ordenes compra'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'ordcomp_update',
            'title' => 'Actualizar ordenes compra'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'ordcomp_delete',
            'title' => 'Eliminar ordenes compra'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'cotizaciones_create',
            'title' => 'Crear cotizaciones'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cotizaciones_index',
            'title' => 'Leer cotizaciones'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cotizaciones_update',
            'title' => 'Actualizar cotizaciones'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cotizaciones_delete',
            'title' => 'Eliminar cotizaciones'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'cajaopen_create',
            'title' => 'Crear caja apertura'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cajaopen_index',
            'title' => 'Leer caja apertura'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cajaopen_update',
            'title' => 'Actualizar caja apertura'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cajaopen_delete',
            'title' => 'Eliminar caja apertura'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'cajaegresos_create',
            'title' => 'Crear caja egresos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cajaegresos_index',
            'title' => 'Leer caja egresos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cajaegresos_update',
            'title' => 'Actualizar caja egresos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'cajaegresos_delete',
            'title' => 'Eliminar caja egresos'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'clients_create',
            'title' => 'Crear clientes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'clients_index',
            'title' => 'Leer clientes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'clients_update',
            'title' => 'Actualizar clientes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'clients_delete',
            'title' => 'Eliminar clientes'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'sucursales_create',
            'title' => 'Crear sucursales'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'sucursales_index',
            'title' => 'Leer sucursales'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'sucursales_update',
            'title' => 'Actualizar sucursales'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'sucursales_delete',
            'title' => 'Eliminar sucursales'
        ])->syncRoles([$role_superadmin, $role_admin]);

        //--- Reportes ---
        Permission::create([
            'name' => 'invoice_create',
            'title' => 'Crear comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'invoice_index',
            'title' => 'Leer comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'invoice_update',
            'title' => 'Actualizar comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'invoice_delete',
            'title' => 'Eliminar comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'kardexreport_index',
            'title' => 'Leer reporte kardex'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'kardexreport_export',
            'title' => 'Exportar reporte kardex'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'comprasreport_index',
            'title' => 'Leer reporte compras'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'comprasreport_export',
            'title' => 'Exportar reporte compras'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'invoicereportgen_index',
            'title' => 'Leer reporte general comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'invoicereportgen_export',
            'title' => 'Exportar reporte general comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'invoicereportform_index',
            'title' => 'Leer reporte formato comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'invoicereportform_export',
            'title' => 'Exportar reporte formato comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'obsproductsreport_index',
            'title' => 'Leer reporte observatorio productos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'obsproductsreport_export',
            'title' => 'Exportar reporte observatorio productos'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'pricelistreport_index',
            'title' => 'Leer reporte listado precios'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'pricelistreport_export',
            'title' => 'Exportar reporte listado precios'
        ])->syncRoles([$role_superadmin, $role_admin]);

        Permission::create([
            'name' => 'lotesreport_index',
            'title' => 'Leer reporte lotes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'lotesreport_export',
            'title' => 'Exportar reporte lotes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        //--- End ---

        //--- Menu ---
        Permission::create([
            'name' => 'menu_productos',
            'title' => 'Menu productos'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_empresa',
            'title' => 'Menu empresa'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_historicadj',
            'title' => 'Menu historico ajustes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_cuentas',
            'title' => 'Menu cuentas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_compras',
            'title' => 'Menu compras'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_caja',
            'title' => 'Menu caja'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_ventas',
            'title' => 'Menu ventas'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_invreport',
            'title' => 'Menu reporte comprobantes'
        ])->syncRoles([$role_superadmin, $role_admin]);
        Permission::create([
            'name' => 'menu_digemid',
            'title' => 'Menu digemid'
        ])->syncRoles([$role_superadmin, $role_admin]);
        //--- End ---


        Permission::create([
            'name' => 'chatbot_genai',
            'title' => 'Chatbot Gen AI'
        ])->syncRoles([$role_superadmin, $role_admin]);
        //--- End ---
    }
}
