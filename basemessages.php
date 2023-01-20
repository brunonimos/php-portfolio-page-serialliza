<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class basemessages {
	
private $query;

private $action;

private $pedido;

private $remetente;

private $destinatario;

private $assunto;

private $conteudo;

private $resumo;

private $email;

private $id;

public function __construct($action,$pedido,$remetente,$destinatario,$assunto,$conteudo,$resumo,$id){
$this->action=$action;
$this->pedido=$pedido;
$this->remetente=$remetente;
$this->destinatario=$destinatario;
$this->assunto=$assunto;
$this->conteudo=$conteudo;
$this->resumo=$resumo;
$this->id=$id;
}

private function setSearch(){
$query="SELECT messages.id,messages.pedido,messages.remetente,messages.destinatario,messages.assunto,messages.conteudo,messages.date,messages.notificacao,users.login,users.tipo FROM messages INNER JOIN users WHERE messages.destinatario=:destinatario AND users.login=:login";
return $query;
}

private function setParamSearch(basemessages $obj){
$params=array();
$params["login"]=$obj->destinatario;
$params["destinatario"]=$obj->destinatario;
return $params;
}

private function setDestinatarioSearch(basemessages $obj){
$query="SELECT pedidos.consultor,pedidos.login,users.email FROM pedidos INNER JOIN users WHERE pedidos.numero=:pedido AND users.login=:remetente";
if($obj->pedido=="0"){
$query="SELECT users.login,users.email,users.tipo FROM users WHERE tipo=:tipo1 OR tipo=:tipo2 OR tipo=:tipo3 OR tipo=:tipo4 ORDER BY rand()";
}
return $query;
}

private function setDestinatarioParamSearch(basemessages $obj){
$params=array();
if($obj->pedido=="0"){
$params["tipo1"]="administrador";
$params["tipo2"]="gestor";
$params["tipo3"]="usuario";
$params["tipo4"]="newuser";
}else{
$params["pedido"]=$obj->pedido;
$params["remetente"]=$obj->remetente;
}
return $params;
}

private function setInsert(){
$query="INSERT INTO messages (pedido,remetente,destinatario,assunto,conteudo,resumo) VALUES (:pedido,:remetente,:destinatario,:assunto,:conteudo,:resumo)";
return $query;
}

private function setParamInsert(basemessages $obj){
$params=array();
$params["pedido"]=$obj->pedido;
$params["remetente"]=$obj->remetente;
$params["destinatario"]=$obj->destinatario;
$params["assunto"]=$obj->assunto;
$params["conteudo"]=$obj->conteudo;
$params["resumo"]=$obj->resumo;
return $params;
}

private function setUpdate(){
$query="UPDATE messages SET notificacao=:notificacao WHERE pedido=:numero AND id=:id AND destinatario=:destinatario";
return $query;
}

private function setParamUpdate(basemessages $obj){
$params=array();
$params["notificacao"]="read";
$params["numero"]=$obj->pedido;
$params["id"]=$obj->id;
$params["destinatario"]=$obj->destinatario;
return $params;
}

private function setDelete(){
$query="DELETE FROM messages WHERE pedido=:numero AND id=:id AND destinatario=:destinatario";
return $query;
}

private function setParamDelete(basemessages $obj){
$params=array();
$params["numero"]=$obj->pedido;
$params["id"]=$obj->id;
$params["destinatario"]=$obj->destinatario;
return $params;
}
		
public function chama(basemessages $obj){
$resposta=array();
if($obj->action=="messagesloader" && $obj->destinatario!==""){
$resposta=$obj->messages_loader($obj);
}
if($obj->action=="messagescreator" && $obj->pedido!=="" && $obj->assunto && $obj->conteudo){
$resposta=$obj->messages_creator($obj);
}
if($obj->action=="messagesreader" && $obj->pedido!=="" && $obj->resumo!=="" && $obj->id!==""){
$resposta=$obj->messages_reader($obj);
}
if($obj->action=="messagesdeleter" && $obj->pedido!=="" && $obj->resumo!=="" && $obj->id!==""){
$resposta=$obj->messages_deleter($obj);
}
return $resposta;
}

private function messages_loader(basemessages $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login'])){
$gotdestinatario=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
if($obj->destinatario==$gotdestinatario){
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetchAll();
if($resultset!==null && is_array($resultset)){
$resposta['RESPONSE']=$resultset;
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function messages_creator(basemessages $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login'])){
$base=new database();
$gotlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
if($obj->remetente==""){
$obj->remetente=$gotlogin;
}
if($obj->destinatario==""){
$base->sql=$obj->setDestinatarioSearch($obj);
$base->commando($obj->setDestinatarioParamSearch($obj));
$resultset=$base->fetchAll();
foreach($resultset as $key => $value){
if($obj->pedido=="0"){
if($value['tipo']=="administrador" || $value['tipo']=="gestor"){
$obj->destinatario=$value['login'];
}
}else{
if($gotlogin==$value['consultor']){
$obj->destinatario=$value['login'];
}else{
$obj->destinatario=$value['consultor'];
}
}
if($obj->remetente==$value['login'] || $obj->remetente==$value['consultor']){
$obj->conteudo=$obj->conteudo. ". E-mail for reply ".$value['email'];
}
}
}
if($obj->destinatario==$gotlogin || $obj->remetente==$gotlogin){
$base->sql=$obj->setInsert();
$base->commando($obj->setParamInsert($obj));
$resposta['RESPONSE']="Message sent successfully.";
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function messages_reader(basemessages $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login'])){
$gotlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
if($gotlogin!==""){
$obj->destinatario=$gotlogin;
$validatordata=['pedido'=>$obj->pedido,'feedback'=>$obj->resumo];
$orderobj=new baseorders("orderscommunication",null,null,null,null,null,null,$validatordata);
$orderobjresp=$orderobj->chama($orderobj);
$resposta['RESPONSE']=$orderobjresp;
if($orderobjresp['RESPONSE']!=="Order updated." || $orderobjresp['RESPONSE']=="Incompatible request status with the message." || $orderobjresp['RESPONSE']=="Request was already updated."){
$base=new database();
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$resposta["MESSAGES"]="Message read.";
}
}else{
$resposta["ERROR"]="Access denied.";
}
}
return $resposta;
}

private function messages_deleter(basemessages $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login'])){
$gotlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
if($gotlogin!==""){
$obj->destinatario=$gotlogin;
$validatordata=['pedido'=>$obj->pedido,'feedback'=>$obj->resumo];
$orderobj=new baseorders("orderscommunication",null,null,null,null,null,null,$validatordata);
$orderobjresp=$orderobj->chama($orderobj);
$resposta['RESPONSE']=$orderobjresp;
if($orderobjresp['RESPONSE']!=="Order updated." || $orderobjresp['RESPONSE']=="Incompatible request status with the message." || $orderobjresp['RESPONSE']=="Request was already updated."){
$base=new database();
$base->sql=$obj->setDelete();
$base->commando($obj->setParamDelete($obj));
$resposta['RESPONSE']="Message deleted.";
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