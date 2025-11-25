<?php
session_start();
if(!isset($_SESSION['email'])){
    http_response_code(401);
    echo 'unauthorized';
    exit;
}
$email=$_SESSION['email'];
$task=$_POST['task'];
$deadline=$_POST['deadline'];

if($_SERVER['REQUEST_METHOD']==="POST" && !empty($task) && !empty($deadline)){

    $conx=new mysqli("localhost","root","","todo");
    if($conx->connect_error){
        http_response_code(500);
        die('connexion failed:'. $conx->connect_error);
    }
    $reqid=$conx->prepare("delete from tasks 
    where task=? and deadline=? and iduser in (select iduser from sign where email=?)");
    $reqid->bind_param("sss",$task,$deadline,$email);
    $reqid->execute();
    if($reqid->affected_rows>0){
        echo "task deleted successfully!";
    }else{
        echo "task not found";
    }
    $reqid->close();
    $conx->close();
}else{
    echo "missing";
}

?>