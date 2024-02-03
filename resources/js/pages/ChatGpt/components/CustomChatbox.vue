<template>
    <form class='chat-box' @submit='onSubmit($event)'>
        <v-row>
            <v-col cols="11">
                <v-text-field
                    placeholder='Escribe una pregunta'
                    v-model='text'
                    outlined
                    hide-details>
                </v-text-field>
            </v-col>
            <v-col cols="1">
                <v-btn
                    :disabled='text === ""'
                    color="success"
                    class="mb-1"
                    block
                    x-large
                    type="submit">
                    <v-icon>mdi-send</v-icon>
                </v-btn>
            </v-col>
        </v-row>
    </form>
</template>

<script>
export default {
    name: 'Chatbox',
    data: () => ({
        // Sirve para mantener el contenido de la caja de texto
        text: ''
    }),
    methods: {
        onSubmit(event) {
            // Dispara un evento gestionado desde el
            // componente padre
            this.$emit("submit", event, this.text);
            this.text = '';
        }
    }
}
</script>

<style scoped>
.chat-box {
    width: 100%;
    display: flex;
}

input {
    width: min(100%, 20rem);
    flex-grow: 1;
}

button:disabled {
    opacity: 0.5;
}
</style>