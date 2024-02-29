<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Nueva Orden de Compra</h2>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-form @submit.prevent="createOrdenCompra">
                <v-row dense class="pa-2">
                    <v-col cols="5">
                        <autocomplete @keypress.enter.native.prevent
                            :search="searchProveedor"
                            :get-result-value="getResultProveedor" 
                            placeholder="Buscar proveedor por nombre/nro. documento"
                            aria-label="Buscar proveedor por nombre/nro. documento"
                            ref="busProv"
                            @submit="handleSubmitProveedor"
                            autocomplete="off" 
                            auto-select
                        ></autocomplete>
                    </v-col>
                </v-row>
                <v-row dense class="pa-2">
                    <v-col cols="6">
                        <v-text-field v-model="addForm.nombre_proveedor" label="Nombre Proveedor" disabled></v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-text-field v-model="addForm.email" label="Correo Electrónico *" :rules="requiredRules"></v-text-field>
                    </v-col>
                    <v-col cols="3">
                        <v-select :items="combo_monedas" label="Moneda *" v-model="addForm.id_moneda" 
                        item-text="moneda" 
                        item-value="id_moneda"
                        :rules="requiredRules"
                    ></v-select>
                    </v-col>
                    <v-col cols="3">
                        <v-select :items="medios_pago" label="Medio Pago *" v-model="addForm.id_medio_pago" required item-text="medio_pago" item-value="id_medio_pago"></v-select>
                    </v-col>
                    <v-col cols="3">
                        <v-select :items="tipos_cambio" label="Tipo Cambio *" v-model="addForm.id_tipo_cambio"
                        :item-text="cambioItemText"
                        item-value="id_tipo_cambio"
                        :rules="requiredRules">
                    </v-select>
                    </v-col>
                    <v-col cols="3">
                        <v-menu
                            v-model="menuFechEmi"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto" >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field label="Fecha de Emisión *" prepend-icon="mdi-calendar"
                                    v-model="formatoFechaEmision" 
                                    :rules="requiredRules"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker no-title v-model="addForm.fecha_emision" @input="menuFechEmi = false" locale="es-ES"></v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="3">
                        <v-menu
                            v-model="menuFechVen"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto" >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field label="Fecha de Vencimiento *" prepend-icon="mdi-calendar"
                                    v-model="formatoFechaVenc"
                                    :rules="requiredRules"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker no-title v-model="addForm.fecha_vencimiento" @input="menuFechVen = false" locale="es-ES"></v-date-picker>
                        </v-menu>
                    </v-col>
                </v-row>
                <v-row dense class="pa-2">
                    <v-col cols="12" style="z-index: 3;">
                        <autocomplete @keypress.enter.native.prevent
                            :search="searchProduct"
                            :get-result-value="getResultProduct" 
                            placeholder="Buscar producto"
                            aria-label="Buscar producto"
                            ref="busProd"
                            @submit="handleSubmitProduct"
                            autocomplete="off" 
                            auto-select
                        ></autocomplete>
                    </v-col>
                </v-row>
                <v-simple-table fixed-header>
                    <thead>
                        <tr>
                            <th class="text-left">Producto</th>
                            <th class="text-left">Unidad</th>
                            <th class="text-left">Laboratorio</th>
                            <th class="text-left">Lista de Precio</th>
                            <th style="width:10%;" class="text-left">P.U (S/.)</th>
                            <th style="width:10%;" class="text-left">CNT</th>
                            <th style="width:10%;" class="text-left">P.T (S/.)</th>
                            <th style="width:3%;"  class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="order_detail.length == 0">
                            <td colspan="12" class="text-center"><b>Aún no se han agregado productos a esta orden de compra</b></td>
                        </tr>
                        <tr v-for="(item, k) in order_detail" :key="k">
                            <td>{{item.nombreProducto}}</td>
                            <td>{{item.und_simbolo}}</td>
                            <td>{{item.laboratorio}}</td>
                            <td>
                                <v-select :items="item.list_precios" label="Lista de precios"
                                    v-model="item.lista_detalle"
                                    v-on:change="changePriceList(item)"
                                    no-data-text="No hay datos">
                                    <template slot="item" slot-scope="data">
                                        {{ data.item.listaprecio.nombre }} ({{data.item.unidades}}) - {{ data.item.precio_compra }}
                                    </template>
                                    <template slot="selection" slot-scope="data">
                                        {{ data.item.listaprecio.nombre }} ({{data.item.unidades}}) - {{ data.item.precio_compra }}
                                    </template>
                                </v-select>
                            </td>
                            <td>
                                <input type="number" placeholder="0,00" step="0.1" min="0" class="form-control" v-model="item.precio_unitario" @change="calcularTotalFila(item)">
                            </td>
                            <td>
                                <input type="number" placeholder="0,00" min="0" class="form-control" v-model="item.cantidad" @change="calcularTotalFila(item)">
                            </td>
                            <td>{{item.precio_total}}</td>
                            <td>
                                <v-btn small color="error" icon @click="borrarFila(item)"><v-icon small> mdi-delete</v-icon></v-btn>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr v-if="op_inafectas > 0">
                            <td colspan="8" style="text-align:right;"><b>Op. Inafectas: </b>{{op_inafectas}}</td>
                        </tr>
                        <tr v-if="op_exoneradas > 0">
                            <td colspan="8" style="text-align:right;"><b>Op. Exoneradas: </b>{{op_exoneradas}}</td>
                        </tr>
                        <tr v-if="op_gravadas > 0">
                            <td colspan="8" style="text-align:right;"><b>Op. Gravadas: </b>{{op_gravadas}}</td>
                        </tr>
                        <tr v-if="icbper > 0">
                            <td colspan="8" style="text-align:right;"><b>ICBPER: </b>{{icbper}}</td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align:right;"><b>IGV (18%): </b>{{ord_igv}}</td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align:right;"><b>Total: </b>{{ord_total}}</td>
                        </tr>
                    </tfoot>
                </v-simple-table>
                <v-card-actions class="justify-center">
                    <v-btn type="submit" color="primary">Guardar</v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </div>
</template>
<script>
export default {
    data: () => ({
        preloader: false,
        breadcrumbs: [{
            text: 'Órdenes de Compra',
            disabled: false,
            to: '/ordenes_compra'
        }, {
            text: 'Nueva Orden de Compra'
        }],

        //--- Formulario ---
        id_cotizacion: 0,
        cotizacion: {},
        
        addForm: new Form({
            id_proveedor: null,
            // id_moneda: null,
            id_medio_pago: null,
            // id_tipo_cambio: null,
            nombre_proveedor: null,
            email: null,
            fecha_emision: null,
            fecha_vencimiento: null,
            //Extras
            ord_detalle: [],
        }),
        menuFechEmi: false,
        menuFechVen: false,
        combo_monedas: [],
        medios_pago: [],
        tipos_cambio: [],
        //--- End ---

        //--- Autocomplete ---
        order_detail: [],
        op_inafectas: 0,
        op_exoneradas:0,
        op_gravadas: 0,
        icbper: 0,
        ord_igv: '0,00',
        ord_total: '0,00',
        //--- End ---

        
        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),
    mounted(){
        this.id_cotizacion = this.$route.params.id;
        this.tiposCambioCombo();
        this.getCotizacion();
        this.monedasCombo();
        this.mediosPagoCombo();
    },
    methods:{
        getCotizacion(){
            this.preloader = true;
            axios.get('/api/prv_cotizacion/'+this.id_cotizacion).then((response) => {
                //Cotizacion
                this.cotizacion = response.data.cotizacion;
                this.addForm.fill(this.cotizacion);
                this.addForm.id_moneda = 1;
                this.addForm.id_medio_pago = 1;
                this.addForm.id_tipo_cambio = 1;
                this.addForm.id_proveedor  = this.cotizacion.proveedor.id_proveedor;
                this.addForm.nombre_proveedor  = this.cotizacion.proveedor.nombre;
                this.addForm.fecha_vencimiento = this.addForm.fecha_emision;

                //Cotizacion Detalle
                response.data.cotizacion_detalle.forEach((val)  => {
                    this.handleSubmitProduct(val.producto, val.cantidad);
                });

            }).catch(e => {
                console.error(e);
            }).finally(()=>(this.preloader = false));
        },
        changePriceList(item){
            item.precio_unitario = item.lista_detalle.precio_compra;
            this.calcularTotalFila(item);
        },
        //--- Funciones Orden Compra ---
        createOrdenCompra(){
            this.preloader = true;
            this.addForm.ord_detalle = this.order_detail;
            this.addForm.post('/api/orden_compra').then((result)=>{
                Toast.fire({
                    icon: 'success',
                    title: 'Orden de compra creada correctamente!'
                });
                this.$router.push('/orden_compra_view/'+result.data.id_orden_compra);

            }).catch(e => {
                var error_messages = e.response.data.errors;
                for (let i in error_messages) {
                    Toast.fire({
                        icon: 'error',
                        title: error_messages[i][0],
                    });
                }
                
            }).finally(() => (this.preloader = false));
        },
        //--- End ---

        //--- AutoProducto ---
        handleSubmitProduct(item, qty=1) {
            axios.get('/api/getListPricebyProduct/'+item.id_producto).then((response) => {
                this.order_detail.push({
                    id_producto:     item.id_producto,
                    nombreProducto:  item.nombreProducto,
                    und_simbolo:     item.unidad_medida.simbolo,
                    laboratorio:     item.laboratorio.nombre,

                    list_precios:  response.data,
                    lista_detalle: response.data[0] ? response.data[0] : null,

                    cantidad:        qty,
                    precio_unitario: response.data[0] ? response.data[0].precio_compra : null,
                    precio_total:    response.data[0] ? response.data[0].precio_compra * qty : null,

                    icbper:        item.tipo_producto.icbper,
                    tipo_impuesto: item.tipo_producto.impuesto,
                });
                this.calcularTotal();
                
            }).catch(e => {
                console.error(e);
            });

            this.$refs.busProd.setValue('');
        },
        getResultProduct(result) {
            return result.nombreProducto;
        },
        searchProduct(input) {
            let token = document.head.querySelector('meta[name="csrf-token"]');
            const requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token.content },
                body: JSON.stringify({ keywords: input })
            };

            return new Promise(resolve => {
                if (input.length < 3) {
                    return resolve([])
                }

                fetch('/api/buscarProductosCompra', requestOptions)
                .then(response => response.json())
                .then(data => {
                    //console.log(data);
                    resolve(data)
                })
            })
        },
        borrarFila(item){
            var idx = this.order_detail.indexOf(item);
            if(idx > -1){
                this.order_detail.splice(idx, 1);
            }
            this.calcularTotal();
        },
        calcularTotalFila(item) {
            var total = parseFloat(item.precio_unitario) * parseFloat(item.cantidad);
            if (!isNaN(total)) {
                item.precio_total = total.toFixed(2);
            }else{
                item.precio_total = '';
            }
            this.calcularTotal();
        },
        calcularTotal() {
            this.op_inafectas  = 0;
            this.op_exoneradas = 0;
            this.op_gravadas   = 0;
            this.icbper        = 0;
            this.comp_igv      = 0;
            this.comp_total    = 0;

            var sum_gravadas  = 0;
            var op_inafectas  = 0;
            var op_exoneradas = 0;
            var total_icbper  = 0;

            this.order_detail.forEach((val)  => {
                var tipo_impuesto = val.tipo_impuesto;
                var u_icbper      = val.icbper;
                
                var total_fila = parseFloat(val.precio_total);

                if (!isNaN(total_fila)) {
                    switch (tipo_impuesto) {
                        case 1:
                            sum_gravadas = sum_gravadas + total_fila;
                            break;

                        case 2:
                            op_inafectas = op_inafectas + total_fila;
                            break;

                        case 3:
                            op_exoneradas = op_exoneradas + total_fila;
                            break;

                        case 4:
                            sum_gravadas = sum_gravadas + total_fila;
                            total_icbper = total_icbper + (u_icbper * val.cantidad);
                            break;
                    }
                }
            });

            this.op_inafectas  = parseFloat(op_inafectas).toFixed(2);
            this.op_exoneradas = parseFloat(op_exoneradas).toFixed(2);
            this.icbper        = parseFloat(total_icbper).toFixed(2);

            this.op_gravadas = parseFloat(sum_gravadas/1.18).toFixed(2);
            this.ord_igv    = parseFloat(sum_gravadas - this.op_gravadas).toFixed(2);
            this.ord_total  = sum_gravadas + 
                                parseFloat(op_inafectas) +
                                parseFloat(op_exoneradas) +
                                parseFloat(total_icbper);
            this.ord_total = parseFloat(this.ord_total).toFixed(2);
        },
        //--- End ---

        //--- AutoProveedor ---
        handleSubmitProveedor(result) {
            this.addForm.id_proveedor     = result.id_proveedor;
            this.addForm.nombre_proveedor = result.nombre;
            this.addForm.email            = result.email;
            this.$refs.busProv.setValue('');
        },
        getResultProveedor(result) {
            return result.nombre+' | '+result.nro_doc;
        },
        searchProveedor(input) {
            let token = document.head.querySelector('meta[name="csrf-token"]');
            const requestOptions = {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token.content },
                body: JSON.stringify({ keywords: input })
            };

            return new Promise(resolve => {
                if (input.length < 3) return;

                fetch('/api/buscarProveedores', requestOptions).then(response => response.json())
                .then(data => {
                    resolve(data)
                })
            })
        },
        //--- End ---


        tiposCambioCombo(){
            axios.get('/api/tiposCambioCombo').then(response => {
                this.tipos_cambio = response.data;

            }).catch(e => {
                console.error(e);
            });
        },
        monedasCombo(){
            axios.get('/api/monedasCombo').then((response) => {
                this.combo_monedas = response.data;

            }).catch(e => {
                console.error(e);
            });
        },
        mediosPagoCombo(){
            axios.get('/api/mediosPagoCombo').then(response => {
                this.medios_pago = response.data;
            });
        },
        cambioItemText(item){
            return `${item.tipo_cambio} - ${item.cambio}`
        },
        
        //--- Date Formatting ---
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        //--- End ---
    },
    computed: {
        //--- Date Formatting ---
        formatoFechaEmision: {
            get: function () {
                return this.formatDate(this.addForm.fecha_emision)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        formatoFechaVenc: {
            get: function () {
                return this.formatDate(this.addForm.fecha_vencimiento)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        //--- End ---
    },
}
</script>