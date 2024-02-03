<template>
    <div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <h3>Roles</h3>
                </v-col>
                <v-col v-if="$can('roles_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="addDialog=true;" dark>
                        Agregar Rol<v-icon>mdi-plus</v-icon>
                    </v-btn>
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
                
                <template v-slot:[`item.status`]="{ item }">
                    <template v-if="$can('roles_delete', 'all')">
                        <v-chip small v-if="item.status == 1" class="ma-2" color="success" @click="DisEnableReg(item)" dark>Habilitado</v-chip>
                        <v-chip small v-if="item.status == 0" class="ma-2" color="error"   @click="DisEnableReg(item)">Deshabilitado</v-chip>
                    </template>
                    <template v-else>
                        <v-chip small v-if="item.status == 1" class="ma-2" color="success" dark>Habilitado</v-chip>
                        <v-chip small v-if="item.status == 0" class="ma-2" color="error">Deshabilitado</v-chip>
                    </template>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn v-if="$can('roles_update', 'all')" small icon @click="EditRec(item)">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('roles_delete', 'all')" small icon @click="DeleteRec(item)">
                        <v-icon small> mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
        
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
                                    <v-text-field
                                        label="Título"
                                        v-model="addForm.title"

                                        autocomplete="off"
                                        :rules="requiredRules"
                                    ></v-text-field>
                                    <v-text-field
                                        label="Identificador"
                                        v-model="addForm.name"
                                        placeholder="administrador, cliente..."

                                        autocomplete="off"
                                        :disabled="dialogEdit"
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
            { text: 'Nº', value: 'index',  sortable: false,},
            { text: 'Título', value: 'title', sortable: false,},
            { text: 'Identificador', value: 'name', sortable: false,},

            { text: 'Estado', value: 'status', sortable: false, align:'center' },
            { text: 'Acciones', value: 'actions', sortable: false, align:'right' }
        ],
        //--- End ---

        //--- Dialog Properties ---
        addDialog: false,
        dialogEdit: false,
        dialogTitle: "Agregar Registro",
        validAddForm: false,

        addForm: {
            id: null,
        },

        defaultAddForm: {
        },
        //--- End ---

        
        //--- Form Rules ---
        requiredRules: [
            v => !!v || 'Campo requerido',
        ],
        //--- End ---
    }),

    methods: {
        //--- Datatable Functions ---
        GetRecords(page = 1, perPage = 25){
            this.loadingTable = true;
            this.dataReg = [];
            axios.get('/api/user_roles?page='+page +'&perPage='+perPage
                
            ).then(response => {
                this.dataReg     = response.data;
                this.currentPage = this.dataReg.current_page;
                this.totalReg    = this.dataReg.total;
                
            }).finally(() => (this.loadingTable = false));
        },
        //--- End ---

        //--- Dialog Functions ---
        // Create/Edit/Delete
        CreateRec(){
            if(this.$refs.addForm.validate()){
                this.preloader = true;
                this.loadingTable = true;
                
                axios.post('/api/user_roles', this.addForm).then((response)=>{
                    this.addDialog = false;
                    // this.GetRecords();

                    Toast.fire({
                        icon: 'success',
                        title: response.data.message,
                    });
                    location.reload();

                }).catch(e => {
                    console.error(e);
                    Toast.fire({
                        icon: 'error',
                        title: 'Advertencia! Ocurrió un error inesperado',
                    });

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

                axios.put('/api/user_roles/'+this.addForm.id, this.addForm).then((response) => {
                    this.addDialog = false;
                    // this.GetRecords();

                    Toast.fire({
                        icon: 'success',
                        title: response.data.message,
                    });
                    location.reload();

                }).catch(e => {
                    console.error(e);
                    Toast.fire({
                        icon: 'error',
                        title: 'Advertencia! Ocurrió un error inesperado',
                    });

                }).finally(()=>(this.loadingTable = false, this.preloader = false));
            }
        },
        DisEnableReg(item){
            this.loadingTable = true;
            if(item.status != 0){
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
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',

            }).then((result) => {
                if (result.value == true) {
                    item.status = item.status != 1? 1 : 0;
                    axios.put('/api/user_roles/'+item.id, item).then((response) => {;
                        this.$swal.fire(
                            swal_title,
                            swal_text,
                            'success'
                        );
                        location.reload();
                        
                    }).catch(e => {
                        console.error(e);
                        Toast.fire({
                            icon: 'error',
                            title: 'Advertencia! Ocurrió un error inesperado',
                        });
                    });
                }

            }).finally(()=>(this.loadingTable = false));
        },
        DeleteRec(item){
            this.loadingTable = true;
            this.$swal.fire({
                title: "Eliminar Registro",
                text: "¿Está seguro que desea eliminar este registro?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                
            }).then((result) => {
                if (result.value == true) {
                    axios.delete('/api/user_roles/'+item.id).then((response)=>{
                        this.$swal.fire(
                            "Eliminado!",
                            "El registro ha sido eliminado.",
                            'success'
                        );
                        location.reload();
                        
                    }).catch(e => {
                        console.error(e);
                        Toast.fire({
                            icon: 'error',
                            title: 'Advertencia! Ocurrió un error inesperado',
                        });
                    });
                }
            }).finally(()=>(this.loadingTable = false));
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