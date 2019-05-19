<?php
// Função que conecta com o banco.
function cnt(){
try{
	$con=new PDO("mysql:host=localhost;dbname=exemplo","root","", array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
}
catch(PDOException $e){
	echo $e->getmessage();
}
	return $con;
}
// URL
$url = 'http://localhost/exemplo';

// Função join.php > index.php.
function in(){
if(!empty($_SESSION["email"])):
	header('Location: '.$url.'/settings/products.php');
else:
	return false;
endif;
}
// Função index.php > join.php.
function out(){
if(empty($_SESSION["email"])):
	header('Location: '.$url.'/login');
else:
	return false;
endif;
}


?>