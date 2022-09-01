<?php
include './auth.php';
if(checkAuth()){
    $name = json_decode($_COOKIE['infos'])->name;
}else{
    header("location: ./login.php");

}



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <title>Document</title>
</head>
<body>
    <?php
    include './front/style/main.php';
    include './front/style/body.php';
    include './front/header.php';
    echo setHeader("Home", $name);
    ?>

    <div class="chatWrapper">
        <div class="chat">

        </div>
        <div class="sendMessage">
            <textarea type="text" id="message" rows=4></textarea>
            <div style="position: relative"><div id="colorPicker"></div><input id="color" type="color"/></div>
            <button id="send">Envoyer</button>
        </div>
    </div>
    <script>
        (async()=>{
        Date.prototype.toString = function () { 
                return ((this.getDate()<10?"0"+this.getDate():this.getDate()) +"/"+ (this.getMonth()+1<10?"0"+(this.getMonth()+1):this.getMonth()+1) +"/"+ this.getFullYear());
            }
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }
        let chat = document.querySelector(".chat");
        let cookies = JSON.parse(decodeURIComponent(getCookie("infos")));
        console.log(cookies);

        let colorPicker = document.querySelector("#colorPicker");
        colorPicker.style.background= "#3e1943";
        let color = document.querySelector("#color");
        color.addEventListener("input", (e)=>{
            console.log(e.target.value);
            colorPicker.style.background= e.target.value;
            messages.messages =  messages.messages;
        })



        let messages=[];
        messages={
            messages_: [],
            get messages(){
                return this.messages_;
            },
            set messages(m){
                console.log("setMessage");
                this.messages_ = m;
                chat.innerHTML = "";
                m.map(message=>{
                let parent = document.createElement("div");
                parent.classList.add("parentWrapper");
                let wrapper = document.createElement("div");
                wrapper.classList.add("wrapperMessage");
                console.log("FROM ID ", messages)
                if(cookies.id == message.from_id){
                    wrapper.classList.add("me");
                }else{
                    wrapper.classList.add("others");
                }
                wrapper.style.background= colorPicker.style.background;
                let author = "";
                users.forEach(user=>{
                    if(user.id == message.from_id){
                        author = user.name;
                        
                    }
                })
                
                let d = new Date(parseInt(message.created_at));
                wrapper.innerHTML = `<div>${message.content} </div><div class="infos">${author} ${d.toString()}</div>`;
                parent.appendChild(wrapper);
                chat.appendChild(parent);
                console.log(wrapper);
                chat.scrollTop = chat.scrollHeight;
            })
            }
        }
        
        let users=[];
        await fetch("./dbUtils/findAll.php", {method: "POST",  headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table: "Users", where: null})})
        .then(e=>e.json())
        .then(e=>{console.log("users", e); users= e.data.values});

        await fetch("./dbUtils/findAll.php", {method: "POST",  headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table: "Messages", where: null})})
        .then(e=>e.json())
        .then(e=>{console.log("messages" ,e); messages.messages= e.data.values});

        

    //MESSAGE HANDLING
    let messageNode = document.querySelector("#message");
    let message = "";
    messageNode.addEventListener("input", (e)=>{
        message = e.target.value;
    })

    let button = document.querySelector("#send");
    button.addEventListener("click", ()=>{
        if(message.length > 0){
            console.log("sending message ...");
            fetch("./dbUtils/create.php", {method: "POST", headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table:"Messages", values:{content: message, from_id: cookies.id, created_at: new Date().getTime()}, pk: null})})
            .then(e=>e.text())
            .then(e=>{console.log(e); 
                let r ={from_id: cookies.id, content: message, created_at: new Date().getTime()};
                messages.messages = [...messages.messages, r];
                socket.send(JSON.stringify({type:"newMessage", data: r}));
        });
        }
    })
    let socket = new WebSocket('ws://localhost:8000');
        socket.onopen = function(e) {
            console.log("Connection established!");
            socket.send(JSON.stringify({type:"greeting", data: "hello"}));
        };

        socket.onmessage = function(e) {
            console.log(e.data);
            let msg = JSON.parse(e.data);
            console.log("new message ", msg);
            if(msg.type == "newMessage" && msg.data != "hello"){
                
                messages.messages =  [...messages.messages , msg.data]; 
            }
        };
        
    })()
    


    //SOCKET
    
       
        /*socket.onmessage = function(event) {
            let data = JSON.parse(event.data);
        console.log(`[message] Data received from server: ${event.data}`);
        };
       

        socket.onclose = function(event) {
        if (event.wasClean) {
            console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`);
        } else {
            // e.g. server process killed or network down
            // event.code is usually 1006 in this case
            console.log('[close] Connection died');
        }
        };

        socket.onerror = function(error) {
        console.log(`[error] ${error.message}`);
        };
        */
        //CREATE
        /*fetch("./dbUtils/create.php", {method: "POST", headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table:"Users", values:{name: "Louis"}, pk: "name"})})
        .then(e=>e.text())
        .then(e=>console.log(e));*/


        //UPDATE
        /*fetch("./dbUtils/update.php", {method: "POST", headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table:"Users", values:{name: "Jean-Eudes"}, where: {id: 2}})})
        .then(e=>e.json())
        .then(e=>console.log(e));
        */
        //FIND
        /*fetch("./dbUtils/find.php", {method: "POST", headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table:"Users", where: null})})
        .then(e=>e.json())
        .then(e=>console.log(e));*/
        
       //FINDALL
       /* fetch("./dbUtils/findAll.php", {method: "POST", headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table:"Users", where: null})})
        .then(e=>e.text())
        .then(e=>console.log(e));*/
        //DELETE
        /*fetch("./dbUtils/delete.php", {method: "POST", headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table:"Users", where: {id: 3}})})
        .then(e=>e.text())
        .then(e=>console.log(e));
        */
    </script>
</body>
</html>



