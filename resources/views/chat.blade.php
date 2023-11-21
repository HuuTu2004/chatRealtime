<html>

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css"
        rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link href="/style.css" type="text/css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.0/echo.common.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
</head>

<body>
    <div>
        @foreach (\App\Models\User::all() as $user)
        <div>
            <a href="/login/{{ $user->id }}">{{ $user->name }}</a>
        </div>
        @endforeach
        <div>
            <a href="/logout">Logout</a>
        </div>
    </div>
    <div id="app" class="container">
        <h3 class=" text-center">Kênh chat chung :{{optional(auth()->user())->name }}</h3>
        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="inbox_chat">
                        <div v-for="user in users" class="chat_list">
                            <div class="chat_people">
                                <div class="chat_img"> <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png"
                                        alt="sunil"> </div>
                                <div class="chat_ib">
                                    <h5>@{{ user.name }} <span class="chat_date">Dec 25</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history">
                        <div v-for="old in oldMessages" >
                            
                            <div v-if="old.user_id !== id" class="incoming_msg">

                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="user_name block ">@{{old.user_name}}</span>
                                    </div>
                                    <div class="col-md-12">

                                        <div class="incoming_msg_img">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png"
                                                alt="sunil">
                                        </div>
                                        <div class="received_msg">
                                            <div class="received_withd_msg">

                                                <p>@{{ old.content }}</p>
                                                <span class="time_date"> 11:01 AM | June 9</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div v-else class="outgoing_msg">

                                <div class="sent_msg">

                                    <p>@{{ old.content }}</p>
                                    <span class="time_date"> 11:01 AM | June 9</span>
                                </div>
                            </div>
                        </div>
                        <div v-for="message in messages">

                            <div v-if="message.user.id !== id" class="incoming_msg">



                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="user_name block ">@{{message.user.name }}</span>
                                    </div>
                                    <div class="col-md-12">

                                        <div class="incoming_msg_img">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png"
                                                alt="sunil">
                                        </div>
                                        <div class="received_msg">
                                            <div class="received_withd_msg">

                                                <p>@{{ message.message }}</p>
                                                <span class="time_date"> 11:01 AM | June 9</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div v-else class="outgoing_msg">

                                <div class="sent_msg">

                                    <p>@{{ message.message }}</p>
                                    <span class="time_date"> 11:01 AM | June 9</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <input v-model="message" @keyup.enter="sendMessage" type="text" class="write_msg"
                                placeholder="Type a message" />
                            <button @click="sendMessage" class="msg_send_btn" type="button"><i
                                    class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    
    <script>
    const appKey = '5a8730d454192d8e9f10';
    new Vue({
        el: "#app",
        data() {
            return {
                id:{{ auth()->id() }} ,
                message: "",
                users: [],
                messages: [],
                oldMessages: [],
            }
        },
        methods: {
            sendMessage() {
                axios.post('/message', {
                    message: this.message
                })
                this.message = ""
            },

        },
        mounted() {
            axios.get('/api/messages')
                .then(response => {
                    this.oldMessages = response.data;

                })
                .catch(error => {
                    console.error(error);
                });
            // pursher
            const appKey = '5a8730d454192d8e9f10';
            const ap1 = 'ap1';
            const echo = new Echo({
                broadcaster: 'pusher',
                key: appKey,
                cluster: ap1,
                encrypted: true
            });
            echo.join('chat')
                .here((users) => {
                    this.users = users;
                })
                .listen('MessageSent', (event) => {
                    this.messages.push(event);
                });

        },
    });
    </script>
</body>

</html>