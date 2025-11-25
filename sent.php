<?php
session_start();
if(!isset($_SESSION['email'])){
    http_response_code(401);
    echo "unauthorized";
    exit;
}

if($_SERVER['REQUEST_METHOD']==="POST"){

    $email=$_SESSION['email'];
    $task=$_POST['task'];
    $deadline=$_POST['deadline'];

    if(!empty($task) && !empty($deadline)){
        $conx=new mysqli("localhost","root","","todo");
        if($conx->connect_error){
        die('connexion failed:'. $conx->connect_error);
        }

        $reqid=$conx->prepare("select iduser from sign where email=?");
        $reqid->bind_param("s",$email);
        $reqid->execute();
        $reqid->store_result();
        $reqid->bind_result($iduser);
        if($reqid->fetch()){
            $reqadd=$conx->prepare('insert into tasks (iduser,task,deadline) values (?,?,?)');
            $reqadd->bind_param("iss",$iduser,$task,$deadline);
            $reqadd->execute();
            echo "Task added successfully!";
            $reqadd->close();
        }else{
            http_response_code(404);
            echo 'user not found';
        }
        $reqid->close();
        $conx->close();
    }else{
        http_response_code(400);
        echo 'missing fields';
    }
    
}

?>