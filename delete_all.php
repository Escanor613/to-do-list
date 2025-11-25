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

$req=$conx->prepare("delete from tasks 
where iduser in (select iduser from sign where email=?)");
$req->bind_param("s",$email);
$req->execute();
if($req->affected_rows>0){
    echo "all tasks deleted successfully!";
}else{
    echo "task not found";
}
$req->close();
$conx->close();

?>