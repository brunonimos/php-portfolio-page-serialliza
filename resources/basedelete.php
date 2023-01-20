<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';
    
class basedelete {

private $query;

private $title;

private $serial;

private $mediaName;

private $author;

public function __construct($title,$serial,$mediaName,$action){
if(is_array($title)){
if(isset($title['delTitle'])){
$this->title=$title;
}
}
$this->serial=$serial;
$this->mediaName=$mediaName;
$this->action=$action;
}

private function setSearch(){
$query="SELECT pedidos.numero FROM pedidos WHERE titulo=:title OR serial=:serial";
return $query;
}

private function setParamSearch(basedelete $obj){
$params=array();
$params["title"]=$obj->title['delTitle'];
$params["serial"]=$obj->serial;
return $params;
}

private function setDelete($obj){
if($obj->action=="contentdeleter"){
$query="DELETE FROM content WHERE titulo=:title AND serial=:serial";
}else if($obj->action=="multimidiadeleter" || $obj->action=="multimidiatruncater"){
$query="DELETE FROM multimidia WHERE titulo=:title AND name=:name";
}
return $query;
}

private function setParamDelete(basedelete $obj){
$params=array();
$params["title"]=$obj->title['delTitle'];
if($obj->action=="contentdeleter"){
$params["serial"]=$obj->serial;
}else if($obj->action=="multimidiadeleter" || $obj->action=="multimidiatruncater"){
$params["name"]=$obj->mediaName['name'];
}
return $params;
}

public function chama(basedelete $obj){
$resposta=array();
if($obj->action=="contentdeleter" && $obj->title['delTitle'] && $obj->serial!==""){
$resposta=$obj->content_deleter($obj);
}
if($obj->action=="multimidiadeleter" && $obj->title['delTitle'] && $obj->mediaName['name']!==""){
$resposta=$obj->multimidia_deleter($obj);
}
if($obj->action=="multimidiatruncater" && $obj->title['delTitle'] && $obj->mediaName['name']!==""){
$resposta=$obj->multimidia_deleter($obj);
}
return $resposta;
}

private function content_deleter(basedelete $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset!==null && is_array($resultset) && isset($resultset['numero'])){
$list=array_map('strval',$resultset);
$resposta['RESPONSE']="Failed to delete open requests. Finalize orders "+$list;
}
if(empty($resposta['RESPONSE'])==true){
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$gotlogin=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$gotlogin && $tipo=="administrador" || $decryptlogin==$gotlogin && $tipo=="gestor"){
$resposta['ACTOR']=$decryptlogin;
$mediaobj=new baseimage($obj->title,$obj->author,null,"drop");
$mediaobjresp=$mediaobj->chama($mediaobj);
if($mediaobjresp['RESPONSE']==""){
$resposta['RESPONSE']="Deleted content.";
}else{
$resposta['RESPONSE']="Content deleted but deleted images failed: ".$mediaobjresp['RESPONSE'];
}
$base->sql=$obj->setDelete($obj);
$base->commando($obj->setParamDelete($obj));
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}
return $resposta;
}

private function multimidia_deleter(basedelete $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$base=new database();
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$gotlogin=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$gotlogin && $tipo=="administrador" || $decryptlogin==$gotlogin && $tipo=="gestor"){
$resposta['ACTOR']=$decryptlogin;
$obj->author=$decryptlogin;
$base->sql=$obj->setDelete($obj);
$base->commando($obj->setParamDelete($obj));
$mediaobj=new baseimage($obj->title,$obj->author,$obj->mediaName,"deletion");
$resposta=array_merge($resposta,$mediaobj->chama($mediaobj));
$resposta['RESPONSE']="Deleted multimedia.";
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
