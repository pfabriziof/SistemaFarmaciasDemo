<template>
    <v-navigation-drawer v-model="navDrawer" app dark class="blue-grey darken-3" id="default-drawer" :width="290">
        <v-list dense nav >

            <v-list-item class="mb-0 justify-space-between pl-3">
                <v-img style="margin:10px 0px" :src="navPic" />
            </v-list-item>

            <v-divider></v-divider>
            <v-list-item-group v-model="selectedItem" color="primary"  >
                <v-list-item link :to="'/dashboard'">
                    <v-list-item-icon>
                        <v-icon>mdi-view-dashboard-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Dashboard</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item v-if="$can('chatbot_genai', 'all')" link :to="'/chatbot-genai'">
                    <v-list-item-icon>
                        <v-icon>mdi-robot-outline</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Chatbot Gen AI</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <div class="pa-1 mt-2 overline" >REPORTES</div>
                <template v-for="(item, i) in reports_routes" :to="item.link">
                    <v-list-group
                        v-bind:key="'AA'+ i"
                        v-if="item.submenu && $can(item.gate, 'all')"
                        :prepend-icon="item.icon"
                        :value="false"
                        color="white"
                        dark>
                        <template v-slot:activator>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </template>

                        <v-list :value="true" no-action sub-group>
                            <div v-for="(subitem, j) in item.submenu" v-bind:key="'A'+ j">
                                <v-list-item
                                    v-if="$can(subitem.gate, 'all')"
                                    :to="subitem.link != '' ? '/'+subitem.link : ''"
                                    link>
                                    <v-list-item-icon>
                                        <v-icon>{{subitem.icon}}</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>{{subitem.title}}</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </div>
                        </v-list>
                    </v-list-group>

                    <v-list-item
                        v-else-if="$can(item.gate, 'all')"
                        v-bind:key="'AB'+ i"
                        :to="item.link != '' ? '/'+item.link : ''"
                        link>
                        <v-list-item-icon>
                            <v-icon>{{item.icon}}</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>

                <div class="pa-1 mt-2 overline">PROCEDIMIENTOS</div>
                <template v-for="(item, i) in procedures_routes" :to="item.link">
                    <v-list-group
                        v-bind:key="'BA'+ i"
                        v-if="item.submenu && $can(item.gate, 'all')"
                        :prepend-icon="item.icon"
                        :value="false"
                        color="white"
                        dark>
                        <template v-slot:activator>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </template>

                        <v-list :value="true" no-action sub-group>
                            <div v-for="(subitem, j) in item.submenu" v-bind:key="'A'+ j">
                                <v-list-item
                                    v-if="$can(subitem.gate, 'all')"
                                    :to="subitem.link != '' ? '/'+subitem.link : ''"
                                    link>
                                    <v-list-item-icon>
                                        <v-icon>{{subitem.icon}}</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>{{subitem.title}}</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </div>
                        </v-list>
                    </v-list-group>

                    <v-list-item 
                        v-else-if="$can(item.gate, 'all')"
                        v-bind:key="'BB'+ i"
                        :to="item.link != '' ? '/'+item.link : ''"
                        link>
                        <v-list-item-icon>
                            <v-icon>{{item.icon}}</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>

                <div class="pa-1 mt-2 overline">CATÁLOGOS</div>
                <template v-for="(item, i) in catalogs_routes" :to="item.link">
                    <v-list-group
                        v-bind:key="'CA'+ i"
                        v-if="item.submenu && $can(item.gate, 'all')"
                        :prepend-icon="item.icon"
                        :value="false"
                        color="white"
                        dark>
                        <template v-slot:activator>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </template>

                        <v-list :value="true" no-action sub-group>
                            <div v-for="(subitem, j) in item.submenu" v-bind:key="'A'+ j">
                                <v-list-item
                                    v-if="$can(subitem.gate, 'all')"
                                    :to="subitem.link != '' ? '/'+subitem.link : ''"
                                    link>
                                    <v-list-item-icon>
                                        <v-icon>{{subitem.icon}}</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>{{subitem.title}}</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </div>
                        </v-list>
                    </v-list-group>

                    <v-list-item 
                        v-else-if="$can(item.gate, 'all')"
                        v-bind:key="'CB'+ i"
                        :to="item.link != '' ? '/'+item.link : ''"
                        link>
                        <v-list-item-icon>
                            <v-icon>{{item.icon}}</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>
                

                <div class="pa-1 mt-2 overline">CONFIGURACIÓN</div>
                <template v-for="(item, i) in config_routes" :to="item.link">
                    <v-list-group
                        v-bind:key="'DA'+ i"
                        v-if="item.submenu && $can(item.gate, 'all')"
                        :prepend-icon="item.icon"
                        :value="false"
                        color="white"
                        dark>
                        <template v-slot:activator>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </template>

                        <v-list :value="true" no-action sub-group>
                            <div v-for="(subitem, j) in item.submenu" v-bind:key="'A'+ j">
                                <v-list-item
                                    v-if="$can(subitem.gate, 'all')"
                                    :to="subitem.link != '' ? '/'+subitem.link : ''"
                                    link>
                                    <v-list-item-icon>
                                        <v-icon>{{subitem.icon}}</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title>{{subitem.title}}</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </div>
                        </v-list>
                    </v-list-group>

                    <v-list-item 
                        v-else-if="$can(item.gate, 'all')"
                        v-bind:key="'DB'+ i"
                        :to="item.link != '' ? '/'+item.link : ''"
                        link>
                        <v-list-item-icon>
                            <v-icon>{{item.icon}}</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>{{item.title}}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </template>

                <v-list-item @click="LogoutSession">
                    <v-list-item-icon>
                        <v-icon color="red">mdi-power</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>Cerrar Sesión</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-item-group>
        </v-list>

    </v-navigation-drawer>
</template>

<script>
export default {
    data() {
        return {
            navDrawer: true,
            navPic: "/assets/images/logo_white.png",
            selectedItem: 0,
            reports_routes: [
                {title:'DIGEMID', icon:'mdi-folder', link:null, gate:"menu_digemid", submenu: [
                    {title:'Observatorio de Productos', icon:'mdi-file-document-outline', link:'digemid_observatorio', gate:"obsproductsreport_index"},
                    {title:'Listado de Precios', icon:'mdi-file-document-outline', link:'digemid_listado_precios', gate:"pricelistreport_index"},
                ]},
                {title:'Reporte Comprobantes', icon:'mdi-file-document-outline', link:'reporte_comprobantes_general', gate:"invoicereportgen_index", submenu: null},
                {title:'Reporte Compras', icon:'mdi-file-document-outline', link:'reporte_compras', gate:"comprasreport_index", submenu: null},
                {title:'Kárdex Valorizado', icon:'mdi-file-document-outline', link:'kardex_valorizado',gate:"kardexreport_index", submenu: null},
            ],
            procedures_routes: [
                {title:'Almacén', icon:'mdi-warehouse', link:'almacen', gate:"almacen_index", submenu: null},
                {title:'Ventas', icon:'mdi-point-of-sale', link:null, gate:"menu_ventas", submenu: [
                    {title:'Comprobantes', icon:'mdi-receipt', link:'comprobantes', gate:"invoice_index"},
                ]},
                {title:'Caja', icon:'mdi-cash-register', link:null, gate:"menu_caja", submenu: [
                    {title:'Apertura', icon:'mdi-cash-lock-open', link:'caja_registro', gate:"cajaopen_index"},
                    {title:'Egresos', icon:'mdi-cash-minus', link:'egreso_registro', gate:"cajaegresos_index"},
                ]},
                {title:'Compras', icon:'mdi-cart-variant', link:null, gate:"menu_compras", submenu: [
                    {title:'Compras', icon:'mdi-receipt', link:'compras', gate:"compras_index"},
                    {title:'Órdenes de Compra', icon:'mdi-receipt', link:'ordenes_compra', gate:"ordcomp_index"},
                    {title:'Cotizaciones', icon:'mdi-receipt', link:'proveedor_cotizaciones', gate:"cotizaciones_index"},
                ]},
                {title:'Cuentas', icon:'mdi-cash-multiple', link:null, gate:"menu_cuentas", submenu: [
                    {title:'Cuentas por Cobrar', icon:'mdi-receipt', link:'cuentas_cobrar', gate:"cuentascobrar_index"},
                    {title:'Cuentas por Pagar', icon:'mdi-receipt', link:'cuentas_pagar', gate:"cuentaspagar_index"},
                ]},
            ],
            catalogs_routes: [
                {title:'Productos', icon:'mdi-package-variant', link:null, gate:"menu_productos", submenu: [
                    {title:'Productos', icon:'mdi-package-variant-closed', link:'productos', gate:"products_index"},
                    {title:'Categorías', icon:'mdi-shape-outline', link:'categorias', gate:"categories_index"},
                    {title:'Marcas', icon:'mdi-package-variant-closed', link:'marcas', gate:"marcas_index"},
                    {title:'Laboratorios', icon:'mdi-microscope', link:'laboratorios', gate:"labs_index"},
                    {title:'Condiciones de Alm.', icon:'mdi-package-variant-closed', link:'condiciones_almacenamiento', gate:"condicionesalm_index"},
                    {title:'Unidades de Medida', icon:'mdi-scale-balance', link:'unidades_medida', gate:"unidadesmed_index"},
                    {title:'Lista de Precios', icon:'mdi-format-list-bulleted-type', link:'lista_precios', gate:"listsprice_index"},
                ]},
                {title:'Empresa', icon:'mdi-office-building', link:null, gate:"menu_empresa", submenu: [
                    {title:'Clientes', icon:'mdi-account-group-outline', link:'clientes', gate:"clients_index"},
                    {title:'Proveedores', icon:'mdi-account-details', link:'proveedores', gate:"proveedores_index"},
                    {title:'Sucursales', icon:'mdi-storefront-outline', link:'sucursales', gate:"sucursales_index"},
                    {title:'Empresas', icon:'mdi-office-building', link:'empresas', gate:"empresas_index"},
                ]},
            ],
            config_routes: [
                {title:'Series', icon:'mdi-numeric', link:'series', gate:"clients_index", submenu:null},
                {title:'Roles y Permisos', icon:'mdi-badge-account', link:'roles_permisos', gate:"roles_index", submenu:null},
                {title:'Usuarios', icon:'mdi-account-outline', link:'usuarios', gate:"users_index", submenu:null},
            ],
        }
    },
    mounted(){
        this.getEmpresaUsuario();
    },

    methods: {
        getEmpresaUsuario(){
            axios.get('api/getEmpresaUsuarioSesion').then((response) => {
                this.navPic = "../storage/files/uploads/"+response.data.file_path.filename;

            }).catch(e => {
                console.error(e);
            });
        },
        LogoutSession(){
            axios.post('/logout').then(response => {
                window.location.href = "login";
            });
        },
    },
}
</script>
