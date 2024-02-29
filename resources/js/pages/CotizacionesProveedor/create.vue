<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <div>
                <h2 style="color: #37474F">Nueva Cotización de Proveedor</h2>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
            </div>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <form @submit.prevent="createCotizacionProveedor">
                <v-row dense class="pa-2">
                    <v-col cols="5" style="z-index: 4;">
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
                    <v-col cols="4">
                        <v-text-field v-model="form.email" label="Correo Electrónico *" :rules="requiredRules"></v-text-field>
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
                            <v-date-picker no-title v-model="form.fecha_emision" @input="menuFechEmi = false" locale="es-ES"></v-date-picker>
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
                            <th class="text-left">Laboratorio</th>
                            <th class="text-left">Unidad</th>
                            <th class="text-left">CNT</th>
                            <th style="width:7%;" class="text-left"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="cotz_detail.length == 0">
                            <td colspan="12" class="text-center">
                                <b>Aún no se han agregado productos a esta cotización</b>
                            </td>
                        </tr>
                        <tr v-for="(item, k) in cotz_detail" :key="k">
                            <td>{{item.nombreProducto}}</td>
                            <td>{{item.laboratorio}}</td>
                            <td>{{item.und_simbolo}}</td>
                            <td><input type="number" placeholder="0,00" min="0" class="form-control" v-model="item.cantidad"></td>
                            <td>
                                <v-btn small color="error" icon @click="borrarFila(item)">
                                    <v-icon small> mdi-delete</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-simple-table>
                <v-card-actions class="justify-center">
                    <v-btn type="submit" color="primary">Guardar</v-btn>
                </v-card-actions>
            </form>
        </v-card>
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
            text: 'Nueva Cotización de Proveedor'
        }],


        form: new Form({
            id_proveedor: null,
            email: null,
            fecha_emision: null,
            //Extras
            productos: null,
        }),
        menuFechEmi: false,
        cotz_detail: [],
        
        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
    }),
    mounted(){
        this.form.fecha_emision = this.todaysDateDefault();
    },
    methods:{
        //--- Funciones Cotizacion ---
        createCotizacionProveedor(){
            this.preloader = true;
            this.form.productos = this.cotz_detail;
            this.form.post('api/prv_cotizacion').then((result)=>{
                Toast.fire({
                    icon: 'success',
                    title: 'Cotización creada correctamente!'
                });
                this.$router.push('/proveedor_cotizaciones_view/'+result.data.id_cotizacion);

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
        handleSubmitProduct(result) {
            this.cotz_detail.push({
                id_producto:    result.id_producto,
                nombreProducto: result.nombreProducto,
                laboratorio:    result.laboratorio.nombre,
                cantidad: 1,
                und_simbolo:    result.unidad_medida.simbolo,
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

                fetch('api/buscarProductosCompra', requestOptions)
                .then(response => response.json())
                .then(data => {
                    //console.log(data);
                    resolve(data)
                })
            })
        },
        borrarFila(item){
            var idx = this.cotz_detail.indexOf(item);
            if(idx > -1){
                this.cotz_detail.splice(idx, 1);
            }
        },
        //--- End ---

        //--- AutoProveedor ---
        handleSubmitProveedor(result) {
            this.form.id_proveedor  = result.id_proveedor;
            this.form.email         = result.email;
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

                fetch('api/buscarProveedores', requestOptions).then(response => response.json())
                .then(data => {
                    resolve(data)
                })
            })
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
                return this.formatDate(this.form.fecha_emision)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        //--- End ---
    },
}
</script>