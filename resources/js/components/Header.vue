<template>
    <v-card>
        <v-app-bar app dark class="blue-grey darken-3" style="margin: 10px; height: 50px;">
            <v-app-bar-nav-icon @click.stop="$emit('toggle-drawer')" style="height: 50px;">
            </v-app-bar-nav-icon>
        </v-app-bar>
    </v-card>
</template>
<style lang="css">
.v-toolbar__content {
    height: 50px !important;
}
</style>
<script>
export default {
    created(){
        this.checkActiveSession();
    },

    methods: {
        checkActiveSession(){
             axios.get("api/auth/checkActiveSession")
             .catch(e=>{
                console.error(e);
                this.frontEndLogout();
             });
        },

        frontEndLogout (){
            localStorage.removeItem('user_data');
            localStorage.removeItem('user_permissions');

            location.replace('/login')
        },
    }
}
</script>

