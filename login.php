<?php
$email=$_POST['email'] ?? '';
$pass=$_POST['password'] ?? '';

if(!$email || !$pass){
    echo "missing fields";
    exit;
}

$conx=new mysqli("localhost","root","","todo");
if($conx->connect_error){
    die("connection failed:" . $conx->connect_error);
}

$req=$conx->prepare("select name,password from sign where email=?");
$req->bind_param('s',$email);
$req->execute();
$req->store_result();
$req->bind_result($name,$hashedpass);
if($req->fetch()){
    if(password_verify($pass,$hashedpass)){
        session_start();
        $_SESSION['name']=$name;
        $_SESSION['email']=$email;
        echo "login successful!";
    }else{
        echo "Wrong password.";
    }
}else{
    echo "No user found with this email.";
}


$req->close();
$conx->close();

?>