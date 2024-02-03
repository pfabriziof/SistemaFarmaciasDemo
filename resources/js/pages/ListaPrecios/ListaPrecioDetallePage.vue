<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Lista de Precios - {{priceListData.codigo}} {{priceListData.nombre}}</h2> 
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre de producto o código"
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
                <v-col v-if="$can('listspricedetail_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="addDialog=true;">Agregar Registro<v-icon>mdi-plus</v-icon></v-btn>            
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
                <template v-slot:[`item.estado`]="{ item }">
                    <v-chip small v-if="item.estado == 1" class="ma-2" color="success">Habilitado</v-chip>
                    <v-chip small v-if="item.estado == 0" class="ma-2" color="error">Deshabilitado</v-chip>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn v-if="$can('listspricedetail_update', 'all')" small icon @click="editReg(item)">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn v-if="$can('listspricedetail_delete', 'all')" small icon @click="deleteReg(item)">
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
                    <span class="headline" v-show="!dialogEditar">Nuevo Precio Producto</span>
                    <span class="headline" v-show="dialogEditar">Editar Precio Producto</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm" @submit.prevent="dialogEditar ? updateReg() : createReg()">
                        <v-row>
                            <v-col>
                                <v-autocomplete
                                    v-model="addForm.producto"
                                    :items="itemsProduct"
                                    :loading="isLoadingProduct"
                                    :search-input.sync="searchProduct"
                                    :item-text="prodItemText"
                                    item-value="id_producto"
                                    v-on:change="handleSubmitProduct()"
                                    label="Buscar producto por nombre..."
                                    hide-no-data
                                    return-object>
                                </v-autocomplete>
                                <v-text-field v-model="addForm.nombreProducto" label="Nombre Producto" disabled></v-text-field>
                                <v-text-field v-model="addForm.codigo_producto" label="Código de Producto" disabled></v-text-field>
                                <v-text-field label="Precio" placeholder="0,00" min="0" v-model="addForm.precio_compra" type="number" class="txt-number" :rules="requiredRules" autocomplete="off"></v-text-field>
                            </v-col>
                        </v-row>
                        <v-card-actions>
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
        breadcrumbs: [{
            text: 'Lista de Precios',
            disabled: false,
            to: '/lista_precios'
        }, {
            text: 'Lista de Precios Detalle'
        }],
        //--- Datatable ---
        loadingTable: false,
        current_page: 1,
        pageCount: 1,
        itemsperpage: 25,
        total_reg: 0,
        dataTabOptions: {},
        data_reg: {},
        headers: [
            { text: 'Código', sortable: false, value: 'producto.codigo_producto' },
            { text: 'Producto', sortable: false, value: 'producto.nombreProducto' },
            { text: 'Precio Compra', sortable: false, value: 'precio_compra', align:'center' },
            { text: 'Precio Venta', sortable: false, value: 'precio_venta', align:'center' },
            { text: 'Estado', value: 'estado', sortable: false, align:'center'},
            { text: 'Acciones', value: 'actions', sortable: false, align:'right' }
        ],
        filter: {
            search_query: null,
        },
        //--- End ---

        //--- Ventana Emergente ---
        addDialog: false,
        dialogEditar: false,
        addForm: new Form({
            id_lista_precio: null,
            producto: {},
            id_producto: null,
            nombreProducto: null,
            codigo_producto: null,
            precio_compra: null,
        }),
        itemsProduct: [],
        isLoadingProduct: false,
        searchProduct: null,
        //--- End ---
        priceListData:{},
        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),

    mounted () {
        this.getListPriceData();
        this.addForm.id_lista_precio = this.$route.params.id;
    },

    methods: {
        getListPriceData(){
            axios.get('/api/price_list/' + this.$route.params.id).then((response) => {
                this.priceListData = response.data;
                
            }).catch(e => {
                console.error(e);
            });
        },
        //--- Datatable ---
        getRegistros(page = 1, per_page = 25){
            this.loadingTable = true;
            this.data_reg = [];

            let dataParameters = {
                id_lista: this.addForm.id_lista_precio,
                per_page: per_page,
                data: this.filter,
            };

            axios.post('/api/getPriceListDetail?page=' + page, dataParameters).then(response => {
                this.data_reg     = response.data;
                this.current_page = this.data_reg.current_page;
                this.pageCount    = this.data_reg.last_page;
                this.total_reg    = this.data_reg.total;

            }).finally(() => (this.loadingTable = false));
        },
        limpiarFiltros(){
            this.filter.search_query = null;
            this.getRegistros();
        },
        //--- End ---
        
        //--- Ventana Emergente ---
        // Add/Edit/Delete Functions
        createReg(){
            this.preloader = true;
            this.loadingTable = true;

            this.addForm.post('/api/pricelist_detail').then((response)=>{
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
            // this.addForm.fill(item);
            this.addForm.id_lista_detalle = item.id_lista_detalle;
            this.addForm.id_producto     = item.id_producto;
            this.addForm.nombreProducto  = item.producto.nombreProducto;
            this.addForm.codigo_producto = item.producto.codigo_producto;
            this.addForm.precio_compra   = item.precio_compra;
            this.addDialog = true;
        },
        updateReg(){
            this.preloader = true;
            this.loadingTable = true;
            
            this.addForm.put('/api/pricelist_detail/'+this.addForm.id_lista_detalle).then((response) => {
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
                var title ='Deshabilitar Detalle de Lista';
                var text = "¿Está seguro que desea deshabilitar este registro?";
                var swal_title = 'Deshabilitado!';
                var swal_text = 'El detalle de lista ha sido deshabilitado.';
            }else{
                var title ='Habilitar Detalle de Lista';
                var text = "¿Está seguro que desea habilitar este registro?";
                var swal_title = 'Habilitado!';
                var swal_text = 'El detalle de lista ha sido habilitado.';
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
                    axios.delete('/api/pricelist_detail/'+item.id_lista_detalle).then(response => {
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

        //--- Autocomplete Producto ---
        handleSubmitProduct(){
            this.addForm.id_producto     = this.addForm.producto.id_producto;
            this.addForm.nombreProducto  = this.addForm.producto.nombreProducto;
            this.addForm.codigo_producto = this.addForm.producto.codigo_producto;
        },
        prodItemText(item){
            return `${item.nombreProducto} | ${item.codigo_producto}`
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
                // console.log('Dialog is closing');
                this.dialogEditar = false;
                this.$refs.addForm.reset()
            }
        },
        searchProduct(val) {
            if(val != null){
                if (val.length < 3) return;

                this.isLoadingProduct = true
                let token = document.head.querySelector('meta[name="csrf-token"]');
                const requestOptions = {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token.content },
                    body: JSON.stringify({ keywords: val })
                };

                fetch('/api/buscarProductos', requestOptions)
                .then(response => response.json())
                .then(data => {
                    this.itemsProduct = data;

                }).catch(e => {
                    console.error(e);
                    
                }).finally(() => (this.isLoadingProduct = false))
            }
        },
    },
}
</script>
