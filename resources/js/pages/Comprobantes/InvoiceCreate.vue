<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Nuevo Comprobante</h2> 
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2">
                <v-col cols="5">
                    <v-autocomplete
                        label="Buscar cliente por nombre/nro. documento"
                        v-model="addForm.cliente"
                        :items="itemsClient"
                        item-value="id_cliente"
                        :item-text="cliItemText"

                        :search-input.sync="searchClient"
                        v-on:change="handleSubmitClient()"
                        :loading="isLoadingClient"
                        return-object
                        hide-no-data>
                    </v-autocomplete>
                </v-col>
                <v-col v-if="$can('clients_create', 'all')" class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="dialogCliente = true">Agregar Cliente<v-icon>mdi-plus</v-icon></v-btn>
                </v-col>
            </v-row>
            <v-row dense class="pa-2">
                <v-col cols="2">
                    <v-select :items="tipos_comp" label="Tipo de Comprobante *"
                        v-model="addForm.id_tipo_comprobante"
                        item-text="tipo_comprobante" 
                        item-value="id_tipo_comprobante"
                        :rules="requiredRules"
                        v-on:change="changeTipoComprobante"
                    ></v-select>
                </v-col>
                <v-col cols="5">
                    <v-text-field v-model="addForm.nombre_cliente" label="Nombre Cliente" disabled></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field v-model="addForm.nro_documento" label="Nro. Documento" disabled></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field v-model="addForm.fecha_emision" label="Fecha de Emisión *" :rules="requiredRules" disabled></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-select :items="series_combo" label="Serie" v-model="addForm.id_serie"
                        item-text="serie" 
                        item-value="id_serie" 
                        :rules="requiredRules"
                    ></v-select>
                </v-col>
                <v-col cols="5">
                    <v-select :items="cli_direcciones" label="Dirección *"
                        v-model="addForm.id_direccion"
                        item-text="direccion" 
                        item-value="id_direccion"
                        :rules="requiredRules"
                    ></v-select>
                </v-col>
                <v-col cols="3">
                    <v-select :items="format_print"  label="Formato *" v-model="addForm.formato_impresion" 
                        item-text="name" 
                        item-value="id" 
                        :rules="requiredRules"
                    ></v-select>
                </v-col>
                <v-col cols="2">
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
                <v-col cols="2">
                    <v-select :items="medios_pago" label="Medio Pago *" v-model="addForm.id_medio_pago" 
                        item-text="medio_pago" 
                        item-value="id_medio_pago"
                        :rules="requiredRules"
                    ></v-select>
                </v-col>
                <v-col cols="2">
                    <v-select :items="combo_monedas" label="Moneda *" v-model="addForm.id_moneda" 
                        item-text="moneda" 
                        item-value="id_moneda"
                        :rules="requiredRules"
                    ></v-select>
                </v-col>
                <v-col cols="3">
                    <v-select :items="tipos_cambio" label="Tipo Cambio *" v-model="addForm.id_tipo_cambio"
                        :item-text="cambioItemText"
                        item-value="id_tipo_cambio"
                        :rules="requiredRules">
                    </v-select>
                </v-col>
                <v-col cols="2">
                    <v-text-field label="Estado" value="Pendiente" readonly></v-text-field>
                </v-col>
            </v-row>
        </v-card>
        <v-card class="mb-4" light style="padding: 15px">
            <v-card-title>Detalle del Comprobante</v-card-title>
            <v-card-text>
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
                            <th class="text-left">Lote</th>
                            <th class="text-left">Lista de Precio</th>
                            <th style="width:10%;">P.U (S/.)</th>
                            <th style="width:10%;">CNT</th>
                            <th style="width:10%;">P.T (S/.)</th>
                            <th style="width:3%;"  class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="invoice_detail.length == 0">
                            <td colspan="9" class="text-center">Aún no se han agregado productos o servicios a este comprobante.</td>
                        </tr>
                        <tr v-for="(item, k) in invoice_detail" :key="k">
                            <td>{{item.nombre_producto}}</td>
                            <td>{{item.und_simbolo}}</td>
                            <td>{{item.laboratorio}}</td>
                            <td>
                                <v-select :items="item.lotes" label="Lote"
                                    v-model="item.lote"
                                    v-on:change="changeLote(item)"
                                    :rules="requiredRules"
                                    no-data-text="No hay datos">
                                    <template slot="item" slot-scope="data">
                                        {{ data.item.lote }} - {{ data.item.cantidad }}
                                    </template>
                                    <template slot="selection" slot-scope="data">
                                        {{ data.item.lote }} - {{ data.item.cantidad }}
                                    </template>
                                </v-select>
                            </td>
                            <td>
                                <v-select :items="item.list_precios" label="Lista de precios"
                                    v-model="item.lista_detalle"
                                    v-on:change="changePriceList(item)"
                                    no-data-text="No hay datos">
                                    <template slot="item" slot-scope="data">
                                        {{ data.item.listaprecio.nombre }} ({{data.item.unidades}}) - {{ data.item.precio_venta }}
                                    </template>
                                    <template slot="selection" slot-scope="data">
                                        {{ data.item.listaprecio.nombre }} ({{data.item.unidades}}) - {{ data.item.precio_venta }}
                                    </template>
                                </v-select>
                            </td>
                            <td>
                                <v-text-field v-model="item.precio_unitario"
                                    @change="calcularTotalFila(item)"
                                    type="number"
                                    min="0"
                                    placeholder="0,00"
                                    class="txt-number"
                                    autocomplete="off"
                                    :rules="requiredRules"
                                ></v-text-field>
                            </td>
                            <td>
                                <v-text-field v-model="item.cantidad"
                                    :label="'max: '+ item.cantidad_max"
                                    @change="calcularTotalFila(item)" 
                                    type="number"
                                    min="0"
                                    placeholder="0,00"
                                    :max='item.cantidad_max'
                                    :disabled="!item.lote"
                                    autocomplete="off"
                                    :rules="requiredRules"
                                ></v-text-field>
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
                            <td colspan="9" style="text-align:right;"><b>IGV (18%): </b>{{inv_igv}}</td>
                        </tr>
                        <tr>
                            <td colspan="9" style="text-align:right;"><b>Total: </b>{{inv_total}}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-center">
                                <v-textarea v-model="addForm.comentario"  rows="2" label="Comentarios/Observaciones"></v-textarea>
                            </td>
                        </tr>
                    </tfoot>
                </v-simple-table>
                <br><br>
                <v-row v-if="addForm.cliente.id_cliente != 1">
                    <v-col cols="2">
                        <v-checkbox v-model="addForm.deuda_generada" label="Generar Deuda" color="primary"></v-checkbox>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field :disabled="!addForm.deuda_generada" v-model="addForm.deuda_adelanto" label="Adelanto de Deuda"></v-text-field>
                    </v-col>
                </v-row>
                <v-card-actions class="justify-end">
                    <v-btn @click="createComprobante" color="primary">Guardar</v-btn>
                </v-card-actions>
            </v-card-text>
        </v-card>

        <!-- Dialog Crear Cliente -->
        <v-dialog v-model="dialogCliente" max-width="50%">
            <v-card>
                <v-card-title>
                    <span class="headline">Crear Cliente</span>
                </v-card-title>
                <v-card-text>
                    <v-tabs v-model="tab_cliente" :show-arrows="false" background-color="transparent">
                        <v-tab to="#tabs-information">Información</v-tab>
                        <v-tab to="#tabs-contact">Contacto</v-tab>
                    </v-tabs>
                    <v-form ref="clientForm" v-model="clientFormValid" lazy-validation>
                        <v-tabs-items v-model="tab_cliente">
                            <v-tab-item value="tabs-information">
                                <v-row>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-select label="Tipo Documento *" v-model="clientForm.id_tipo_doc"
                                            :items="tipos_doc"
                                            item-text="tipo_documento"
                                            item-value="id_tipo_doc"
                                            :rules="requiredRules"
                                            @change="TipoDocChanged(clientForm.id_tipo_doc)"
                                        ></v-select>
                                        <v-text-field v-model="clientForm.nombre" label="Nombre / Razón Social *" :rules="requiredRules"></v-text-field>

                                        <v-autocomplete label="Departamento *" v-model="clientForm.id_departamento"
                                            :items="departamentos"
                                            item-text="name"
                                            item-value="id"
                                            @change="getProvinces(clientForm.id_departamento)"
                                            :rules="requiredRules"
                                            no-data-text="No se encontraron registros"
                                        ></v-autocomplete>
                                        <v-autocomplete label="Distrito *" v-model="clientForm.id_distrito"
                                            :items="distritos"
                                            item-text="name"
                                            item-value="id"
                                            :rules="requiredRules"
                                            no-data-text="No se encontraron registros"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field :label="nrodoc_label" v-model="clientForm.nro_doc"
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

                                        <v-select label="Tipo de Cliente *" v-model="clientForm.tipo_cliente"
                                            :items="tipo_cliente" 
                                            item-text="text"
                                            item-value="id"
                                            :rules="requiredRules"
                                        ></v-select>

                                        <v-autocomplete label="Provincia *" v-model="clientForm.id_provincia"
                                            :items="provincias"
                                            item-text="name"
                                            item-value="id"
                                            @change="getDistricts(clientForm.id_provincia, clientForm.id_departamento,)"
                                            :rules="requiredRules"
                                            no-data-text="No se encontraron registros"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col class="text-right">
                                        <v-btn small color="indigo" dark @click="addDireccion">Agregar Dirección<v-icon>mdi-plus</v-icon></v-btn>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-simple-table>
                                            <thead>
                                                <tr>
                                                    <th class="text-left"></th>
                                                    <th class="text-left" style="width:3%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(dir, i) in create_direcciones" :key=i>
                                                    <td>
                                                        <div>
                                                            <v-text-field v-model="dir.direccion" :label="dir.label"></v-text-field>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <v-btn small color="error" icon @click="delDireccion(dir)"><v-icon small> mdi-delete</v-icon></v-btn>
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
                                        <v-text-field v-model="clientForm.email" label="Correo Electrónico" :rules="emailRules"></v-text-field>

                                        <v-text-field v-model="clientForm.nombre_contacto" label="Nombre Contacto"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field v-model="clientForm.telefono" label="Teléfono"></v-text-field>

                                        <v-text-field v-model="clientForm.telefono_contacto" label="Teléfono Contacto"></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-tab-item>
                        </v-tabs-items>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="dialogCliente = false">Cancelar</v-btn>
                    <v-btn color="primary" @click="saveCliente" :disabled="!clientFormValid">Guardar</v-btn>
                </v-card-actions>
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
            text: 'Comprobantes',
            disabled: false,
            to: '/comprobantes'
        }, {
            text: 'Nuevo Comprobante'
        }],
        
        //--- Formulario Comprobante ---
        menuFechVenc: false,
        tipos_doc:[],
        tipos_comp:[],
        series_combo:[],
        medios_pago:[],
        combo_monedas:[],
        tipos_cambio: [],

        addForm: new Form({
            id_tipo_comprobante: 2,
            formato_impresion: 1,
            cliente:{},
            id_direccion: null,
            fecha_emision: null,
            fecha_vencimiento: null,

            inv_detalle: [],
        }),
        cli_direcciones:[],
        format_print: [
            { id:1, name:'Ticket' },
            { id:2, name:'A4' },
            { id:3, name:'A5' },
        ],
        //--- End ---
        
        // Totales
        op_inafectas: 0,
        op_exoneradas:0,
        op_gravadas: 0,
        icbper: 0,
        inv_igv: '0,00',
        inv_total: '0,00',
        // Fin

        //--- Autocomplete ---
        invoice_detail: [],

        itemsClient: [],
        isLoadingClient: false,
        searchClient: null,
        //--- End ---

        //--- Formulario Cliente ---
        dialogCliente: false,
        clientFormValid: true,
        tab_cliente: 0,
        cli_tipos_doc:[],
        clientForm:  new Form({
            id_tipo_doc: 2,
            tipo_cliente: 1,
            nro_doc: "",
            cliente: "",
            nombre_cliente: "",
            nro_documento: "",

            id_departamento: null,
            id_provincia: null,
            id_distrito: null,
            cli_direcciones: [],
        }),
        create_direcciones: [{id_direccion: "", label: "Dirección 1", direccion: "1-"}],
        tipo_cliente: [{ id: 1, text: "Interno" }, { id: 2, text: "Distribuidor" },],

        //Tipo Doc
        nrodoc_label: "Nro. DNI *",
        tipos_doc: [],
        requiredDocRules: [
            v => !!v || 'Campo obligatorio',
            (v) => (v && v.length == 8) || "DNI debe tener 8 cifras",
        ],

        //Ubigeo
        departamentos: [],
        provincias: [],
        distritos: [],
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            v => !!v || 'Campo obligatorio',
            v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
        ],
    }),

    mounted(){
        this.cargaClientesVarios();

        this.tiposDocCombo();
        this.getDepartments();
        this.seriesComprobanteCombo();

        this.tiposComprobantesCombo();
        this.monedasCombo();
        this.mediosPagoCombo();
        this.tiposCambioCombo();

        this.addForm.fecha_emision     = this.todaysDate();
        this.addForm.fecha_vencimiento = this.todaysDateDefault();
    },

    methods: {
        //--- Carga de Datos ---
        tiposCambioCombo(){
            axios.get('/api/tiposCambioCombo').then(response => {
                this.tipos_cambio = response.data;
                this.addForm.id_tipo_cambio = 1;
            }).catch(e => {
                console.error(e);
            });
        },
        tiposDocCombo(){
            axios.get('api/tiposDocCombo').then((response) => {
                this.combo_TiposDoc = response.data;
                this.cli_tipos_doc = response.data;

            }).catch(e => {
                console.error(e);
            }) 
        },
        tiposComprobantesCombo(){
            axios.get('api/tiposComprobantesCombo').then((response) => {
                this.tipos_comp = response.data;

            }).catch(e => {
                console.error(e);
            }) 
        },
        seriesComprobanteCombo(){
            axios.get('api/seriesComprobanteCombo/'+this.addForm.id_tipo_comprobante).then((response) => {
                this.series_combo = response.data;
                this.addForm.id_serie = response.data[0].id_serie;

            }).catch(e => {
                console.error(e);
            }) 
        },
        mediosPagoCombo(){
            axios.get('/api/mediosPagoCombo').then((response) => {
                this.medios_pago = response.data;
                this.addForm.id_medio_pago = 1;

            }).catch(e => {
                console.error(e);
            });
        },
        monedasCombo(){
            axios.get('/api/monedasCombo').then((response) => {
                this.combo_monedas = response.data;
                this.addForm.id_moneda = 1;

            }).catch(e => {
                console.error(e);
            });
        },
        getDirecciones(item){
            this.cli_direcciones = [];
            axios.get('/api/getClienteDirecciones/'+item.id_cliente).then((response) => {
                response.data.direcciones.forEach((val, index)  => {
                    if(index == 0){
                        this.addForm.id_direccion = val.id_direccion;
                    }
                    this.cli_direcciones.push({
                        id_direccion: val.id_direccion,
                        direccion: val.direccion,
                    })
                });
            });
        },
        cambioItemText(item){
            return `${item.tipo_cambio} - ${item.cambio}`
        },
        //--- End ---


        createComprobante(){
            this.preloader = true;
            this.addForm.inv_detalle = this.invoice_detail;
            this.addForm.inv_total = this.inv_total;
            this.addForm.post('/api/comprobante').then((result)=>{
                this.$swal.fire({
                    icon: 'success',
                    title: 'Comprobante creado correctamente!',
                    confirmButtonText: 'Aceptar'
                });
                this.$router.push('/comprobantes_visualize/'+result.data.id_comprobante);

            }).catch(e => {
                var error_messages = e.response.data.errors;
                for (let i in error_messages) {
                    Toast.fire({
                        icon: 'error',
                        title: error_messages[i][0],
                    });
                    if(error_messages[i][1] && error_messages[i][1] === "sunat_error"){
                        this.$router.push('/comprobantes');
                    }
                }
            }).finally(() =>{this.preloader = false});
        },
        
        //--- Eventos ---
        changeTipoComprobante(){
            this.seriesComprobanteCombo();

            this.addForm.cliente = {};
            this.addForm.nombre_cliente = null;
            this.addForm.nro_documento  = null;
            this.cli_direcciones = [];

            if(this.addForm.id_tipo_comprobante == 2){
                this.cargaClientesVarios();
            }
        },
        changePriceList(item){
            item.precio_unitario = item.lista_detalle.precio_venta;
            if(item.lista_detalle){
                item.cantidad_max = Math.floor(item.lote.cantidad / item.lista_detalle.unidades);
            }else{
                item.cantidad_max = item.lote.cantidad;
            }
            this.calcularTotalFila(item);
        },
        changeLote(item){
            item.cantidad = 1;
            if(item.lista_detalle){
                item.cantidad_max = Math.floor(item.lote.cantidad / item.lista_detalle.unidades);
            }else{
                item.cantidad_max = item.lote.cantidad;
            }
        },
        //--- End ---
        

        //--- Filas Productos Functions ---
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
            this.inv_igv       = 0;
            this.inv_total     = 0;

            var sum_gravadas  = 0;
            var op_inafectas  = 0;
            var op_exoneradas = 0;
            var total_icbper  = 0;

            this.invoice_detail.forEach((val)  => {
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
                            // console.log(total_icbper+" + "+ "( "+u_icbper+" + "+ val.cantidad +" )");
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
            this.inv_igv    = parseFloat(sum_gravadas - this.op_gravadas).toFixed(2);
            this.inv_total  = sum_gravadas + 
                                parseFloat(op_inafectas) +
                                parseFloat(op_exoneradas) +
                                parseFloat(total_icbper);
            this.inv_total = parseFloat(this.inv_total).toFixed(2);
        },
        borrarFila(item){
            var idx = this.invoice_detail.indexOf(item);
            if(idx > -1){
                this.invoice_detail.splice(idx, 1);
            }
            this.calcularTotal();
        },
        //--- End ---
        
        //--- Productos Autocomplete ---
        handleSubmitProduct(result) {
            var r_listapre = [];
            axios.get('/api/getListPricebyProduct/'+result.id_producto).then((response) => {
                response.data.forEach((val)  => {
                    r_listapre.push(val);
                });
                
                //--- Descartar lotes sin stock ---
                if (result.lotes.length <= 0){
                    Swal.fire({
                        icon: 'error',
                        title: 'El producto "'+result.nombreProducto+'" no cuenta con stock en este momento.'
                    });
                    return;
                }
                //--- End --- 

                //--- Asignar la Cantidad Maxima ---
                var cantidad_max;
                if(r_listapre[0]){
                    // console.log(result.lotes[0].cantidad +'/'+ r_listapre[0].unidades);
                    cantidad_max = Math.floor(result.lotes[0].cantidad / r_listapre[0].unidades);
                }else{
                    cantidad_max = result.lotes[0].cantidad;
                }
                //--- End ---
                    
                this.invoice_detail.push({
                    id_producto:     result.id_producto,
                    nombre_producto: result.nombreProducto,
                    und_simbolo:     result.unidad_medida.simbolo,
                    laboratorio:     result.laboratorio.nombre,

                    icbper:         result.tipo_producto.icbper,
                    tipo_impuesto:  result.tipo_producto.impuesto,

                    lotes:          result.lotes,
                    lote:           result.lotes[0],
                    list_precios:   r_listapre,
                    lista_detalle:  r_listapre[0] ? r_listapre[0] : null,

                    cantidad: 1,
                    cantidad_max:   cantidad_max,
                    // cantidad_max:   result.lotes[0].cantidad,

                    precio_unitario: r_listapre[0].precio_venta,
                    precio_total:    r_listapre[0].precio_venta,
                });

                this.calcularTotal();
            });
            this.$refs.busProd.setValue('');
        },
        getResultProduct(result) {
            var ubicacion = result.ubicacion != null? result.ubicacion : '';
            return result.laboratorio.nombre + ' - ' + result.nombreProducto +' | '+ ubicacion +' | '+ result.stock;
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

                fetch('api/buscarProductos', requestOptions)
                .then(response => response.json())
                .then(data => {
                    resolve(data)
                })
            })
        },
        //--- End ---


        //--- Cliente Autocomplete ---
        TipoDocChanged(tipo_doc, nro_doc=null){
            this.clientForm.nro_doc = nro_doc;

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
        cargaClientesVarios(){
            axios.get('/api/getClientePredeterminado').then((response) => {
                this.addForm.cliente = response.data;

                this.addForm.nombre_cliente = this.addForm.cliente.nombre;
                this.addForm.nro_documento  = this.addForm.cliente.nro_doc;
                this.getDirecciones(this.addForm.cliente);
            });
        },
        handleSubmitClient(){
            this.addForm.nombre_cliente = this.addForm.cliente.nombre;
            this.addForm.nro_documento  = this.addForm.cliente.nro_doc;
            this.getDirecciones(this.addForm.cliente);
        },
        cliItemText(item){
            return `${item.nombre} | ${item.nro_doc}`
        },
        //--- End ---


        //--- Dialog Crear Cliente ---
        buscarDoc(){
            this.preloader = true;
            if(this.clientForm.id_tipo_doc == 2){
                axios.post("/api/searchDni", {dni: this.clientForm.nro_doc,}).then((response) => {
                    console.log(response.data.data);
                    if(response.data.success == true){
                        this.clientForm.nombre = response.data.data.nombres +" "+ response.data.data.apellido_paterno +" "+ response.data.data.apellido_materno;
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
                axios.post("api/searchRuc", {ruc: this.clientForm.nro_doc,}).then((response) => {
                    if (response.data.success == true) {
                        this.clientForm.nombre = response.data.data.nombre_o_razon_social;
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
        addDireccion(){
            var idx = parseInt(this.create_direcciones.length) + 1;
            this.create_direcciones.push({id_direccion: "", label: "Dirección " + idx, direccion: ""});
        },
        delDireccion(item){
            var idx = this.create_direcciones.indexOf(item);
            if(idx > -1){
                this.create_direcciones.splice(idx, 1);
            }
        },
        saveCliente(){
            this.clientForm.cli_direcciones = this.create_direcciones;

            axios.post("/api/cliente", this.clientForm).then((response) => {
                this.dialogCliente = false;
                var cliente_aux = response.data.cliente;
                if(cliente_aux.id_tipo_doc === 1){
                    //FACTURA
                    this.addForm.id_tipo_comprobante = 1;
                    this.changeTipoComprobante();

                }else{
                    //BOLETA
                    this.addForm.id_tipo_comprobante = 2;
                    this.changeTipoComprobante();

                }
                
                this.addForm.cliente = cliente_aux;
                this.addForm.nombre_cliente = this.addForm.cliente.nombre;
                this.addForm.nro_documento  = this.addForm.cliente.nro_doc;
                this.getDirecciones(this.addForm.cliente);

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
            });
        },

        // Ubigeo
        getDepartments(){
            axios.get('/api/getDepartments').then(response => {
                this.departamentos = response.data;
            });
        },
        getProvinces(id){
            this.distritos = [];
            axios.get('/api/getProvinces/'+id).then(response => {
                this.provincias = response.data;

            });
        },
        getDistricts(province_id, department_id){
            axios.post('/api/getDistricts', {province_id: province_id, department_id: department_id}).then(response => {
                this.distritos = response.data;
            });
        },
        // Fin 
        //--- End ---

        
        //--- Date Formatting ---
        todaysDate(){
            var date = new Date;
            return ((date.getDate() < 10)?"0":"") + date.getDate() +"-"+(((date.getMonth()+1) < 10)?"0":"") + (date.getMonth()+1) +"-"+ date.getFullYear();
        },
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
    watch: {
        searchClient (val) {
            if(val != null){
                if (val.length < 3) return;

                this.isLoadingClient = true
                let token = document.head.querySelector('meta[name="csrf-token"]');
                const requestOptions = {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token.content },
                    body: JSON.stringify({ keywords: val })
                };

                fetch('api/buscarClientesComprobante/'+this.addForm.id_tipo_comprobante, requestOptions)
                .then(res => res.json())
                .then(data => {
                    this.itemsClient = data;

                }).catch(e => {
                    console.error(e);

                }).finally(() => (this.isLoadingClient = false))
            }
        },
        dialogCliente(){
            if(!this.dialogCliente){
                this.$refs.clientForm.reset();

            }else{
                this.create_direcciones = [{id_direccion: "", label: "Dirección 1", direccion: ""}];
                this.clientForm.id_tipo_doc  = 2;
                this.clientForm.tipo_cliente = 1;
                this.TipoDocChanged(2);
            }
        },
    },
    computed: {
        //--- Date Formatting ---
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