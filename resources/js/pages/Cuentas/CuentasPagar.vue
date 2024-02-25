<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Cuentas Por Pagar</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de proveedor o número de documento"
                        v-model="filter.searchTerm"
                        append-icon="mdi-magnify"
                        class="flex-grow-1 mr-1"
                        hide-details
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
                                hide-details
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
                                hide-details
                            ></v-text-field>
                        </template>
                        <v-date-picker no-title v-model="filter.fechaFin" @input="menuFechaFin = false" locale="es-ES"></v-date-picker>
                    </v-menu>
                </v-col>
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
            <v-row dense class="pa-2 align-center">
                <v-col class="text-right">
                    <v-btn small color="success" class="mr-2"  :href="urltoreturn">
                        Exportar Reporte<v-icon>mdi-file-excel</v-icon>
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
                <template v-slot:[`item.fecha_emision`]="{ item }">
                    {{item.fecha_emision | formatDate}}
                </template>
                <template v-slot:[`item.nro_comprobante`]="{ item }">
                    {{item.serie_factura}}-{{item.nro_factura| zerosPadStart}}
                </template>
                <template v-slot:[`item.add_payment`]="{ item }">
                    <v-btn v-if="$can('cuentaspagar_update', 'all') && item.total_monto_pendiente > 0"
                        @click="agregarPago(item)"
                        color="success"
                        small>
                        <v-icon>mdi-plus</v-icon>Agregar Pago
                    </v-btn>
                    <v-chip small v-else-if="item.total_monto_pendiente > 0" class="ma-2" color="error">Deuda Pendiente</v-chip>
                    <v-chip small v-else class="ma-2" color="success">Deuda Saldada</v-chip>
                </template>
                <template v-slot:[`item.detail_payment`]="{ item }">
                    <v-btn small color="primary" @click="verDetalleDeuda(item)"><v-icon>mdi-magnify</v-icon>Ver Detalle</v-btn>
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

        <!-- Add Pago Dialog -->
        <v-dialog v-model="dialogPayment" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline">Agregar Pago</span>
                </v-card-title>
                <v-divider></v-divider>
                <br>
                <v-card-text>
                    <v-form ref="formPayment" @submit.prevent="registrarPago()">
                        <v-row>
                            <v-col>
                                <h4>Compra: Factura #{{form_pago.serie_factura}}-{{form_pago.nro_factura | zerosPadStart}}</h4>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field v-model="form_pago.total_deuda" label="Total Deuda" disabled></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model="form_pago.total_pagado" label="Total Pagado" disabled></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model="form_pago.total_pendiente" label="Saldo Pendiente" disabled></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-alert border="top" colored-border type="info" elevation="2" dense>Desde aqui usted puede agregar pagos a la deuda seleccionada. <b>tenga en cuenta los montos introducidos.</b></v-alert>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-menu
                                    v-model="menuFechaPago"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field label="Fecha de Pago" prepend-icon="mdi-calendar"
                                            v-model="formatoFechaPago"
                                            v-bind="attrs"
                                            v-on="on"
                                            readonly
                                            hide-details
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker no-title v-model="form_pago.fecha_pago" @input="menuFechaPago = false" locale="es-ES"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model="form_pago.monto_pago" label="Monto *" placeholder="0,00" type="number" class="txt-number" :rules="requiredRules" autocomplete="off"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                 <v-textarea v-model="form_pago.comentario"  rows="1" label="Comentarios/Observaciones"></v-textarea>
                            </v-col>
                        </v-row>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="dialogPayment = false" >Cancelar</v-btn>
                            <v-btn type="submit" color="primary"> Guardar </v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-- Fin -->
        <!-- Ver Detalle Deuda -->
        <v-dialog v-model="dialogDetailP" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline">Detalle Deuda</span>
                </v-card-title>
                <v-divider></v-divider>
                <br>
                <v-card-text>
                    <v-simple-table>
                        <tbody>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto Pagado</th>
                                <th>Comentario</th>
                                <th v-if="$can('cuentaspagar_delete', 'all')">Acciones</th>
                            </tr>
                            <tr v-if="deuda_pagos == ''">
                                <td colspan="5" class="text-center"><b>Aún no se han agregado pagos a esta deuda.</b></td>
                            </tr>
                            <tr v-for="pago in deuda_pagos.data" :key="pago.id">
                                <td>{{pago.fecha | formatDate}}</td>
                                <td>{{pago.monto_pagado}}</td>
                                <td>{{pago.comentario}}</td>
                                <td v-if="$can('cuentaspagar_delete', 'all')">
                                    <v-btn small icon @click="deletePago(pago.id_pago)">
                                        <v-icon small> mdi-delete</v-icon>
                                    </v-btn>    
                                </td>
                            </tr>
                        </tbody>
                    </v-simple-table>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-- Fin -->
    </div>
</template>

<script>
export default {
    data: () => ({
        preloader: false,
        urltoreturn: '',
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
            {text: 'Fecha', value: 'fecha_emision', sortable: false},
            {text: 'Nro. Comprobante', value: 'nro_comprobante', sortable: false},
            {text: 'Proveedor', value: 'nombre', sortable: false},
            {text: 'Monto Deuda Pendiente', value: 'total_monto_pendiente', sortable: false, align: "center"},
            {text: 'Agregar Pago', value: 'add_payment', sortable: false, align: "center"},
            {text: 'Detalle', value: 'detail_payment', sortable: false, align: "center"},
        ],
        menuFechaInicio: false,
        menuFechaFin: false,
        filter: {
            searchTerm: "",
            fechaInicio: "",
            fechaFin: "",
        },
        //--- End ---

        //--- Dialog Add Payment ---
        dialogPayment: false,
        menuFechaPago: false,
        form_pago: new Form({
            id_deuda: '',
            total_deuda: '',
            total_pagado: '',
            total_pendiente: '',
            //Datos Comprobante
            serie_factura: '',
            nro_factura: '',
            //Monto pago
            monto_pago: '',
            fecha_pago: '',
            comentario: '',
        }),
        //--- End ---

        //--- Dialog Detail Payment ---
        dialogDetailP: '',
        deuda_pagos: '',
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),
    mounted(){
        // this.filter.fechaInicio = this.firstDateMonth();
        this.filter.fechaFin    = this.todaysDateDefault();
        this.form_pago.fecha_pago = this.todaysDateDefault();
        this.getExportData();
    },
    methods:{
        //--- Datatable ---
        getRegistros(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();
            
            axios.get('api/getCuentasPagar?page='+ page +"&perPage="+perPage
                    +"&sortDesc="+sortDesc
                    +"&sortBy="+sortBy
                    +"&"+myParams
            ).then(response => {
                this.dataReg     = response.data;
                this.currentPage = this.dataReg.current_page;
                this.pageCount    = this.dataReg.last_page;
                this.totalReg    = this.dataReg.total;
                this.getExportData();
                
            }).finally(() => (this.loadingTable = false));
        },
        limpiarFiltros(){
            this.filter = {};
            // this.filter.fechaInicio = this.firstDateMonth();
            this.filter.fechaFin    = this.todaysDateDefault();

            this.getRegistros();
        },
        getExportData(){
            const exportdata={
                searchTerm: this.filter.searchTerm,
                fechaInicio: this.filter.fechaInicio,
                fechaFin:    this.filter.fechaFin,
            }
            this.urltoreturn = '/exportarCuentasPagar/'+JSON.stringify(exportdata);
        },
        //--- End ---

        //--- Add Payment Functions ---
        agregarPago(item){
            this.dialogPayment = true;

            this.form_pago.serie_factura     = item.serie_factura;
            this.form_pago.nro_factura       = item.nro_factura;

            this.form_pago.id_deuda      = item.id_deuda;
            this.form_pago.total_deuda   = item.total_deuda;
            this.form_pago.total_pagado  = item.total_monto_pagado;
            this.form_pago.total_pendiente  = item.total_monto_pendiente;
        },
        registrarPago(){
            this.preloader = true;
            this.loading = true;
            this.form_pago.post('/api/deuda_compra').then((result)=>{
                this.dialogPayment = false;
                this.getRegistros();
                Toast.fire({
                    icon: 'success',
                    title: 'Pago registrado correctamente!'
                });

            }).catch(e => {
                var error_messages = e.response.data.errors;
                for (let i in error_messages) {
                    Toast.fire({
                        icon: 'error',
                        title: error_messages[i][0],
                    });
                }
                
            }).finally(() => (this.preloader = false, this.loading = false));
        },
        //--- End ---

        //--- Deuda Detalle Functions ---
        verDetalleDeuda(item, page=1){
            this.preloader = true;
            axios.post('/api/getDeudaCompraPagos?page=' + page, item).then(response => {
                this.deuda_pagos = response.data.data !=''? response.data : '';
                this.dialogDetailP = true;

            }).finally(() => (this.preloader = false));
        },
        deletePago(id){
            Swal.fire({
                title: 'Eliminar Pago',
                text: "¿Está seguro que desea eliminar este pago?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.value == true) {
                    axios.post('/api/deleteDeudaCompraPago/'+id).then((response)=>{                     
                        Swal.fire(
                            'Eliminado!',
                            'El pago ha sido eliminado.',
                            'success'
                        );
                        this.dialogDetailP = false;
                        this.getRegistros();
                    
                    }).catch(e => {
                        console.error(e);
                        Toast.fire({
                            icon: 'error',
                            title: "Ocurrió un error",
                        });
                    });
                }
            });
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
        formatoFechaPago: {
            get: function () {
                return this.formatDate(this.form_pago.fecha_pago)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        //--- End ---
    },
    watch: {
        dataTabOptions(event) {
            this.itemsPerPage = event.itemsPerPage;
            this.getRegistros(event.page, event.itemsPerPage);
        },
        dialogPayment(){
            if(!this.dialogPayment){
                // this.$refs.formPayment.reset();
                this.form_pago.serie_factura = '';
                this.form_pago.nro_factura   = '';

                this.form_pago.id_deuda      = '';
                this.form_pago.total_deuda   = '';
                this.form_pago.total_pagado  = '';
                this.form_pago.total_pendiente  = '';

                this.form_pago.fecha_pago = this.todaysDateDefault();
                this.form_pago.monto_pago = '';
                this.form_pago.comentario = '';
            }
        },
    },
}
</script>