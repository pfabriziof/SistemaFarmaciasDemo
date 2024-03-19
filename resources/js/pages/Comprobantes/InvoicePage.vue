<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Comprobantes</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de cliente o número de documento"
                        v-model="filter.searchTerm"
                        append-icon="mdi-magnify"
                        class="flex-grow-1 mr-1"
                    ></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-menu
                        v-model="menuFechaInicio"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field label="Fecha de Inicio" prepend-icon="mdi-calendar"
                                v-model="formatoFechaInicio"
                                v-bind="attrs"
                                v-on="on"
                                readonly
                            ></v-text-field>
                        </template>
                        <v-date-picker no-title v-model="filter.fechaInicio" @input="menuFechaInicio = false" locale="es-ES"></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="2">
                    <v-menu
                        v-model="menuFechaFin"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field label="Fecha de Fin" prepend-icon="mdi-calendar"
                                v-model="formatoFechaFin"
                                v-bind="attrs"
                                v-on="on"
                                readonly
                            ></v-text-field>
                        </template>
                        <v-date-picker no-title v-model="filter.fechaFin" @input="menuFechaFin = false" locale="es-ES"></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="2">
                    <v-select label="Tipo de Comprobante" v-model="filter.id_tipo_comprobante"
                        :items="tipos_comp"
                        item-text="tipo_comprobante"
                        item-value="id_tipo_comprobante"
                        ></v-select>
                </v-col>
                <!-- <v-col cols="2">
                    <v-select label="Estado de Documento" v-model="filter.id_estado"
                        :items="estados_comp"
                        item-text="estado"
                        item-value="id_estado_comprobante"
                        ></v-select>
                </v-col> -->
                <v-col class="text-right">
                    <v-btn color="primary" class="mr-2" @click="getRegistros">
                        <v-icon>mdi-magnify</v-icon>Buscar
                    </v-btn>
                    <v-btn class="mr-2" @click="limpiarFiltros">
                        <v-icon>mdi-reload</v-icon>Limpiar
                    </v-btn>
                </v-col>
            </v-row>
        </v-card>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center" v-if="$can('invoice_create', 'all')">
                <v-col v-if="!opened_caja" cols="12">
                    <v-alert dense type="info">Debe <b>aperturar una caja</b> para poder generar comprobantes.</v-alert>
                </v-col>
                <v-col v-else class="text-right">
                    <v-btn small color="primary" class="mr-2" :to="'/comprobantes_create'">
                        Agregar Comprobante<v-icon>mdi-plus</v-icon>
                    </v-btn>            
                </v-col>
            </v-row>
            <v-data-table
                :headers="headers"
                :items="dataReg.data"
                :page="currentPage"
                :items-per-page="itemsPerPage"
                :server-items-length="totalReg"
                :options.sync="dataTabOptions"
                class="elevation-1"
                :footer-props="{
                    itemsPerPageOptions: [25,50,100,250,500],
                    'items-per-page-text':'Registros por página',
                }"
                :loading="loadingTable" loading-text="Cargando... Por favor, espere">
                <template slot="no-data">
                    Aún no se han agregado registros.
                </template>
                <template #[`item.index`]="{ item }">
                    {{ dataReg.data.indexOf(item) + 1 }}
                </template>
                <template v-slot:[`item.nro_comprobante`]="{ item }">
                    {{item.serie.serie}}-{{item.correlativo | zerosPadStart}}
                </template>
                <template v-slot:[`item.fecha_emision`]="{ item }">
                    {{item.fecha_emision | formatDate}}
                </template>
                <template v-slot:[`item.estado`]="{ item }">
                    <v-chip v-if="item.id_estado_comprobante == 1" class="ma-2" color="success">Aprobado</v-chip>
                    <v-chip v-if="item.id_estado_comprobante == 2" class="ma-2" color="danger">Anulado</v-chip>
                    <v-chip v-if="item.id_estado_comprobante == 3" class="ma-2" color="error">Rechazado</v-chip>
                    <v-chip v-if="item.id_estado_comprobante == 4" class="ma-2" color="warning">Pendiente</v-chip>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn small icon :to="'/comprobantes_visualize/'+item.id_comprobante">
                        <v-icon small> mdi-file-eye-outline</v-icon>
                    </v-btn>
                    <v-btn small icon @click="generarPDF(item)">
                        <v-icon small>mdi-file-pdf</v-icon>
                    </v-btn>
                    <template v-if="item.id_estado_comprobante != 2">
                        <v-btn small icon v-if="item.external_id" @click="verXml(item)">
                            <v-icon small>mdi-xml</v-icon>
                        </v-btn>
                    </template>
                </template>
            </v-data-table>
            <div class="text-center pt-2">
                <v-pagination
                    v-model="currentPage"
                    :length="pageCount"
                    :disabled="totalReg<=0"
                    circle
                ></v-pagination>
            </div>
        </v-card>
    </div>
</template>
 
<script>
export default {
    data: () => ({
        preloader: false,
        opened_caja: null,
        //--- Datatable ---
        loadingTable: false,
        currentPage: 1,
        pageCount: 1,
        itemsPerPage: 25,
        totalReg: 0,
        dataTabOptions: {},
        dataReg: {},
        headers: [
            {text: 'Nº', value: 'index',  sortable: false},
            {text: 'Nro. Comprobante', value: 'nro_comprobante', sortable: false, align:'center'},
            {text: 'Cliente', value: 'nombreCliente', sortable: false},
            {text: 'Tipo', value: 'tipo_comprobante.tipo_comprobante', sortable: false},
            {text: 'Emitido', value: 'fecha_emision', sortable: false},
            {text: 'Acciones', value: 'actions', sortable: false, align:'right'},
        ],
        // {text: 'Estado', value: 'estado', sortable: false, align:'center'},
        menuFechaInicio: false,
        menuFechaFin: false,
        filter: {
            searchTerm: "",
            fechaInicio: "",
            fechaFin: "",
            id_tipo_comprobante: "",
            id_estado: "",
        },
        tipos_comp: [],
        estados_comp: [],
        //--- End ---
    }),

    mounted () {
        // this.filter.fechaInicio = this.firstDateMonth();
        this.filter.fechaFin    = this.todaysDateDefault();

        this.consultarCaja();
        this.estadosInvoiceCombo();
        this.tiposComprobantesCombo();
    },

    methods: {
        //--- Datatable ---
        getRegistros(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();
            
            axios.get('api/comprobante?page='+ page +"&perPage="+perPage
                    +"&sortDesc="+sortDesc
                    +"&sortBy="+sortBy
                    +"&"+myParams
            ).then(response => {
                this.dataReg     = response.data;
                this.currentPage = this.dataReg.current_page;
                this.pageCount    = this.dataReg.last_page;
                this.totalReg    = this.dataReg.total;
                
            }).finally(() => (this.loadingTable = false));
        },
        limpiarFiltros(){
            this.filter = {};
            // this.filter.fechaInicio = this.firstDateMonth();
            this.filter.fechaFin    = this.todaysDateDefault();

            this.getRegistros();
        },
        verPdf(item){
            window.open('https://pruebas.bytesoluciones.net/print/document/'+item.external_id+'/'+item.formato_impresion);
            
        },
        verXml(item){
            window.open('https://pruebas.bytesoluciones.net/downloads/document/xml/'+item.external_id);
        },

        generarPDF(item){
            window.open('/generarComprobantePDF/'+item.id_comprobante);
        },
        //--- End ---


        //--- Carga de Datos ---
        tiposComprobantesCombo(){
            axios.get('api/tiposComprobantesCombo').then((response) => {
                this.tipos_comp = response.data;
            }).catch(e => {
                console.error(e);
            });
        },
        estadosInvoiceCombo(){
            axios.get('api/estadosInvoiceCombo').then((response) => {
                this.estados_comp = response.data;

            }).catch(e => {
                console.error(e);
            }) 
        },
        consultarCaja(){
            this.preloader = true;
            axios.get('/api/getCajaAbierta').then(response => {
                if(response.data != ''){
                    this.opened_caja = response.data;
                }
            }).finally(() => (this.preloader = false));
        },
        //--- End ---


        //--- Date Formatting ---
        todaysDateDefault(){
            var date = new Date;
            return date.getFullYear() +"-" + (((date.getMonth()+1) < 10)?"0":"") + (date.getMonth()+1) +"-" + ((date.getDate() < 10)?"0":"") + date.getDate();
        },
        firstDateMonth(){
            // var date = new Date(new Date().getFullYear(), 0, 1);
            var date = new Date;
            date.setDate(1);

            return date.getFullYear() +"-" + (((date.getMonth()+1) < 10)?"0":"") + (date.getMonth()+1) +"-" + ((date.getDate() < 10)?"0":"") + date.getDate();
        },
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        //--- End ---
    },
    computed: {
        //--- Date Formatting ---
        formatoFechaInicio: {
            get: function () {
                return this.formatDate(this.filter.fechaInicio)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        formatoFechaFin: {
            get: function () {
                return this.formatDate(this.filter.fechaFin)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
    },
    watch: {
        dataTabOptions(event) {
            this.itemsPerPage = event.itemsPerPage;
            this.getRegistros(event.page, event.itemsPerPage);
        },
    },
}
</script>