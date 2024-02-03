<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Sucursales</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de sucursal"
                        v-model="filter.searchTerm"
                        append-icon="mdi-magnify"
                        class="flex-grow-1 mr-1"
                        hide-details
                    ></v-text-field>
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
                <v-col v-if="$can('sucursales_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="addDialog=true;">Agregar Sucursal<v-icon>mdi-plus</v-icon></v-btn>            
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
                
                <template v-slot:[`item.estado`]="{ item }">
                    <v-chip small v-if="item.estado == 1" class="ma-2" color="success">Habilitado</v-chip>
                    <v-chip small v-if="item.estado == 0" class="ma-2" color="error">Deshabilitado</v-chip>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn v-if="$can('sucursales_update', 'all')" small icon @click="editReg(item)">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('sucursales_delete', 'all')" small icon @click="deleteReg(item)">
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
                    <span class="headline" v-show="!dialogEditar">Agregar Sucursal</span>
                    <span class="headline" v-show="dialogEditar">Editar Sucursal</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm" @submit.prevent="dialogEditar ? updateReg() : createReg()">
                        <v-row>
                            <v-col cols="12" sm="6"  md="6">
                                <v-select :items="empresas_combo" label="Empresa *" v-model="addForm.id_empresa" 
                                    item-text="nombre"
                                    item-value="id_empresa"
                                    :rules="requiredRules"
                                ></v-select>
                                <v-text-field v-model="addForm.nombre_sucursal" label="Nombre Sucursal *" :rules="requiredRules" autocomplete="off"></v-text-field>
                                <v-autocomplete label="Departamento *" v-model="addForm.id_departamento"
                                    :items="departamentos_list"
                                    item-text="name"
                                    item-value="id"
                                    @change="getProvinces(addForm.id_departamento)"
                                    :rules="requiredRules"
                                    no-data-text="No se encontraron registros"
                                ></v-autocomplete>
                                <v-autocomplete label="Provincia *" v-model="addForm.id_provincia"
                                    :items="provincias_list"
                                    item-text="name"
                                    item-value="id"
                                    @change="getDistricts(addForm.id_provincia, addForm.id_departamento,)"
                                    :rules="requiredRules"
                                    no-data-text="No se encontraron registros"
                                ></v-autocomplete>
                                <v-autocomplete label="Distrito *" v-model="addForm.id_distrito"
                                    :items="distritos_list"
                                    item-text="name"
                                    item-value="id"
                                    :rules="requiredRules"
                                    no-data-text="No se encontraron registros"
                                ></v-autocomplete>
                                <v-text-field v-model="addForm.telefono" label="Teléfono" type="number" class="txt-number" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.email" label="Correo Electrónico" :rules="emailRules" autocomplete="off"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6"  md="6">
                                <v-text-field v-model="addForm.cod_domicilio_fiscal" label="Código Dom. Fiscal" type="number" class="txt-number" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.direccion_fiscal" label="Dirección Fiscal" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.direccion_comercial" label="Dirección Comercial" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.direccion_web" label="Dirección Web" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.nro_cuenta_bancario" label="Nro. Cuenta Bancario" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.cci_bancario" label="CCI Bancario" autocomplete="off"></v-text-field>
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
        itemsPerPage: 25,
        totalReg: 0,
        dataTabOptions: {},
        dataReg: {},
        headers: [
            {text: 'Sucursal', value: 'nombre_sucursal', sortable: false},
            {text: 'Email', value: 'email', sortable: false},
            {text: 'Teléfono', value: 'telefono', sortable: false},
            {text: 'Departamento', value: 'departamento.name', sortable: false},
            {text: 'Provincia', value: 'provincia.name', sortable: false},
            {text: 'Distrito', value: 'distrito.name', sortable: false},
            {text: 'Estado', value: 'estado', sortable: false, align:'center'},
            {text: 'Acciones', value: 'actions', sortable: false, align:'right'}
        ],
        filter: {
            searchTerm: "",
        },
        //--- End ---

        //--- Ventana Emergente ---
        authUser: null,

        addDialog: false,
        dialogEditar: false,
        addForm: new Form({ 
            id_sucursal: null,
            id_empresa: null,
            nombre_sucursal: null,
            cod_domicilio_fiscal: null,
            direccion_fiscal: null,
            id_departamento: null,
            id_provincia: null,
            id_distrito: null,
            telefono: null,
            direccion_comercial: null,
            email: null,
            direccion_web: null,
            nro_cuenta_bancario: null,
            cci_bancario: null,
        }),
        defaultAddForm: {
        },
        empresas_combo:[],

        //Ubigeo
        departamentos_list: [],
        provincias_list: [],
        distritos_list: [],
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            //v => !!v || 'Campo obligatorio',
            v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
        ],
    }),
    mounted() {
        this.getLoggedUser();
        this.empresasCombo();

        this.getDepartments();
    },
    methods: {
        //--- Datatable ---
        getRegistros(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();

            axios.get('api/sucursal?page='+ page +"&perPage="+perPage
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
            this.getRegistros();
        },
        //--- End ---

        //--- Ventana Emergente ---
        // Add/Edit/Delete Functions
        createReg(){
            this.preloader = true;
            this.loadingTable = true;

            axios.post('api/sucursal', this.addForm).then((response)=>{
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
        editReg(item) {
            this.dialogEditar = true;
            this.addForm = Object.assign({}, item);

            this.getProvinces(this.addForm.id_departamento);
            this.getDistricts(this.addForm.id_provincia, this.addForm.id_departamento);


            this.addDialog = true;
        },
        updateReg(){
            this.preloader = true;
            this.loadingTable = true;
            
            axios.put('api/sucursal/'+this.addForm.id_sucursal, this.addForm).then((response) => {
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
        deleteReg(item){
            this.loadingTable = true;
            if(item.estado!=0){
                var title ='Deshabilitar Sucursal';
                var text = "¿Está seguro que desea deshabilitar esta sucursal?";
                var swal_title = 'Deshabilitada!';
                var swal_text = 'La sucursal ha sido deshabilitada.';
            }else{
                var title ='Habilitar Sucursal';
                var text = "¿Está seguro que desea habilitar esta sucursal?";
                var swal_title = 'Habilitada!';
                var swal_text = 'La sucursal ha sido habilitada.';
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
                    axios.delete('/api/sucursal/'+item.id_sucursal).then(response => {
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
                        
                        this.getRegistros();
                        
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
        //--- End ---

        
        //--- Carga de Datos ---
        getLoggedUser(){
            this.authUser = JSON.parse(localStorage.getItem('user_data'));
            this.addForm.id_empresa = this.authUser.sucursal.empresa.id_empresa;
        },
        empresasCombo(){
            axios.get('/api/empresasCombo').then(response => {
                this.empresas_combo = response.data;
            });
        },
        //--- End ---
        //--- Ubigeo ---
        getDepartments(){
            axios.get('api/getDepartments').then(response => {
                this.departamentos_list = response.data;
            });
        },
        getProvinces(id){
            this.distritos_list = [];
            axios.get('api/getProvinces/'+id).then(response => {
                this.provincias_list = response.data;

            });
        },
        getDistricts(province_id, department_id){
            axios.post('api/getDistricts', {province_id: province_id, department_id: department_id}).then(response => {
                this.distritos_list = response.data;
            });
        },
        //--- End ---
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

            }else{
                if(this.dialogEditar == false){
                    this.addForm.id_empresa = this.authUser.sucursal.empresa.id_empresa;
                }
            }
        },
    },
}
</script>