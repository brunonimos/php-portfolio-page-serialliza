<?php

require realpath('../vendor/autoload.php');
require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class basesessao {

private $query;

private $id;

private $login;

private $decryplogin;

private $auth;

public function __construct($id,$login,$auth){
$this->id=$id;
$this->login=$login;
$this->auth=$auth;
}

public function chama(basesessao $obj){
$resposta=$obj->sessao($obj);
return $resposta;
}

private function sessao(basesessao $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if($obj->login!==null && $obj->id!==null && $obj->auth!==null){
$resposta['NOTIFIED']="already";
$obj->decryplogin=AesCtr::decrypt($obj->login,$process->getPassOfCookie(),256);
$decryptid=AesCtr::decrypt($obj->id,$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($obj->auth,$process->getPassOfCookie(),256);
$explode=explode("*",$decryptauth);
$gotlogin=$explode[0];
$gotid=$explode[1];
$gotaccesstime=$explode[2];
$tipoexplode=explode("#",$decryptauth);
$gottipo=$tipoexplode[1];
if($obj->decryplogin==$gotlogin && $decryptid==$gotid){
$resposta['ID']=$obj->id;
$resposta['LOGIN']=$obj->decryplogin;
if($gottipo=="newuser" || $gottipo=="suspended"){
$msgobj=new basemessages("messagesloader",null,null,$obj->decryplogin,null,null,null,null);
$msgobjresp=$msgobj->chama($msgobj);
$msgcount=0;
if(empty($msgobjresp['RESPONSE'])==false){
foreach($msgobjresp['RESPONSE'] as $key => $value){
if($resposta['LOGIN']==$value['login'] && $value['notificacao']=="unread"){
$msgcount++;
}
}
$resposta['UNREAD']=$msgcount;
}
if($gottipo=="newuser"){
$resposta['ERROR']="Welcome ".$gotlogin;
}else if($gottipo=="suspended"){
$resposta['ERROR']="Your access has been suspended or cancelled. The data may be stored for audit purposes for a period of no later than 12 months after cancellation as agreed in the terms of use.";
}
}else if($gottipo=="usuario" || $gottipo=="gestor" || $gottipo=="administrador"){
$msgobj=new basemessages("messagesloader",null,null,$obj->decryplogin,null,null,null,null);
$msgobjresp=$msgobj->chama($msgobj);
$resposta['ORDER']=true;
$msgcount=0;
if(empty($msgobjresp['RESPONSE'])==false){
foreach($msgobjresp['RESPONSE'] as $key => $value){
if($resposta['LOGIN']==$value['login'] && $value['notificacao']=="unread"){
$msgcount++;
}
}
$resposta['UNREAD']=$msgcount;
}
if($gottipo=="administrador"){
$resposta['ADMTOOLS']=$gottipo;
}
if($gottipo=="gestor"){
$resposta['GESTORTOOLS']=$gottipo;
}
}
session_start();
$_SESSION['ID']=$obj->id;
$_SESSION['LOGIN']=$obj->decryplogin;
}else{
$resposta['ERROR']="Session expired, please sign in again.";
}
}else{
$resposta['ERROR']="guest";
if(isset($_COOKIE["notified"]) && $_COOKIE["notified"]=="already"){
$resposta['NOTIFIED']=$_COOKIE["notified"];
}else{
setcookie("notified","already");
$resposta['NOTIFIED']="not";
}
}
return $resposta;
}

}

?>
