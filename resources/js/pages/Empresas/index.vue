<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Empresas</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de empresa o ruc"
                        v-model="filter.searchTerm"
                        append-icon="mdi-magnify"
                        class="flex-grow-1 mr-1"
                        hide-details
                    ></v-text-field>
                </v-col>
                <v-col class="text-right">
                    <v-btn color="primary" class="mr-2" @click="GetRecords">
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
                <v-col v-if="$can('empresas_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="addDialog=true;">Agregar Empresa<v-icon>mdi-plus</v-icon></v-btn>
                </v-col>
            </v-row>
            <v-data-table
                :headers="headers"
                :items="dataReg.data"
                :page="currentPage"
                :items-per-page="itemsperpage"
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
                <template v-slot:[`item.estado`]="{ item }">
                    <v-chip small v-if="item.estado == 1" class="ma-2" color="success">Habilitado</v-chip>
                    <v-chip small v-if="item.estado == 0" class="ma-2" color="error">Deshabilitado</v-chip>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn v-if="$can('empresas_update', 'all')" small icon @click="editReg(item)">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('empresas_delete', 'all')" small icon @click="deleteReg(item)">
                        <v-icon small> mdi-delete</v-icon>
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
                    <span class="headline" v-show="!dialogEditar">Agregar Empresa</span>
                    <span class="headline" v-show="dialogEditar">Editar Empresa</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm" @submit.prevent="dialogEditar ? updateReg() : createReg()" enctype="multipart/form-data">
                        <v-row>
                            <v-col>
                                <v-text-field v-model="addForm.nombre" label="Nombre Empresa *" :rules="requiredRules" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.ruc" label="RUC *" type="number" class="txt-number" :rules="requiredRules" autocomplete="off"></v-text-field>
                                <template>
                                    <v-file-input label="Logo *" v-model="file_input"
                                        accept="image/*"
                                        ref="file"
                                        :rules="requiredRules"
                                        @change="onFileChange"
                                    ></v-file-input>
                                    <v-img :src="pic_url"
                                        contain
                                        max-height="300"
                                        max-width="600"
                                    />
                                </template>
                            </v-col>
                        </v-row>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="addDialog = false" >Cancelar</v-btn>
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
        itemsperpage: 25,
        totalReg: 0,
        dataTabOptions: {},
        dataReg: {},
        headers: [
            {text: 'RUC', value: 'ruc', sortable: false},
            {text: 'Empresa', value: 'nombre', sortable: false},
            {text: 'Estado', value: 'estado', sortable: false, align:'center'},
            {text: 'Acciones', value: 'actions', sortable: false, align:'right'}
        ],
        filter: {
            searchTerm: "",
        },
        //--- End ---
        //--- Ventana Emergente ---
        addDialog: false,
        dialogEditar: false,
        addForm: new Form({ 
            id_empresa: null,
            nombre: null,
            ruc: null,
        }),
        defaultAddForm: {
        },

        file_input: null,
        pic_file: null,
        pic_url: "../assets/images/default-empty-pic.png",
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),
    mounted() {
        //
    },
    methods:{
        //--- Datatable ---
        GetRecords(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();

            axios.get('api/empresa?page='+ page +"&perPage="+perPage
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
            this.GetRecords();
        },
        //--- End ---

        //--- VENTANA EMERGENTE ---
        // Add/Edit/Delete Functions
        createReg(){
            this.preloader = true;
            this.loadingTable = true;
            
            axios.post('api/empresa', this.addForm).then((response)=>{
                this.submitFiles(response.data.empresa.id_empresa);
                this.GetRecords();
                this.addDialog = false;

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
        editReg(item) {
            this.dialogEditar = true;
            this.addForm = Object.assign({}, item);
            this.pic_url = item.file_path? '..'+item.file_path.path + item.file_path.filename : this.pic_url;
            this.addDialog = true;
        },
        updateReg(){
            this.preloader = true;
            this.loadingTable = true;

            axios.put('api/empresa/'+this.addForm.id_empresa, this.addForm).then((response) => {
                this.addDialog = false;
                this.submitFiles(response.data.empresa.id_empresa);
                this.GetRecords();

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
        deleteReg(item){
            this.loadingTable = true;
            if(item.estado!=0){
                var title ='Deshabilitar Empresa';
                var text = "¿Está seguro que desea deshabilitar esta empresa?";
                var swal_title = 'Deshabilitada!';
                var swal_text = 'La empresa ha sido deshabilitada.';
            }else{
                var title ='Habilitar Empresa';
                var text = "¿Está seguro que desea habilitar esta empresa?";
                var swal_title = 'Habilitada!';
                var swal_text = 'La empresa ha sido habilitada.';
            }
            Swal.fire({
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
                    axios.delete('/api/empresa/'+item.id_empresa).then(response => {
                        if(response.data.success == true){
                            Swal.fire(
                                swal_title,
                                swal_text,
                                'success'
                            );
                        }else{
                            Swal.fire(
                                "Error!",
                                response.data.message,
                                'error'
                            );
                        }
                        
                        this.GetRecords();

                    }).catch(e => {
                        console.error(e);
                        Toast.fire({
                            icon: 'error',
                            title: "Ocurrió un error",
                        });
                    });
                }
            }).finally(()=>(this.loadingTable = false));
        },

        //--- Upload Image Functions ---
        onFileChange(file) {
            if (!file) {
                this.pic_file = null;
                this.pic_url  = "../assets/images/default-empty-pic.png";

                return;
            }
            this.pic_file = this.$refs.file.lazyValue;
            this.pic_url = URL.createObjectURL(this.pic_file);
        },
        async submitFiles(id) {
            let formData = new FormData();
            formData.append('id', id);
            formData.append('file', this.pic_file);
            axios.post('/files/upload-company-file',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
                
            ).then(function(data) {
                setTimeout(()=>{
                    this.$router.go();
                }, 1000);

            }.bind(this)).catch(function(e) {
                console.error(e);
            });
        }
        //--- END ---
    },
    watch: {
        dataTabOptions(event) {
            this.itemsperpage = event.itemsPerPage;
            this.GetRecords(event.page, event.itemsPerPage);
        },
        addDialog(){
            if(!this.addDialog){
                this.dialogEditar = false;
                this.$refs.addForm.resetValidation();
                this.addForm = Object.assign({}, this.defaultAddForm);

                this.file_input = null;
                this.pic_file = null;
                this.pic_url  = "../assets/images/default-empty-pic.png";
            }
        },
    },
}
</script>