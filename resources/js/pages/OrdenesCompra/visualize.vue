<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Orden de Compra N°{{padding}}</h2>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card v-if="orden_compra" class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2">
                <v-col cols="12">
                    <v-alert dense type="info" v-if="orden_compra.estado==3">La orden de compra se encuentra en <b>espera</b> y requiere de <b>aprobación</b>.</v-alert>
                    <v-alert dense type="warning" v-if="orden_compra.estado==2">La orden de compra fue <b>anulada</b> y no puede realizar ninguna acción.</v-alert>
                </v-col>
            </v-row>
            
            <v-row dense class="pa-2" v-if="!opened_caja">
                <v-col cols="12">
                    <v-alert dense type="info">Debe <b>aperturar una caja</b> para poder generar compras.</v-alert>
                </v-col>
            </v-row>
            <v-row dense class="pa-2">
                <v-col class="text-right">
                    <template v-if="$can('compras_create', 'all')">
                        <router-link :to="{name:'CreateCompraFromOrden', params:{id: this.id_orden_compra}}" style="text-decoration: none; color: inherit;">
                            <v-btn color="indigo" v-if="orden_compra.estado==1 && opened_caja" dark><v-icon small>mdi-text-box-plus-outline</v-icon><b>Generar Compra</b></v-btn>
                        </router-link>
                    </template>
                    
                    <v-btn @click="generarPDF" color="red" v-if="orden_compra.estado!=2" dark>
                        <v-icon small>mdi-file-pdf</v-icon><b>PDF</b>
                    </v-btn>
                    <v-btn @click="dialogEmail = true" color="teal" v-if="orden_compra.estado==1" dark>
                        <v-icon small>mdi-email-send</v-icon><b>Email</b>
                    </v-btn>
                </v-col>
            </v-row>
            
            <v-row dense class="pa-2">
                <v-col cols="6">
                    <v-text-field v-model="orden_compra.proveedor.nombre" label="Nombre Proveedor" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field v-model="orden_compra.email" label="Correo Electrónico" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="Moneda" v-model="orden_compra.moneda.moneda" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="Medio Pago" v-model="orden_compra.medio_pago.medio_pago" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="Tipo Cambio" v-model="tipo_cambio" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="Fecha de Emisión" v-model="orden_compra.fecha_emision" readonly></v-text-field>
                </v-col>
                <v-col cols="3">
                    <v-text-field label="Fecha de Vencimiento" v-model="orden_compra.fecha_vencimiento" readonly></v-text-field>
                </v-col>
            </v-row>
            <v-simple-table fixed-header>
                <thead>
                    <tr>
                        <th class="text-left">Producto</th>
                        <th class="text-left">Unidad</th>
                        <th class="text-left">Lista de Precio</th>
                        <th style="width:10%;" class="text-right">P.U (S/.)</th>
                        <th style="width:10%;" class="text-right">CNT</th>
                        <th style="width:10%;" class="text-right">P.T (S/.)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, k) in orden_detalle" :key="k">
                        <td>{{item.nombre_producto}}</td>
                        <td>{{item.und_simbolo}}</td>
                        <td>{{item.lista_detalle.listaprecio.nombre }} ({{item.lista_detalle.unidades}}) - {{item.lista_detalle.precio_compra }}</td>
                        <td class="text-right">{{item.precio_unitario}}</td>
                        <td class="text-right">{{item.cantidad}}</td>
                        <td class="text-right">{{item.precio_total}}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr v-if="orden_compra.op_inafectas > 0">
                        <td colspan="8" style="text-align:right;"><b>Op. Inafectas: </b>{{orden_compra.op_inafectas}}</td>
                    </tr>
                    <tr v-if="orden_compra.op_exoneradas > 0">
                        <td colspan="8" style="text-align:right;"><b>Op. Exoneradas: </b>{{orden_compra.op_exoneradas}}</td>
                    </tr>
                    <tr v-if="orden_compra.op_gravadas > 0">
                        <td colspan="8" style="text-align:right;"><b>Op. Gravadas: </b>{{orden_compra.op_gravadas}}</td>
                    </tr>
                    <tr v-if="orden_compra.icbper > 0">
                        <td colspan="8" style="text-align:right;"><b>ICBPER: </b>{{orden_compra.icbper}}</td>
                    </tr>
                    <tr>
                        <td colspan="8" style="text-align:right;"><b>IGV (18%): </b>{{orden_compra.igv}}</td>
                    </tr>
                    <tr>
                        <td colspan="8" style="text-align:right;"><b>Total: </b>{{orden_compra.total}}</td>
                    </tr>
                </tfoot>
            </v-simple-table>
            <v-card-actions class="justify-center" v-if="$can('ordcomp_update', 'all')">
                <v-btn @click="aprobarAnularOrden" color="primary" v-if="orden_compra.estado==3"><v-icon small>mdi-checkbox-marked-circle-outline</v-icon>Aprobar Orden de Compra</v-btn>

                <v-btn @click="aprobarAnularOrden" color="error" v-if="orden_compra.estado==1"><v-icon small>mdi-close-circle-outline</v-icon>Anular Orden de Compra</v-btn>
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
        id_orden_compra: 0,
        preloader: false,
        padding: '',
        orden_compra: null,
        orden_detalle: [],
        breadcrumbs: [{
            text: 'Órdenes de Compra',
            disabled: false,
            to: '/ordenes_compra'
        }, {
            text: 'Ver Orden de Compra'
        }],
        tipo_cambio: '',
        opened_caja: null,

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
        this.id_orden_compra = this.$route.params.id;
        this.defaultFormEmail.id = this.id_orden_compra;
        this.getOrdenCompra();

        this.consultarCaja();
    },
    methods:{
        getOrdenCompra(){
            this.preloader = true;
            axios.get('/api/orden_compra/'+this.id_orden_compra).then((response) => {
                this.orden_compra  = response.data.orden_compra;
                this.orden_detalle = response.data.orden_detalle;
                this.padding = ('00000'+this.orden_compra.numeracion).slice(-5);
                this.tipo_cambio = this.orden_compra.tipo_cambio.tipo_cambio + ' - ' + this.orden_compra.tipo_cambio.cambio;
                
                //--- Email Data ---
                this.defaultFormEmail.to_name   = this.orden_compra.proveedor.nombre;
                this.defaultFormEmail.to_email  = this.orden_compra.email;
                this.defaultFormEmail.from_name = this.orden_compra.sucursal.empresa.nombre;

                this.formEmail = Object.assign({}, this.defaultFormEmail);
                //--- End ---

            }).catch(e => {
                console.error(e);
                
            }).finally(() => (this.preloader = false));
        },
        //--- Consulta Caja Abierta ---
        consultarCaja(){
            this.preloader = true;
            axios.get('/api/cajaAbierta').then(response => {
                if(response.data != ''){
                    this.opened_caja = response.data;
                }
            }).finally(() => (this.preloader = false));
        },
        //--- End ---

        aprobarAnularOrden(){
            if(this.orden_compra.estado == 1){//Anulacion
                var title ='Anular Orden de Compra';
                var text = "¿Está seguro que desea anular esta orden de compra?";
                var toast_text = 'La orden de compra ha sido anulada.';
            }else{//Aprobacion
                var title ='Aprobar Orden de Compra';
                var text = "¿Está seguro que desea aprobar esta orden de compra?";
                var toast_text = 'La orden de compra ha sido aprobada.';
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
                    axios.delete('/api/orden_compra/'+this.id_orden_compra).then(()=>{
                        Toast.fire({
                            icon: 'success',
                            title: toast_text,
                        });
                        this.$router.go();
                    });
                }
            });
        },
        generarPDF(){
            window.open('/generarOrdenCompraPDF/'+this.id_orden_compra);
        },

        //--- Email Functions ---
        enviarEmail(){
            this.preloader = true;
            axios.post('/api/sendMailOrdenCompra', this.formEmail).then(response => {
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