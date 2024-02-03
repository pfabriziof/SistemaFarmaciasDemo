<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Series</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field
                        label="Nombre o nro. de documento"
                        v-model="filter.searchTerm"
                        append-icon="mdi-magnify"
                        class="flex-grow-1 mr-1"
                    ></v-text-field>
                </v-col>
                <v-col class="text-right">
                    <v-btn color="primary" class="mr-2" @click="GetAllRecords" dark>
                        <v-icon>mdi-magnify</v-icon>Buscar
                    </v-btn>
                    <v-btn class="mr-2" @click="ClearFilters">
                        <v-icon>mdi-reload</v-icon>Limpiar
                    </v-btn>
                </v-col>
            </v-row>
        </v-card>

        <v-card class="mb-4" light style="padding: 15px">
            <v-tabs
                v-model="mainPageTab"
                :show-arrows="false"
                color="primary"
                background-color="transparent"
                fixed-tabs>
                <v-tab v-if="$can('seriesinv_index', 'all')" to="#tabs-invoice">Series Comprobante</v-tab>
            </v-tabs>
             <v-tabs-items v-model="mainPageTab">
                <v-tab-item value="tabs-invoice">
                    <inv-series ref="invseries_table" 
                        :searchTerm="filter.searchTerm"

                        :key="componentKey"
                    ></inv-series>
                </v-tab-item>
             </v-tabs-items>
        </v-card>
    </div>
</template>

<script>
import InvSeries from "./components/InvSeries";

export default {
    components: {
        InvSeries,
    },
    data: () => ({
        preloader: false,

        //--- Filters ---
        menuStartDate: false,
        menuEndDate: false,
        filter: {
            searchTerm: '',
        },
        //--- End ---

        mainPageTab: 0,
        componentKey: 0,
    }),
    mounted() {
        //
    },
    methods: {
        //--- Filters Functions ---
        GetAllRecords(){
            this.forceRerender();

            const test = [
                this.$refs.invseries_table
            ];
            const loop = async () => {
                this.preloader = true;
                for (let i = 0; i < test.length; i++) {
                    test[i].GetRecords();
                }
            }
            loop();
            this.preloader = false;
        },
        ClearFilters(){
            this.filter.searchTerm = '';
            this.GetAllRecords();
        },

        forceRerender() {
            this.componentKey += 1;
        },
        //--- End ---
    },
}
</script>