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