<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
    <?php 
        include './front/style/login.php';
        include './front/style/body.php';
        include './front/header.php';
        echo setHeader("Login");
        
    
    ?>
    <div class="form">
        <div class="inputWrapper">
            
            <input type="text" name="name" placeholder="jean" autocomplete="no">
            <label for="name">Your name</label>
        </div>
        <div id="nameError"></div>
        <div class="inputWrapper">
            
            <input type="text" name="pwd" placeholder="3.14159265" autocomplete="no">
            <label for="pwd">Your pwd</label>
        </div>
        <div id="pwdError"></div>

        <button id="submit" class="buttonDisabled">S'inscrire</button>
        <div id="queryMessage"></div>
    </div>

    <script>
        let button = document.getElementById("submit");
        
        class Element{
            constructor(mainNode, errorNode, min){
                this.main = mainNode;
                this.error = errorNode;
                this.value_ = null;
                this.min = min;
            }
            get value(){
                return this.value_;
            }
            set value(v){
                this.value_=v;
                if(v.length < this.min){
                    button.disabled= true;
                    if(!button.classList.contains("buttonDisabled")){
                        button.classList.add("buttonDisabled"); 
                        button.classList.remove("buttonEnabled"); 
                    }
                    this.error.textContent = "too short";
                    if(!this.error.classList.contains("error")){
                        this.error.classList.add("error"); 
                    }
                    if(this.main.classList.contains("exact")){
                        this.main.classList.remove("exact"); 
                    }
                }else{
                    button.disabled= false;
                    if(!button.classList.contains("buttonEnabled")){
                        button.classList.remove("buttonDisabled"); 
                        button.classList.add("buttonEnabled"); 
                    }
                     
                    if(this.error.classList.contains("error")){
                        this.error.classList.remove("error"); 
                    }
                    if(!this.main.classList.contains("exact")){
                        this.main.classList.add("exact"); 
                    }
                    this.error.textContent = "";
                }
            }

        }
        let name = new Element(document.querySelector("[name='name']"), document.querySelector("#nameError"), 3);
        console.log(name);
        let pwd = new Element(document.querySelector("[name='pwd']"), document.querySelector("#pwdError"), 8);
        
        name.main.addEventListener("input", (e)=>{
            name.value = e.target.value;
        })
        pwd.main.addEventListener("input", (e)=>{
            pwd.value = e.target.value;
        })
        let queryMessage = document.querySelector("#queryMessage");
        button.addEventListener("click", ()=>{
            fetch("./dbUtils/signin.php", {method: "POST", headers: {'Content-Type': "application/json", 'Accept': "application/json"}, body: JSON.stringify({table:"Users", name: name.value, pwd: pwd.value})})
            .then(e=>e.json())
            .then(e=>{console.log(e);
                    if(e.message == true){
                        queryMessage.textContent = "Vous êtes connecté !";
                        setTimeout(()=>{queryMessage.textContent = "";window.location.href = "./";}, 2000);
                        
                    }else{
                        queryMessage.textContent = "Vous n'avez pas pu vous logger!"
                        setTimeout(()=>{queryMessage.textContent = ""}, 2000);
                    }
                });
            })
    </script>
</body>
</html>