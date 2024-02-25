<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Dashboard - Bienvenido/a {{usuario.name}}</h2>
        </div>

        <v-row>
            <v-col cols="12" md="6">
                <v-card class="mb-4" light style="padding: 15px;">
                    <div style="display: flex;align-items: center;">
                        <v-icon color="success" x-large>mdi-beaker</v-icon>
                        <div class="info-box-content">
                        <p style="margin-bottom: 4px;" class="font-weight-bold">Ventas</p>
                        <p style="margin-bottom: 4px;" class="font-weight-bold">{{ventas_today}}</p>
                        </div>
                    </div>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card class="mb-4" light style="padding: 15px">
                    <div style="display: flex;align-items: center;">
                        <v-icon color="error" x-large>mdi-beaker</v-icon>
                        <div class="info-box-content">
                        <p style="margin-bottom: 4px;" class="font-weight-bold">Egresos</p>
                        <p style="margin-bottom: 4px;" class="font-weight-bold">{{egresos_today}}</p>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>
              
        <v-card class="mb-4" light style="padding: 15px">
            <p class="text-center"><strong>Ventas vs. Egresos - - Últimos 7 días</strong></p>
            <div class="chart">
                <canvas id="salesChart" height="180" style="height: 180px;"></canvas>
            </div>
        </v-card>


        <v-row>
            <v-col cols="12" md="6">
                <v-card class="mb-4" light style="padding: 15px">
                    <p class="text-center"><strong>Ventas vs. Egresos - Hoy</strong></p>
                    <v-row>
                        <v-col cols="12" md="8">
                            <div class="chart-responsive">
                                <canvas id="pieChart" height="150"></canvas>
                            </div>
                        </v-col>
                        <v-col cols="12" md="4">
                            <ul class="chart-legend clearfix">
                                <li><v-icon color="success">mdi-checkbox-blank-circle</v-icon> Ventas</li>
                                <li><v-icon color="error">mdi-checkbox-blank-circle</v-icon> Egresos</li>
                            </ul>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card class="mb-4" light style="padding: 15px">
                    <p class="text-center">
                        <strong>Top Productos Vendidos</strong>
                    </p>
                    <v-simple-table>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th class="text-left">Nro.</th>
                                <th class="text-left">Nombre</th>
                                <th class="text-left">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in top_productos" :key="index">
                                <td class="text-left">{{ index + 1 }}</td>
                                <td class="text-left">{{ item.nombre_producto }}</td>
                                <td class="text-left">{{ item.cnt }}</td>
                            </tr>
                        </tbody>
                    </template>
                    </v-simple-table>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    data: () => ({
        salesChartCanvas :null,
        pieChartCanvas :null,
        ventas_today: 0,
        egresos_today: 0,
        top_productos:[],
        usuario:{},

        filter: {
            fechaInicio: "",
            fechaFin: "",
        },
        menuFechaInicio: false,
        menuFechaFin: false,
    }),
    mounted() {
        this.usuario = JSON.parse(localStorage.getItem('user_data'));
        // this.filter.fechaInicio = this.firstDateMonth();
        this.GetDashboardTopProductos();
        this.GetDashboardHistorico();
        this.GetDashboardVentasEgresos();
    },
    methods:{
        GetDashboardVentasEgresos(){
            axios.get('api/getVentasEgresos').then((response) => {
                this.pieChartCanvas  = document.getElementById('pieChart').getContext("2d");
                this.ventas_today = response.data[0].ventas;
                this.egresos_today = response.data[0].egresos;
                
                var pieData        = {
                    labels: [
                        'Ventas', 
                        'Egresos'
                    ],
                    datasets: [
                        {
                            data: [Number(response.data[0].ventas),Number(response.data[0].egresos)],
                            backgroundColor : [ '#00a65a', '#f56954'],
                        }
                    ]
                }
                var pieOptions     = {
                    legend: {
                        display: false
                    }
                }
                //Create pie or douhnut chart
                // You can switch between pie and douhnut using the method below.
                var pieChart = new Chart(this.pieChartCanvas, {
                    type: 'doughnut',
                    data: pieData,
                    options: pieOptions,
                })
                
            }).catch(e => {
                console.error(e);
            });
        },
        GetDashboardHistorico(){
            axios.get('api/getDashboardHistorico').then((response) => {
                var labels_chart=[];
                var ventas_chart=[];
                var egresos_chart=[];
                for (let index = 0; index < response.data.length; index++) {
                    labels_chart.push(response.data[index].month);
                    ventas_chart.push(Number(response.data[index].incomes));
                    egresos_chart.push(Number(response.data[index].expenses));
                }
                this.salesChartCanvas  = document.getElementById('salesChart').getContext("2d");
                var salesChartData = {
                    labels  : labels_chart,//['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [
                        {
                            label               : 'Ventas',
                            backgroundColor     : 'rgba(60,141,188,0.9)',
                            borderColor         : 'rgba(60,141,188,0.8)',
                            pointRadius          : false,
                            pointColor          : '#3b8bba',
                            pointStrokeColor    : 'rgba(60,141,188,1)',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(60,141,188,1)',
                            data                : ventas_chart//[28, 48, 40, 19, 86, 27, 90]
                        },
                        {
                            label               : 'Egresos',
                            backgroundColor     : 'rgba(210, 214, 222, 1)',
                            borderColor         : 'rgba(210, 214, 222, 1)',
                            pointRadius         : false,
                            pointColor          : 'rgba(210, 214, 222, 1)',
                            pointStrokeColor    : '#c1c7d1',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data                : egresos_chart//[65, 59, 80, 81, 56, 55, 40]
                        },
                    ]
                }

                var salesChartOptions = {
                    maintainAspectRatio : false,
                    responsive : true,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            gridLines : {
                                display : false,
                            }
                        }],
                        yAxes: [{
                            gridLines : {
                                display : false,
                            }
                        }]
                    }
                }

                // This will get the first returned node in the jQuery collection.
                var salesChart = new Chart(this.salesChartCanvas, { 
                    type: 'line', 
                    data: salesChartData, 
                    options: salesChartOptions
                })

            }).catch(e => {
                console.error(e);
            });
        },
        GetDashboardTopProductos(){
            axios.get('api/getTopProductos?fechaInicio='+this.filter.fechaInicio
                    +'&fechaFin='+this.filter.fechaFin
            ).then((response) => {
                this.top_productos = response.data;

            }).catch(e => {
                console.error(e);
            });
        },

        //--- Date Formatting ---
        todaysDateDefault(){
            var date = new Date;
            return date.getFullYear() +"-" + (((date.getMonth()+1) < 10)?"0":"") + (date.getMonth()+1) +"-" + ((date.getDate() < 10)?"0":"") + date.getDate();
        },
        firstDateMonth(){
            // var date = new Date(new Date().getFullYear(), 0, 1);
            var date = new Date;
            date.setDate(1);

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
        formatoFechaInicio: {
            get: function () {
                return this.formatDate(this.filter.fechaInicio)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
        formatoFechaFin: {
            get: function () {
                return this.formatDate(this.filter.fechaFin)
            },
            set: function (newValue) {
                return this.formatDate(newValue);
            }
        },
    },
}
</script>
