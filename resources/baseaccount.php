<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';
require realpath('../vendor/autoload.php');
    
class baseaccount {

private $query;

private $action;

private $login;

private $email;

private $senha;

private $token;

private $situacao;

private $motivo;

private $exists=false;

private $recovereleaser=false;

public function __construct($action,$email,$senha,$login,$situacao,$motivo,$token){
$this->action=$action;
/*Not working within no reason.
$this->senha=AesCtr::encrypt($senha,$senha,256);
*/
$this->senha=$senha;
$this->email=$email;
$this->login=$login;
$this->situacao=$situacao;
$this->motivo=$motivo;
$this->token=$token;
}

private function setCancelSearch(){
$query="SELECT users.foto,pedidos.numero,pedidos.login FROM users LEFT JOIN pedidos AS pedidos ON pedidos.login=users.login WHERE users.login=:login";
return $query;
}

private function setCancelParamSearch(baseaccount $obj){
$params=array();
$params["login"]=$obj->login;
return $params;
}

private function setSearch(){
$query="SELECT users.login,users.email,users.tipo,account.senha,account.token,account.date,account.status FROM users LEFT JOIN account AS account ON account.email=users.email WHERE users.email=:email";
return $query;
}

private function setParamSearch(baseaccount $obj){
$params=array();
$params["email"]=$obj->email;
return $params;
}

private function setPostSearch(){
$query="SELECT account.login,account.email,account.senha,account.token,account.date,account.status FROM account WHERE account.token=:token AND status=:status AND account.date>DATE_SUB(NOW(),INTERVAL 180 MINUTE)";
return $query;
}

private function setParamPostSearch(baseaccount $obj){
$params=array();
$params["token"]=$obj->token;
$params['status']="active";
return $params;
}

private function setInsert(baseaccount $obj){
$query="UPDATE account SET account.login=:login,account.senha=:senha,account.token=:token,account.date=now(),account.status=:status WHERE account.email=:email AND account.date<DATE_SUB(NOW(),INTERVAL 180 MINUTE)";
if($obj->exists===false){
$query="INSERT INTO account (login,senha,token,email,date) VALUES (:login,:senha,:token,:email,now())";
}
return $query;
}

private function setParamInsert(baseaccount $obj){
$params=array();
$params["login"]=$obj->login;
$params["senha"]=$obj->senha;
$params["token"]=$obj->token;
$params["email"]=$obj->email;
if($obj->exists===true){
$params["status"]="active";
}
return $params;
}

private function setUpdate(){
$query="UPDATE users SET tipo=:situacao WHERE login=:login AND email=:email";
return $query;
}

private function setParamUpdate(baseaccount $obj){
$params=array();
$params["situacao"]=$obj->situacao;
$params["login"]=$obj->login;
$params["email"]=$obj->email;
return $params;
}

private function setPostUpdate($obj){
$query="UPDATE users SET senha=:senha WHERE login=:login AND email=:email";
return $query;
}

private function setParamPostUpdate(baseaccount $obj){
$params=array();
$params["senha"]=$obj->senha;
$params["login"]=$obj->login;
$params["email"]=$obj->email;
return $params;
}

private function setReleaseUpdate(){
$query="UPDATE account SET status=:status WHERE token=:token";
return $query;
}

private function setParamReleaseUpdate(baseaccount $obj){
$params=array();
$params["status"]="released";
$params["token"]=$obj->token;
return $params;
}

private function setDelete(){
$query="DELETE FROM users WHERE login=:login AND email=:email";
return $query;
}

private function setParamDelete(baseaccount $obj){
$params=array();
$params["login"]=$obj->login;
$params["email"]=$obj->email;
return $params;
}

public function chama(baseaccount $obj){
$resposta=array();
if($obj->email!==""){
if($obj->action=="accountrecoversender" && $obj->senha!==""){
$resposta=$obj->recover_sender($obj);
}else if($obj->login!=="" && $obj->situacao!=="" && $obj->motivo!==""){
if($obj->action=="accountjointeam"){
$resposta=$obj->account_join($obj);
}else if($obj->action=="accountleaveteam"){
$resposta=$obj->account_leave($obj);
}else if($obj->action=="accountsuspend"){
$resposta=$obj->account_suspend($obj);
}else if($obj->action=="accountreactivate"){
$resposta=$obj->account_reactivate($obj);
}else if($obj->action=="accountcancel"){
$resposta=$obj->account_cancel($obj);
}
}
}
if($obj->action=="accountrecoverreceiver" && $obj->token!==""){
$resposta=$obj->recover_receiver($obj);
}
return $resposta;
}

private function recover_sender(baseaccount $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if($obj->email!==""){
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));

$resultset=$base->fetch();
if($resultset['email']==$obj->email){
$obj->login=$resultset['login'];
$id=$process->getNum_gen(8,true);
$obj->token=$id;
if($resultset['token']!==null){
$obj->exists=true;
}
$base->sql=$obj->setInsert($obj);
$base->commando($obj->setParamInsert($obj));
/*
Sem provedor de email. Devolver: https://serialliza.herokuapp.com/RecoveryDo".$obj->token.""
*/
$resposta['RESPONSE']="An email with the link to renew the password has been forwarded.";
}else{
$resposta['RESPONSE']="Incorrect email.";
}
}else{
$resposta['RESPONSE']="Invalid email.";
}
return $resposta;
}

private function recover_receiver(baseaccount $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$base=new database();
$base->sql=$obj->setPostSearch();
$base->commando($obj->setParamPostSearch($obj));
$resultset=$base->fetch();
if($resultset['token']==""){
$resposta['RESPONSE']="Expired token.";
}else{
$obj->senha=$resultset['senha'];
$obj->login=$resultset['login'];
$obj->email=$resultset['email'];
$base->sql=$obj->setPostUpdate($obj);
$base->commando($obj->setParamPostUpdate($obj));
$base->sql=$obj->setReleaseUpdate();
$base->commando($obj->setParamReleaseUpdate($obj));
$resposta['RESPONSE']="Changed password.";
}
return $resposta;
}

private function account_join(baseaccount $obj){
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
if($decryptlogin==$gotlogin && $tipo=="administrador"){
$resposta['ACTOR']=$decryptlogin;
if($obj->email!==""){
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset['email']==$obj->email){
if($resultset['tipo']!=="newuser"){
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$assunto="Entry into the management.";
$numero=0;
$resumo="Information";
$msgobj=new basemessages("messagescreator",$numero,$gotlogin,$obj->login,$assunto,$obj->motivo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if($msgobjresp['RESPONSE']=="Message sent successfully."){
$resposta['RESPONSE']="Manager enabled.";
}else{
$resposta['RESPONSE']="Manager enabled but failed to communicate.";
}
}else{
$resposta['RESPONSE']="It is impossible to hire before the user data is filled out and the registration confirmed.";    
}
}else{
$resposta['RESPONSE']="Incorrect email.";
}
}else{
$resposta['RESPONSE']="Invalid e-mail.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function account_leave(baseaccount $obj){
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
if($decryptlogin==$gotlogin && $tipo=="administrador"){
$resposta['ACTOR']=$decryptlogin;
if($obj->email!==""){
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset['email']==$obj->email){
if($resultset['tipo']!=="newuser"){
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$assunto="Departure from the management staff.";
$numero=0;
$resumo="Information";
$msgobj=new basemessages("messagescreator",$numero,$gotlogin,$obj->login,$assunto,$obj->motivo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if($msgobjresp['RESPONSE']=="Message sent successfully."){
$resposta['RESPONSE']="Manager disabled.";
}else{
$resposta['RESPONSE']="Manager disabled but failed to communicate.";
}
}else{
$resposta['RESPONSE']="It is impossible to dismiss before the user's data is filled out and the registration confirmed.";    
}
}else{
$resposta['RESPONSE']="Incorrect email.";
}
}else{
$resposta['RESPONSE']="Invalid e-mail.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function account_suspend(baseaccount $obj){
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
if($decryptlogin==$gotlogin && $tipo=="administrador"){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$assunto="Suspensão de acesso.";
$numero=0;
$resumo="Informação";
$msgobj=new basemessages("messagescreator",$numero,$gotlogin,$obj->login,$assunto,$obj->motivo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if($msgobjresp['RESPONSE']=="Message sent successfully."){
$resposta['RESPONSE']="Client suspended.";
}else{
$resposta['RESPONSE']="Client suspended but failed to communicate.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function account_reactivate(baseaccount $obj){
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
if($decryptlogin==$gotlogin && $tipo=="administrador"){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$assunto="Access reactivation.";
$numero=0;
$resumo="Information";
$msgobj=new basemessages("messagescreator",$numero,$gotlogin,$obj->login,$assunto,$obj->motivo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if($msgobjresp['RESPONSE']=="Message sent successfully."){
$resposta['RESPONSE']="Customer reactivated.";
}else{
$resposta['RESPONSE']="Client reactivated but failed to communicate.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function account_cancel(baseaccount $obj){
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
if($decryptlogin==$gotlogin && $tipo=="administrador"){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$obj->setCancelSearch();
$base->commando($obj->setCancelParamSearch($obj));
$resultset=$base->fetchAll();
if($resultset[0]['numero']==null){
$base->sql=$obj->setDelete();
$base->commando($obj->setParamDelete($obj));
$assunto="Cancellation of access.";
$numero=0;
$resumo="Information";
$msgobj=new basemessages("messagescreator",$numero,$gotlogin,$obj->login,$assunto,$obj->motivo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if(file_exists('../storage/data/images/users/'.$obj->login)){
$foto=$resultset[0]['foto'];
if(file_exists('../storage/data/images/users/'.$obj->login."/".$foto)){
unlink('../storage/data/images/users/'.$obj->login."/".$foto);
}
}
if($msgobjresp['RESPONSE']=="Message sent successfully."){
$resposta['RESPONSE']="Cliente cancelado.";
}else{
$resposta['RESPONSE']="Customer canceled.";
}
}else{
$resposta['RESPONSE']="Client cannot be cancelled as long as there are open orders. Contact your administrator to cancel orders.";
}
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
