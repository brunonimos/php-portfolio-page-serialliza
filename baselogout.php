<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class baselogout {

private $query;

private $validate;

private $id;

private $login;

private $auth;

public function __construct($validate,$id,$login,$auth){
$this->validate=$validate;
$this->id=$id;
$this->login=$login;
$this->auth=$auth;
}

public function chama(baselogout $obj){
$resposta=$obj->logout($obj);
return $resposta;
}
	
private function logout(baselogout $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if($obj->validate!==null && $obj->id!==null && $obj->login!==null && $obj->auth!==null){
$decryptid=AesCtr::decrypt($obj->id,$process->getPassOfCookie(),256);
$decryptvalidate=AesCtr::decrypt($obj->validate,$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($obj->auth,$process->getPassOfCookie(),256);
$explode=explode("*",$decryptauth);
$gotlogin=$explode[0];
$gotid=$explode[1];
if($decryptid==$decryptvalidate && $decryptvalidate==$gotid){
setcookie('login','',time()-999999);
setcookie('id','',time()-999999);
setcookie('auth','',time()-999999);
setcookie('status','',time()-999999);
unset($_COOKIE['login']);
unset($_COOKIE['id']);
unset($_COOKIE['auth']);
unset($_COOKIE['status']);
session_unset();
$resposta['LOGOUT']="Logout successfully accomplished.";
}else{
$resposta['ERROR']="Access denied.";
}
}else{
$resposta['ERROR']="Access denied.";
}
return $resposta;
}

}
	
?>