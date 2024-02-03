<template>
    <v-app id="main" style="background: #efefef;">
        <navbar ref="navbar"></navbar>
        
        <app-header @toggle-drawer="$refs.navbar.drawer = !$refs.navbar.drawer"></app-header>

        <v-main>
            <v-container style="padding: 10px 30px;">
                <router-view></router-view>
            </v-container>
        </v-main>

        <app-footer></app-footer>
    </v-app>
</template>
<script>
    import Navbar from './components/Navbar'
    import Header from './components/Header'
    import Footer from './components/Footer'

    export default {
        name: 'App',
        components: {
            'navbar': Navbar,
            'app-header': Header,
            'app-footer': Footer
        }
    }
</script>
<style lang="scss" scoped>
*{
    text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important;
    font-size: 12px !important;
}
html{
    font-size: 12px !important;
}
</style>