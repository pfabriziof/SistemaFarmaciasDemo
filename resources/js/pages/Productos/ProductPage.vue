<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Productos</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de producto o código"
                        v-model="filter.searchTerm"
                        append-icon="mdi-magnify"
                        class="flex-grow-1 mr-1"
                        hide-details
                    ></v-text-field>
                </v-col>
                <v-col class="text-right">
                    <v-btn color="primary" class="mr-2" @click="GetRecords()">
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
                <v-col v-if="$can('products_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" :to="'/crear_productos'">Agregar Producto<v-icon>mdi-plus</v-icon></v-btn>            
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
                    <v-btn v-if="$can('products_update', 'all')" small icon :to="'/editar_productos/'+item.id_producto">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('products_delete', 'all')" small icon @click="deleteItem(item)">
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
            { text: 'Producto', sortable: false, value: 'nombreProducto' },
            { text: 'Código', sortable: false, value: 'codigo_producto' },
            { text: 'Categoría', sortable: false, value: 'categoria.categoria' },
            { text: 'Stock', sortable: false, value: 'stock' },
            { text: 'Stock Mínimo', sortable: false, value: 'stock_minimo' },
            { text: 'Estado', value: 'estado', sortable: false, align:'center'},
            { text: 'Acciones', value: 'actions', sortable: false , align:'right'}
        ],
        filter: {
            searchTerm: "",
        },
        //--- End ---
    }),

    mounted () {
        //
    },

    methods: {
        //--- Datatable ---
        GetRecords(page = 1, perPage = 25, sortDesc = 0, sortBy = ""){
            this.loadingTable = true;
            this.dataReg = [];
            const myParams = new URLSearchParams(this.filter).toString();

            axios.get('api/producto?page='+ page +"&perPage="+perPage
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

        deleteItem (item) {
            this.loadingTable = true;
            if(item.estado!=0){
                var title ='Deshabilitar Producto';
                var text = "¿Está seguro que desea deshabilitar este producto?";
                var swal_title = 'Deshabilitado!';
                var swal_text = 'El producto ha sido deshabilitado.';
            }else{
                var title ='Habilitar Producto';
                var text = "¿Está seguro que desea habilitar este producto?";
                var swal_title = 'Habilitado!';
                var swal_text = 'El producto ha sido habilitado.';
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
                    axios.delete('api/producto/'+item.id_producto).then((response)=>{
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
                        
                    }).catch(e=> {
                        console.error(e);
                        Toast.fire({
                            icon: 'error',
                            title: "Ocurrió un error",
                        });

                    });
                }
            }).finally(()=>(this.loadingTable = false));
        },
    },
    watch: {
        dataTabOptions(event) {
            this.itemsPerPage = event.itemsPerPage;
            this.GetRecords(event.page, event.itemsPerPage);
        },
    },
}
</script>