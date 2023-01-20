<?php

require realpath('../vendor/autoload.php');
require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';
require_once __DIR__.'/frameworks/ZipMaster/ZipMaster.php';

class basebanking {
	
private $query;

private $login;

private $id;

private $status;

private $remessa;

private $retornos;

private $action;

public function __construct($action,$id,$retornos){
$this->action=$action;
$this->retornos=$retornos;
$this->id=$id;
}

private function setSearch(basebanking $obj){
if($obj->id=="all"){
$query="SELECT * FROM contas WHERE status=:status";
}else{
$query="SELECT * FROM contas WHERE numero=:id";
}
return $query;
}

private function setParamSearch(basebanking $obj){
$params=array();
if($obj->id=="all"){
$params["status"]="Pendente";
}else{
$params["id"]=$obj->id;
}
return $params;
}

private function setUpdate(){
$query="UPDATE contas SET retorno=:retorno AND status=:status WHERE numero=:id";
return $query;
}
   
private function setParamPostUpdate(basebanking $obj){
$params=array();
$params["status"]=$obj->status;
$params["id"]=$obj->id;
return $params;
}
   
private function setPostUpdate(){
$query="UPDATE contas SET status=:status WHERE numero=:id";
return $query;
}
    	
public function chama(basebanking $obj){
$resposta=array();
if($obj->action=="remessadownloader"){
$resposta=$obj->banking_downloader($obj);
}else if($obj->action=="retornouploader"){
$resposta=$obj->banking_uploader($obj);
}else if($obj->action=="paychecker"){
$resposta=$obj->banking_paychecker($obj);
}else if($obj->action=="payunchecker"){
$resposta=$obj->banking_payunchecker($obj);
}
return $resposta;
}

private function banking_downloader(basebanking $obj){
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
$base->sql=$obj->setSearch($obj);
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetchAll();
if($obj->id=="all"){
$folder=realpath("../storage/data/billing/remessa");
$zipdest=realpath("../storage/data/billing");
$zipMount=new ZipMaster\ZipMaster($zipdest.'/remessas.zip',$folder);
$zipMount->archive();
$zipfile=realpath("../storage/data/billing/remessas.zip");
$resposta=$obj->download_file($zipfile,"remessas.zip");
}else{
foreach($resultset as $key => $value){
$dirname=realpath('../'.$value['remessa']);
$filenamearr=explode("/",$value['remessa']);
$filename=end($filenamearr);
$resposta=$obj->download_file($dirname,$filename);
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

private function banking_paychecker(basebanking $obj){
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
$obj->status="Baixado";
$base=new database();
$base->sql=$obj->setPostUpdate($obj);
$base->commando($obj->setParamPostUpdate($obj));
$resposta['RESPONSE']="Payment cleared.";
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function banking_payunchecker(basebanking $obj){
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
$obj->status="Pendente";
$base=new database();
$base->sql=$obj->setPostUpdate($obj);
$base->commando($obj->setParamPostUpdate($obj));
$resposta['RESPONSE']="Decompensated payment.";
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function banking_uploader(basebanking $obj){
$resposta=array();
$params=array();
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
$bankresult=array();
foreach($obj->retornos['name'] as $key => $value){
$filepath=$obj->retornos['tmp_name'][$key];
$filename=$obj->retornos['name'][$key];
$result=$obj->upload_file($filepath,$filename);
array_push($bankresult,$result);
if($result['ERROR']==""){
foreach($result as $retorno => $bankinfo){
$id=$bankinfo['ID'];
$params["retorno"]=$bankinfo['PATH'];
$params["status"]=$bankinfo[''.$id.''];
$params["id"]=$id;
$base->sql=$obj->setUpdate($obj);
$base->commando($params);
}
}
}
foreach($bankresult as $info => $message){
if($message['ERROR']!==""){
$resposta['RESPONSE'].=" ".$process->cleanstring($message['ERROR']).".";
}else if($message['RESPONSE']!==""){
$resposta['RESPONSE'].=" ".$message['RESPONSE'];
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

private function download_file($filepath,$filename){
$resposta=array();
$formatarr=explode(".",$filename);
$format=end($formatarr);
if($format=="txt" || $format=="zip"){
ob_clean();
$fp=@fopen($filepath,'rb');
if(strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")){
header('Content-Type: "application/octet-stream"');
header('Content-Disposition: attachment; filename='.$filename.'');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header("Content-Transfer-Encoding: binary");
header('Pragma: public');
header("Content-Length: ".filesize($filepath));
}else{
header('Content-Type: "application/octet-stream"');
header('Content-Disposition: attachment; filename='.$filename.'');
header("Content-Transfer-Encoding: binary");
header('Expires: 0');
header('Pragma: no-cache');
header("Content-Length: ".filesize($filepath));
}
fpassthru($fp);
fclose($fp);
ob_end_flush();
$resposta['RESPONSE']="File downloaded.";
}else{
$resposta['RESPONSE']="Failed to download file, imcompatible format.";
}
return $resposta;
}

private function upload_file($filepath,$filename){
$resposta=array();
if(!file_exists("../storage/data/billing/retorno")){
mkdir("../storage/data/billing/retorno",0777,true);
}
move_uploaded_file($filepath,"../storage/data/billing/retorno/".$filename);
if(file_exists("../storage/data/billing/retorno/".$filename)){
$realfilepath="../storage/data/billing/retorno/".$filename;
$cnabFactory=new Cnab\Factory();
try{
$retorno=$cnabFactory->createRetorno($realfilepath);
}catch(Exception $e){
if($e->getMessage()==""){
$bankfile=$retorno->listDetalhes();
foreach($bankfile as $bankinfo){
$valorRecebido=$bankinfo->getValorRecebido();
$nossoNumero=$bankinfo->getNossoNumero();
$numerodoc=$bankinfo->getNumeroDocumento();
if($numerodoc!==""){
$resposta['RESPONSE']="File ".$filename." accepted as return";
$resposta['PATH']=$filepath;
$resposta['ID']=$numerodoc;
if($valorRecebido>0){
$resposta[''.$numerodoc.'']="Pago";
}else{
$resposta[''.$numerodoc.'']="Pendente";
}
}
}
}else{
$resposta['ERROR']="File error ".$filename.". ".$e->getMessage();
unlink("../storage/data/billing/retorno/".$filename);
}
}
}
return $resposta;
}

}

?>