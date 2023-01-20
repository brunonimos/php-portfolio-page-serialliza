<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class baselogin {
	
private $query;

private $login;

private $senha;

private $tipo;

public function __construct($login,$senha,$tipo){
$this->login=$login;
$this->senha=$senha;
$this->tipo=$tipo;
}

private function setSearch(){
$query="SELECT * FROM users WHERE login=:login";
return $query;
}

private function setParamSearch(baselogin $obj){
$params=array();
$params["login"]=$obj->login;
return $params;
}
		
public function chama(baselogin $obj){
$resposta=array();
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset!==null && is_array($resultset) && isset($resultset['login']) && isset($resultset['senha'])){
/*Not working within no reason.
$senha=AesCtr::decrypt($resultset['senha'],$obj->senha,256);
*/
$senha=$resultset['senha'];
if($obj->senha==$senha){
$obj->tipo=$resultset['tipo'];
$resposta=$obj->signin($obj,$base);
}else{
$resposta['ERROR']="Incorrect login ".$obj->login." senha ".$senha." e senha ".$obj->senha.".";
}
}else{
$resposta['ERROR']="Incorrect login ".$obj->login." or password, sign up to access.";
}
return $resposta;
}

private function signin(baselogin $obj,database $base){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$id=$process->getNum_gen(8,true);
$signature=$obj->login."*".$id."*".$process->getGetDatetimeNow("complex")."#".$obj->tipo;
$encryptauth=AesCtr::encrypt($signature,$process->getPassOfCookie(),256);
$encryptid=AesCtr::encrypt($id,$process->getPassOfCookie(),256);
$encryptlogin=AesCtr::encrypt($obj->login,$process->getPassOfCookie(),256);
setcookie("id",$encryptid);
setcookie("login",$encryptlogin);
setcookie("auth",$encryptauth);
$resposta['LOGIN']=$obj->login;
return $resposta;
}

}

?>