<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';
    
class baseedit {

private $query;

private $tokenid;

private $titles;

private $title;

private $oldtitle;

private $content;

private $author;

private $categoria;

private $media;

private $videos;

private $serial;

private $cursodata;

public function __construct($tokenid,$titles,$content,$categoria,$serial,$media,$videos,$cursodata){
if(is_array($titles)){
$this->titles=$titles;
if(isset($titles['newTitle']) && $titles['newTitle']!==""){
$this->title=$titles['newTitle'];
}
}
$this->tokenid=$tokenid;
$this->content=$content;
$this->categoria=$categoria;
$this->media=$media;
$this->videos=$videos;
$this->serial=$serial;
$this->cursodata=$cursodata;
}

private function setSearch(){
$query="SELECT * FROM content WHERE titulo=:title";
return $query;
}

private function setUpdate(){
$query="UPDATE content SET titulo=:title, content=:content, categoria=:categoria, startdate=:inicio, enddate=:fim, weekdays=:dias, starttime=:horarioini, endtime=:horariofim, valor=:valor, multa=:multa, parcelas=:parcelas, carga=:carga WHERE titulo=:oldtitle AND autor=:author AND serial=:serial";
return $query;
}

private function setDelete(){
$query="DELETE FROM content WHERE titulo=:title AND autor=:author AND serial=:serial";
return $query;
}

private function setParamSearch(baseedit $obj){
$params=array();
$params["title"]=$obj->title;
return $params;
}

private function setParamUpdate(baseedit $obj){
$params=array();
$params["oldtitle"]=$obj->oldtitle;
$params["title"]=$obj->title;
$params["content"]=$obj->content;
$params["categoria"]=$obj->categoria;
$params["author"]=$obj->author;
$params["serial"]=$obj->serial;
$params["inicio"]=$obj->cursodata["inicio"];
$params["fim"]=$obj->cursodata["fim"];
if(empty($obj->cursodata["dias"])==true || $obj->cursodata["dias"]==""){
$params["dias"]=null;
}else{
foreach($obj->cursodata["dias"] as $field => $day){
$params["dias"].=";".$day;
}
}
$params["horarioini"]=str_replace(":","-",$obj->cursodata["horarioini"]);
$params["horariofim"]=str_replace(":","-",$obj->cursodata["horariofim"]);
$params["valor"]=$obj->cursodata["valor"];
$params["multa"]=$obj->cursodata["multa"];
$params["parcelas"]=$obj->cursodata["parcelas"];
$params["carga"]=$obj->cursodata["carga"];
return $params;
}

public function chama(baseedit $obj){
$resposta=array();
$resposta=$obj->editar($obj);
return $resposta;
}

private function editar(baseedit $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$base=new database();
$decrypttoken=AesCtr::decrypt($obj->tokenid,$process->getPassOfCookie(),256);
$tokenarr=explode("*",$decrypttoken);
$obj->serial=$tokenarr[0];
$obj->oldtitle=$tokenarr[1];
$obj->author=$tokenarr[2];
$validate=$tokenarr[3];
if($obj->oldtitle!==$obj->title){
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset!==null && is_array($resultset) && isset($resultset['titulo']) && isset($resultset['content']) && $resultset['titulo']!==$obj->title){
$resposta['RESPONSE']="Existing title or content, modify.";
}
}
if(empty($resposta['RESPONSE'])==true){
$authentic="";
$params=array();
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$gotlogin=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$gotlogin && $tipo=="administrador" || $decryptlogin==$gotlogin && $tipo=="gestor"){
$resposta['ACTOR']=$decryptlogin;
if($obj->title!=="" || $obj->content!=="" || $obj->categoria!=="" || empty($obj->media)==false){
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$resposta['RESPONSE']="Conteudo alterado.";
if(file_exists("../storage/data/images/contents/".$obj->oldtitle)){
rename("../storage/data/images/contents/".$obj->oldtitle,"../storage/data/images/contents/".$obj->title);
}else{
if(!file_exists("../storage/data/images/contents/".$obj->title)){
mkdir("../storage/data/images/contents/".$obj->title,0777,true);
}
}
if(empty($obj->media)==false){
$mediaobj=new baseimage($obj->titles,$obj->author,$obj->media,"edition");
$mediaobjresp=$mediaobj->chama($mediaobj);
$resposta['RESPONSE']=$mediaobjresp;
if($mediaobjresp['RESPONSE']==""){
$resposta['RESPONSE']="Altered content.";
}else{
$resposta['RESPONSE']="Content changed but failed to change images: ".$mediaobjresp['RESPONSE'];
}
}
if($obj->videos!==""){
$videodata=explode(";",$obj->videos);
if(empty($videodata)==false){
$videoobj=new basevideo($obj->titles,$obj->author,$videodata,"edition");
$videoobjresp=$videoobj->chama($videoobj);
if($videoobjresp['RESPONSE']==""){
$resposta['RESPONSE']="Altered content.";
}else{
$resposta['RESPONSE']="Content changed but failed to change videos: ".$videoobjresp['RESPONSE'];
}
}else{
$videodata==["video"=>$obj->videos];
$videoobj=new basevideo($obj->titles,$obj->author,$videodata,"edition");
$videoobjresp=$videoobj->chama($videoobj);
if($videoobjresp['RESPONSE']==""){
$resposta['RESPONSE']="Altered content.";
}else{
$resposta['RESPONSE']="Content changed but video changed: ".$videoobjresp['RESPONSE'];
}
}
}

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

}
    
?>
