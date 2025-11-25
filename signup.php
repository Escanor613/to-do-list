<?php
$name=$_POST["name"] ?? '';
$email=$_POST["email"] ?? '';
$pass=$_POST["password"] ?? '';

if(!$name || !$email || !$pass){
    echo "missing fields";
    exit;
}
$conx=new mysqli("localhost","root","","todo");
if($conx->connect_error){
    die('Connection failed:'.$conx->connect_error);
}

$hashedpass=password_hash($pass,PASSWORD_DEFAULT);

$checkemail=$conx->prepare('select email from sign where email=?');
$checkemail->bind_param("s",$email);
$checkemail->execute();
$checkemail->store_result();
$checkemail->bind_result($resemail);
if($checkemail->fetch()){
    echo "email already exist";
    exit;
}

$req=$conx->prepare("insert into sign (name,email,password) values (?,?,?)");
$req->bind_param("sss",$name,$email,$hashedpass);
$req->execute();

if($req->affected_rows > 0){
    session_start();
    $_SESSION['name']=$name;
    $_SESSION['email']=$email;
    echo "User added successfully!";
}else{
    echo "Failed to add user.";
}
$req->close();
$conx->close();



?>