<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Clientes</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de cliente o número de documento"
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
                <v-col v-if="$can('clients_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="addDialog=true;">Agregar Cliente<v-icon>mdi-plus</v-icon></v-btn>            
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
                <template v-slot:[`item.estado`]="{ item }">
                    <v-chip small v-if="item.estado == 1" class="ma-2" color="success">Habilitado</v-chip>
                    <v-chip small v-if="item.estado == 0" class="ma-2" color="error">Deshabilitado</v-chip>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn v-if="$can('cuentascobrar_index', 'all')" small icon :to="'/deuda_cliente/' + item.id_cliente"><v-icon small> mdi-information</v-icon></v-btn>
                    <v-btn v-if="$can('clients_update', 'all')" small icon @click="editReg(item)">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('clients_delete', 'all')" small icon @click="deleteReg(item)">
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
                    <span class="headline" v-show="!dialogEditar">Agregar Cliente</span>
                    <span class="headline" v-show="dialogEditar">Editar Cliente</span>
                </v-card-title>
                <v-card-text>
                    <v-tabs v-model="addFormTab" :show-arrows="false" background-color="transparent">
                        <v-tab to="#tabs-information">Información</v-tab>
                        <v-tab to="#tabs-contact">Contacto</v-tab>
                    </v-tabs>
                    <v-form ref="addForm" v-model="addFormValid" lazy-validation @submit.prevent="dialogEditar ? updateReg() : createReg()">
                        <v-tabs-items v-model="addFormTab">
                            <v-tab-item value="tabs-information">
                                <v-row>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-select label="Tipo Documento *" v-model="addForm.id_tipo_doc"
                                            :items="tipos_doc"
                                            item-text="tipo_documento"
                                            item-value="id_tipo_doc"
                                            :rules="requiredRules"
                                            @change="TipoDocChanged(addForm.id_tipo_doc)"
                                        ></v-select>
                                        <v-text-field v-model="addForm.nombre" label="Nombre / Razón Social *" :rules="requiredRules" autocomplete="off"></v-text-field>
                                        <v-autocomplete label="Departamento *" v-model="addForm.id_departamento"
                                            :items="departamentos_list"
                                            item-text="name"
                                            item-value="id"
                                            @change="getProvinces(addForm.id_departamento)"
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
                                    </v-col>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field :label="nrodoc_label" v-model="addForm.nro_doc"
                                            :rules="requiredDocRules"
                                            autocomplete="off"
                                            type="number"
                                            class="txt-number">
                                            <template #append-outer>
                                                <v-btn
                                                    @click="buscarDoc()"
                                                    :disabled='!addForm.nro_doc'
                                                    color="primary"
                                                    class="mb-1">
                                                    <v-icon>mdi-account-search</v-icon>
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        
                                        <v-select label="Tipo de Cliente *" v-model="addForm.tipo_cliente"
                                            :items="type_bp" 
                                            item-text="text"
                                            item-value="id"
                                            :rules="requiredRules"
                                        ></v-select>
                                        <v-autocomplete label="Provincia *" v-model="addForm.id_provincia"
                                            :items="provincias_list"
                                            item-text="name"
                                            item-value="id"
                                            @change="getDistricts(addForm.id_provincia, addForm.id_departamento,)"
                                            :rules="requiredRules"
                                            no-data-text="No se encontraron registros"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col class="text-right">
                                        <v-btn small color="indigo" dark @click="addDireccion">Agregar Dirección<v-icon>mdi-plus</v-icon></v-btn>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-simple-table>
                                            <tbody>
                                                <tr v-for="(item, i) in direcciones" :key=i>
                                                    <td>
                                                        <v-text-field v-model="item.direccion" :label="item.label"></v-text-field>
                                                    </td>
                                                    <td v-if="item.id_direccion" class="text-center">
                                                        <v-chip 
                                                            v-if="item.estado == 1"
                                                            @click="disenableDireccion(item)"
                                                            color="success"
                                                            small>
                                                            Habilitado
                                                        </v-chip>
                                                        <v-chip 
                                                            v-if="item.estado == 0"
                                                            @click="disenableDireccion(item)"
                                                            color="error"
                                                            small>
                                                            Deshabilitado
                                                        </v-chip>
                                                    </td>
                                                    <td v-else class="text-center">
                                                        <v-btn small color="error" icon @click="delDireccion(item)">
                                                            <v-icon small>mdi-delete</v-icon>
                                                        </v-btn>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </v-simple-table>
                                    </v-col>
                                </v-row>
                            </v-tab-item>

                            <v-tab-item value="tabs-contact">
                                <v-row>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field v-model="addForm.email" label="Correo Electrónico" autocomplete="off"></v-text-field>
                                        <v-text-field v-model="addForm.contacto_nombre" label="Nombre Contacto" autocomplete="off"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field v-model="addForm.telefono" label="Teléfono" autocomplete="off"></v-text-field>
                                        <v-text-field v-model="addForm.contacto_telefono" label="Teléfono Contacto" autocomplete="off"></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-tab-item>
                        </v-tabs-items>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="addDialog=false;">Cancelar</v-btn>
                            <v-btn type="submit" color="primary" :disabled="!addFormValid"> Guardar </v-btn>
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
            {text: "Nombre", value: "nombre", sortable: false},
            {text: "Tipo Doc.", value: "tipo_doc.tipo_documento", sortable: false},
            {text: "Nro. Doc", value: "nro_doc", sortable: false},
            {text: 'Departamento', value: 'departamento.name', sortable: false},
            {text: 'Provincia', value: 'provincia.name', sortable: false},
            {text: "Estado", value: "estado",  sortable: false, align: "center"},
            {text: "Acciones", value: "actions", sortable: false, align: "right"},
        ],
        filter: {
            searchTerm: "",
        },
        //--- End ---

        //--- Ventana Emergente ---
        addDialog: false,
        dialogEditar: false,
        addFormTab: 0,
        addFormValid: true,
        addForm: new Form({
            id_cliente: 0,
            id_tipo_doc: 2,
            tipo_cliente: 1,
            nro_doc: null,
            nombre: null,
            id_departamento: null,
            id_provincia: null,
            id_distrito: null,
            cli_direcciones: [],

            email: null,
            telefono: null,
            contacto_nombre: null,
            contacto_telefono: null,
        }),
        type_bp: [{ id: 1, text: "Interno" }, { id: 2, text: "Distribuidor" },],
        direcciones: [{id_direccion: "", label: "Dirección 1", direccion: ""}],

        //Tipo Doc
        nrodoc_label: "Nro. DNI *",
        tipos_doc: [],

        //Ubigeo
        departamentos_list: [],
        provincias_list: [],
        distritos_list: [],
        //--- End ---


        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            // (v) => !!v || "Campo obligatorio",
            (v) => /.+@.+\..+/.test(v) || "Correo Electrónico debe ser válido",
        ],
        requiredDocRules: [
            v => !!v || 'Campo obligatorio',
            (v) => (v && v.length == 8) || "DNI debe tener 8 cifras",
        ],
    }),

    mounted() {
        this.tiposDocCombo();
        this.getDepartments();
    },
    methods: {
        //--- Carga de Datos ---
        buscarDoc(){
            this.preloader = true;
            if(this.addForm.id_tipo_doc == 2){
                axios.post("/api/searchDni", {dni: this.addForm.nro_doc,}).then((response) => {
                    console.log(response.data.data);
                    if(response.data.success == true){
                        this.addForm.nombre = response.data.data.nombres +" "+ response.data.data.apellido_paterno +" "+ response.data.data.apellido_materno;
                        this.preloader = false;
                        Toast.fire({
                            icon: "success",
                            title: "Nro. de DNI Encontrado!",
                        });
                    }else{
                        Toast.fire({
                            icon: "error",
                            title: "No se encontró el nro. de DNI.",
                        });
                        this.preloader = false;
                    }
                });
            } else {
                axios.post("api/searchRuc", {ruc: this.addForm.nro_doc,}).then((response) => {
                    if (response.data.success == true) {
                        this.addForm.nombre = response.data.data.nombre_o_razon_social;
                        this.preloader = false;
                        Toast.fire({
                            icon: "success",
                            title: "Nro. de RUC Encontrado!",
                        });
                    }else{
                        Toast.fire({
                            icon: "error",
                            title: "No se encontró el nro. de RUC.",
                        });
                        this.preloader = false;
                    }
                });
            }
        },
        tiposDocCombo(){
            axios.get("/api/tiposDocCombo").then((response) => {
                this.tipos_doc = response.data;

            }).catch(e => {
                console.error(e);
            });
        },
        //--- End ---

        //--- Datatable ---
        getRegistros(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();
            
            axios.get('api/cliente?page='+ page +"&perPage="+perPage
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
        TipoDocChanged(tipo_doc, nro_doc=null){
            this.addForm.nro_doc = nro_doc;

            switch (tipo_doc) {
                case 1:
                    this.nrodoc_label = "Nro. RUC *";
                    this.requiredDocRules = [
                        v => !!v || 'Campo obligatorio',
                        (v) => (v && v.length == 11) || "RUC debe tener 11 cifras",
                    ];
                    break;

                case 2:
                    this.nrodoc_label = "Nro. DNI *";
                    this.requiredDocRules = [
                        v => !!v || 'Campo obligatorio',
                        (v) => (v && v.length == 8) || "DNI debe tener 8 cifras",
                    ];
                    break;

                default:
                    this.nrodoc_label = "Nro. Documento *";
                    this.requiredDocRules = [
                        v => !!v || 'Campo obligatorio',
                    ];
                    break;

            }
        },
        // Add/Edit/Delete Functions
        createReg(){
            this.preloader = true;
            this.loadingTable = true;
            this.addForm.cli_direcciones = this.direcciones;

            this.addForm.post('api/cliente').then((response)=>{
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
            this.addForm.fill(item);
            this.TipoDocChanged(this.addForm.id_tipo_doc, this.addForm.nro_doc);

            this.getProvinces(this.addForm.id_departamento);
            this.getDistricts(this.addForm.id_provincia, this.addForm.id_departamento);
            this.direcciones = [];
            item.direcciones.forEach((val, index)  => {
                var idx = parseInt(index) + 1;
                this.direcciones.push({
                    id_cliente: val.id_cliente,
                    id_direccion: val.id_direccion,
                    direccion: val.direccion,
                    estado: val.estado,
                    label: "Dirección " + idx,
                })
            });
            this.addForm.cli_direcciones = this.direcciones;
            

            this.addDialog = true;
        },
        updateReg(){
            this.preloader = true;
            this.loadingTable = true;
            
            this.addForm.put('api/cliente/'+this.addForm.id_cliente).then((response) => {
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
                var title ='Deshabilitar Cliente';
                var text = "¿Está seguro que desea deshabilitar este cliente?";
                var swal_title = 'Deshabilitado!';
                var swal_text = 'El cliente ha sido deshabilitado.';
            }else{
                var title ='Habilitar Cliente';
                var text = "¿Está seguro que desea habilitar este cliente?";
                var swal_title = 'Habilitado!';
                var swal_text = 'El cliente ha sido habilitado.';
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
                    axios.delete('/api/cliente/'+item.id_cliente).then(response => {
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

        //--- Address Functions ---
        addDireccion(){
            var idx = parseInt(this.direcciones.length) + 1;
            this.direcciones.push({id_direccion: "", label: "Dirección " + idx, direccion: ""});
        },
        disenableDireccion(item){
            item.estado = item.estado != 1? 1 : 0;
        },
        delDireccion(item){
            var idx = this.direcciones.indexOf(item);
            if(idx > -1){
                this.direcciones.splice(idx, 1);
            }
        },
        //--- END ---

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
                // console.log('Dialog is closing');
                this.dialogEditar = false;
                this.$refs.addForm.reset();
                this.direcciones = [{id_direccion: "", label: "Dirección 1", direccion: ""}];
                
            }else{
                if(this.dialogEditar == false){
                    this.addForm.id_tipo_doc  = 2;
                    this.addForm.tipo_cliente = 1;
                    this.TipoDocChanged(2);
                }
            }
        }
    }
};
</script>