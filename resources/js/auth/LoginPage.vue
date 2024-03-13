
<template>
    <v-app id="main" style="
        background-image: url(../assets/images/background.jpg);
        background-repeat: no-repeat;
        background-size: cover;">
        <v-main style="display: flex; align-items: center;">
        <div v-if="preloader" class="overlay">
            <div class="triple-spinner"></div>
        </div>
            <v-row>
                <v-col cols="4"></v-col>
                <v-col cols="4">
                    <v-card class="text-center pa-1"  max-width="500px" style="margin: auto;">
                        <v-col class="text-center">
                            <v-img src="/assets/images/logo.png" />
                        </v-col>
                        <v-card-title class="justify-center">Inicio de Sesión</v-card-title>
                        <v-card-text>
                            <v-form ref="form" v-model="isFormValid" lazy-validation>
                                <v-text-field
                                    v-model="user.email"
                                    :rules="emailRules"
                                    :validate-on-blur="false"
                                    :error="error"
                                    label="Email"
                                    name="email"
                                    outlined
                                    @keyup.enter="submit"
                                    @change="resetErrors"
                                ></v-text-field>

                                <v-text-field
                                    v-model="user.password"
                                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                    :rules="requiredRules"
                                    :type="showPassword ? 'text' : 'password'"
                                    :error="error"
                                    :error-messages="errorMessages"
                                    :label="'Contraseña'"
                                    name="password"
                                    outlined
                                    @change="resetErrors"
                                    @keyup.enter="submit"
                                    @click:append="showPassword = !showPassword"
                                ></v-text-field>

                                <v-btn color="secondary" @click="login" block x-large>
                                    Ingresar
                                </v-btn>
                                <div v-if="errorProvider" class="error--text">{{ errorProviderMessages }}</div>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
                
                <v-col cols="4">
                    <v-card class="text-center"  max-width="400px" style="margin: auto;">
                        <v-card-title class="justify-center">Credenciales de Acceso</v-card-title>
                        <v-card-text>
                            <p>
                                <b>Usuario&nbsp;&nbsp;&nbsp;&nbsp; - &nbsp;&nbsp;&nbsp;&nbsp;Contraseña</b><br>
                                admin@gmail.com - admin2023<br>
                                cajero@gmail.com - cajero2023<br>
                                almacen@gmail.com - almacen2023<br>
                                contabilidad@gmail.com - contabilidad2023
                            </p> 
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-main>
    </v-app>
</template>

<script>
export default {
    data: () => ({
        preloader: false,
        navPic: "/assets/images/logo_white.png",
        
        email: '',
        password: '',
        user:{
            email:null,
            password: null
        },
        // sign in buttons
        isLoading: false,
        isSignInDisabled: false,

        // form
        isFormValid: true,
        email: '',
        password: '',

        // form error
        error: false,
        errorMessages: '',

        errorProvider: false,
        errorProviderMessages: '',

        // show password field
        showPassword: false,

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            (v) => !!v || 'Correo Electrónico es obligatorio',
            (v) => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido'
        ],
    }),
    methods: {
        login(){
            this.preloader = true;
            axios.post('/login', {
                email: this.user.email,
                password: this.user.password
                
            }).then(response => {
                this.$router.push('/');
                localStorage.setItem('user_data', JSON.stringify(response.data.user_data))
                localStorage.setItem('user_permissions',JSON.stringify(response.data.permissions));
                location.reload();

            }).catch(e => {
                let error_messages = e.response.data.errors;
                for (let i in error_messages) {
                    Swal.fire(
                        "Error!",
                        error_messages[i][0],
                        'error'
                    );
                }
            }).finally(()=>(this.preloader = false));
        },
        resetErrors() {
            this.error = false
            this.errorMessages = ''

            this.errorProvider = false
            this.errorProviderMessages = ''
        },
    },
    computed: {
        csrf_token() {
        }
    },
}
</script>

<style scoped lang="scss">
*{
    text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important;
}
</style>