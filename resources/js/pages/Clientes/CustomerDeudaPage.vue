<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">{{cliente.nombre}}</h2> 
                <h2 style="color: #37474F">Cuenta Corriente</h2> 
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
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
                        <v-date-picker no-title v-model="filter.fecha_inicio" @input="menuFechaInicio = false" locale="es-ES"></v-date-picker>
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
                        <v-date-picker no-title v-model="filter.fecha_fin" @input="menuFechaFin = false" locale="es-ES"></v-date-picker>
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
            <v-data-table
                :headers="headers"
                :items="data_reg.data"
                :page="current_page"
                :items-per-page="itemsperpage"
                :server-items-length="total_reg"
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
                <template v-slot:[`item.fecha`]="{ item }">
                    {{item.fecha | formatDate}}
                </template>
                <template v-slot:[`item.add_payment`]="{ item }">
                    <v-btn small v-if="$can('cuentascobrar_update', 'all') && item.total_monto_pendiente > 0" color="success" @click="agregarPago(item)">
                        <v-icon>mdi-plus</v-icon>Agregar Pago
                    </v-btn>
                    <v-chip small v-if="item.total_monto_pendiente <= 0" class="ma-2" color="success">Deuda Saldada</v-chip>  
                </template>
                <template v-slot:[`item.detail_payment`]="{ item }">
                    <v-btn small color="primary" @click="verDetalleDeuda(item)"><v-icon>mdi-magnify</v-icon>Ver Detalle</v-btn>  
                </template>
            </v-data-table>
            <div class="text-center pt-2">
                <v-pagination
                    v-model="current_page"
                    :length="pageCount"
                    :disabled="total_reg<=0"
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
                    <v-form ref="form_payment" @submit.prevent="registrarPago()">
                        <v-row>
                            <v-col>
                                <h4>Comprobante: {{form_payment.tipo_comprobante}} #{{form_payment.serie}}-{{form_payment.correlativo}}</h4>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field v-model="form_payment.total_deuda" label="Total Deuda" disabled></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model="form_payment.total_pagado" label="Total Pagado" disabled></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model="form_payment.total_pendiente" label="Saldo Pendiente" disabled></v-text-field>
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
                                    <v-date-picker no-title v-model="form_payment.fecha_pago" @input="menuFechaPago = false" locale="es-ES"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model="form_payment.monto_pago" label="Monto *" placeholder="0,00" type="number" class="txt-number" :rules="requiredRules" autocomplete="off"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                 <v-textarea v-model="form_payment.comentario"  rows="1" label="Comentarios/Observaciones"></v-textarea>
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
                                <th v-if="$can('cuentascobrar_delete', 'all')">Acciones</th>
                            </tr>
                            <tr v-if="deuda_pagos.length <= 0">
                                <td colspan="5" class="text-center"><b>Aún no se han agregado pagos a esta deuda.</b></td>
                            </tr>
                            <tr v-for="pago in deuda_pagos.data" :key="pago.id">
                                <td>{{pago.fecha}}</td>
                                <td>{{pago.monto_pagado}}</td>
                                <td>{{pago.comentario}}</td>
                                <td v-if="$can('cuentascobrar_delete', 'all')">
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
        breadcrumbs: [{
            text: 'Clientes',
            disabled: false,
            to: '/clientes'
        }, {
            text: 'Ver Deuda'
        }],
        //--- Datatable ---
        loadingTable: false,
        current_page: 1,
        pageCount: 1,
        itemsperpage: 25,
        total_reg: 0,
        dataTabOptions: {},
        data_reg: {},
        headers: [
            {text: 'Fecha', sortable: false, value: 'fecha'},
            {text: 'Monto Deuda', sortable: false, value: 'total_deuda'},
            {text: 'Cancelado', sortable: false, value: 'total_monto_pagado'},
            {text: 'Pendiente', sortable: false, value: 'total_monto_pendiente'},
            {text: 'Agregar Pago', sortable: false, value: 'add_payment', align: "right"},
            {text: 'Detalle', sortable: false, value: 'detail_payment', align: "right"},
        ],
        menuFechaInicio: false,
        menuFechaFin: false,
        filter: {
            id_cliente: 0,
            fecha_inicio: null,
            fecha_fin: null,
        },
        cliente: {},
        //--- End ---

        //--- Dialog Add Payment ---
        dialogPayment: false,
        menuFechaPago: false,
        form_payment: new Form({
            id_deuda: null,
            total_deuda: null,
            total_pagado: null,
            total_pendiente: null,
            //Datos Comprobante
            tipo_comprobante: null,
            serie: null,
            correlativo: null,
            //Monto pago
            monto_pago: null,
            fecha_pago: null,
            comentario: null,
        }),
        defaultFormPayment: {
            id_deuda: null,
            total_deuda: null,
            total_pagado: null,
            total_pendiente: null,
            //Datos Comprobante
            tipo_comprobante: null,
            serie: null,
            correlativo: null,
            //Monto pago
            monto_pago: null,
            fecha_pago: null,
            comentario: null,
        },
        //--- End ---

        //--- Dialog Detail Payment ---
        dialogDetailP: false,
        deuda_pagos: [],
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),

    mounted(){
        this.filter.id_cliente = this.$route.params.id;
        this.filter.fecha_inicio = this.firstDateMonth();
        this.filter.fecha_fin    = this.todaysDateDefault();
        this.form_payment.fecha_pago = this.todaysDateDefault();
        this.getCliente();
    },

    methods: {
        //--- Datatable ---
        getCliente(){
            this.preloader = true;
            axios.get('/api/getClienteDeuda/'+this.filter.id_cliente).then((response) => {
                this.cliente = response.data;

            }).catch(e => {
                console.error(e);

            }).finally(() => (this.preloader = false)); 
        },
        getRegistros(page = 1, per_page = 25){
            this.loadingTable = true;
            this.data_reg = [];

            let dataParameters = {
                per_page: per_page,
                data: this.filter,
            };

            axios.post('/api/getDeudasCliente?page=' + page, dataParameters).then(response => {
                this.data_reg     = response.data;
                this.current_page = this.data_reg.current_page;
                this.pageCount    = this.data_reg.last_page;
                this.total_reg    = this.data_reg.total;

            }).finally(() => (this.loadingTable = false));
        },
        limpiarFiltros(){
            this.filter.fecha_inicio = null;
            this.filter.fecha_fin    = null;
            this.getRegistros();
        },
        //--- End ---

        //--- Add Payment Functions ---
        agregarPago(item){
            this.dialogPayment = true;

            this.form_payment.tipo_comprobante = item.comprobante.tipo_comprobante.tipo_comprobante;
            this.form_payment.serie            = item.comprobante.serie.serie;
            this.form_payment.correlativo      = item.comprobante.correlativo;

            this.form_payment.id_deuda      = item.id_deuda;
            this.form_payment.total_deuda   = item.total_deuda;
            this.form_payment.total_pagado  = item.total_monto_pagado;
            this.form_payment.total_pendiente  = item.total_monto_pendiente;
        },
        registrarPago(){
            this.preloader = true;
            this.loadingTable = true;
            axios.post('/api/deuda', this.form_payment).then((result)=>{
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
                
            }).finally(() => (this.preloader = false, this.loadingTable = false));
        },
        //--- End ---

        //--- Deuda Detalle Functions ---
        verDetalleDeuda(item, page=1){
            this.preloader = true;
            axios.post('/api/getDeudaPagos?page=' + page, item).then(response => {
                this.deuda_pagos = response.data.data != []? response.data : [];
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
                    axios.post('/api/deleteDeudaPago/'+id).then((response)=>{                     
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
                return this.formatDate(this.filter.fecha_inicio)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        formatoFechaFin: {
            get: function () {
                return this.formatDate(this.filter.fecha_fin)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        formatoFechaPago: {
            get: function () {
                return this.formatDate(this.form_payment.fecha_pago)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        //--- Fin Date Formatting ---
    },
    watch: {
        dialogPayment(){
            if(!this.dialogPayment){
                this.$refs.form_payment.resetValidation();
                this.form_payment = Object.assign({}, this.defaultFormPayment);
                this.form_payment.fecha_pago = this.todaysDateDefault();
            }
        },
    },
}
</script>