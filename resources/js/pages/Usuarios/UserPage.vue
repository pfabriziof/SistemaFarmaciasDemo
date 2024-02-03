<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Usuarios</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de usuario o email"
                        v-model="filter.search_query"
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
                <v-col v-if="$can('users_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="addDialog=true;">
                        Agregar Usuario<v-icon>mdi-plus</v-icon>
                    </v-btn>            
                </v-col>
            </v-row>
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
                <template #[`item.index`]="{ item }">
                    {{ data_reg.data.indexOf(item) + 1 }}
                </template>
                
                <template v-slot:[`item.estado`]="{ item }">
                    <v-chip small v-if="item.estado == 1" class="ma-2" color="success">Habilitado</v-chip>
                    <v-chip small v-if="item.estado == 0" class="ma-2" color="error">Deshabilitado</v-chip>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn v-if="$can('users_update', 'all')" small icon @click="editReg(item)">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('users_delete', 'all')" small icon @click="deleteReg(item)">
                        <v-icon small> mdi-delete</v-icon>
                    </v-btn>
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

        <!-- Add/Edit Dialog -->
        <v-dialog v-model="addDialog" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline" v-show="!dialogEditar">Agregar Usuario</span>
                    <span class="headline" v-show="dialogEditar">Editar Usuario</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm" @submit.prevent="dialogEditar ? updateReg() : createReg()">
                        <v-row>
                            <v-col>
                                <v-text-field v-model="addForm.name" label="Nombre y Apellido *" :rules="requiredRules" autocomplete="off"></v-text-field>
                                <v-text-field v-model="addForm.email" label="Correo Electrónico *" :rules="emailRules" autocomplete="off"></v-text-field>
                                <v-select label="Sucursal" v-model="addForm.id_sucursal"
                                    :items="combo_sucursales"
                                    item-text="nombre_sucursal"
                                    item-value="id_sucursal"
                                    :rules="requiredRules"
                                ></v-select>
                                <v-select label="Rol" v-model="addForm.id_role"
                                    :items="roles_list"
                                    item-text="title"
                                    item-value="id"
                                    :rules="requiredRules"
                                ></v-select>
                                
                                <!-- Password Fields -->
                                <v-text-field v-model="addForm.password" label="Contraseña * (min: 4 caracteres)"
                                    :rules="requiredRules"
                                    :append-icon="userPass ? 'mdi-eye' : 'mdi-eye-off'"
                                    :type="userPass ? 'password' : 'text'"
                                    @click:append="() => (userPass = !userPass)"
                                ></v-text-field>
                                <v-text-field v-model="addForm.confirm_password" label="Confirmar Contraseña *"
                                    v-if="dialogEditar != true"
                                    :rules="requiredRules"
                                    :append-icon="confirmPass ? 'mdi-eye' : 'mdi-eye-off'"
                                    :type="confirmPass ? 'password' : 'text'"
                                    @click:append="() => (confirmPass = !confirmPass)"
                                ></v-text-field>
                                <!-- Password Fields -->
                                
                                <!-- <v-switch v-for="(item, i) in roles_list" :key=i
                                    v-model="roles_list[i].checked"
                                    :label="item.title"
                                    hide-details
                                ></v-switch> -->
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
        current_page: 1,
        pageCount: 1,
        itemsperpage: 25,
        total_reg: 0,
        dataTabOptions: {},
        data_reg: {},
        headers: [
            {text: 'Nº', value: 'index',  sortable: false},
            { text: 'Nombre', sortable: false, value: 'name' },
            { text: 'Correo Electrónico', sortable: false, value: 'email' },
            
            { text: 'Estado', sortable: false, value: 'estado' },
            { text: 'Acciones', value: 'actions', sortable: false, align:'right' }
        ],
        filter: {
            search_query: '',
        },
        //--- End ---

        //--- Ventana Emergente ---
        addDialog: false,
        dialogEditar: false,
        addForm: {
            id: null,
            id_sucursal: null,
            id_role: 1,
            name: null,
            email: null,
        },
        defaultAddForm: {
            id_role: 1,
        },
        roles_list: [],
        combo_sucursales: [],
        userPass: String,
        confirmPass: String,
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            v => !!v || 'Campo obligatorio',
            v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
        ],
    }),

    mounted() {
        this.sucursalesCombo();
        this.getUserRoles();
    },

    methods: {
        //--- Datatable ---
        getRegistros(page = 1, per_page = 25){
            this.loadingTable = true;
            this.data_reg = [];

            axios.get('/api/usuario?page='+page +'&per_page='+per_page
                +'&search_query='+this.filter.search_query
     
            ).then(response => {
                this.data_reg     = response.data;
                this.current_page = this.data_reg.current_page;
                this.pageCount    = this.data_reg.last_page;
                this.total_reg    = this.data_reg.total;
                
            }).catch(e => {
                console.error(e);

            }).finally(() => (this.loadingTable = false));
        },
        limpiarFiltros(){
            this.filter.search_query = '';
            this.getRegistros();
        },
        //--- End ---

        //--- Ventana Emergente ---
        // Add/Edit/Delete Functions
        createReg(){
            this.preloader = true;
            this.loadingTable = true;

            axios.post('api/usuario', this.addForm).then((response)=>{
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
            this.addDialog = true;
        },
        updateReg(){
            this.preloader = true;
            this.loadingTable = true;

            axios.put('api/usuario/'+this.addForm.id, this.addForm).then((response) => {
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
                var title ='Deshabilitar Usuario';
                var text = "¿Está seguro que desea deshabilitar este usuario?";
                var swal_title = 'Deshabilitado!';
                var swal_text = 'El usuario ha sido deshabilitado.';
            }else{
                var title ='Habilitar Usuario';
                var text = "¿Está seguro que desea habilitar este usuario?";
                var swal_title = 'Habilitado!';
                var swal_text = 'El usuario ha sido habilitado.';
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
                    axios.delete('/api/usuario/'+item.id).then(response => {
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
        sucursalesCombo(){
            axios.get('/api/sucursalesCombo').then((response) => {
                this.combo_sucursales = response.data;
                
            }).catch(e => {
                console.error(e);
            })
        },
        getUserRoles(){
            axios.get('/api/userRolesCombo').then(response => {
                this.roles_list = response.data;
                // this.roles_list.forEach(function (val) {
                //     val.checked = false;
                // });

            }).catch(e => {
                console.error(e);
            });
        },
        //--- End ---
    },

    watch: {
        dataTabOptions(event) {
            this.itemsperpage = event.itemsPerPage;
            this.getRegistros(event.page, event.itemsPerPage);
        },
        addDialog(){
            if(!this.addDialog){
                this.dialogEditar = false;
                
                this.$refs.addForm.reset();
                this.addForm = Object.assign({}, this.defaultAddForm);
            }
        },
    },
}
</script>