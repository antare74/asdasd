@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card mt-3">
                <div class="card-body">
                    <!-- @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form action="{{ route('send.web-notification') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Message Title</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="form-group">
                            <label>Message Body</label>
                            <textarea class="form-control" name="body"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Send Notification</button>
                    </form> -->

                    <form>
                        <textarea id="textarea" rows=10></textarea>
                        <br/>
                        <input id="message"/>
                        <button type="button" onclick="sendMessage()">Send</button>
                        <button type="button" onclick="reconnect()">reconnect</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</script>
@endsection
@section('script')
<script type=module>
    // import { AMQPWebSocketClient } from './js/amqp-websocket-client.mjs'

    //   const textarea = document.getElementById("textarea")
    //   const input = document.getElementById("message")

    //   const tls = window.location.scheme === "https:"
    //   const url = `${tls ? "wss" : "ws"}://0.0.0.0:15675/ws`
    //   const amqp = new AMQPWebSocketClient(url, "/", "guest", "guest")

    //   let c = amqp.connect()
    //   c.onmessage = (msg) => {
    //     alert(msg.data)
    //     textarea.value += msg.data + "\n"
    //   }

    //   async function start() {
    //     try {
    //       const conn = await amqp.connect()
    //       console.log("Connected", conn)
    //       const ch = await conn.channel()
    //       attachPublish(ch)
    //       const q = await ch.queue("hello")
    //       await q.bind("amq.fanout", "")
    //       const consumer = await q.subscribe({noAck: false}, (msg) => {
    //         console.log(msg)
    //         textarea.value += msg.bodyToString() + "\n"
    //         msg.ack()
    //       })
    //     } catch (err) {
    //       console.error("Error", err, "reconnecting in 1s")
    //       disablePublish()
    //       setTimeout(start, 1000)
    //     }
    //   }

    //   function attachPublish(ch) {
    //     document.forms[0].onsubmit = async (e) => {
    //       e.preventDefault()
    //       try {
    //         await ch.basicPublish("amq.fanout", "", input.value, { contentType: "text/plain" })
    //       } catch (err) {
    //         console.error("Error", err, "reconnecting in 1s")
    //         disablePublish()
    //         setTimeout(start, 1000)
    //       }
    //       input.value = ""
    //     }
    //   }

    //   function disablePublish() {
    //     document.forms[0].onsubmit = (e) => { alert("Disconnected, waiting to be reconnected") }
    //   }

    //   start()
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>
<!-- <script src="./js/mqttws31.min.js" type="text/javascript"></script> -->
<script>
    const credentials = {
                onSuccess:onConnect,
                userName: "guest",
                password: "guest",
                useSSL: false
            }
    console.log("myclientid_" + parseInt(Math.random() * 100, 10))
        // Create a client instance
        // client = new Paho.MQTT.Client(location.hostname, Number(location.port), "clientId");
        var client = new Paho.MQTT.Client("localhost", 15675, "/ws", `client_${parseInt(Math.random() * 100, 10)}`);
        // connect the client
        client.connect(credentials);

        // set callback handlers
        client.onConnectionLost = onConnectionLost;
        client.onMessageArrived = onMessageArrived;

        // called when the client connects
        function onConnect() {
        // Once a connection has been made, make a subscription and send a message.
            // console.log("onConnect");
            client.subscribe("communities/event/3");
            // message = new Paho.MQTT.Message("Hello");
            // message.destinationName = "rt";
            // client.send(message);
        }

        function sendMessage() {
            let inputMessage = document.getElementById("message").value;
            console.log(inputMessage);
            message = new Paho.MQTT.Message(inputMessage);
            message.destinationName = "onegaisho";
            client.send(message);
        }

        // called when the client loses its connection
        function onConnectionLost(responseObject) {
            reconnect();
            if (responseObject.errorCode !== 0) {
                console.log("onConnectionLost:"+responseObject.errorMessage);
            }
        }

        // called when a message arrives
        function onMessageArrived(message) {
            let msg = isJsonString(message.payloadString)? JSON.parse(message.payloadString) : message.payloadString;
            console.log(msg);
            // console.log("onMessageArrived:"+message.payloadString);
        }

        function isJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        function toJSObject(json) {
            return JSON.parse(json);
        }

        function reconnect() {
            console.log("Reconnecting...");
            client.connect({
                onSuccess:onConnect,
                userName: "guest",
                password: "guest",
                useSSL: false
            });
        }
</script>
@endsection
