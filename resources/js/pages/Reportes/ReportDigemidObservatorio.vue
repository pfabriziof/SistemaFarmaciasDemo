<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Observatorio de Productos</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-select :items="combo_sucursales" label="Sucursal" v-model="filter.id_sucursal"
                        item-text="nombre_sucursal"
                        item-value="id_sucursal"
                        hide-details
                        @change="getRegistros"
                    ></v-select>
                </v-col>
            </v-row>
        </v-card>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col v-if="$can('obsproductsreport_export', 'all')" class="text-right">
                    <v-btn small color="success" class="mr-2" :href="urltoreturn">
                        Exportar Excel <v-icon>mdi-file-excel</v-icon>
                    </v-btn>            
                </v-col>
            </v-row>
            <v-data-table
                :headers="headers"
                :items="data_reg.data"
                :items-per-page="itemsperpage"
                class="elevation-1"
                :footer-props="{
                    itemsPerPageOptions: [25,50,100,250,500],
                    'items-per-page-text':'Registros por página',
                }"
                :loading="loadingTable" loading-text="Cargando... Por favor, espere">
                <template slot="no-data">
                    Aún no se han agregado registros.
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
export default {
    data: () => ({
        preloader: false,
        urltoreturn: '',
        //--- Datatable ---
        loadingTable: false,
        current_page: 1,
        itemsperpage: 25,
        total_reg: 0,
        dataTabOptions: {},
        data_reg: {},
        headers: [
            {text: 'CodProd',  value: 'codigo_producto', sortable: false, align:'center'},
            {text: 'Fecha', value: 'fecha', sortable: false, align:'center'},
            {text: 'Producto', value: 'nombre_producto', sortable: false, align:'center'},
            {text: 'Prec. Empaque S/.', value: 'precio_emp', sortable: false, align:'center'},
            {text: 'Prec. Unitario S/.', value: 'precio_und', sortable: false, align:'center'},
        ],

        filter:{
            id_sucursal: 1,
        },
        combo_sucursales: [],
        //--- End ---
    }),
    mounted() {
        this.sucursalesCombo();
        this.getRegistros();
    },
    methods:{
        //--- Carga de Datos ---
        sucursalesCombo(){
            this.preloader = true;

            axios.get('/api/sucursalesCombo').then((response) => {
                this.combo_sucursales = response.data;
                
            }).catch(e => {
                console.error(e);
            }).finally(() => (this.preloader = false));
        },
        //--- End ---

        //--- Datatable ---
        getRegistros(page = 1, per_page = 25){
            this.loadingTable = true;
            this.data_reg = [];

            let dataParameters = {
                per_page: per_page,
                data: this.filter,
            };

            axios.post('api/getDigemidObservatorio?page=' + page, dataParameters).then(response => {
                this.data_reg     = response.data;
                this.current_page = page;
                // this.total_reg    = this.data_reg.length;

                this.getExportData();
                
            }).finally(() => (this.loadingTable = false));
        },
        
        getExportData(){
            this.urltoreturn = '/exportarReporteObservatorio/' + this.filter.id_sucursal;
        },
        //--- End ---
    },
}
</script>