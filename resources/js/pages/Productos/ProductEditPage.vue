<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Editar Producto</h2> 
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>

        <v-form ref="form">
            <v-row>
                <v-col cols="12" md="7">
                    <v-card class="mb-4" light style="padding: 15px">
                        <v-card-title>Información del Producto</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field label="Código de Producto *" v-model="producto.codigo_producto" :rules="requiredRules" autocomplete="off"></v-text-field>
                                    <v-text-field label="Nombre *" v-model="producto.nombreProducto" :rules="requiredRules" autocomplete="off"></v-text-field>
                                    <v-select label="Marca *" v-model="producto.id_marca"
                                        :items="marcas"
                                        item-text="marca"
                                        item-value="id_marca"
                                        :rules="requiredRules"
                                    ></v-select>
                                    <v-select label="Laboratorio *" v-model="producto.id_laboratorio"
                                        :items="laboratorios"
                                        item-text="nombre"
                                        item-value="id_laboratorio"
                                        :rules="requiredRules"
                                    ></v-select>
                                    <v-text-field label="Registro Sanitario" v-model="producto.registro_sanitario" autocomplete="off"></v-text-field>
                                    <v-select label="Unidades de Medida *" v-model="producto.id_unidad_medida"
                                        :items="unidades_medida" 
                                        item-text="unidad_medida"
                                        item-value="id_unidad_medida"
                                        :rules="requiredRules"
                                    ></v-select>
                                    <v-text-field label="Principio Activo" v-model="producto.principio_activo" autocomplete="off"></v-text-field>
                                    <v-textarea label="Indicaciones" v-model="producto.indicaciones"
                                        outlined
                                        hide-details
                                        rows="6"
                                        autocomplete="off"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select label="Categoría *" v-model="producto.id_categoria"
                                        :items="categorias_combo" 
                                        item-text="categoria"
                                        item-value="id_categoria"
                                        :rules="requiredRules"
                                    ></v-select>
                                    <v-text-field label="Stock Mínimo *" v-model="producto.stock_minimo"
                                        type="number" class="txt-number"
                                        autocomplete="off"
                                        :rules="requiredRules"
                                    ></v-text-field>
                                    <v-select label="Condiciones de Alm." v-model="producto.id_condicion_alm"
                                        :items="condiciones_alm"
                                        item-text="descripcion"
                                        item-value="id_condicion_alm"
                                    ></v-select>
                                    <v-text-field label="Ubicación" v-model="producto.ubicacion" autocomplete="off"></v-text-field>
                                    <v-menu
                                        v-model="menuFechaVigencia"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto">
                                        <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="formatoVigenciaRegistro"
                                            label="Vigencia Reg. Sanitario"
                                            prepend-icon="mdi-calendar"
                                            readonly
                                            v-bind="attrs"
                                            v-on="on"
                                        ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="producto.vigencia_registro" @input="menuFechaVigencia = false" locale="es-ES"></v-date-picker>
                                    </v-menu>
                                    <v-text-field label="Concentración" v-model="producto.concentracion" autocomplete="off"></v-text-field>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <v-select label="Tipo Producto *" v-model="producto.id_tipo_producto"
                                        :items="producto_tipos"
                                        item-text="tipo"
                                        item-value="id_producto_tipo"
                                        :rules="requiredRules"
                                    ></v-select>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" md="12">
                                    <v-spacer></v-spacer> 
                                    <v-btn class="btn-actions" :to="'/productos'">Cancelar</v-btn>
                                    <v-btn color="primary" @click="update()">Guardar cambios</v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="5">
                    <v-card class="mb-4" light>
                        <v-card-title>
                            <v-row dense class="pa-2 align-center">
                                <v-col cols="6">
                                    Detalle de Lotes
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Stock Actual (NIU)" v-model="producto.stock" type="number" disabled></v-text-field>
                                </v-col>
                            </v-row>
                        </v-card-title>
                        <v-card-text>
                            <v-simple-table fixed-header>
                                <thead>
                                    <tr>
                                        <th class="text-center">Lote</th>
                                        <th class="text-center">Cantidad (NIU)</th>
                                        <th class="text-center" width="150px">Fecha V.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="lotes.length == 0">
                                        <td colspan="4" class="text-center"><b>Aún no se han agregado compras de lotes de este producto.</b></td>
                                    </tr>
                                    <tr v-for="lote in lotes" :key="lote.id">
                                        <td class="text-center">{{lote.lote}}</td>
                                        <td class="text-center">{{lote.cantidad}}</td>
                                        <td class="text-center">{{lote.fecha_expiracion | formatDate}}</td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>  
                    </v-card>
                    <br>
                    <v-card class="mb-4" light>
                        <v-card-title>
                            Lista de Precios<v-spacer></v-spacer>
                            <v-btn fab color="primary" icon @click="addListDetail">
                                <v-icon>mdi-plus-circle</v-icon>
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <v-simple-table fixed-header>
                                <thead>
                                    <tr>
                                        <th class="text-left">Lista Precio</th>
                                        <th class="text-left">Precio Compra</th>
                                        <th class="text-left">Precio Venta</th>
                                        <th class="text-left">Unidades</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="list_detail.length == 0">
                                        <td colspan="5" class="text-center">
                                            <b>Puede agregar listas de precio al producto</b>
                                        </td>
                                    </tr>
                                    <tr v-for="item in list_detail" :key="item.id">
                                        <td>
                                            <v-select label="Lista" v-model="item.id_lista_precio"
                                                :items="prices_list"
                                                item-text="nombre"
                                                item-value="id_lista_precio"
                                                @change="priceListChange(item)"
                                                :rules="requiredRules"
                                            ></v-select>
                                        </td>
                                        <td>
                                            <v-text-field label="P.C." v-model="item.precio_compra"
                                                type="number"
                                                class="txt-number"
                                                placeholder="0,00"
                                                min="0"
                                                autocomplete="off"
                                                :rules="requiredRules"
                                            ></v-text-field>
                                        </td>
                                        <td>
                                            <v-text-field label="P.V." v-model="item.precio_venta"
                                                type="number"
                                                class="txt-number"
                                                placeholder="0,00"
                                                min="0"
                                                autocomplete="off"
                                                :rules="requiredRules"
                                            ></v-text-field>
                                        </td>
                                        <td>
                                            <v-text-field label="Unidades" v-model="item.unidades"
                                                type="number" min="0" placeholder="0"
                                                autocomplete="off"
                                                :rules="requiredRules"
                                            ></v-text-field>
                                        </td>

                                        <td v-if="item.id_lista_detalle" class="text-center">
                                            <v-chip 
                                                v-if="item.estado == 1"
                                                @click="disenableListDetail(item)"
                                                color="success"
                                                small>
                                                Habilitado
                                            </v-chip>
                                            <v-chip 
                                                v-if="item.estado == 0"
                                                @click="disenableListDetail(item)"
                                                color="error"
                                                small>
                                                Deshabilitado
                                            </v-chip>
                                        </td>
                                        <td v-else class="text-center">
                                            <v-btn small color="error" icon @click="delListDetail(item)">
                                                <v-icon small>mdi-delete</v-icon>
                                            </v-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>  
                    </v-card>
                </v-col>
            </v-row>
        </v-form>
    </div>
</template>

<script>
export default {
    data: () => ({
        preloader: false,
        breadcrumbs: [{
            text: 'Productos',
            disabled: false,
            to: '/productos'
        }, {
            text: 'Editar Producto'
        }],
        categorias_combo: [],
        marcas: [],
        unidades_medida: [],
        prices_list: [],
        producto:{
            vigencia_registro: null,
        },
        lotes: [],
        list_detail:[],
        menuFechaVigencia: false,

        laboratorios:[],
        condiciones_alm:[],
        producto_tipos:[],

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),

    mounted () {
        this.categoriasCombo();
        this.laboratoriosCombo();
        this.condicionesAlmCombo();
        this.productosTiposCombo();
        this.unidadesMedidaCombo();
        this.marcasCombo();
        this.priceListsCombo();

        this.getProducto(this.$route.params.id);
        this.getAllListPriceByProduct(this.$route.params.id);
    },

    methods: {
        //--- Main Product Functions ---
        getProducto(id){
            this.preloader = true;
            axios.get('/api/producto/'+id).then(response => {
                this.producto = response.data;
                this.lotes    = response.data.lotes;
                if(this.producto.stock <= this.producto.stock_minimo){
                    Swal.fire({
                        icon: 'warning',
                        title: "El stock actual del producto es inferior al stock mínimo asignado",
                    });
                }

            }).finally(()=>(this.preloader = false));
        },
        getAllListPriceByProduct(id){
            axios.get('/api/getAllListPriceByProduct/'+id).then(response => {
                this.list_detail = response.data;
            });
        },
        update(){
            this.preloader = true;
            axios.put('/api/producto/'+this.$route.params.id,{
                data: this.producto,
                lotes: this.lotes,
                list_detail: this.list_detail
            }).then(response => {
                Toast.fire({
                    icon: 'success',
                    title: response.data.message,
                });

                this.$router.go();
                
            }).catch(e => {
                var error_messages = e.response.data.errors;
                for (let i in error_messages) {
                    Toast.fire({
                        icon: 'error',
                        title: error_messages[i][0],
                    });
                }
            }).finally(()=>(this.preloader = false));
        },
        //--- End ---

        //--- Lista Precios---
        addListDetail(){
            this.list_detail.push({
                id_lista_precio: null,
                id_lista_detalle: null,
                precio_compra: null,
                precio_venta: null,
            });
        },
        delListDetail(item){
            var idx = this.list_detail.indexOf(item);
            if(idx > -1){
                this.list_detail.splice(idx, 1);
            }
        },
        disenableListDetail(item){
            item.estado = item.estado != 1? 1 : 0;
        },
        priceListChange(item){
            if(item.id_lista_precio == 1){
                item.unidades = 1;
            }
        },
        //--- End ---

        //--- Date Formatting ---
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        //--- End ---

        
        //--- Getting Related Info ---
        priceListsCombo(){
            axios.get('/api/priceListsCombo').then(response => {
                this.prices_list = response.data;
            });
        },
        laboratoriosCombo(){
            axios.get('/api/laboratoriosCombo').then(response => {
                this.laboratorios = response.data;
            }); 
        },
        condicionesAlmCombo(){
            axios.get('/api/condicionesAlmCombo').then(response => {
                this.condiciones_alm = response.data;
            }); 
        },
        productosTiposCombo(){
            axios.get('/api/productosTiposCombo').then(response => {
                this.producto_tipos = response.data;
            }); 
        },
        categoriasCombo(){
            axios.get('/api/categoriasCombo').then(response => {
                this.categorias_combo = response.data;
            });
        },
        marcasCombo(){
            axios.get('/api/marcasCombo').then(response => {
                this.marcas = response.data;
            });
        },
        unidadesMedidaCombo(){
            axios.get('/api/unidadesMedidaCombo').then(response => {
                this.unidades_medida = response.data;
            });
        },
        //--- End ---
    },
    computed: {
        //--- Date Formatting ---
        formatoVigenciaRegistro: {
            get: function () {
                return this.formatDate(this.producto.vigencia_registro)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
    },
}
</script>