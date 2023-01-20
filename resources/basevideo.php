<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';
require_once __DIR__.'/frameworks/YouTubeTool.php';
require_once __DIR__.'/frameworks/YouTubeVideo.php';

class basevideo {

private $query;

private $oldtitle;

private $newtitle;

private $author;

private $videos;

private $action;

public function __construct($titles,$author,$videos,$action){
if(is_array($titles)){
if(isset($titles['oldTitle']) && $titles['oldTitle']!==null){
$this->oldtitle=$titles['oldTitle'];
}
if(isset($titles['newTitle']) && $titles['newTitle']!==null){
$this->newtitle=$titles['newTitle'];
}
}
$this->author=$author;
$this->videos=$videos;
$this->action=$action;
}

private function setInsert(){
$query="INSERT INTO multimidia (titulo,name,link,extension,autor,subject) VALUES (:title,:name,:link,:extension,:author,:subject)";
return $query;
}

private function setDelete(){
$query="DELETE FROM multimidia WHERE titulo=:title AND name=:name";
return $query;
}

private function setParamDelete(basevideo $obj){
$params=array();
$params["title"]=$obj->oldtitle;
$params["name"]=$obj->image;
return $params;
}

private function setParamDeleleAll(basevideo $obj){
$params=array();
$params["title"]=$obj->oldtitle;
return $params;
}

public function chama(basevideo $obj){
$resposta=array();
if($obj->action=="creation"){
$resposta=$obj->videoCreator($obj);
}
if($obj->action=="edition"){
$resposta=$obj->videoEditor($obj);
}
if($obj->action=="deletion"){
$resposta=$obj->videoDeleter($obj);
}
return $resposta;
}

private function videoCreator(basevideo $obj){
$msg="";
$params=array();
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
if($decryptlogin==$gotlogin && $tipo=="administrador" || $decryptlogin==$gotlogin && $tipo=="gestor"){
$resposta['ACTOR']=$decryptlogin;
foreach($obj->videos as $key => $value){
$videoid=Ling\YouTubeUtils\YouTubeTool::getId($obj->videos[$key]);
if($videoid!==""){
$params["title"]=$obj->newtitle;
$params["name"]=$videoid;
$params["link"]=$obj->videos[$key];
$params["extension"]="youtube";
$params["author"]=$obj->author;
$params["subject"]="video";
$base=new database();
$base->sql=$obj->setInsert();
$base->commando($params);
}else{
$msg.="Unsupported video ".$obj->videos[$key].".";
}
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta['RESPONSE']=$msg;
}

private function videoEditor(basevideo $obj){
$msg="";
$params=array();
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
if($decryptlogin==$gotlogin && $tipo=="administrador" || $decryptlogin==$gotlogin && $tipo=="gestor"){
$resposta['ACTOR']=$decryptlogin;
foreach($obj->videos as $key => $value){
$videoid=Ling\YouTubeUtils\YouTubeTool::getId($obj->videos[$key]);
if($videoid!==""){
$params["title"]=$obj->newtitle;
$params["name"]=$videoid;
$params["link"]=$obj->videos[$key];
$params["extension"]="youtube";
$params["author"]=$obj->author;
$params["subject"]="video";
$base=new database();
$base->sql=$obj->setInsert();
$base->commando($params);
}else{
$msg.="Unsupported video ".$obj->videos[$key].".";
}
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta['RESPONSE']=$msg;
}

private function videoDeleter(){
$msg="";
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
if($decryptlogin==$gotlogin && $tipo=="administrador" || $decryptlogin==$gotlogin && $tipo=="gestor"){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$obj->setDelete();
$base->commando($obj->setParamDelete($obj));
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta['RESPONSE']=$msg;
}

}
	
?>