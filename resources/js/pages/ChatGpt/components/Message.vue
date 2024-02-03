<template>
    <div :class='["message", { dark }]'>
        <h5>{{ author }}</h5>
        {{ text }}
    </div>
</template>

<script>
export default {
    name: 'Message',
    props: [
        'text', // Contenido del mensaje
        'author', // Autor del mensaje
        'dark' // Color de fondo de la caja de texto
    ]
}
</script>

<style scoped>
.message {
    background: #A5E181;
    border-radius: 10px;
    padding: 1rem;
    width: fit-content;
}

.message.dark {
    background: #67CB98;
}

h5 {
    margin: 0 0 .5rem 0;
}
</style>