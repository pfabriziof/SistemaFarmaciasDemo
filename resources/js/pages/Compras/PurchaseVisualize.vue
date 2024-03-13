<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Visualizar Compra</h2>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card v-if="compra" class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2">
                <v-col cols="12">
                    <v-alert dense type="info" v-if="compra.id_estado==3">La compra se encuentra en <b>espera</b> y requiere de <b>aprobación</b>.</v-alert>
                    <v-alert dense type="warning" v-if="compra.id_estado==2">La compra fue <b>anulada</b> y no puede realizar ninguna acción.</v-alert>
                </v-col>
            </v-row>
            <v-row dense class="pa-2">
                <v-col class="text-right">
                    <v-btn @click="generarPDF" color="red" v-if="compra.id_estado!=2" dark>
                        <v-icon small>mdi-file-pdf</v-icon>PDF
                    </v-btn>
                    <v-btn @click="dialogEmail = true" color="teal" v-if="compra.id_estado==1" dark>
                        <v-icon small>mdi-email-send</v-icon>Email
                    </v-btn>
                </v-col>
            </v-row>
            <v-row dense class="pa-2">
                <v-col cols="6">
                    <v-text-field v-model="compra.nombreProveedor" label="Nombre Proveedor" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field v-model="compra.email" label="Correo Electrónico" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field v-model="compra.nro_guia_remision" label="Guía de Remisión" readonly></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field v-model="compra.tipo_comprobante.tipo_comprobante" label="Tipo de Comprobante" readonly></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field v-model="compra.serie_factura" label="Serie" readonly></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field v-model="compra.nro_factura" label="Nro. Factura" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field v-model="compra.fecha_emision" label="Fecha de Emisión" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field v-model="compra.fecha_vencimiento" label="Fecha de Venc." readonly></v-text-field>
                </v-col>
                 <v-col cols="3">
                    <v-select v-model="compra.origen_dinero" label="Origen Dinero *"
                        :items="origenes_dinero"
                        item-text="name"
                        item-value="id"
                        readonly
                    ></v-select>
                 </v-col>
                 <v-col cols="3">
                    <v-text-field label="Moneda" v-model="compra.moneda.moneda" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="Medio Pago" v-model="compra.medio_pago.medio_pago" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="Tipo Cambio" v-model="tipo_cambio" readonly></v-text-field>
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
                        <th style="width:3%;" class="text-left">P.U (S/.)</th>
                        <th class="text-left">CNT</th>
                        <th style="width:3%;" class="text-left">P.T (S/.)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, k) in compra_detalle" :key="k">
                        <td>{{item.nombre_producto}}</td>
                        <td>{{item.und_simbolo}}</td>
                        <td>{{item.lote_name}}</td>
                        <td>{{item.lote_fecha_exp}}</td>
                        <td>{{item.lista_detalle.listaprecio.nombre}} ({{item.lista_detalle.unidades}}) - {{item.lista_detalle.precio_compra}}</td>
                        <td>{{item.precio_unitario}}</td>
                        <td>{{item.cantidad_visual}}</td>
                        <td>{{item.precio_total}}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr v-if="compra.op_inafectas > 0">
                        <td colspan="8" style="text-align:right;"><b>Op. Inafectas: </b>{{compra.op_inafectas}}</td>
                    </tr>
                    <tr v-if="compra.op_exoneradas > 0">
                        <td colspan="8" style="text-align:right;"><b>Op. Exoneradas: </b>{{compra.op_exoneradas}}</td>
                    </tr>
                    <tr v-if="compra.op_gravadas > 0">
                        <td colspan="8" style="text-align:right;"><b>Op. Gravadas: </b>{{compra.op_gravadas}}</td>
                    </tr>
                    <tr v-if="compra.icbper > 0">
                        <td colspan="8" style="text-align:right;"><b>ICBPER: </b>{{compra.icbper}}</td>
                    </tr>
                    <tr>
                        <td colspan="8" style="text-align:right;"><b>IGV (18%): </b>{{compra.igv}}</td>
                    </tr>
                    <tr>
                        <td colspan="8" style="text-align:right;"><b>Total: </b>{{compra.total}}</td>
                    </tr>
                </tfoot>
            </v-simple-table>
            <v-card-actions class="justify-center">
                <template v-if="$can('compras_update', 'all')">
                    <v-btn @click="aprobarAnularCompra" color="primary" v-if="compra.id_estado==3"><v-icon small>mdi-checkbox-marked-circle-outline</v-icon>Aprobar Compra</v-btn>
                    
                    <v-btn @click="aprobarAnularCompra" color="error" v-if="compra.id_estado==1"><v-icon>mdi-close-circle-outline</v-icon>Anular Compra</v-btn>
                </template>
            </v-card-actions>
        </v-card>

        <!-- Email Dialog -->
        <v-dialog v-model="dialogEmail" max-width="45%">
            <v-card>
                <v-card-title>
                    <span class="headline">Datos del Envío</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="formEmail">
                        <v-row>
                            <v-col>
                                <v-text-field label="Nombre Destinatario" v-model="formEmail.to_name" :rules="requiredRules"></v-text-field>
                                <v-text-field label="Email Destinatario" v-model="formEmail.to_email" :rules="emailRules"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="dialogEmail=false;">Cancelar</v-btn>
                    <v-btn color="primary" @click="enviarEmail">Enviar</v-btn>
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
            text: 'Compras',
            disabled: false,
            to: '/compras'
        }, {
            text: 'Visualizar Compra'
        }],

        id_compra: 0,
        compra:null,
        compra_detalle: [],

        tipo_cambio: null,
        origenes_dinero: [
            { id:1, name:'Caja Chica' },
            { id:2, name:'Cuenta Bancaria' },
            { id:3, name:'Otra Fuente' },
        ],


        //--- Email ---
        dialogEmail: false,
        formEmail: {
            id: null,
            from_name: null,
            to_name:   null,
            to_email:  null,
            subject:   null,
            content:   null,
        },
        defaultFormEmail: {
            id: null,
            from_name: null,
            to_name:   null,
            to_email:  null, //'unreleasedpizzas@gmail.com'
            subject:   null,
            content:   null,
        },
        //--- End ---

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            // v => !!v || 'Campo obligatorio',
            v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
        ],
    }),
    mounted(){
        this.id_compra = this.$route.params.id;
        this.defaultFormEmail.id = this.id_compra;
        this.getCompra();
    },
    methods:{
        getCompra(){
            this.preloader = true;
            axios.get('/api/compra/'+this.id_compra).then((response) => {
                //Compra
                this.compra = response.data.compra;
                this.compra_detalle = response.data.compra_detalle;
                this.tipo_cambio = this.compra.tipo_cambio.tipo_cambio + ' - ' + this.compra.tipo_cambio.cambio;

                //--- Email Data ---
                this.defaultFormEmail.to_name   = this.compra.proveedor.nombre;
                this.defaultFormEmail.to_email  = this.compra.email;
                this.defaultFormEmail.from_name = this.compra.sucursal.empresa.nombre;

                this.formEmail = Object.assign({}, this.defaultFormEmail);
                //--- End ---

            }).catch(e => {
                console.error(e);

            }).finally(()=>(this.preloader = false));
        },
        aprobarAnularCompra(){
            if(this.compra.id_estado == 1){//Anulacion
                var title ='Anular Compra';
                var text = "¿Está seguro que desea anular esta compra?";
                var toast_text = 'La compra ha sido anulada.';
            }else{//Aprobacion
                var title ='Aprobar Compra';
                var text = "¿Está seguro que desea aprobar esta compra?";
                var toast_text = 'La compra ha sido aprobada.';
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
                    this.preloader = true;
                    axios.delete('/api/compra/'+this.id_compra).then(()=>{
                        Toast.fire({
                            icon: 'success',
                            title: toast_text,
                        });
                        this.$router.go();
                    }).finally(()=>(this.preloader = false));
                }
            });
        },
        generarPDF(){
            window.open('/generarCompraPDF/'+this.id_compra);
        },

        //--- Email Functions ---
        enviarEmail(){
            this.preloader = true;
            axios.post('/api/sendMailCompra', this.formEmail).then(response => {
                this.dialogEmail = false;
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
                
            }).finally(()=>(this.preloader = false));
        },
        //--- End ---
    },
    watch: {
        dialogEmail(){
            if(!this.dialogEmail){
                this.formEmail = Object.assign({}, this.defaultFormEmail);
            }
        },
    },
}
</script>