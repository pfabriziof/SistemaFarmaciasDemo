<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3" v-if="comprobante">
            <div>
                <h2 style="color: #37474F">
                    Visualizar {{this.comprobante.tipo_comprobante.tipo_comprobante}} #{{this.comprobante.serie.serie}}-{{this.correlativo}}
                </h2>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card class="mb-4" light style="padding: 15px" v-if="comprobante">
            <v-card-title class="headline">
                <v-row dense>
                    <v-col cols="12" v-if="comprobante.id_estado_comprobante == 2">
                        <v-alert dense type="warning">El comprobante fue <b>anulado</b> y no puede realizar ninguna acción.</v-alert>
                    </v-col>
                    <v-col class="text-right" v-if="comprobante.id_estado_comprobante != 2">
                        <v-btn depressed color="teal" v-if="comprobante.external_id" @click="dialogEmail = true" dark>
                            <v-icon>mdi-email-send</v-icon>Email
                        </v-btn>
                        <v-btn depressed color="error" v-if="comprobante.external_id" @click="verPdf">
                            <v-icon>mdi-file-pdf</v-icon>PDF
                        </v-btn>
                        <v-btn depressed color="primary" v-if="comprobante.external_id" @click="verXml">
                            <v-icon>mdi-xml</v-icon>XML
                        </v-btn>
                        <v-btn depressed color="success" v-if="comprobante.external_id && comprobante.id_tipo_comprobante === 1" @click="verCdr">
                            <v-icon>mdi-file-document</v-icon>CDR
                        </v-btn>
                        <!-- <v-btn depressed color="info" v-if="$can('invoice_update', 'all') && comprobante.external_id==null" @click="enviarSunat">
                            <v-icon>mdi-send-lock</v-icon>SUNAT
                        </v-btn> -->
                    </v-col>
                </v-row>
            </v-card-title>
            <v-row dense class="pa-2">
                <v-col cols="2">
                    <v-text-field readonly v-model="comprobante.tipo_comprobante.tipo_comprobante" label="Tipo Comp."></v-text-field>
                </v-col>
                <v-col cols="5">
                    <v-text-field readonly v-model="comprobante.nombreCliente" label="Nombre / Razón Social"></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field readonly v-model="comprobante.nroDocCliente" :label="label_nrodoc"></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field readonly v-model="comprobante.fecha_emision" label="Fecha de Emisión"></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field readonly v-model="comprobante.serie.serie" label="Serie"></v-text-field>
                </v-col>
                <v-col cols="5">
                    <v-text-field readonly v-model="comprobante.direccionCliente" label="Dirección"></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field readonly v-model="comprobante.formato_impresion" label="Formato"></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field readonly v-model="comprobante.fecha_vencimiento" label="Fecha de Venc."></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field readonly v-model="comprobante.medio_pago.medio_pago" label="Medio Pago"></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field readonly v-model="comprobante.moneda.moneda" label="Moneda"></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field readonly v-model="comp_tipocambio" label="Tipo Cambio"></v-text-field>
                </v-col>
                <v-col cols="2">
                    <v-text-field readonly v-model="comprobante.estado.estado" label="Estado"></v-text-field>
                </v-col>
            </v-row>
        </v-card>
        <br>
        <v-card class="mb-4" light style="padding: 15px" v-if="comprobante">
            <v-card-title>Detalle del Comprobante</v-card-title>
            <v-card-text>
                <v-simple-table fixed-header>
                    <thead>
                        <tr>
                            <th class="text-left">Producto</th>
                            <th class="text-left">Unidad</th>
                            <th class="text-left">Laboratorio</th>
                            <th class="text-left">Lote</th>
                            <th class="text-left">Lista de Precio</th>
                            <th style="width:13%;" class="text-right">P.U (S/.)</th>
                            <th style="width:13%;" class="text-right">CNT</th>
                            <th style="width:13%;" class="text-right">P.T (S/.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="comprobante_detalle.length == 0">
                            <td colspan="7" class="text-center"><b>Aún no se han agregado productos o servicios a este comprobante.</b></td>
                        </tr>
                        <tr v-for="(item, k) in comprobante_detalle" :key="k">
                            <td>{{item.nombre_producto}}</td>
                            <td>{{item.und_simbolo}}</td>
                            <td>{{item.laboratorio}}</td>
                            <td>{{item.lote}}</td>
                            <td>{{item.lista_detalle}}</td>
                            <td class="text-right">{{item.precio_unitario}}</td>
                            <td class="text-right">{{item.cantidad}}</td>
                            <td class="text-right">{{item.precio_total}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr v-if="comprobante.op_inafectas > 0">
                            <td colspan="8" style="text-align:right;"><b>Op. Inafectas: </b>{{comprobante.op_inafectas}}</td>
                        </tr>
                        <tr v-if="comprobante.op_exoneradas > 0">
                            <td colspan="8" style="text-align:right;"><b>Op. Exoneradas: </b>{{comprobante.op_exoneradas}}</td>
                        </tr>
                        <tr v-if="comprobante.op_gravadas > 0">
                            <td colspan="8" style="text-align:right;"><b>Op. Gravadas: </b>{{comprobante.op_gravadas}}</td>
                        </tr>
                        <tr v-if="comprobante.icbper > 0">
                            <td colspan="8" style="text-align:right;"><b>ICBPER: </b>{{comprobante.icbper}}</td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align:right;"><b>IGV (18%): </b>{{comprobante.igv}}</td>
                        </tr>
                        <tr>
                            <td colspan="8" style="text-align:right;"><b>Total: </b>{{comprobante.total}}</td>
                        </tr>
                        <tr>
                            <td colspan="8" class="text-center">
                                <v-textarea v-model="comprobante.comentario"  rows="2" label="Comentarios/Observaciones" readonly></v-textarea>
                            </td>
                        </tr>
                    </tfoot>
                </v-simple-table>
                <br><br>
                <v-card-actions>
                    <v-row dense class="pa-2">
                        <v-col cols="12" class="text-center" v-if="comprobante.id_estado_comprobante != 2">
                            <v-btn depressed v-if="$can('invoice_delete', 'all')" @click="dialogDelete = true" color="error">
                                <v-icon>mdi-close-circle-outline</v-icon>Anular comprobante
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-actions>
            </v-card-text>
        </v-card>
        
        <!-- Dialog Anular Comprobantes -->
        <v-dialog v-model="dialogDelete" max-width="45%">
            <v-card>
                <v-card-title class="subtitle-1">¿Está seguro que desea anular este comprobante?</v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="anularDocumentoConfirm" ref="formAnulacion">
                        <v-row>
                            <v-col>
                                <v-textarea
                                    label="Motivo Anulación *"
                                    v-model="formAnulacion.motivo_anulacion"
                                    rows="3"
                                    :rules="requiredRules"
                                    outlined
                                    autocomplete="off"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-form>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="dialogDelete = false">Cancelar</v-btn>
                        <v-btn color="primary" @click="anularDocumentoConfirm">Aceptar</v-btn>
                    </v-card-actions>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-- Fin -->

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
            text: 'Comprobantes',
            disabled: false,
            to: '/comprobantes'
        }, {
            text: 'Visualizar Comprobante'
        }],

        dialogDelete: false,
        formAnulacion: new Form({
            motivo_anulacion: null,
        }),

        comprobante: null,
        comprobante_detalle: [],
        
        correlativo: null,
        label_nrodoc: null,
        comp_tipocambio: null,


        //--- Email ---
        dialogEmail: false,
        formEmail: {
            id: null,
            from_name: null,
            to_name:   null,
            to_email:  null,
            subject:   null,
            content:   null,

            type_id: null,
        },
        defaultFormEmail: {
            id: null,
            from_name: null,
            to_name:   null,
            to_email:  null, //'unreleasedpizzas@gmail.com'
            subject:   null,
            content:   null,

            type_id: null,
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

    mounted() {
        this.id_comprobante = this.$route.params.id;
        this.defaultFormEmail.id = this.id_comprobante;
        this.getComprobante();
    },


    methods: {
        getComprobante(){
            this.preloader = true;
            axios.get('/api/comprobante/'+this.id_comprobante).then((response) => {
                this.comprobante = response.data.comprobante;

                this.correlativo = String(this.comprobante.correlativo).padStart(8,'0');
                this.comp_tipocambio = this.comprobante.tipo_cambio.tipo_cambio + ' - ' + this.comprobante.tipo_cambio.cambio;
                this.comprobante.fecha_emision = this.formatDate(this.comprobante.fecha_emision);
                this.comprobante.fecha_vencimiento = this.formatDate(this.comprobante.fecha_vencimiento);
                switch (this.comprobante.id_tipo_comprobante) {
                    case 1:
                        this.label_nrodoc = 'RUC';
                        break;

                    case 2:
                        this.label_nrodoc = 'DNI';
                        break;

                    case 3:
                        this.label_nrodoc = 'RUC/DNI';
                        break;

                    default:
                        this.label_nrodoc = 'Nro. Documento';
                        break;
                }

                response.data.comprobante_detalle.forEach((val)  => {
                    var lista_dt = val.lista_detalle;
                    this.comprobante_detalle.push({
                        nombre_producto: val.nombre_producto,
                        und_simbolo:     val.und_simbolo,
                        laboratorio:     val.producto.laboratorio.nombre,
                        lote:            val.lote_producto,

                        lista_detalle:  lista_dt.listaprecio.nombre +' ('+ lista_dt.unidades +') - '+ lista_dt.precio_venta,

                        cantidad:        val.cantidad_visual,
                        precio_unitario: val.precio_unitario,
                        precio_total:    val.precio_total,
                    });
                });


                //--- Email Data ---
                this.defaultFormEmail.type_id   = this.comprobante.id_tipo_comprobante

                this.defaultFormEmail.to_name   = this.comprobante.nombreCliente;
                this.defaultFormEmail.to_email  = this.comprobante.cliente.email;
                this.defaultFormEmail.from_name = this.comprobante.sucursal.empresa.nombre;

                this.formEmail = Object.assign({}, this.defaultFormEmail);
                //--- End ---

            }).catch(e => {
                console.error(e);
            }).finally(()=>(this.preloader = false));
        },
        anularDocumentoConfirm () {
            this.formAnulacion.delete('/api/comprobante/'+this.id_comprobante).then(response => {
                Toast.fire({
                    icon: 'success',
                    title: 'El comprobante ha sido anulado.',
                });
                this.$router.go();

            }).catch(e => {
                console.error(e);
                Toast.fire({
                    icon: 'error',
                    title: 'Error al anular el comprobante.',
                });
            });
        },

        enviarSunat(){
            this.preloader = true;
            axios.get('/api/enviarComprobanteSunat/'+this.id_comprobante).then((response) => {
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

            }).finally(() =>{this.preloader = false});
        },
        verPdf(){
            window.open('https://pruebas.bytesoluciones.net/print/document/'+this.comprobante.external_id+'/'+this.comprobante.formato_impresion);
        },
        verXml(){
            window.open('https://pruebas.bytesoluciones.net/downloads/document/xml/'+this.comprobante.external_id);
        },
        verCdr(){
            window.open('https://pruebas.bytesoluciones.net/downloads/document/cdr/'+this.comprobante.external_id);
        },

        //--- Email Functions ---
        enviarEmail(){
            this.preloader = true;
            axios.post('/api/sendMailComprobante', this.formEmail).then(response => {
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

        //--- Date Formatting ---
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        //--- End ---
    },
    watch: {
        dialogEmail(){
            if(!this.dialogEmail){
                this.formEmail = Object.assign({}, this.defaultFormEmail);
            }
        },
        dialogDelete(){
            if(!this.dialogDelete){
                this.$refs.formAnulacion.reset();
            }
        },
    },
}
</script>