<?php
function checkAuth(){
    if(array_key_exists("auth", $_COOKIE)){

        return true;

    }else{
        return false;
    }
}
?>