/*import Homepage from '.../pages/Homepage.vue'
import About from '.../pages/About.vue'
import Contact from '.../pages/Contact.vue'*/

export default {
    mode: 'history',
    routes: [
        { path: '/', name:'inicio', component: require('../pages/Inicio.vue').default },
        { path: '/dashboard', name:'dashboard', component: require('../pages/Inicio.vue').default },
        { path: '/chatbot-genai', name:'chatbot-genai', component: require('../pages/ChatbotGenAI/ChatbotGenAIPage.vue').default },

        //--- REPORTES ---
        { path: '/reporte_compras', component: require('../pages/Reportes/ReporteCompras.vue').default },
        { path: '/kardex_valorizado', component: require('../pages/Kardex/KardexIndex.vue').default },

        //--- REPORTES DIGEMID ---
        { path: '/digemid_observatorio', component: require('../pages/Reportes/ReportDigemidObservatorio.vue').default },
        { path: '/digemid_listado_precios', component: require('../pages/Reportes/ReportDigemidListado.vue').default },

        //--- REPORTES COMPROBANTES ---
        { path: '/reporte_comprobantes_general', component: require('../pages/Reportes/ReportInvoiceGeneral.vue').default },
        //--- End ---
        //--- END ---
        
        //--- PROCEDIMIENTOS ---
        //--- VENTAS ---
        //Comprobantes
        { path: '/comprobantes', component: require('../pages/Comprobantes/InvoicePage.vue').default },
        { path: '/comprobantes_visualize/:id', name: 'ComprobanteVisualize', component: require('../pages/Comprobantes/InvoiceVisualize.vue').default },
        { path: '/comprobantes_create', component: require('../pages/Comprobantes/InvoiceCreate.vue').default },

        //--- CAJA ---
        { path: '/caja_registro', name: 'cajaregistro', component: require('../pages/Caja/CashPage.vue').default },
        { path: '/egreso_registro', name: 'egresoregistro', component: require('../pages/Caja/ExpensesPage.vue').default },
        //--- End ---

        //--- COMPRAS ---
        //Compras
        { path: '/compras', component: require('../pages/Compras/PurchasesPage.vue').default },
        { path: '/compras_create', component: require('../pages/Compras/PurchasesCreatePage.vue').default },
        { path: '/compras_visualize/:id', name: 'CompraVisualize', component: require('../pages/Compras/PurchaseVisualize.vue').default },
        { path: '/compra_from_orden/:id', name: 'CreateCompraFromOrden', component: require('../pages/Compras/CompraFromOrden.vue').default },

        //Cotizaciones Proveedor
        { path: '/proveedor_cotizaciones', component: require('../pages/CotizacionesProveedor/index.vue').default },
        { path: '/proveedor_cotizacion_create', component: require('../pages/CotizacionesProveedor/create.vue').default },
        { path: '/proveedor_cotizaciones_view/:id', name: 'PrvCotizacionVisualize', component: require('../pages/CotizacionesProveedor/visualize.vue').default },

        //Ordenes de Compra
        { path: '/ordenes_compra', component: require('../pages/OrdenesCompra/index.vue').default },
        { path: '/orden_compra_create/:id', name:'OrdenCompraCreate', component: require('../pages/OrdenesCompra/create.vue').default },
        { path: '/orden_compra_view/:id', name: 'OrdenCompraVisualize', component: require('../pages/OrdenesCompra/visualize.vue').default },
        //--- End ---

        //--- Cuentas ---
        { path: '/cuentas_cobrar', component: require('../pages/Cuentas/CuentasCobrar.vue').default },
        { path: '/cuentas_pagar', component: require('../pages/Cuentas/CuentasPagar.vue').default },
        //--- End ---

        //--- Almacen ---
        { path: '/almacen', component: require('../pages/Almacen/AlmacenIndex.vue').default },
        //--- End ---
        //--- END ---
     
        //--- CATALOGOS ---
        //--- Productos ---
        { path: '/productos', name:'products', component: require('../pages/Productos/ProductPage.vue').default },
        { path: '/crear_productos', component: require('../pages/Productos/ProductCreatePage.vue').default },
        { path: '/editar_productos/:id', component: require('../pages/Productos/ProductEditPage.vue').default },

        { path: '/categorias', name:'category', component: require('../pages/Categorias/CategoryPage.vue').default }, 
        { path: '/marcas', component: require('../pages/Marcas/index.vue').default },
        { path: '/laboratorios', component: require('../pages/Laboratorios/index.vue').default },
        { path: '/condiciones_almacenamiento', component: require('../pages/CondicionesAlm/index.vue').default },
        { path: '/unidades_medida', name: 'unidades_medida', component: require('../pages/UnidadesMedida/UnidadesMedidaPage.vue').default },

        { path: '/lista_precios', component: require('../pages/ListaPrecios/ListaPrecioPage.vue').default },
        { path: '/lista_precios_detalle/:id', component: require('../pages/ListaPrecios/ListaPrecioDetallePage.vue').default },
        //--- End ---

        //--- Empresa ---
        { path: '/clientes', component: require('../pages/Clientes/CustomerPage.vue').default },
        { path: '/deuda_cliente/:id',  name:'customers', component: require('../pages/Clientes/CustomerDeudaPage.vue').default },

        { path: '/proveedores', name:'proveedores', component: require('../pages/Proveedores/ProveedoresPage.vue').default },
        { path: '/deuda_proveedor/:id', name: 'deudeproveedor', component: require('../pages/Proveedores/ProveedorVerDeuda.vue').default },
        
        { path: '/sucursales', component: require('../pages/Sucursales/index.vue').default },
        { path: '/empresas', component: require('../pages/Empresas/index.vue').default },
        //--- End ---
        //--- END ---

        //--- CONFIGURACION ---
        { path: '/usuarios', name:'user', component: require('../pages/Usuarios/UserPage.vue').default },
        { path: '/roles_permisos', component: require('../pages/Roles/UserRolesMainPage.vue').default },
        { path: '/series', name:'series', component: require('../pages/Series/SeriesMainPage.vue').default },
        //--- END ---
      ]
}
