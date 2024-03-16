<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F"><b>Nueva Compra</b></h2>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-form @submit.prevent="createCompra">
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
                        <v-text-field v-model="addForm.nro_guia_remision" label="Guía de Remisión (Opcional)"></v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <v-select label="Tipo de Comprobante *" v-model="addForm.id_tipo_comprobante"
                            :items="tipos_comprobante" 
                            item-text="name" 
                            item-value="id"
                            :rules="requiredRules"
                        ></v-select>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field v-model="addForm.serie_factura" label="Serie *" :rules="requiredRules"></v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field v-model="addForm.nro_factura" label="Nro. Factura *" type="number" class="txt-number" :rules="requiredRules"></v-text-field>
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
                            v-model="menuFechVenc"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto" >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field label="Fecha de Venc. *" prepend-icon="mdi-calendar"
                                    v-model="formatoFechaVenc" 
                                    :rules="requiredRules"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker no-title v-model="addForm.fecha_vencimiento" @input="menuFechVenc = false" locale="es-ES"></v-date-picker>
                        </v-menu>
                    </v-col>
                    <v-col cols="3">
                        <v-select :items="origenes_dinero" label="Origen Dinero *" v-model="addForm.origen_dinero"
                            item-text="name"
                            item-value="id"
                            :rules="requiredRules"
                        ></v-select>
                    </v-col>
                    <v-col cols="3">
                        <v-select label="Moneda *" v-model="addForm.id_moneda"
                            :items="combo_monedas" 
                            item-text="moneda"
                            item-value="id_moneda"
                            :rules="requiredRules"
                        ></v-select>
                    </v-col>
                    <v-col cols="3">
                        <v-select label="Medio Pago *" v-model="addForm.id_medio_pago"
                            :items="medios_pago"
                            item-text="medio_pago"
                            item-value="id_medio_pago"
                            :rules="requiredRules"
                        ></v-select>
                    </v-col>
                    <v-col cols="3">
                        <v-select label="Tipo Cambio *" v-model="addForm.id_tipo_cambio"
                            :items="tipos_cambio"
                            :item-text="cambioItemText"
                            item-value="id_tipo_cambio"
                            :rules="requiredRules">
                        </v-select>
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
                            <th class="text-left">Lote</th>
                            <th class="text-left">Fecha V.</th>
                            <th class="text-left">Lista de Precio</th>
                            <th class="text-left">P.U (S/.)</th>
                            <th class="text-left">CNT</th>
                            <th class="text-left">P.T (S/.)</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="purchase_detail.length == 0">
                            <td colspan="9" class="text-center">Aún no se han agregado productos o servicios a esta compra.</td>
                        </tr>
                        <tr v-for="(item, k) in purchase_detail" :key="k">
                            <td>{{item.nombre_producto}}</td>
                            <td>{{item.und_simbolo}}</td>
                            <td>
                                <input placeholder="Lote *" v-model="item.lote" autocomplete="off" type="text" class="form-control">
                            </td>
                            <td>
                                <input v-model="item.lote_fecha_exp" type="date" class="form-control" autocomplete="off">
                            </td>
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

                            <td class="text-right">
                                <v-btn small color="error" icon @click="borrarFila(item)"><v-icon small> mdi-delete</v-icon></v-btn>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr v-if="op_inafectas > 0">
                            <td colspan="9" style="text-align:right;"><b>Op. Inafectas: </b>{{op_inafectas}}</td>
                        </tr>
                        <tr v-if="op_exoneradas > 0">
                            <td colspan="9" style="text-align:right;"><b>Op. Exoneradas: </b>{{op_exoneradas}}</td>
                        </tr>
                        <tr v-if="op_gravadas > 0">
                            <td colspan="9" style="text-align:right;"><b>Op. Gravadas: </b>{{op_gravadas}}</td>
                        </tr>
                        <tr v-if="icbper > 0">
                            <td colspan="9" style="text-align:right;"><b>ICBPER: </b>{{icbper}}</td>
                        </tr>
                        <tr>
                            <td colspan="9" style="text-align:right;"><b>IGV (18%): </b>{{comp_igv}}</td>
                        </tr>
                        <tr>
                            <td colspan="9" style="text-align:right;"><b>Total: </b>{{comp_total}}</td>
                        </tr>
                    </tfoot>
                </v-simple-table>
                <br>
                <br>
                <v-row>
                    <v-col cols="2">
                        <v-checkbox v-model="addForm.deuda_generada" label="Generar Deuda" color="primary"></v-checkbox>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field :disabled="!addForm.deuda_generada" v-model="addForm.deuda_adelanto" label="Adelanto de Deuda"></v-text-field>
                    </v-col>
                </v-row>
                <v-card-actions class="justify-end">
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
            text: 'Compras',
            disabled: false,
            to: '/compras'
        }, {
            text: 'Nueva Compra'
        }],
        
        //--- Formulario ---
        addForm: new Form({
            id_proveedor: null,
            id_moneda: null,
            id_medio_pago: null,
            id_tipo_cambio: null,
            origen_dinero: 1,
            id_tipo_comprobante: 1,
            nombre_proveedor: null,
            email: null,
            fecha_emision: null,
            fecha_vencimiento: null,

            compra_detalle: null,
        }),
        menuFechEmi: false,
        menuFechVenc: false,
        combo_monedas: [],
        medios_pago: [],
        tipos_cambio: [],
        origenes_dinero: [
            { id:1, name:'Caja Chica' },
            { id:2, name:'Cuenta Bancaria' },
            { id:3, name:'Otra Fuente' },
        ],
        tipos_comprobante: [
            { id:1, name:'Factura' },
            { id:2, name:'Boleta' },
            // { id:'3', name:'Nota de Compra' },
        ],
        //--- End ---
        //--- Autocomplete ---
        purchase_detail: [],

        op_inafectas: 0,
        op_exoneradas:0,
        op_gravadas: 0,
        icbper: 0,
        comp_igv: '0,00',
        comp_total: '0,00',
        //--- End ---

        startTime: null,
        endTime: null,

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),
    mounted(){
        this.addForm.fecha_emision     = this.todaysDateDefault();
        this.addForm.fecha_vencimiento = this.todaysDateDefault();

        this.tiposCambioCombo();
        this.monedasCombo();
        this.mediosPagoCombo();

        this.startTime = new Date();
    },
    methods:{
        changePriceList(item){
            item.precio_unitario = item.lista_detalle.precio_compra;
            this.calcularTotalFila(item);
        },
        //--- Funciones Compra ---
        createCompra(){
            this.preloader = true;

            //Se calcula el tiempo de permanencia
            this.endTime = new Date();
            let timeDiff = this.endTime - this.startTime;
            timeDiff /= 1000;
            
            this.addForm.time_elapsed = Math.round(timeDiff);
            this.addForm.compra_detalle = this.purchase_detail;
            this.addForm.comp_total = this.comp_total;
            this.addForm.post('/api/compra').then((result)=>{
                Toast.fire({
                    icon: 'success',
                    title: 'Compra creada correctamente!'
                });
                this.$router.push('/compras_visualize/'+result.data.id_compra);
                
            }).catch(e => {
                var error_messages = e.response.data.errors;
                for (let i in error_messages) {
                    Toast.fire({
                        icon: 'error',
                        title: error_messages[i][0],
                    });
                }

            }).finally(() =>{this.preloader = false});
        },
        //--- End ---
        //--- Funciones Compra Detalle ---
        handleSubmitProduct(item, qty=1, unit_price=null) {
            axios.get('/api/getListPricebyProduct/'+item.id_producto).then((response) => {
                if(unit_price == null){
                    unit_price = response.data[0] ? response.data[0].precio_compra : null;
                }
                this.purchase_detail.push({
                    id_producto:       item.id_producto,
                    nombre_producto:   item.nombreProducto,
                    und_simbolo:       item.unidad_medida.simbolo,

                    lote:              '',
                    lote_fecha_exp:    '',
                    
                    list_precios:  response.data,
                    lista_detalle: response.data[0] ? response.data[0] : null,

                    cantidad:        qty,
                    precio_unitario: unit_price,
                    precio_total:    unit_price ? unit_price * qty : null,

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
            var idx = this.purchase_detail.indexOf(item);
            if(idx > -1){
                this.purchase_detail.splice(idx, 1);
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

            this.purchase_detail.forEach((val)  => {
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
            this.comp_igv    = parseFloat(sum_gravadas - this.op_gravadas).toFixed(2);
            this.comp_total  = sum_gravadas + 
                                parseFloat(op_inafectas) +
                                parseFloat(op_exoneradas) +
                                parseFloat(total_icbper);
            this.comp_total = parseFloat(this.comp_total).toFixed(2);
        },
        //--- End ---
        //--- Autcomplete Proveedor ---
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



        //--- Carga de Datos ---
        tiposCambioCombo(){
            axios.get('/api/tiposCambioCombo').then(response => {
                this.tipos_cambio = response.data;
                this.addForm.id_tipo_cambio = 1;
            });
        },
        monedasCombo(){
            axios.get('/api/monedasCombo').then(response => {
                this.combo_monedas = response.data;
                this.addForm.id_moneda = 1;
            });
        },
        mediosPagoCombo(){
            axios.get('/api/mediosPagoCombo').then(response => {
                this.medios_pago = response.data;
                this.addForm.id_medio_pago = 1;
            });
        },
        cambioItemText(item){
            return `${item.tipo_cambio} - ${item.cambio}`
        },
        //--- End ---
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