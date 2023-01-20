<?php

require realpath('../vendor/autoload.php');
require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class baseimage {

private $query;

private $oldtitle;

private $newtitle;

private $deltitle;

private $author;

private $images;

private $filetype;

private $action;

public function __construct($titles,$author,$images,$action){
    
\Cloudinary::config(array("cloud_name" => "seriallized", "api_key" => "631792715993572", "api_secret" => "rpiQxNwYSxWOtMyTlKeEMWLbLPM", "secure" => true));
    
if(is_array($titles)){
if(isset($titles['oldTitle']) && $titles['oldTitle']!==null){
$this->oldtitle=$titles['oldTitle'];
}
if(isset($titles['newTitle']) && $titles['newTitle']!==null){
$this->newtitle=$titles['newTitle'];
}
if(isset($titles['delTitle']) && $titles['delTitle']!==null){
$this->deltitle=$titles['delTitle'];
}
}
$this->author=$author;
$this->images=$images;
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

private function setParamDelete(baseimage $obj){
$params=array();
$params["title"]=$obj->deltitle;
$params["name"]=$obj->images['name'];
return $params;
}

public function chama(baseimage $obj){
$resposta=array();
if($obj->action=="creation"){
$resposta=$obj->imageCreator($obj);
}
if($obj->action=="edition"){
$resposta=$obj->imageEditor($obj);
}
if($obj->action=="drop"){
$resposta=$obj->imageDeleterAll($obj);
}
if($obj->action=="deletion"){
$resposta=$obj->imageDeleterAll($obj);
}
return $resposta;
}

private function imageCreator(baseimage $obj){
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
$allowedextensions=array("image/jpeg","image/jpg","image/png");
$allowedsize=5097152;
$keyb=0;
foreach($obj->images['name'] as $keyb => $valub){ 

$obj->filetype=explode("/",$obj->images['type'][$keyb]);
$imgname=str_replace(" ","",$obj->images['name'][$keyb]);
$params["title"]=$obj->newtitle;
$params["name"]=$imgname;
$params["extension"]=$obj->filetype[1];
$params["author"]=$obj->author;
$params["subject"]=$obj->filetype[0];

if($obj->images['size'][$keyb]<$allowedsize){
if(in_array($obj->images['type'][$keyb],$allowedextensions)===false){
$msg.=$obj->filetype[1]." file type ".$obj->images['name']." not allowed, choose JPEG, JPG, or PNG formats.";
}else{
$uploadfile="../storage/data/images/contents/".$obj->newtitle."/".$imgname."/".$imgname;
$uploadfolder="../storage/data/images/contents/".$obj->newtitle."/".$imgname;
mkdir($uploadfolder,0777,true);
move_uploaded_file($obj->images['tmp_name'][$keyb],$uploadfile);
try{

$deepzoom=Jeremytubbs\Deepzoom\DeepzoomFactory::create([
'path'=>'../storage/data/images/contents/'.$obj->newtitle.'',
'driver'=>'imagick',
'format'=>''.$obj->filetype[1].'',
]);
$response=$deepzoom->makeTiles($uploadfile,$imgname,$imgname);
print_r($response);

}catch(Exception $e){
print_r($e);
$msg.=$e;
}

if(file_exists($uploadfile)){
if($obj->filetype[0]=="image"){

$params["link"]=$obj->cloudupload($uploadfile,$uploadfolder,$imgname,$obj->newtitle,$obj->filetype[1]);

if($params["link"]!==""){
$delete=$obj->localdeleter($uploadfolder);
if($delete){
}
}

}else if($obj->filetype[0]=="video"){
$params["link"]="storage/data/videos/contents/";
}
$base=new database();
$base->sql=$obj->setInsert();
$base->commando($params);
}else{
print_r("File not exists.");
}

}

}else{
print_r("File size ".$obj->images['name'][$keyb]." must be less than 5 MB.");
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

private function imageEditor(baseimage $obj){
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
$allowedextensions=array("image/jpeg","image/jpg","image/png");
$allowedsize=5097152;
$keyb=0;
foreach($obj->images['name'] as $keyb => $valub){
$obj->filetype=explode("/",$obj->images['type'][$keyb]);
$imgname=str_replace(" ","",$obj->images['name'][$keyb]);
$params["title"]=$obj->newtitle;
$params["name"]=$imgname;
$params["extension"]=$obj->filetype[1];
$params["author"]=$obj->author;
$params["subject"]=$obj->filetype[0];
if($obj->images['size'][$keyb]<$allowedsize){
if(in_array($obj->images['type'][$keyb],$allowedextensions)===false){
$msg.=$obj->filetype[1]." file type ".$obj->images['name']." not allowed, choose JPEG, JPG, or PNG formats.";
}else{
$uploadfile="../storage/data/images/contents/".$obj->newtitle."/".$imgname."/".$imgname;
$uploadfolder="../storage/data/images/contents/".$obj->newtitle."/".$imgname;
mkdir($uploadfolder,0777,true);
move_uploaded_file($obj->images['tmp_name'][$keyb],$uploadfile);
try{
$deepzoom=Jeremytubbs\Deepzoom\DeepzoomFactory::create([
'path'=>'../storage/data/images/contents/'.$obj->newtitle.'',
'driver'=>'imagick',
'format'=>''.$obj->filetype[1].'',
]);
$response=$deepzoom->makeTiles($uploadfile,$imgname,$imgname);
print_r($response);
}catch(Exception $e){
print_r($e);
$msg.=$e;
}

if(file_exists($uploadfile)){
if($obj->filetype[0]=="image"){
$params["link"]=$obj->cloudupload($uploadfile,$uploadfolder,$imgname,$obj->newtitle,$obj->filetype[1]);
if($params["link"]!==""){
$delete=$obj->localdeleter($uploadfolder);
if($delete){
}
}
}
$base=new database();
$base->sql=$obj->setInsert();
$base->commando($params);
}

}
}else{
print_r("File size ".$obj->images['name'][$keyb]." must be less than 5 MB.");
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

private function imageDeleterAll($obj){
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
$deleter=$obj->clouddeleter($obj->deltitle);
if($deleter!==null){
$base=new database();
$base->sql=$obj->setDelete();
$base->commando($obj->setParamDelete($obj));
}
if($obj->deltitle!=="" && empty($obj->images)==false){
$delete=true;
if($delete){

}else{
$msg.="There was a problem deleting files from the image.";
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

private function imageDeleterSingle($obj){
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
$deleter=$obj->clouddeleter($obj->deltitle."/".$obj->images);
if($deleter!==null){
$base=new database();
$base->sql=$obj->setDelete();
$base->commando($obj->setParamDelete($obj));
}
if($obj->deltitle!=="" && empty($obj->images)==false){
$delete=true;
if($delete){

}else{
$msg.="There was a problem deleting files from the image.";
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

private function localdeleter($dirname){
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
if(is_dir($dirname)===true){
$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirname),RecursiveIteratorIterator::CHILD_FIRST);
foreach ($files as $file){
if(in_array($file->getBasename(),array('.','..'))!==true){
if($file->isDir()===true){
rmdir($file->getPathName());
}else if(($file->isFile()===true) || ($file->isLink()===true)){
unlink($file->getPathname());
}
}
}
return rmdir($dirname);
}else if((is_file($dirname)===true)||(is_link($dirname)===true)){
return unlink($dirname);
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return false;
}

private function cloudupload($uploadfile,$uploadfolder,$imgname,$newTitle,$fileType){

$realImageName=str_replace(".".$fileType,"",$imgname);

$responseImage=\Cloudinary\Uploader::upload($uploadfile,array("public_id" => $newTitle."/".$imgname."/".$realImageName, "resource_type" => "auto"));
$link=str_replace("v".$responseImage["version"]."/","",$responseImage["secure_url"]);

$subDirName=$imgname."_files";
$dirname=$uploadfolder."/".$subDirName;
if(is_dir($dirname)){
$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirname),RecursiveIteratorIterator::CHILD_FIRST);
foreach ($files as $file){
if(in_array($file->getBasename(),array('.','..'))!==true){
if($file->isDir()===false){
$subfileName=$file->getFilename();
$subfileExtension=explode('.',$subfileName);
$subfileName=str_replace(".".$fileType,"",$subfileName);
$subpathTiles=$files->getSubPath();
if($subfileExtension[1]=="png"){
\Cloudinary\Uploader::upload($file->getPathname(),array("public_id" => $newTitle."/".$imgname."/".$subDirName."/".$subpathTiles."/".$subfileName, "resource_type" => "auto"));
}
}
}
}
}
$dziFile=$uploadfolder."/".$imgname.".dzi";
$jsFile=$uploadfolder."/".$imgname.".js";
$responseDzi=\Cloudinary\Uploader::upload($dziFile,array("public_id" => $newTitle."/".$imgname."/".$imgname.".dzi", "resource_type" => "auto"));
$responseJs=\Cloudinary\Uploader::upload($jsFile,array("public_id" => $newTitle."/".$imgname."/".$imgname.".js", "resource_type" => "auto"));

return $link;
}

private function clouddeleter($publicID){
$api = new \Cloudinary\Api();
$deleteResponse = $api->delete_resources_by_prefix($publicID);
return $deleteResponse;
}

}
	
?>