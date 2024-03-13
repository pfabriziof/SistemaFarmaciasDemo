<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Crear Producto</h2> 
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
                                        :items="marcas_combo"
                                        item-text="marca"
                                        item-value="id_marca"
                                        :rules="requiredRules"
                                    ></v-select>
                                    <v-select label="Laboratorio *" v-model="producto.id_laboratorio"
                                        :items="laboratorios_combo"
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
                                        :items="condicionesalm_combo" 
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
                                    <v-select :items="producto_tipos"  label="Tipo Producto *" v-model="producto.id_tipo_producto"
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
                                    <v-btn color="primary"  @click="create()">Guardar cambios</v-btn>
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
                                <v-col class="text-right">
                                    <v-btn fab color="primary" icon @click="addLote">
                                        <v-icon>mdi-plus-circle</v-icon>
                                    </v-btn>           
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field label="Stock Inicial (NIU)" v-model="producto.stock" type="number" disabled></v-text-field>
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
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="lotes.length == 0">
                                        <td colspan="4" class="text-center"><b>Puede agregar lotes al producto. (Opcional)</b></td>
                                    </tr>
                                    <tr v-for="lote in lotes" :key="lote.id">
                                        <td><v-text-field label="Lote" v-model="lote.lote" autocomplete="off"></v-text-field></td>
                                        <td>
                                            <v-text-field v-model="lote.cantidad" min="0" type="number" placeholder="0"
                                            @change="calcularStock()"
                                            autocomplete="off"></v-text-field>
                                        </td>
                                        <td><input placeholder="Fecha V." v-model="lote.fecha_expiracion" type="date" class="form-control"></td>
                                        <td>
                                            <v-btn small color="error" icon @click="delLote(lote)"><v-icon small> mdi-delete</v-icon></v-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-simple-table>
                        </v-card-text>  
                    </v-card>
                    <br>
                    <v-card class="mb-4" light>
                        <v-card-title>
                            Lista de Precios<v-spacer></v-spacer>
                            <v-btn fab color="primary" icon @click="addPriceList">
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
                                        <th class="text-left"></th>
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
                                                :items="pricelist_combo"
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
                                        <td>
                                            <v-btn small color="error" icon @click="delPriceList(item)">
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
            text: 'Crear Producto'
        }],
        categorias_combo: [],
        marcas_combo: [],
        unidades_medida: [],
        pricelist_combo: [],
        laboratorios_combo:[],
        condicionesalm_combo:[],
        producto_tipos:[],
        producto:{
            codigo_producto: null,
            nombreProducto: null,
            vigencia_registro: null,
            id_condicion_alm: 1,
            id_marca: null,
            id_unidad_medida: null,
            id_laboratorio: null,
            id_categoria: null,
            id_unidad_medida: 2,
            id_tipo_producto: 1,
            stock_minimo: null,
            stock: 0,
        },
        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        lotes: [],
        list_detail:[],
        menuFechaVigencia: false,
    }),

    mounted () {
        this.producto.vigencia_registro = this.todaysDateDefault();

        this.unidadesMedidaCombo();
        this.categoriasCombo();
        this.marcasCombo();
        this.priceListsCombo();
        this.laboratoriosCombo();
        this.condicionesAlmCombo();
        this.productosTiposCombo();
    },

    methods: {
        categoriasCombo(){
            axios.get('/api/categoriasCombo').then(response => {
                this.categorias_combo = response.data;
            });
        },
        marcasCombo(){
            axios.get('/api/marcasCombo').then(response => {
                this.marcas_combo = response.data;
            });
        },
        priceListsCombo(){
            axios.get('/api/priceListsCombo').then(response => {
                this.pricelist_combo = response.data;
            });
        },
        laboratoriosCombo(){
            axios.get('/api/laboratoriosCombo').then(response => {
                this.laboratorios_combo = response.data;
            }); 
        },
        condicionesAlmCombo(){
            axios.get('/api/condicionesAlmCombo').then(response => {
                this.condicionesalm_combo = response.data;
            }); 
        },
        productosTiposCombo(){
            axios.get('/api/productosTiposCombo').then(response => {
                this.producto_tipos = response.data;
            }); 
        },
        unidadesMedidaCombo(){
            axios.get('/api/unidadesMedidaCombo').then(response => {
                this.unidades_medida = response.data;
            });
        },
        create(){
            this.preloader = true;
            axios.post('/api/producto',{
                data: this.producto,
                lotes: this.lotes,
                list_detail: this.list_detail
            }).then(response => {
                Toast.fire({
                    icon: 'success',
                    title: response.data.message,
                });
                this.$router.push('/productos');
                
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

        //--- Date Formatting ---
        todaysDateDefault(){
            var date = new Date;
            return date.getFullYear() +"-" + (((date.getMonth()+1) < 10)?"0":"") + (date.getMonth()+1) +"-" + ((date.getDate() < 10)?"0":"") + date.getDate();
        },
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        //--- Fin Date Formatting ---

        calcularStock(){
            var stock_total;
            stock_total = this.lotes.reduce(function (sum, lote) {
                var total_fila = parseFloat(lote.cantidad);
                if (!isNaN(total_fila)) {
                    return sum + total_fila;
                }
            }, 0);
            
            if(!isNaN(stock_total)){
                this.producto.stock = stock_total;
            }else{
                this.producto.stock = 0;
            }
        },

        //--- Listas / Lotes Precios---
        addLote(){
            this.lotes.push({
                lote: null,
                cantidad: null,
                fecha_expiracion: null
            });
        },
        delLote(item){
            var idx = this.lotes.indexOf(item);
            if(idx > -1){
                this.lotes.splice(idx, 1);
            }
            this.calcularStock();
        },

        addPriceList(){
            this.list_detail.push({
                id_lista_precio: null,
                precio_compra: null,
                precio_venta: null,
            });
        },
        delPriceList(item){
            var idx = this.list_detail.indexOf(item);
            if(idx > -1){
                this.list_detail.splice(idx, 1);
            }
        },
        priceListChange(item){
            if(item.id_lista_precio == 1){
                item.unidades = 1;
            }
        },
        //---Fin Listas Lotes Precios---
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