<template>
    <div>
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="12">
                    <h3>Asignar Permisos a Roles</h3>
                </v-col>
                <v-col cols="4">
                    <v-select label="Rol" v-model="id_role"
                        :items="roles_list"
                        item-text="title"
                        item-value="id"
                    ></v-select>
                </v-col>
                <v-col cols="4">
                    <v-text-field v-if="id_role != null"
                        label="Ingresar término de búsqueda"
                        v-model="filter.searchTerm"
                        class="flex-grow-1 mr-1">
                        <template #append-outer>
                            <v-btn
                                @click="GetRecords()"
                                color="primary"
                                class="mb-1">
                                <v-icon>mdi-magnify</v-icon>
                            </v-btn>
                        </template>
                    </v-text-field>
                </v-col>
            </v-row>
            <v-data-table v-if="id_role != null"
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
                
                <template v-slot:[`item.actions`]="{ item }">
                    <v-switch
                        :value="item.id"
                        v-model="permissions_assigned"
                    ></v-switch>
                </template>
            </v-data-table>
            <div class="text-center pt-2" v-if="id_role != null">
                <v-pagination
                    v-model="currentPage"
                    :length="pageCount"
                    :disabled="totalReg<=0"
                    circle
                ></v-pagination>
            </div>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="secondary darken-1" text @click="ClearPermissions">Cancelar</v-btn>
                <v-btn color="primary" :disabled="!id_role" @click="SavePermissions(id_role);"> Guardar </v-btn>
            </v-card-actions>
        </v-card>
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
        dataReg: [],
        tableHeaders: [
            { text: 'Nº', value: 'index',  sortable: false,},
            { text: 'Permiso', value: 'title', sortable: false,},
            { text: 'Identificador', value: 'name', sortable: false,},

            { text: 'Asignar', value: 'actions', sortable: false, align:'left' }
        ],
        filter: {
            searchTerm: '',
        },

        permissions_assigned: [],
        //--- End ---

        id_role: null,
        roles_list: [],
    }),
    mounted(){
        this.GetUserRoles();
    },
    methods: {
        //--- Datatable Functions ---
        GetRecords(page = 1, perPage = 25){
            this.loadingTable = true;
            this.dataReg = [];
            axios.get('/api/permissions?page='+page +'&perPage='+perPage
                +'&searchTerm='+this.filter.searchTerm
                
            ).then(response => {
                this.dataReg     = response.data;
                this.currentPage = this.dataReg.current_page;
                this.pageCount   = this.dataReg.last_page;
                this.totalReg    = this.dataReg.total;
                
            }).finally(() => (this.loadingTable = false));
        },
        ClearPermissions(){
            this.permissions_assigned = [];
            this.id_role = null;
        },
        SavePermissions(id){
            this.preloader = true;
            axios.post('/api/assignPermissionsToRole/'+id, 
                {permissions: this.permissions_assigned}
            ).then((response)=>{
                localStorage.setItem('user_data', JSON.stringify(response.data.user_data))
                localStorage.setItem('user_permissions',JSON.stringify(response.data.permissions));

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
        },
        //--- End ---


        //--- Loading Data ---
        GetUserRoles(){
            axios.get('/api/userRolesCombo').then(response => {
                this.roles_list = response.data;

            }).catch(e => {
                console.error(e);
            });
        },
        GetRolePermissions(id){
            axios.get('/api/permissions/'+id).then(response => {
                this.permissions_assigned = response.data.map(({ id }) => id);

            }).catch(e => {
                console.error(e);
            });
        },
        //--- End ---
    },
    watch:{
        dataTabOptions(event) {
            this.itemsPerPage = event.itemsPerPage;
            this.GetRecords(event.page, event.itemsPerPage);
        },
        
        id_role(){
            if(this.id_role != null){
                this.GetRolePermissions(this.id_role);
            }
        }
    }
}
</script>