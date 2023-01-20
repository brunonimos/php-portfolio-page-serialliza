<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class basecreation {

private $query;

private $titles;

private $title;

private $content;

private $author;

private $categoria;

private $media;

private $videos;

private $cursodata;

private $serial;

public function __construct($titles,$content,$categoria,$media,$videos,$cursodata){
if(is_array($titles)){
$this->titles=$titles;
if(isset($titles['newTitle']) && $titles['newTitle']!==null){
$this->title=$titles['newTitle'];
}
}
$this->content=$content;
$this->categoria=$categoria;
$this->media=$media;
$this->videos=$videos;
$this->serial="".$this->random_number(12)."";
$this->cursodata=$cursodata;
}

private function setSearch(){
$query="SELECT * FROM content WHERE titulo=:title OR content=:content OR serial=:serial";
return $query;
}

private function setParamSearch(basecreation $obj){
$params=array();
$params["title"]=$obj->title;
$params["content"]=$obj->content;
$params["serial"]=$obj->serial;
return $params;
}

private function setInsert(){
$query="INSERT INTO content (titulo,content,categoria,autor,serial,startdate,enddate,weekdays,starttime,endtime,valor,multa,parcelas,carga) VALUES (:title,:content,:categoria,:author,:serial,:inicio,:fim,:dias,:horarioini,:horariofim,:valor,:multa,:parcelas,:carga)";
return $query;
}

private function setParamInsert(basecreation $obj){
$params=array();
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

public function chama(basecreation $obj){
$resposta=array();
$resposta=$obj->criar($obj);
return $resposta;
}

private function criar(basecreation $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$params=array();
$base=new database();
$base->sql=$obj->setSearch();
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetch();
if($resultset!==null && is_array($resultset) && isset($resultset['titulo']) && isset($resultset['content']) && $resultset['titulo']!==$obj->title){
$resposta['RESPONSE']="Existing title or content, modify.";
}
if(empty($resposta['RESPONSE'])==true){
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$obj->author=$loginexplode[0];
$tipo=$tipoexplode[1];
if($decryptlogin==$obj->author && $tipo=="administrador" || $decryptlogin==$obj->author && $tipo=="gestor"){
$resposta['ACTOR']=$decryptlogin;
$params["title"]=$obj->title;
$base->sql=$obj->setInsert();
$base->commando($obj->setParamInsert($obj));
$resposta['RESPONSE']="Content created.";
if(empty($obj->media)==false){
$mediaobj=new baseimage($obj->titles,$obj->author,$obj->media,"creation");
$mediaobjresp=$mediaobj->chama($mediaobj);
if($mediaobjresp['RESPONSE']==""){
$resposta['RESPONSE']="Content created.";
}else{
$resposta['RESPONSE']="Content created but failed to create images: ".$mediaobjresp['RESPONSE'];
}
}
if($obj->videos!==""){
$videodata=explode(";",$obj->videos);
if(empty($videodata)==false){
$videoobj=new basevideo($obj->titles,$obj->author,$videodata,"creation");
$videoobjresp=$videoobj->chama($videoobj);
if($videoobjresp['RESPONSE']==""){
$resposta['RESPONSE']="Content created.";
}else{
$resposta['RESPONSE']="Content created but failed to create videos: ".$videoobjresp['RESPONSE'];
}
}else{
$videodata==["video"=>$obj->videos];
$videoobj=new basevideo($obj->titles,$obj->author,$videodata,"creation");
$videoobjresp=$videoobj->chama($videoobj);
if($videoobjresp['RESPONSE']==""){
$resposta['RESPONSE']="Content created.";
}else{
$resposta['RESPONSE']="Content created but failed to create videos: ".$videoobjresp['RESPONSE'];
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

function random_number($length){
return join('',array_map(function($value){
return $value==1 ? mt_rand(1,9):mt_rand(0,9);
},range(1,$length)));
}

}
	
?>