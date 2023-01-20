<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class baseappnotify {
	
private $query;

private $notifyId;

private $notifyText;

private $notifyDate;

private $notifyTime;

private $notifyProduct;

private $notifyAuthor;

private $data;

private $action;

public function __construct($action,$notifyId,$notifyText,$notifyDate,$notifyTime,$notifyProduct,$data){
$this->action=$action;
$this->notifyId=$notifyId;
$this->notifyText=$notifyText;
$this->notifyDate=$notifyDate;
$this->notifyTime=$notifyTime;
$this->notifyProduct=$notifyProduct;
$this->data=$data;
}

private function setSearch(){
//$query="SELECT * FROM appnotify WHERE product=:notifyProduct";
$query="SELECT * FROM appnotify";
return $query;
}

private function setParamSearch(baseappnotify $obj){
$params=array();
$params["notifyProduct"]=$obj->notifyProduct;
return $params;
}

private function setInsert(){
$query="INSERT INTO appnotify (notifyId,notifyMessage,dispatchDate,dispatchTime,product,notifyAuthor) VALUES (:notifyText,:notifyDate,:notifyTime,:notifyProduct,:notifyAuthor)";
//INSERT INTO `appnotify` (`notifyId`, `product`, `notifyMessage`, `dispatchDate`, `dispatchTime`, `notifyAuthor`) VALUES (NULL, 'S-Guitar Pro app.', 'Test', '2021-03-17', '21:40:51','brunonimos');
return $query;
}

private function setParamInsert(baseappnotify $obj){
$params=array();
$params["notifyId"]=NULL;
$params["notifyProduct"]=$obj->notifyProduct;
$params["notifyText"]=$obj->notifyText;
$params["notifyDate"]=$obj->notifyDate;
$params["notifyTime"]=$obj->notifyTime;
$params["notifyAuthor"]=$obj->notifyAuthor;
return $params;
}

private function setDelete($obj){
if($obj->action=="notifyDeleter"){
$query="DELETE FROM appnotify WHERE id=:notifyId";
}
return $query;
}

private function setParamDelete(baseappnotify $obj){
$params=array();
$params["notifyId"]=$obj->notifyId;
return $params;
}
        
public function chama(baseappnotify $obj){
if($obj->action=="notifyReading"){
$resposta=$obj->notify_loader($obj);
}else if($obj->action=="notifyCreator"){
$resposta=$obj->notify_creator($obj);
}else if($obj->action=="notifyDeleter"){
$resposta=$obj->notify_deleter($obj);
}
return $resposta;
}

private function notify_loader(baseappnotify $obj){
$resposta=array();
//$resposta=new stdClass();
$object=["object"=>$obj];
$process=new process($object);
$base=new database();
$base->sql=$obj->setSearch();
$base->commandoquery();
$resultset=$base->fetchAll();
if(!empty($resultset) && is_array($resultset)){
foreach($resultset as $keya => $valuea){
if($resultset[$keya]['product']==$obj->notifyProduct){
foreach($resultset[$keya] as $keyb => $valueb){
//$resultset[$keya]=@html_entity_decode($valueb);
//$resultset[$keya]=@htmlspecialchars($valueb);
//$resultset[$keya]=$process->cleanstring($valueb);
if($keyb==$obj->data){
if($keya==0){
//$resposta["APPRESPONSE"]=$valueb;
}else{
//$resposta["APPRESPONSE"].="&".$valueb;
}
}
}
}
}
$resposta["APPRESPONSE"]=$resultset;
//$resposta->app="notify";
//$resposta->results=$resultset;
}else{
//$resposta->error="Theres is nothing to notify for product ".$obj->notifyProduct."";
$resposta['ERROR']="Theres is nothing to notify for product ".$obj->notifyProduct."";
}
return $resposta;
}

private function notify_creator(baseappnotify $obj){
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$obj->author=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$obj->author && $tipo=="administrador" || $decryptlogin==$obj->author && $tipo=="gestor"){
$obj->notifyAuthor=$decryptlogin;
$base=new database();
$base->sql=$obj->setInsert();
$base->commando($obj->setParamInsert($obj));
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}

private function notify_deleter(baseappnotify $obj){
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$obj->author=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$obj->author && $tipo=="administrador" || $decryptlogin==$obj->author && $tipo=="gestor"){
$base=new database();
$base->sql=$obj->setDelete($obj);
$base->commando($obj->setParamDelete($obj));
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}

}

?>