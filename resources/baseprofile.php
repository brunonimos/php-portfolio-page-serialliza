<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class baseprofile {
	
private $query;

private $action;

private $login;

private $nome;

private $cpf;

private $rg;

private $email;

private $telefone;

private $endereco;

private $bio;

private $foto;

private $filetype;

private $tipo;

public function __construct($action,$nome,$cpf,$rg,$email,$telefone,$endereco,$bio,$foto){
$this->action=$action;
$this->nome=$nome;
$this->cpf=$cpf;
$this->rg=$rg;
$this->email=$email;
$this->telefone=$telefone;
$this->endereco=$endereco;
$this->bio=$bio;
$this->foto=$foto;
}

private function setUpdate(baseprofile $obj){
$query="UPDATE users SET nome=:nome, cpf=:cpf, rg=:rg, email=:email, telefone=:telefone, endereco=:endereco, bio=:bio, foto=:foto, tipo=:tipo WHERE login=:login";
if($obj->tipo=="administrador" || $obj->tipo=="gestor" || $obj->tipo=="usuario"){
$query="UPDATE users SET nome=:nome, email=:email, telefone=:telefone, endereco=:endereco, bio=:bio, foto=:foto WHERE login=:login";
}
return $query;
}

private function setParamUpdate(baseprofile $obj){
$params=array();
$params["nome"]=$obj->nome;
if($obj->tipo=="newuser"){
$params["cpf"]=$obj->cpf;
$params["rg"]=$obj->rg;
if($obj->nome!=="" && $obj->cpf!=="" && $obj->rg!=="" && $obj->endereco!==""){
$params["tipo"]="usuario";
}else{
$params["tipo"]=$obj->tipo;
}
if($params["cpf"]!=="" && $params["rg"]!==""){
$object=["object"=>$obj];
$process=new process($object);
$id=$process->getNum_gen(8,true);
$signature=$obj->login."*".$id."*".$process->getGetDatetimeNow("complex")."#".$params["tipo"];
$encryptauth=AesCtr::encrypt($signature,$process->getPassOfCookie(),256);
$encryptid=AesCtr::encrypt($id,$process->getPassOfCookie(),256);
$encryptlogin=AesCtr::encrypt($obj->login,$process->getPassOfCookie(),256);
setcookie("id",$encryptid);
setcookie("login",$encryptlogin);
setcookie("auth",$encryptauth);
}
}
$params["email"]=$obj->email;
$params["telefone"]=$obj->telefone;
$params["endereco"]=$obj->endereco;
$params["bio"]=$obj->bio;
$params["foto"]=$obj->foto['name'];
$params["login"]=$obj->login;
return $params;
}
		
public function chama(baseprofile $obj){
$resposta=array();
if($obj->action=="profileditor"){
$resposta=$obj->profile_editor($obj);
}
return $resposta;
}

private function profile_editor(baseprofile $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['id']) && isset($_COOKIE['login']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$obj->login=$decryptlogin;
$resposta['ACTOR']=$decryptlogin;
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$obj->tipo=$tipoexplode[1];
}
$iscpfvalid=$obj->cpfvalidator($obj->cpf);
if($obj->login!==""){
if($iscpfvalid===true){
$base=new database();
if($obj->foto['tmp_name']!==""){
$allowedextensions=array("image/jpeg","image/jpg","image/png");
$allowedsize=2097152;
$obj->filetype=explode("/",$obj->foto['type']);
if(!file_exists("../storage/data/images/users/".$obj->login)){
mkdir("../storage/data/images/users/".$obj->login,0777,true);
}
if($obj->filetype[0]=="image" && in_array($obj->foto['type'],$allowedextensions)===true && $obj->foto['size']<$allowedsize){
$obj->foto['name']=$obj->login.".".$obj->filetype[1];
$uploadfile="../storage/data/images/users/".$obj->login."/".$obj->foto['name'];
move_uploaded_file($obj->foto['tmp_name'],$uploadfile);
if(file_exists($uploadfile)){
$base->sql=$obj->setUpdate($obj);
$base->commando($obj->setParamUpdate($obj));
$resposta['RESPONSE']="Data changed.";
}
}else{
$resposta['RESPONSE']="Image format or size not allowed. Please choose a JPEG, JPG or PNG image smaller than 2 MB.";
}
}else{
$base->sql=$obj->setUpdate($obj);
$base->commando($obj->setParamUpdate($obj));
$resposta['RESPONSE']="Data changed.";
}
}else{
$resposta['RESPONSE']="Invalid CPF";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function cpfvalidator($cpf){
if(empty($cpf)){
return false;
}
$cpf=preg_replace("/[^0-9]/","",$cpf);
$cpf=str_pad($cpf,11,'0',STR_PAD_LEFT);
if(strlen($cpf)!=11){
return false;
}else if(preg_match('/(\d)\1{10}/',$cpf)){
return false;
}else{
for($t=9;$t<11;$t++){
for($d=0,$c=0;$c<$t;$c++){
$d+=$cpf[$c]*(($t+1)-$c);
}
$d=((10*$d) % 11) % 10;
if($cpf[$c]!=$d){
return false;
}
}
return true;
}
}

}

?>