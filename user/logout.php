<?php
session_start();
if(!isset($_SESSION['user']['accessToken'])){
     header("Location: login");
}
else{
    $access_token = "";
    session_destroy();
    header("Location: login");
}
?>