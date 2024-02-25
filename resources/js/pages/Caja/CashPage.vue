<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Apertura de Caja</h2>
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
                <v-col cols="4">
                    <div v-if="opened_cash.id_caja!=0">
                        <p>Fecha de Apertura: {{ opened_cash.fecha_apertura | formatDate}}</p>
                        <p>Monto Apertura: {{opened_cash.monto_apertura}}</p>
                    </div>
                </v-col>
                <v-col class="text-right">
                    <v-btn v-if="$can('cajaopen_create','all') && opened_cash.id_caja!=0" small color="primary" class="mr-2" @click="dialogCerrarCaja=true;">
                        Cerrar Caja
                        <v-icon>mdi-key</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('cajaopen_delete','all') && opened_cash.id_caja==0" small color="success" class="mr-2" @click="dialogAbrirCaja=true;">
                        Abrir Caja
                        <v-icon>mdi-key</v-icon>
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
                <template v-slot:[`item.fecha_cierre`]="{ item }">
                    <div v-if="item.fecha_cierre==null">
                        Sin Cierre
                    </div>
                    <div v-else>
                        {{ item.fecha_cierre | formatDate}}
                    </div>
                </template>
                <template v-slot:[`item.fecha_apertura`]="{ item }">
                    {{item.fecha_apertura | formatDate}}
                </template>
                <template v-slot:[`item.monto_cierre`]="{ item }">
                    <div v-if="item.monto_cierre==null"> Sin Cierre  </div>
                    <div v-else> {{item.monto_cierre}} </div>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn small icon @click="verDetalleCaja(item)"><v-icon small> mdi-file-eye-outline</v-icon></v-btn>
                    <v-btn small icon @click="generarPDF(item)"><v-icon small>mdi-file-pdf</v-icon></v-btn>
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

        <!-- Dialog Abrir Caja -->
        <v-dialog v-model="dialogAbrirCaja" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline">Abrir Caja</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="formAbrirCaja">
                        <v-row>
                        <v-col cols="12" sm="12"  md="12" >
                            <v-text-field v-model="formAbrirCaja.fecha" label="Fecha" :rules="requiredRules" disabled></v-text-field>
                            <v-text-field v-model="formAbrirCaja.hora" readonly label="Hora" :rules="requiredRules" disabled></v-text-field>
                            <v-text-field v-model="formAbrirCaja.monto_apertura" label="Monto de Apertura" placeholder="0,00" type="number" class="txt-number" autocomplete="off" :rules="requiredRules"></v-text-field>
                        </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>

                <v-card-actions>
                    <v-col class="text-right">
                        <v-btn color="blue darken-1" text @click="dialogAbrirCaja = false">Cancelar</v-btn>
                        <v-btn color="primary" @click="abrirCaja"> Guardar </v-btn>
                    </v-col>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Fin -->
        <!-- Dialog Cerrar Caja -->
        <v-dialog v-model="dialogCerrarCaja" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline">Cerrar Caja</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="formCerrarCaja">
                        <v-row>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="formCerrarCaja.fecha" label="Fecha" disabled></v-text-field>
                                <v-text-field label="Monto de Apertura" v-model="opened_cash.monto_apertura"
                                    placeholder="0,00"
                                    type="number"
                                    class="txt-number"
                                    autocomplete="off"
                                    readonly
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="formCerrarCaja.hora" disabled label="Hora"></v-text-field>
                                <v-text-field label="Monto de Cierre" v-model="montoFinal"
                                    placeholder="0,00"
                                    type="number"
                                    class="txt-number"
                                    autocomplete="off"
                                    readonly
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="12" md="6"  v-for="mt in montosDia" :key="mt.id_medio_pago">
                                <v-text-field v-model="mt.monto" :label="mt.medio_pago" readonly></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-col class="text-right">
                        <v-btn color="blue darken-1" text @click="dialogCerrarCaja = false">Cancel</v-btn>
                        <v-btn color="primary" @click="cerrarCajaConfirm">Aceptar</v-btn>
                    </v-col>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Fin -->
        <!-- Dialog Ver Detalle Caja -->
        <v-dialog v-model="dialogVerCaja" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline">Ver Detalle Caja</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="ver_caja.fecha_apertura" label="Fecha" readonly></v-text-field>
                                <v-text-field v-model="ver_caja.monto_apertura" label="Monto de Apertura" placeholder="0,00" type="number" class="txt-number" autocomplete="off"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="ver_caja.fecha_cierre" readonly label="Hora"></v-text-field>
                                <v-text-field v-model="ver_caja.monto_cierre" readonly label="Monto de Cierre" placeholder="0,00" type="number" class="txt-number" autocomplete="off"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
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
        opened_cash: {id_caja:0},
        
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
            {text: 'Apertura', sortable: false, value: 'fecha_apertura'},
            {text: 'Cierre', sortable: false, value: 'fecha_cierre'},
            {text: 'Monto Apertura', sortable: false, value: 'monto_apertura'},
            {text: 'Monto Cierre', sortable: false, value: 'monto_cierre'},
            {text: 'Usuario', sortable: false, value: 'usuario.name'},
            {text: 'Acciones', value: 'actions', sortable: false, align:'right'}
        ],
        menuFechaInicio: false,
        menuFechaFin: false,
        filter: {
            fechaInicio: "",
            fechaFin: "",
        },
        //--- End ---
        
        //--- Ventana Emergente ---
        dialogAbrirCaja: false,
        formAbrirCaja: {
            fecha: '',
            hora: '',
            monto_apertura: '',
        },
        dialogCerrarCaja: false,
        formCerrarCaja: {
            fecha: '',
            hora: '',
            monto_apertura: '',
        },
        dialogVerCaja: false,
        ver_caja: new Form({
            fecha_apertura: '',
            monto_apertura: '',
            fecha_cierre: '',
            monto_cierre: 0,
        }),
        //--- End ---
        
        datos:{},
        montosDia: [],
        montoFinal: 0,

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),

    mounted () {
        // this.filter.fechaInicio = this.firstDateMonth();
        this.filter.fechaFin    = this.todaysDateDefault();

        this.consultarCaja();
        this.getMontosDia();
    },

    methods: {
        //--- Datatable ---
        getRegistros(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();

            axios.get('api/caja?page='+ page +"&perPage="+perPage
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
            // this.filter.fechaInicio = this.firstDateMonth();
            this.filter.fechaFin    = this.todaysDateDefault();

            this.getRegistros();
        },
        generarPDF(item){
            window.open('/generarCajaPDF/'+item.id_caja);
        },
        //--- End ---
        
        //--- Caja Abierta pendiente de Cierre ---
        consultarCaja(){
            this.preloader = true;
            axios.get('/api/cajaAbierta').then(response => {
                if(response.data != ''){
                    this.opened_cash=response.data;
                }
                
            }).finally(() => (this.preloader = false));
        },
        getMontosDia(){
            this.preloader = true;
            axios.get('api/getMontosDelDia').then(response => {
                this.montosDia = response.data;
                response.data.forEach((val)  => {
                    this.montoFinal+=parseFloat(val.monto);
                });
            }).finally(() => (this.preloader = false));
        },
        cerrarCaja (item) {
            this.editedIndex = this.data.data.indexOf(item)
            this.formAbrirCaja = Object.assign({}, item)
            this.dialogCerrarCaja = true
        },
        cerrarCajaConfirm () {
            this.loadingTable = true;
            this.datos.caja = this.opened_cash;
            this.datos.caja_detalle = this.montosDia;
            axios.put('/api/caja/'+this.opened_cash.id_caja, this.datos).then(response => {
                Toast.fire({
                    icon: 'success',
                    title: response.data.message,
                });
                this.$router.go();
            }).finally(() => (this.loadingTable = false, this.dialogCerrarCaja = false));
        },
        //--- End ---

        //--- Ver Detalle Caja ---
        verDetalleCaja(item){
            this.dialogVerCaja = true;
            this.ver_caja.fill(item);
        },
        //--- End ---

        //--- Abrir Caja ---
        abrirCaja() {
            this.loadingTable = true;
            axios.post('/api/caja', this.formAbrirCaja).then(response => {
                Toast.fire({
                    icon: 'success',
                    title: response.data.message,
                });
                this.$router.go();
            }).finally(() => (this.loadingTable = false));
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

        todaysDate(){
            var date = new Date;
            return ((date.getDate() < 10)?"0":"") + date.getDate() +"-"+(((date.getMonth()+1) < 10)?"0":"") + (date.getMonth()+1) +"-"+ date.getFullYear();
        },
        todaysTime(){
            var date = new Date;
            return ((date.getHours() < 10)?"0":"") + date.getHours() +":"+ ((date.getMinutes() < 10)?"0":"") + date.getMinutes() +":"+ (date.getSeconds()<10?"0":"") + date.getSeconds();;
        },
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        //--- End ---
    },
    computed: {
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
        dialogAbrirCaja(){
            if(!this.dialogAbrirCaja){
                // console.log('Dialog is closing');
                this.editedIndex = -1;
                this.$refs.formAbrirCaja.reset();
                
            }else{
                this.formAbrirCaja.fecha = this.todaysDate();
                this.formAbrirCaja.hora  = this.todaysTime();
            }
        },
        dialogCerrarCaja(){
            if(!this.dialogCerrarCaja){
                // console.log('Dialog is closing');
                this.editedIndex = -1;
                
            }else{
                this.formCerrarCaja.fecha = this.todaysDate();
                this.formCerrarCaja.hora  = this.todaysTime();
            }
        }
    },
}
</script>