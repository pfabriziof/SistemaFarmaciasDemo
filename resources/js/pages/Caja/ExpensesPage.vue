<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Caja - Egresos</h2>
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
                <v-col class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="addDialog=true;">
                        Agregar Egreso <v-icon>mdi-plus</v-icon>
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
                <template v-slot:[`item.fecha_egreso`]="{ item }">
                    {{item.fecha_egreso | formatDate}} {{item.fecha_egreso | formatTime}}
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn small icon @click="editReg(item)">
                        <v-icon small>mdi-file-eye-outline</v-icon>
                    </v-btn>
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

        <!-- Add/Edit Dialog -->
        <v-dialog v-model="addDialog" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline" v-show="!dialogEditar">Agregar Egreso</span>
                    <span class="headline" v-show="dialogEditar">Visualizar Egreso</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm" @submit.prevent="!dialogEditar ? null : createReg()">
                        <v-row>
                            <v-col cols="12" sm="6"  md="6">
                                <v-text-field v-model="addForm.fecha_egreso" label="Fecha" disabled></v-text-field>
                                <v-select label="Tipo de Egreso *" v-model="addForm.id_tipo_egreso" :items="tipos_egreso"
                                    item-text="tipo_egreso" 
                                    item-value="id_tipo_egreso"
                                    :rules="requiredRules"
                                    :readonly="dialogEditar"
                                ></v-select>
                                <v-select label="Motivo *" v-model="addForm.id_motivo_egreso"
                                    :items="motivos_egreso" 
                                    item-text="motivo" 
                                    item-value="id_egreso_motivo"
                                    :rules="requiredRules"
                                    :readonly="dialogEditar"
                                ></v-select>
                            </v-col>
                            <v-col cols="12" sm="6"  md="6">
                                <v-select label="Método de Gasto *" v-model="addForm.metodo_gasto" :items="metodos_gasto"
                                    item-text="name" 
                                    item-value="id"
                                    :rules="requiredRules"
                                    :readonly="dialogEditar"
                                ></v-select>
                                <v-text-field v-model="addForm.monto" label="Monto de Egreso *" type="number" 
                                    class="txt-number"
                                    autocomplete="off" 
                                    :rules="requiredRules" 
                                    :readonly="dialogEditar"
                                ></v-text-field>
                                <v-text-field v-model="addForm.detalle" label="Detalle" 
                                    autocomplete="off"
                                    :readonly="dialogEditar"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-card-actions v-if="$can('cajaegresos_create','all') && !dialogEditar">
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="addDialog = false">Cancelar</v-btn>
                            <v-btn type="submit" color="primary"> Guardar </v-btn>
                        </v-card-actions>
                    </v-form>
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
            // {text: 'ID', value: 'id_egreso', sortable: false},
            {text: 'Fecha y Hora', value: 'fecha_egreso', sortable: false},
            {text: 'Tipo Egreso', value: 'tipo_egreso.tipo_egreso', sortable: false},
            {text: 'Motivo', value: 'motivo_egreso.motivo', sortable: false},
            {text: 'Monto', value: 'monto', sortable: false},
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
        addDialog: false,
        dialogEditar: false,
        addForm: new Form({
            id_tipo_egreso: null,
            id_motivo_egreso: null,
            metodo_gasto: null,
            fecha_egreso: null,
            monto: null,
            detalle: null,
        }),
        defaultAddForm: {
        },
        
        tipos_egreso: [],
        motivos_egreso: [],
        metodos_gasto: [
            { id:1, name:'Caja Chica' },
            { id:2, name:'Cuenta Bancaria' },
        ],
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),

    mounted () {
        // this.filter.fechaInicio = this.firstDateMonth();
        this.filter.fechaFin    = this.todaysDateDefault();
        this.addForm.fecha_egreso = this.todaysDateDefault();

        this.tiposEgresosCombo();
        this.motivosEgresoCombo();
    },

    methods: {
        //--- Carga de Datos ---
        tiposEgresosCombo(){
            axios.get('/api/tiposEgresosCombo').then(response => {
                this.tipos_egreso = response.data;
            });
        },
        motivosEgresoCombo(){
            axios.get('/api/motivosEgresoCombo').then(response => {
                this.motivos_egreso = response.data;
            });
        },
        //--- End ---

        //--- Datatable ---
        getRegistros(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();

            axios.get('api/egresos?page='+ page +"&perPage="+perPage
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
        //--- End ---

        //--- Ventana Emergente ---
        // Add/Edit/Delete Functions
        createReg(){
            this.preloader = true;
            this.loadingTable = true;

            axios.post('api/egresos', this.addForm).then((response)=>{
                this.addDialog = false;
                this.getRegistros();

                Toast.fire({
                    icon: 'success',
                    title: response.data.message,
                });
            }).catch(e => {
                var error_messages = e.response.data.errors;
                for (let i in error_messages) {
                    Toast.fire({
                        icon: 'error',
                        title: error_messages[i][0],
                    });
                }
            }).finally(()=>(this.loadingTable = false, this.preloader = false));
        },
        editReg(item){
            this.dialogEditar = true;
            this.addForm = Object.assign({}, item);
            this.addDialog = true;
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
        //--- Fin Date Formatting ---
    },
    watch: {
        dataTabOptions(event) {
            this.itemsPerPage = event.itemsPerPage;
            this.getRegistros(event.page, event.itemsPerPage);
        },
        addDialog(){
            if(!this.addDialog){
                this.dialogEditar = false;
                this.$refs.addForm.resetValidation();
                this.addForm = Object.assign({}, this.defaultAddForm);
                this.addForm.fecha_egreso = this.todaysDateDefault();
            }
        },
    },
}
</script>
