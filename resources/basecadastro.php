<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class basecadastro {
	
private $query;

private $login;

private $nome;

private $senha;

private $email;

private $conditions;

public function __construct($login,$nome,$senha,$email,$conditions){
$this->login=$login;
$this->nome=$nome;
/*Not working within no reason.
$this->senha=AesCtr::encrypt($senha,$senha,256);
*/
$this->senha=$senha;
$this->email=$email;
$this->conditions=$conditions;
}	
	
private function setSearch(){
$query="SELECT * FROM users WHERE login=:login OR email=:email";
return $query;
}

private function setParamSearch(basecadastro $obj){
$params=array();
$params["login"]=$obj->login;
$params["email"]=$obj->email;
return $params;
}


private function setInsert(){
$query="INSERT INTO users (login,nome,senha,email,conditions) VALUES (:login,:nome,:senha,:email,:conditions)";
return $query;
}

private function setParamInsert(basecadastro $obj){
$params=array();
$params["login"]=$obj->login;
$params["nome"]=$obj->nome;
$params["senha"]=$obj->senha;
$params["email"]=$obj->email;
$params["conditions"]=$obj->conditions;
return $params;
}
		
public function chama(basecadastro $obj){
$resposta=array();
if($obj->login!=="" && $obj->nome!=="" && $obj->senha!=="" && $obj->email!=="" && $obj->conditions!==""){
$resposta=$obj->cadastro($obj);
}
return $resposta;
}

private function cadastro(basecadastro $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset==null){
$id=$process->getNum_gen(8,true);
$signature=$obj->login."*".$id."*".$process->getGetDatetimeNow("complex")."#newuser";
$encryptauth=AesCtr::encrypt($signature,$process->getPassOfCookie(),256);
$encryptid=AesCtr::encrypt($id,$process->getPassOfCookie(),256);
$obj->setParamInsert($obj);
$base->sql=$obj->setInsert();
$base->commando($obj->setParamInsert($obj));
$resposta['LOGIN']=$obj->login;
$encryptlogin=AesCtr::encrypt($obj->login,$process->getPassOfCookie(),256);
setcookie("id",$encryptid);
setcookie("login",$encryptlogin);
setcookie("auth",$encryptauth);
}else{
$resposta['ERROR']="Registration ".$obj->login." already exists, choose another login or email";
}
return $resposta;
}

}

?>