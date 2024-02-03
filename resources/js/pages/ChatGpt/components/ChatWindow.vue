<template>
    <div class='window'>
        <div class='messages' ref="msgContainer">
            <Message v-for='message in messages' :key='message.id'
                :class='["message", { right: message.isMine }]'
                :dark='!message.isMine'
                :text='message.text'
                :author='message.author'
            />
        </div>
        <br>
        <CustomChatbox class='chat-box' @submit='onSubmit' />
    </div>
</template>

<script>
import CustomChatbox from "./CustomChatbox.vue";
import Message from "./Message.vue";

export default {
    // name: 'App',
    components: {
        // RegisterDialog,
        CustomChatbox,
        Message
    },
    data: () => ({
        username: {},
        messages: [],
    }),
    created() {
        this.username = JSON.parse(localStorage.getItem('user_data')).name;
    },
    methods: {
        // Este metodo se llamara cuando un nuevo mensaje es enviado
        onSubmit(event, text) {
            event.preventDefault();
            this.messages.push({
                isMine: true,
                text: text,
                author: this.username
            });
            this.ScrollToLastMessage();

            this.SendQueryToChatGPT(text);
        },
        SendQueryToChatGPT(text){
            this.$parent.$parent.chatForm.text = text;
            axios.post('/api/chatgpt/SendQuery', this.$parent.$parent.chatForm).then((response)=>{
                this.messages.push({
                    isMine: false,
                    text: response.data.message,
                    author: "ChatGPT"
                });
                this.ScrollToLastMessage();

            }).catch(e => {
                this.messages.push({
                    isMine: false,
                    text: e.response.data.message,
                    author: "ChatGPT"
                });
                console.error(e);
            });
        },

        ScrollToLastMessage(){
            this.$nextTick(() => {
                var container = this.$refs.msgContainer;
                container.scrollTop = container.scrollHeight;
            });
        },
    },
}
</script>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
}

button {
    border: 0;
    background: #2a60ff;
    color: white;
    cursor: pointer;
    padding: 1rem;
}

input {
    border: 0;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.1);
}
</style>

<style scoped>
.window {
    height: 60vh;
    display: flex;
    flex-direction: column;
}

.messages {
    flex-grow: 1;
    overflow: auto;
    padding: 1rem;
    background-color: #526b77;
}

.message+.message {
    margin-top: 1rem;
}

.message.right {
    margin-left: auto;
}
</style>