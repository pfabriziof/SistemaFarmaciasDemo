<template>
    <div>
        <v-row dense class="pa-2 align-center">
            <v-col v-if="$can('seriesinv_create', 'all')" class="text-right">
                <v-btn small color="primary" class="mr-2" @click="addDialog=true;" dark>Agregar Registro<v-icon>mdi-plus</v-icon></v-btn>
            </v-col>
        </v-row>
        <v-data-table
            :headers="tableHeaders"
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
            
            <template v-slot:[`item.estado`]="{ item }">
                <v-chip small v-if="item.estado == 1" class="ma-2" color="success">Habilitado</v-chip>
                <v-chip small v-if="item.estado == 0" class="ma-2" color="error">Deshabilitado</v-chip>
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn v-if="$can('seriesinv_update', 'all')" small icon @click="EditRec(item)">
                    <v-icon small> mdi-pencil</v-icon>
                </v-btn>
                <v-btn v-if="$can('seriesinv_delete', 'all')" small icon @click="DeleteRec(item)">
                    <v-icon small> mdi-delete</v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <!-- Add/Edit Dialog -->
        <v-dialog v-model="addDialog" max-width="50%">
            <v-card>
                <v-card-title>
                    <span class="headline">{{dialogTitle}}</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm"
                        v-model="validAddForm"
                        @submit.prevent="dialogEdit ? UpdateRec() : CreateRec()"
                        lazy-validation>
                        <v-row>
                            <v-col cols="12" sm="12" md="12">
                                <v-autocomplete
                                    label="Sucursal"
                                    v-model="addForm.id_sucursal"
                                    :items="combo_sucursales"
                                    item-text="nombre_sucursal"
                                    item-value="id_sucursal"
                                    :rules="requiredRules"
                                ></v-autocomplete>
                                <v-select label="Tipo de Comprobante"
                                    v-model="addForm.id_tipo_comprobante"
                                    :items="list_invtypes" 
                                    item-text="tipo_comprobante" 
                                    item-value="id_tipo_comprobante"
                                    :rules="requiredRules"
                                ></v-select>
                                <v-text-field
                                    label="Nombre Serie"
                                    v-model="addForm.serie"
                                    autocomplete="off"
                                    :rules="requiredRules"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="secondary darken-1" text @click="addDialog = false">Cancelar</v-btn>
                            <v-btn type="submit" color="primary" :disabled="!validAddForm"> Guardar </v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-- End -->
    </div>
</template>

<script>
export default {
    data: () => ({
        //--- Datatable ---
        loadingTable: false,
        currentPage: 1,
        itemsPerPage: 25,
        totalReg: 0,
        dataTabOptions: {},
        dataReg: [],
        tableHeaders: [
            { text: 'Nº', value: 'index',  sortable: false },
            { text: 'Tipo Comprobante', value: 'tipo.tipo_comprobante', sortable: false, align:'center'},
            { text: 'Serie', value: 'serie', sortable: false, align:'center'},
            { text: 'Sucursal', value: 'sucursal.nombre_sucursal', sortable: false, align:'center'},
            
            { text: 'Estado', value: 'estado', sortable: false, align:'center' },
            { text: 'Acciones', value: 'actions', sortable: false, align:'right' }
        ],

        menuStartDate: false,
        menuEndDate: false,
        filter: {
            searchTerm: '',
            startDate: '',
            endDate: '',
        },
        //--- End ---

        //--- Dialog Properties ---
        showFileComponent: false,

        addDialog: false,
        dialogEdit: false,
        dialogTitle: "Agregar Registro",
        validAddForm: false,
        addFormTab: 0,

        addForm: {
        },
        combo_sucursales: [],
        list_invtypes: [],

        defaultAddForm: {
        },
        //--- End ---

        //--- Form Rules ---
        requiredRules: [
            v => !!v || 'Campo requerido',
        ],
        //--- End ---
    }),

    mounted(){
        this.sucursalesCombo();
        this.getListTiposComprobante();
    },

    methods: {
        //--- Datatable Functions ---
        GetRecords(page = 1, perPage = 25){
            this.loadingTable = true;
            this.dataReg = [];
            axios.get('api/series_invoice?page='+page +'&perPage='+perPage
                +'&searchTerm='+this.filter.searchTerm
                
            ).then(response => {
                this.dataReg     = response.data;
                this.currentPage = this.dataReg.currentPage;
                this.totalReg    = this.dataReg.total;
                
            }).finally(() => (this.loadingTable = false));
        },
        ClearFilters(){
            this.filter.searchTerm = '';
            this.GetRecords();
        },
        //--- End ---

        //--- Dialog Functions ---
        // Create/Edit/Delete
        CreateRec(){
            if(this.$refs.addForm.validate()){
                this.preloader = true;
                this.loadingTable = true;
                
                axios.post('api/series_invoice', this.addForm).then((response)=>{
                    this.addDialog = false;
                    this.GetRecords();

                    Toast.fire({
                        icon: 'success',
                        title: response.data.message,
                    });

                }).catch(e => {
                    console.error(e);

                }).finally(()=>(this.loadingTable = false, this.preloader = false));
            }
        },
        EditRec(item) {
            this.dialogEdit = true;
            this.dialogTitle = "Editar Registro";
            
            this.addForm = Object.assign({}, item);

            this.addDialog = true;
        },
        UpdateRec(){
            if(this.$refs.addForm.validate()){
                this.preloader = true;
                this.loadingTable = true;

                axios.put('api/series_invoice/'+this.addForm.id_serie, this.addForm).then((response) => {
                    this.addDialog = false;
                    this.GetRecords();

                    Toast.fire({
                        icon: 'success',
                        title: response.data.message,
                    });

                }).catch(e => {
                    console.error(e);

                }).finally(()=>(this.loadingTable = false, this.preloader = false));
            }
        },
        DeleteRec(item){
            this.loadingTable = true;
            if(item.estado!=0){
                var title ='Deshabilitar Registro';
                var text = "¿Está seguro que desea deshabilitar este registro?";
                var swal_title = 'Deshabilitado!';
                var swal_text = 'El registro ha sido deshabilitado.';
            }else{
                var title ='Habilitar Registro';
                var text = "¿Está seguro que desea habilitar este registro?";
                var swal_title = 'Habilitado!';
                var swal_text = 'El registro ha sido habilitado.';
            }
            this.$swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                
            }).then((result) => {
                if (result.value == true) {
                    axios.delete('api/series_invoice/'+item.id_serie).then((response)=>{
                        if(response.data.success == true){
                            this.$swal.fire(
                                swal_title,
                                swal_text,
                                'success'
                            );
                        }else{
                            this.$swal.fire(
                                "Error!",
                                response.data.message,
                                'error'
                            );
                        }
                        this.GetRecords();
                        
                    }).catch(e => {
                        console.error(e);
                        this.$swal.fire({
                            icon: 'error',
                            title: "Ocurrió un error",
                        });
                    });
                }
            }).finally(()=>(this.loadingTable = false));
        },
        //--- End ---

        //--- Loading Data ---
        sucursalesCombo(){
            axios.get('/api/sucursalesCombo').then((response) => {
                this.combo_sucursales = response.data;

            }).catch(e => {
                console.error(e);
            });
        },
        getListTiposComprobante(){
            axios.get('api/tiposComprobantesCombo').then((response) => {
                this.list_invtypes = response.data;

            }).catch(e => {
                console.error(e);
            });
        },
        //--- End ---
    },
    watch: {
        dataTabOptions(event) {
            this.itemsPerPage = event.itemsPerPage;
            this.GetRecords(event.page, event.itemsPerPage);
        },
        addDialog(){
            if(!this.addDialog){
                this.dialogEdit = false;
                this.dialogTitle = "Agregar Registro";

                this.$refs.addForm.resetValidation();
                this.validAddForm = false;
                this.addForm = Object.assign({}, this.defaultAddForm);
            }
        },
    },
}
</script>