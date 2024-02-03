<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Cotización de Compra N°{{padding}}</h2>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card v-if="cotizacion" class="mb-4" light style="padding: 15px">
            <form>
                <v-card-text>
                    <v-row dense class="pa-2">
                        <v-col cols="12">
                            <v-alert dense type="info" v-if="cotizacion.id_estado==3">La cotización se encuentra en <b>espera</b> y requiere de <b>aprobación</b>.</v-alert>
                            <v-alert dense type="warning" v-if="cotizacion.id_estado==2">La cotización fue <b>anulada</b> y no puede realizar ninguna acción.</v-alert>
                        </v-col>
                    </v-row>
                    <v-row dense class="pa-2">
                        <v-col class="text-right">
                            <v-btn @click="generarPDF" color="red" v-if="cotizacion.id_estado!=2" dark>
                                <v-icon small>mdi-file-pdf</v-icon><b>PDF</b>
                            </v-btn>
                            <v-btn @click="dialogEmail = true" color="teal" v-if="cotizacion.id_estado==1" dark>
                                <v-icon small>mdi-email-send</v-icon><b>Email</b>
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-row dense class="pa-2">
                        <v-col cols="6">
                            <v-text-field v-model="form.nombre_proveedor" label="Nombre Proveedor" readonly></v-text-field>
                        </v-col>
                        <v-col cols="3">
                            <v-text-field v-model="form.email" label="Correo Electrónico" readonly></v-text-field>
                        </v-col>
                        <v-col cols="3">
                            <v-text-field v-model="form.fecha_emision" label="Fecha de Emisión" readonly></v-text-field>
                        </v-col>
                    </v-row>
                    <v-simple-table fixed-header>
                        <thead>
                            <tr>
                                <th class="text-left">Producto</th>
                                <th class="text-left">CNT</th>
                                <th class="text-left">Unidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, k) in cotizacion_detalle" :key="k">
                                <td>{{item.nombreProducto}}</td>
                                <td>{{item.cantidad}}</td>
                                <td>{{item.und_simbolo}}</td>
                            </tr>
                        </tbody>
                    </v-simple-table>
                    <br><br>
                    <v-card-actions>
                        <v-row dense class="pa-2">
                            <v-col cols="12" class="text-center">
                                <template v-if="$can('ordcomp_create', 'all')">
                                    <router-link :to="{name:'OrdenCompraCreate', params:{id: this.id_cotizacion}}" style="text-decoration: none; color: inherit;" v-if="cotizacion.id_estado==1">
                                        <v-btn depressed color="accent"><v-icon small>mdi-text-box-plus-outline</v-icon><b>Generar orden de compra</b></v-btn>
                                    </router-link>
                                </template>
                                <template v-if="$can('cotizaciones_update', 'all')">
                                    <v-btn @click="aprobarAnularCotizacion" depressed color="primary" v-if="cotizacion.id_estado==3"><v-icon small>mdi-checkbox-marked-circle-outline</v-icon>Aprobar Cotización</v-btn>
                                </template>
                                <template v-if="$can('cotizaciones_delete', 'all')">
                                    <v-btn @click="aprobarAnularCotizacion" depressed color="error" v-if="cotizacion.id_estado==1"><v-icon small>mdi-close-circle-outline</v-icon>Anular Cotización</v-btn>
                                </template>
                            </v-col>
                        </v-row>
                    </v-card-actions>
                </v-card-text>
            </form>
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
            text: 'Proveedor Cotizaciones',
            disabled: false,
            to: '/proveedor_cotizaciones'
        }, {
            text: 'Ver Cotización de Proveedor'
        }],

        id_cotizacion: 0,
        padding: null,
        cotizacion: null,
        cotizacion_detalle: [],
        form: new Form({
            nombre_proveedor: null,
            email: null,
            fecha_emision: null,
        }),


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
        this.id_cotizacion = this.$route.params.id;
        this.defaultFormEmail.id = this.id_cotizacion;
        this.getCotizacion();
    },
    methods:{
        getCotizacion(){
            this.preloader = true;
            axios.get('/api/prv_cotizacion/'+this.id_cotizacion).then((response) => {
                //Cotizacion
                this.cotizacion = response.data.cotizacion;
                this.form.fill(this.cotizacion);
                this.form.nombre_proveedor = this.cotizacion.proveedor.nombre;
                this.padding = ('00000'+this.cotizacion.numeracion).slice(-5);

                //Cotizacion Detalle
                response.data.cotizacion_detalle.forEach((val)  => {
                    this.cotizacion_detalle.push({
                        id_detalle:     val.id_cotz_detalle_prv,
                        id_producto:    val.id_producto,
                        nombreProducto: val.nombre_producto,
                        cantidad:       val.cantidad,
                        und_simbolo:    val.und_simbolo,
                    });
                });

                //--- Email Data ---
                this.defaultFormEmail.to_name   = this.cotizacion.proveedor.nombre;
                this.defaultFormEmail.to_email  = this.cotizacion.email;
                this.defaultFormEmail.from_name = this.cotizacion.sucursal.empresa.nombre;

                this.formEmail = Object.assign({}, this.defaultFormEmail);
                //--- End ---

            }).catch(e => {
                console.error(e);

            }).finally(()=>(this.preloader = false));
        },
        aprobarAnularCotizacion(){
            if(this.cotizacion.id_estado == 1){//Anulacion
                var title ='Anular Cotización';
                var text = "¿Está seguro que desea anular esta cotización?";
                var toast_text = 'La cotización ha sido anulada.';
            }else{//Aprobacion
                var title ='Aprobar Cotización';
                var text = "¿Está seguro que desea aprobar esta cotización?";
                var toast_text = 'La cotización ha sido aprobada.';
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
                    axios.delete('/api/prv_cotizacion/'+this.id_cotizacion).then(()=>{
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
            window.open('/generarProveedorCotizacionPDF/'+this.id_cotizacion);
        },
        enviarEmail(){
            this.preloader = true;
            axios.post('/api/sendMailCotizacion', this.formEmail).then(response => {
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