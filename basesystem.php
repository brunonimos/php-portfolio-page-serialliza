<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class basesystem {
	
private $query;

private $login;

private $empresa;

private $cnpj;

private $endereco;

private $email;

private $agencia;

private $conta;

private $digito;

private $cedente;

private $registro;

private $taxa;

private $senha;

private $action;

public function __construct($action,$empresa,$cnpj,$endereco,$email,$agencia,$conta,$digito,$cedente,$registro,$taxa,$senha){
$this->action=$action;
$this->empresa=$empresa;
$this->cnpj=$cnpj;
$this->endereco=$endereco;
$this->email=$email;
$this->agencia=$agencia;
$this->conta=$conta;
$this->digito=$digito;
$this->cedente=$cedente;
$this->registro=$registro;
$this->taxa=$taxa;
$this->senha=$senha;
}

private function setUpdate(){
$query="UPDATE internal SET superlogin=:login,empresa=:empresa,cnpj=:cnpj,superendereco=:endereco,superemail=:email,agencia=:agencia,conta=:conta,digito=:digito,cedente=:cedente,registro=:registro,taxa=:taxa,supersenha=:senha WHERE tipo=:tipo";
return $query;
}

private function setParamUpdate(basesystem $obj){
$params=array();
$params["login"]=$obj->login;
$params["empresa"]=$obj->empresa;
$params["cnpj"]=$obj->cnpj;
$params["endereco"]=$obj->endereco;
$params["email"]=$obj->email;
$params["agencia"]=$obj->agencia;
$params["conta"]=$obj->conta;
$params["digito"]=$obj->digito;
$params["cedente"]=$obj->cedente;
$params["registro"]=$obj->registro;
$params["taxa"]=$obj->taxa;
$params["senha"]=$obj->senha;
$params["tipo"]="administrador";
return $params;
}
		
private function setSearch(){
$query="SELECT internal.supersenha FROM internal WHERE tipo=:tipo";
return $query;
}

private function setParamSearch(basesystem $obj){
$params=array();
$params["tipo"]="administrador";
return $params;
}
        
public function chama(basesystem $obj){
$resposta=array();
if($obj->action=="systemload"){
$resposta=$obj->system_loader($obj);
}else if($obj->action=="systemconfig"){
$resposta=$obj->system_config($obj);
}
return $resposta;
}

private function system_loader(basesystem $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$gotlogin=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$gotlogin){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$this->setSearch();
$base->commando($this->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset!==null && is_array($resultset)){
$resposta['LOADED']=$resultset;
}
}else{
$resposta['LOADED']="Access denied.";
}
}else{
$resposta['LOADED']="Access denied.";
}
return $resposta;
}

private function system_config(basesystem $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$obj->login=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$obj->login && $tipo=="administrador"){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$resposta['RESPONSE']="Data changed successfully.";
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

}

?>