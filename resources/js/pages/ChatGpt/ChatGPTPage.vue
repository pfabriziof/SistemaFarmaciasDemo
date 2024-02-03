<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Chat GPT</h2>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col class="text-right">
                    <v-select label="Tabla" v-model="chatForm.dbTable"
                        :items="dbTableList"
                        item-text="title" 
                        item-value="table_name"
                    ></v-select>
                </v-col>
                <v-col cols="12">
                    <ChatWindow />
                </v-col>
            </v-row>
        </v-card>
    </div>
</template>

<script>
import ChatWindow from "./components/ChatWindow.vue";

export default {
    components: {
        ChatWindow,
    },

    data() {
        return {
            preloader: false,
            chatForm: {
                dbTable: "",
                text: "",
            },
            validChatForm: false,
            username: "",
            
            dbTableList:[],

            requiredRules: [
                v => !!v || 'Campo obligatorio',
            ],
        }
    },
    mounted(){
        this.GetCompressedTables();
    },
    methods:{
        //--- Loading Data ---
        GetCompressedTables(){
            axios.get('/api/compressedTablesCombo').then(response => {
                this.dbTableList = response.data;
                this.chatForm.dbTable = this.dbTableList[0].table_name;

            }).catch(e => {
                console.error(e);
            });
        },
        //--- End ---
    }
}
</script>