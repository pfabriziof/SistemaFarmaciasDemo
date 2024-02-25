<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Proveedor Cotizaciones</h2>
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
                <v-col v-if="$can('cotizaciones_create', 'all')" class="text-right">
                    <router-link to="/proveedor_cotizacion_create">
                        <v-btn small color="primary" class="mr-2">Agregar Cotización<v-icon>mdi-plus</v-icon></v-btn>
                    </router-link>
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
                <template v-slot:[`item.fecha_emision`]="{ item }">
                    {{item.fecha_emision | formatDate}}
                </template>
                <template v-slot:[`item.estado`]="{ item }">
                    <v-chip class="ma-2" :color="getStatusColor(item.id_estado)">{{item.estado.estado}}</v-chip>
                </template>
                
                <template v-slot:[`item.actions`]="{ item }">
                    <router-link :to="{name:'PrvCotizacionVisualize', params:{id: item.id_cotizacion_prv}}" style="text-decoration: none; color: inherit;">
                        <v-btn small icon><v-icon small> mdi-file-eye-outline</v-icon></v-btn>
                    </router-link>
                    <v-btn small icon @click="verPdf(item)">
                        <v-icon small>mdi-file-pdf</v-icon>
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
    </div>
</template>
<script>
export default {
    data: () => ({
        preloader: false,
        opened_caja: '',
        //--- Datatable ---
        loadingTable: false,
        currentPage: 1,
        pageCount: 1,
        itemsPerPage: 25,
        totalReg: 0,
        dataTabOptions: {},
        dataReg: {},
        headers: [
            {text: 'Nº', value: 'numeracion', sortable: false},
            {text: 'Proveedor', value: 'proveedor.nombre', sortable: false},
            {text: 'Correo Electrónico', value: 'email', sortable: false},
            {text: 'Fecha Emisión', value: 'fecha_emision', sortable: false},
            {text: 'Estado', value: 'estado', sortable: false,},
            {text: 'Acciones', value: 'actions', sortable: false},
        ],
        menuFechaInicio: false,
        menuFechaFin: false,
        filter: {
            searchTerm: "",
            fechaInicio: "",
            fechaFin: "",
        },
        //--- End ---
    }),
    mounted(){
        // this.filter.fechaInicio = this.firstDateMonth();
        this.filter.fechaFin    = this.todaysDateDefault();
    },
    methods:{
        //--- Datatable ---
        getRegistros(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();
            
            axios.get('api/prv_cotizacion?page='+ page +"&perPage="+perPage
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
        getStatusColor(id){
            switch (id) {
                case 1:
                    return 'success';
                case 2:
                    return 'error';
                case 3:
                    return 'warning';
            }
        },

        verPdf(item){
            window.open('/generarProveedorCotizacionPDF/'+item.id_cotizacion_prv);
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
    },
}
</script>
