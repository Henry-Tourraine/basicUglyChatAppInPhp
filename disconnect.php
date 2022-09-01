<?php

    setcookie("auth", "", time()-3600, '/');
    setcookie("infos", "", time()-3600, '/');
    header("Location: ./");
?>