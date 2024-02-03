<template>
    <div class="flex-grow-1">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Gestión de roles de usuarios y permisos</h2>
        </div>
        <v-tabs v-model="rolesTab" :show-arrows="false" background-color="transparent">
            <v-tab to="#tabs-roles"><b>ROLES</b></v-tab>
            <v-tab v-if="$can('permissions_index', 'all')" to="#tabs-permissions"><b>PERMISOS</b></v-tab>
        </v-tabs>
        <br>
        
        <v-tabs-items v-model="rolesTab" id="custom-tab-items">
            <v-tab-item value="tabs-roles">
                <roles-page ref="roles_page" v-if="$can('roles_index', 'all')">    
                </roles-page>
                <assign-permissions-page v-if="$can('assign_permissions', 'all')" ref="assign_permissions_page">    
                </assign-permissions-page>
            </v-tab-item>
            <v-tab-item value="tabs-permissions">
                <permissions-page ref="permissions_page">    
                </permissions-page>
            </v-tab-item>
        </v-tabs-items>
    </div>
</template>

<script>
import PermissionsPage from "./components/PermissionsPage.vue";
import RolesPage from "./components/RolesPage.vue";
import AssignPermissionsPage from "./components/AssignPermissionsPage.vue";


export default {
    components: {
        PermissionsPage,
        RolesPage,
        AssignPermissionsPage,
    },
    data: () => ({
        preloader: false,
        breadcrumbs_title: "Gestión de roles de usuarios y permisos",
        breadtitle_icon: "mdi-cog-outline",
        breadcrumbs: [{
            text: "Inicio",
            disabled: false,
            href: "#",
            color: "white",
            
        }, {
            text: "Roles y Permisos",
            color: "white",
        },],

        rolesTab: 0,
    }),
    mounted(){
        //
    },
    methods: {
        //--- Loading Data ---
        //--- End ---
    },
}
</script>
<style>
#custom-tab-items {
    background-color: transparent !important;
}
</style>