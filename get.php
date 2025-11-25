<?php
session_start();
if(!isset($_SESSION['email'])){
    http_response_code(401);
    echo 'unauthorized';
    exit;
}

$email=$_SESSION['email'];

$conx=new mysqli("localhost","root","","todo");
if($conx->connect_error){
    http_response_code(500);
    die('connexion failed:'. $conx->connect_error);
}

$reqid=$conx->prepare("select iduser from sign where email=?");
$reqid->bind_param("s",$email);
$reqid->execute();
$reqid->store_result();
$reqid->bind_result($iduser);
if($reqid->fetch()){
    $reqget=$conx->prepare('select task,deadline from tasks where iduser=?');
    $reqget->bind_param("i",$iduser);
    $reqget->execute();
    $reqget->bind_result($task,$deadline);
    while($reqget->fetch()){
        echo "$task|$deadline\n";
    }
    $reqget->close();
}else{
    http_response_code(404);
    echo 'user not found';
}
$reqid->close();
$conx->close();

?>