<?php

require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class basecontent {

private $query;

private $search;

private $token;

public function __construct($token,$search,$content){
$this->token=$token;
$this->search=$search;
$this->content=$content;
}

private function setSearch(basecontent $obj){
$query="SELECT * FROM content ORDER by categoria ASC,date DESC";
if($obj->content=="top"){
$query="SELECT * FROM content ORDER by score DESC LIMIT 5";
}
if($obj->content=="searchmode" && $obj->search!==""){
$query="SELECT * FROM content WHERE LOWER(titulo) LIKE :searchA OR LOWER(content) LIKE :searchB OR LOWER(autor) LIKE :searchC OR LOWER(categoria) LIKE :searchD OR LOWER(serial) LIKE :searchE OR LOWER(startdate) LIKE :searchF OR LOWER(starttime) LIKE :searchG OR LOWER(weekdays) LIKE :searchH";
}
if($obj->content=="recoverymode" && $obj->token!==""){
$query="SELECT account.token,account.date,account.status FROM account WHERE token=:token AND status=:status";
}
if($obj->content=="profile" || $obj->content=="alladms" || $obj->content=="allusers"){
$query="SELECT * FROM users";
}
if($obj->content=="orders" || $obj->content=="myorders"){
$query="SELECT * FROM pedidos";
}
if($obj->content=="contact"){
$query="SELECT A.conditions, B.numero, B.login, B.consultor FROM users AS A LEFT JOIN pedidos AS B ON B.login=A.login";
}
if($obj->content=="billing" || $obj->content=="mybilling" || $obj->content=="banking"){
$query="SELECT * FROM contas INNER JOIN internal";
}
if($obj->content=="messages"){
$query="SELECT * FROM messages";
}
if($obj->content=="info" || $obj->content=="privacy"){
$query="SELECT * FROM info";
}
if($obj->content=="system"){
$query="SELECT * FROM internal";
}
if($obj->content=="notifications"){
$query="SELECT * FROM appnotify";
}
return $query;
}

private function setSearchMultimidia(){
$query="SELECT * FROM multimidia WHERE titulo=:title";
return $query;
}

private function setParamSearch(basecontent $obj){
$params=array();
if($obj->content=="searchmode" && $obj->search!==""){
$params["searchA"]='%'.$obj->search.'%';
$params["searchB"]='%'.$obj->search.'%';
$params["searchC"]='%'.$obj->search.'%';
$params["searchD"]='%'.$obj->search.'%';
$params["searchE"]='%'.$obj->search.'%';
$params["searchF"]='%'.$obj->search.'%';
$params["searchG"]='%'.$obj->search.'%';
$params["searchH"]='%'.$obj->search.'%';
}else if($obj->content=="recoverymode" && $obj->token!==""){
$params["token"]=$obj->token;
$params["status"]="active";
}
return $params;
}

private function setUsers(){
$query="SELECT * FROM users WHERE login=:login";
return $query;
}

public function chama(basecontent $obj){
$resposta=$obj->pesquisar($obj);
return $resposta;
}

private function pesquisar(basecontent $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
$authentic=null;
$tipo=null;
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$authentic=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$tipodecript=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$tipodecript);
$tipo=$tipoexplode[1];
}
$base=new database();
$base->sql=$obj->setSearch($obj);
$domtree=new DOMDocument('1.0','UTF-8');
$xmlRoot=$domtree->createElement("xml");
$xmlRoot=$domtree->appendChild($xmlRoot);
$params=array();
$currentTrack=$domtree->createElement("contents");
if($obj->content=="searchmode" || $obj->content=="recoverymode"){
$base->commando($obj->setParamSearch($obj));
}else{
$base->commandoquery();
}
$resultset=$base->fetchAll();
foreach($resultset as $row => $value){
if($obj->content!=="all" && $obj->content!=="top" && $obj->content!=="searchmode" && $obj->content!=="recoverymode" && $obj->content!=="orders" && $obj->content!=="billing" && $obj->content!=="banking" && $obj->content!=="profile" && $obj->content!=="messages" && $obj->content!=="myorders" && $obj->content!=="mybilling" && $obj->content!=="contact" && $obj->content!=="alladms" && $obj->content!=="allusers" && $obj->content!=="info" && $obj->content!=="privacy" && $obj->content!=="system" && $obj->content!=="notificationReader" && $obj->content!=="notificationManager"){
if($obj->content==$value['serial']){
if($tipo=="administrador" || $tipo=="gestor"){
$encrypttoken=AesCtr::encrypt($value['serial']."*".$value['titulo']."*".$value['autor']."*".$process->getGetDatetimeNow("complex"),$process->getPassOfCookie(),256);
setcookie("tokenid",$encrypttoken);
}
$obj->forcontentsmount($value,$obj,$process,$authentic,$tipo,$params,$base,$domtree,$xmlRoot,$currentTrack);
}
}else if($obj->content=="all" || $obj->content=="searchmode"){
setcookie('tokenid','',time()-999999);
unset($_COOKIE['tokenid']);
$obj->forcontentsmount($value,$obj,$process,$authentic,$tipo,$params,$base,$domtree,$xmlRoot,$currentTrack);
}else if($obj->content=="recoverymode"){
setcookie('tokenid','',time()-999999);
unset($_COOKIE['tokenid']);
$obj->forrecoverymount($value,$obj,$domtree,$xmlRoot,$currentTrack);
}else if($obj->content=="profile" || $obj->content=="orders" || $obj->content=="billing" || $obj->content=="banking" || $obj->content=="messages" || $obj->content=="myorders" || $obj->content=="mybilling" || $obj->content=="contact" || $obj->content=="alladms" || $obj->content=="allusers" || $obj->content=="info" || $obj->content=="privacy" || $obj->content=="system" || $obj->content=="notificationReader" || $obj->content=="notificationManager"){
if($obj->content=="profile"){
if($tipo=="administrador" || $tipo=="gestor" || $tipo=="usuario" || $tipo=="newuser"){
$obj->forprofilemount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="orders"){
if($tipo=="administrador" || $tipo=="gestor"){
$obj->forordersmount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="billing"){
if($tipo=="administrador" || $tipo=="gestor"){
$obj->forbillingmount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="banking"){
if($tipo=="administrador"){
$obj->forbankingmount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="myorders"){
if($tipo=="administrador" || $tipo=="gestor" || $tipo=="usuario"){
$obj->formyordersmount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="mybilling"){
if($tipo=="administrador" || $tipo=="gestor" || $tipo=="usuario"){
$obj->formybillingmount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="contact"){
$obj->forcontactmount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}
if($obj->content=="messages"){
$obj->formessagesmount($value,$obj,$authentic,$domtree,$xmlRoot,$currentTrack);
}
if($obj->content=="alladms"){
$obj->foralladmsmount($value,$obj,$domtree,$xmlRoot,$currentTrack);
}
if($obj->content=="allusers"){
if($tipo=="administrador"){
$obj->forallusersmount($value,$obj,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="info"){
$obj->forinfomount($value,$obj,$domtree,$xmlRoot,$currentTrack);
}
if($obj->content=="privacy"){
$obj->forinfomount($value,$obj,$domtree,$xmlRoot,$currentTrack);
}
if($obj->content=="system"){
if($tipo=="administrador"){
$obj->forsystemmount($value,$obj,$authentic,$tipo,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
if($obj->content=="notifications"){
if($tipo=="administrador"){
$obj->fornotifymount($value,$obj,$domtree,$xmlRoot,$currentTrack);
}else{
$obj->forerrormount($domtree,$xmlRoot,$currentTrack);
}
}
}
}
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('FIX',"Fix for list"));
if($tipo=="administrador" || $tipo=="gestor"){
$currentTrack->appendChild($domtree->createElement('creatable','1'));
}
$placeholder=end($currentTrack);
$resposta=$domtree->saveXML();
$resposta=simplexml_load_string($resposta,"SimpleXMLElement",LIBXML_NOCDATA);
return $resposta;
}

private function forcontentsmount(array $value,basecontent $obj,process $process,$authentic,$tipo,array $params,database $base,DOMDocument $domtree,$xmlRoot,$currentTrack){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('titulo',$value['titulo']));
$currentTrack->appendChild($domtree->createElement('descricao',$value['content']));
$currentTrack->appendChild($domtree->createElement('autor',$value['autor']));
$currentTrack->appendChild($domtree->createElement('categoria',$value['categoria']));
if(strcasecmp($value['categoria'],"curso")==0 || strcasecmp($value['categoria'],"cursos")==0){
$starttime=explode(";",$value['starttime']);
$endtime=explode(";",$value['endtime']);
$weekdays=explode(";",$value['weekdays']);
foreach($weekdays as $key => $day){
if($key>0){
$currentTrack->appendChild($domtree->createElement($day))->appendChild($domtree->createElement('checkbox','checked'))->parentNode
->appendChild($domtree->createElement($day.'start',$starttime[$key]))->parentNode
->appendChild($domtree->createElement($day.'end',$endtime[$key]))->parentNode;
}
}
$currentTrack->appendChild($domtree->createElement('inicio',$value['startdate']));
$currentTrack->appendChild($domtree->createElement('fim',$value['enddate']));
$currentTrack->appendChild($domtree->createElement('dias',$value['weekdays']));
$currentTrack->appendChild($domtree->createElement('entrada',$value['starttime']));
$currentTrack->appendChild($domtree->createElement('saida',$value['endtime']));
$currentTrack->appendChild($domtree->createElement('valor',$value['valor']));
$currentTrack->appendChild($domtree->createElement('multa',$value['multa'] * 100));
$currentTrack->appendChild($domtree->createElement('parcelas',$value['parcelas']));
$currentTrack->appendChild($domtree->createElement('carga',$value['carga']));
}
$currentTrack->appendChild($domtree->createElement('date',$value['date']));
$currentTrack->appendChild($domtree->createElement('serial',$value['serial']));
$currentTrack->appendChild($domtree->createElement('externalReference',$value['externalReference']));
$autortemp=$value['autor'];
$titletemp=$value['titulo'];
if($tipo=="administrador" || $tipo=="gestor" && $authentic==$autortemp){
$currentTrack->appendChild($domtree->createElement('editable','1'));
}
if($tipo=="administrador" || $tipo=="gestor" || $tipo=="usuario" && $authentic!==""){
if(strtolower($value['categoria'])=="cursos" || strtolower($value['categoria'])=="curso"){
$currentTrack->appendChild($domtree->createElement('orderable','1'));
}
}
$params["title"]=$value['titulo'];
$base->sql=$obj->setSearchMultimidia();
$base->commando($params);
$imagerootctrl=0;
$videorootctrl=0;
$root="";
$templink="";
foreach($base->fetchAll() as $row => $value){
if($value['subject']=="image" || $value['subject']=="video"){
if($value['subject']=="image"){
$root="image";
$templink=$value['link'];
if($imagerootctrl<1){
$currentTrack->appendChild($domtree->createElement('image'));
$imagerootctrl++;
}
}else if($value['subject']=="video"){
$root="video";
$templink="https://www.youtube.com/embed/".$value['name'];
if($videorootctrl<1){
$currentTrack->appendChild($domtree->createElement('video'));
$videorootctrl++;
}
}
$imageEdit='0';
if($tipo=="administrador" || $tipo=="gestor" && $authentic==$autortemp){
$imageEdit='1';
}
$currentTrack->appendChild($domtree->createElement($root))->appendChild($domtree->createElement('titulo',$value['titulo']))->parentNode
->appendChild($domtree->createElement('name',$value['name']))->parentNode
->appendChild($domtree->createElement('link',$templink))->parentNode
->appendChild($domtree->createElement('extension',$value['extension']))->parentNode
->appendChild($domtree->createElement('autor',$value['autor']))->parentNode
->appendChild($domtree->createElement('subject',$value['subject']))->parentNode
->appendChild($domtree->createElement('date',$value['date']))->parentNode
->appendChild($domtree->createElement('editable',$imageEdit))->parentNode;
}
}
$user=new database();
$user->sql=$obj->setUsers();
$user->commando(["login"=>$autortemp]);
foreach($user->fetchAll() as $row => $value){
$currentTrack->appendChild($domtree->createElement('nome',$value['nome']));
if($value['foto']!=="null"){
if($value['foto']=="Sem foto.png"){
$currentTrack->appendChild($domtree->createElement('foto',"storage/data/images/default/".$value['foto']));
}else{
$currentTrack->appendChild($domtree->createElement('foto',"storage/data/images/users/".$value['login']."/".$value['foto']));
}
}
}
$currentTrack->appendChild($domtree->createElement('loaded','contents'));
}

private function forrecoverymount(array $value,basecontent $obj,DOMDocument $domtree,$xmlRoot,$currentTrack){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
if($value['token']==$obj->token){
$currentTrack->appendChild($domtree->createElement('TOKEN',$obj->token));
}
}

private function forprofilemount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic==$value['login']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
if($value['foto']!=="Sem Foto.png"){
$currentTrack->appendChild($domtree->createElement('FOTO','storage/data/images/users/'.$value['login'].'/'.$value['foto']));
}else{
$currentTrack->appendChild($domtree->createElement('FOTO','storage/data/images/default/Sem Foto.png'));
}
$currentTrack->appendChild($domtree->createElement('LOGIN',$value['login']));
$currentTrack->appendChild($domtree->createElement('NOME',$value['nome']));
$currentTrack->appendChild($domtree->createElement('EMAIL',$value['email']));
if($value['telefone']!==null && $value['telefone']!==""){
$unmasktel=["(",")"," ","-"];
$currentTrack->appendChild($domtree->createElement('TEL',str_replace($unmasktel,"",$value['telefone'])));
}else{
$currentTrack->appendChild($domtree->createElement('TEL','0'));
}
if($value['cpf']!==null && $value['cpf']!==""){
$currentTrack->appendChild($domtree->createElement('CPF',$value['cpf']));
}else{
$currentTrack->appendChild($domtree->createElement('CPF','0'));
}
if($value['rg']!==null && $value['rg']!==""){
$currentTrack->appendChild($domtree->createElement('RG',$value['rg']));
}else{
$currentTrack->appendChild($domtree->createElement('RG','0'));
}
if($value['bio']!==null && $value['bio']!==""){
$currentTrack->appendChild($domtree->createElement('BIO',$value['bio']));
}else{
$currentTrack->appendChild($domtree->createElement('BIO','Biografia'));
}
$fulladdress=explode(";",$value['endereco']);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=$fulladdress[7];
if($logradouro!==null && $logradouro!==""){
$currentTrack->appendChild($domtree->createElement('LOG',$logradouro));
}else{
$currentTrack->appendChild($domtree->createElement('LOG',"Rua"));
}
if($endereco!==null && $endereco!==""){
$currentTrack->appendChild($domtree->createElement('END',$endereco));
}else{
$currentTrack->appendChild($domtree->createElement('END',"Endereco"));
}
if($numero!==null && $numero!==""){
$currentTrack->appendChild($domtree->createElement('NUMERO',$numero));
}else{
$currentTrack->appendChild($domtree->createElement('NUMERO',"Numero"));
}
if($complemento!==null && $complemento!==""){
$currentTrack->appendChild($domtree->createElement('COMP',$complemento));
}else{
$currentTrack->appendChild($domtree->createElement('COMP',"Complemento"));
}
if($bairro!==null && $bairro!==""){
$currentTrack->appendChild($domtree->createElement('BAIRRO',$bairro));
}else{
$currentTrack->appendChild($domtree->createElement('BAIRRO',"Bairro"));
}
if($cidade!==null  && $cidade!==""){
$currentTrack->appendChild($domtree->createElement('CIDADE',$cidade));
}else{
$currentTrack->appendChild($domtree->createElement('CIDADE',"Cidade"));
}
if($estado!==null && $estado!==""){
$currentTrack->appendChild($domtree->createElement('ESTADO',$estado));
}else{
$currentTrack->appendChild($domtree->createElement('ESTADO',"SP"));
}
if($cep!==null && $cep!==""){
$currentTrack->appendChild($domtree->createElement('CEP',$cep));
}else{
$currentTrack->appendChild($domtree->createElement('CEP',"CEP"));
}
$currentTrack->appendChild($domtree->createElement('loaded','profile'));
}
}

private function forordersmount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic!==""){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('numero',$value['numero']));
$currentTrack->appendChild($domtree->createElement('titulo',$value['titulo']));
$currentTrack->appendChild($domtree->createElement('serial',$value['serial']));
$currentTrack->appendChild($domtree->createElement('date',$value['date']));
$currentTrack->appendChild($domtree->createElement('situacao',$value['situacao']));
$currentTrack->appendChild($domtree->createElement('motivo',$value['motivo']));
$currentTrack->appendChild($domtree->createElement('feedback',$value['feedback']));
$currentTrack->appendChild($domtree->createElement('consultor',$value['consultor']));
$currentTrack->appendChild($domtree->createElement('login',$value['login']));
$currentTrack->appendChild($domtree->createElement('nome',$value['nome']));
$currentTrack->appendChild($domtree->createElement('cpf',$value['cpf']));
$currentTrack->appendChild($domtree->createElement('rg',$value['rg']));
$currentTrack->appendChild($domtree->createElement('email',$value['email']));
$currentTrack->appendChild($domtree->createElement('telefone',$value['telefone']));
$currentTrack->appendChild($domtree->createElement('startdate',$value['startdate']));
$currentTrack->appendChild($domtree->createElement('enddate',$value['enddate']));
$currentTrack->appendChild($domtree->createElement('multa',$value['multa'] * 100));
$rescbase=$value['valor'] * $value['parcelas'];
$currentTrack->appendChild($domtree->createElement('rescisao',round($rescbase * $value['multa'],2)));
$starttime=explode(";",$value['starttime']);
$endtime=explode(";",$value['endtime']);
$weekdays=explode(";",$value['weekdays']);
foreach($weekdays as $key => $day){
if($key>0 && $day!=="segunda" && $starttime!==null && $endtime!==null){
$currentTrack->appendChild($domtree->createElement($day))->appendChild($domtree->createElement('checkbox','checked'))->parentNode
->appendChild($domtree->createElement($day.'start',$starttime[$key]))->parentNode
->appendChild($domtree->createElement($day.'end',$endtime[$key]))->parentNode;
}
}
$currentTrack->appendChild($domtree->createElement('loaded','orders'));
}
}

private function forbillingmount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic!==""){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$fulladdress=explode(";",$value['endereco']);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=$fulladdress[7];
$formattedadress=$logradouro." ".$endereco.", ".$numero." ".$complemento.", ".$bairro.", ".$cidade.", ".$estado.", CEP ".$cep;
$currentTrack->appendChild($domtree->createElement('id',$value['numero']));
$currentTrack->appendChild($domtree->createElement('pedido',$value['pedido']));
$currentTrack->appendChild($domtree->createElement('serial',$value['serial']));
$currentTrack->appendChild($domtree->createElement('vencimento',$value['vencimento']));
$currentTrack->appendChild($domtree->createElement('valor',$value['valor']));
$currentTrack->appendChild($domtree->createElement('nome',$value['nome']));
$currentTrack->appendChild($domtree->createElement('cpf',$value['cpf']));
$currentTrack->appendChild($domtree->createElement('endereco',$formattedadress));
$currentTrack->appendChild($domtree->createElement('email',$value['email']));
$currentTrack->appendChild($domtree->createElement('demonstrativo',$value['demonstrativo']));
$currentTrack->appendChild($domtree->createElement('status',$value['status']));
$currentTrack->appendChild($domtree->createElement('empresa',$value['empresa']));
$currentTrack->appendChild($domtree->createElement('cnpj',$value['cnpj']));
$currentTrack->appendChild($domtree->createElement('superendereco',$value['superendereco']));
$currentTrack->appendChild($domtree->createElement('superemail',$value['superemail']));
$currentTrack->appendChild($domtree->createElement('agencia',$value['agencia']));
$currentTrack->appendChild($domtree->createElement('conta',$value['conta']));
$currentTrack->appendChild($domtree->createElement('digito',$value['cedente']));
$currentTrack->appendChild($domtree->createElement('registro',$value['registro']));
$currentTrack->appendChild($domtree->createElement('taxa',$value['taxa']));
$currentTrack->appendChild($domtree->createElement('loaded','billing'));
}
}

private function forbankingmount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic!==""){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('id',$value['numero']));
$currentTrack->appendChild($domtree->createElement('remessa',$value['remessa']));
if($value['retorno']==""){
$currentTrack->appendChild($domtree->createElement('retorno','Retorno não gerado'));
}else{
$currentTrack->appendChild($domtree->createElement('retorno',$value['retorno']));
}
$currentTrack->appendChild($domtree->createElement('vencimento',$value['vencimento']));
$currentTrack->appendChild($domtree->createElement('valor',$value['valor']));
$currentTrack->appendChild($domtree->createElement('nome',$value['nome']));
$currentTrack->appendChild($domtree->createElement('cpf',$value['cpf']));
$currentTrack->appendChild($domtree->createElement('status',$value['status']));
$currentTrack->appendChild($domtree->createElement('demonstrativo',$value['demonstrativo']));
$currentTrack->appendChild($domtree->createElement('loaded','banking'));
}
}

private function formyordersmount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic==$value['login']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('numero',$value['numero']));
$currentTrack->appendChild($domtree->createElement('titulo',$value['titulo']));
$currentTrack->appendChild($domtree->createElement('serial',$value['serial']));
$currentTrack->appendChild($domtree->createElement('date',$value['date']));
$currentTrack->appendChild($domtree->createElement('situacao',$value['situacao']));
$currentTrack->appendChild($domtree->createElement('motivo',$value['motivo']));
$currentTrack->appendChild($domtree->createElement('feedback',$value['feedback']));
$currentTrack->appendChild($domtree->createElement('consultor',$value['consultor']));
$currentTrack->appendChild($domtree->createElement('startdate',$value['startdate']));
$currentTrack->appendChild($domtree->createElement('enddate',$value['enddate']));
$starttime=explode(";",$value['starttime']);
$endtime=explode(";",$value['endtime']);
$weekdays=explode(";",$value['weekdays']);
foreach($weekdays as $key => $day){
if($key>0 && $day!=="segunda" && $starttime!==null && $endtime!==null){
$currentTrack->appendChild($domtree->createElement($day))->appendChild($domtree->createElement('checkbox','checked'))->parentNode
->appendChild($domtree->createElement($day.'start',$starttime[$key]))->parentNode
->appendChild($domtree->createElement($day.'end',$endtime[$key]))->parentNode;
}
}
$currentTrack->appendChild($domtree->createElement('loaded','myorders'));
}
}

private function formybillingmount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic==$value['login']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$fulladdress=explode(";",$value['endereco']);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=$fulladdress[7];
$formattedadress=$logradouro." ".$endereco.", ".$numero." ".$complemento.", ".$bairro.", ".$cidade.", ".$estado.", CEP ".$cep;
$currentTrack->appendChild($domtree->createElement('id',$value['numero']));
$currentTrack->appendChild($domtree->createElement('pedido',$value['pedido']));
$currentTrack->appendChild($domtree->createElement('serial',$value['serial']));
$currentTrack->appendChild($domtree->createElement('vencimento',$value['vencimento']));
$currentTrack->appendChild($domtree->createElement('valor',$value['valor']));
$currentTrack->appendChild($domtree->createElement('nome',$value['nome']));
$currentTrack->appendChild($domtree->createElement('cpf',$value['cpf']));
$currentTrack->appendChild($domtree->createElement('endereco',$formattedadress));
$currentTrack->appendChild($domtree->createElement('email',$value['email']));
$currentTrack->appendChild($domtree->createElement('demonstrativo',$value['demonstrativo']));
$currentTrack->appendChild($domtree->createElement('status',$value['status']));
$currentTrack->appendChild($domtree->createElement('empresa',$value['empresa']));
$currentTrack->appendChild($domtree->createElement('cnpj',$value['cnpj']));
$currentTrack->appendChild($domtree->createElement('superendereco',$value['superendereco']));
$currentTrack->appendChild($domtree->createElement('superemail',$value['superemail']));
$currentTrack->appendChild($domtree->createElement('agencia',$value['agencia']));
$currentTrack->appendChild($domtree->createElement('conta',$value['conta']));
$currentTrack->appendChild($domtree->createElement('digito',$value['cedente']));
$currentTrack->appendChild($domtree->createElement('registro',$value['registro']));
$currentTrack->appendChild($domtree->createElement('taxa',$value['taxa']));
$currentTrack->appendChild($domtree->createElement('loaded','mybilling'));
}
}

private function forcontactmount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic==$value['login']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('pedido',$value['numero']));
$currentTrack->appendChild($domtree->createElement('active',true));
$currentTrack->appendChild($domtree->createElement('loaded','contact'));
}else if($tipo=="administrador" && $authentic==$value['consultor']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('pedido',$value['numero']));
$currentTrack->appendChild($domtree->createElement('active',true));
$currentTrack->appendChild($domtree->createElement('managed','(G)'));
$currentTrack->appendChild($domtree->createElement('loaded','contact'));
}else if($tipo=="gestor" && $authentic==$value['consultor']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('pedido',$value['numero']));
$currentTrack->appendChild($domtree->createElement('active',true));
$currentTrack->appendChild($domtree->createElement('managed','(G)'));
$currentTrack->appendChild($domtree->createElement('loaded','contact'));
}else if($value['login']==""){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('pedido','0'));
$currentTrack->appendChild($domtree->createElement('active',false));
$currentTrack->appendChild($domtree->createElement('loaded','contact'));
}
}

private function formessagesmount(array $value,basecontent $obj,$authentic,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic==$value['destinatario']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('id',$value['id']));
$currentTrack->appendChild($domtree->createElement('pedido',$value['pedido']));
$currentTrack->appendChild($domtree->createElement('notificacao',$value['notificacao']));
$currentTrack->appendChild($domtree->createElement('remetente',$value['remetente']));
$currentTrack->appendChild($domtree->createElement('destinatario',$value['destinatario']));
$currentTrack->appendChild($domtree->createElement('assunto',$value['assunto']));
$currentTrack->appendChild($domtree->createElement('conteudo',$value['conteudo']));
$currentTrack->appendChild($domtree->createElement('date',$value['date']));
$currentTrack->appendChild($domtree->createElement('resumo',$value['resumo']));
$currentTrack->appendChild($domtree->createElement('loaded','messages'));
}
}

private function foralladmsmount(array $value,basecontent $obj,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($value['tipo']=="gestor" || $value['tipo']=="administrador"){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
if($value['foto']!=="Sem Foto.png"){
$currentTrack->appendChild($domtree->createElement('FOTO','storage/data/images/users/'.$value['login'].'/'.$value['foto']));
}else{
$currentTrack->appendChild($domtree->createElement('FOTO','storage/data/images/default/Sem Foto.png'));
}
$currentTrack->appendChild($domtree->createElement('NOME',$value['nome']));
if($value['bio']==""){
$currentTrack->appendChild($domtree->createElement('BIO',"Administrador"));
}else{
$currentTrack->appendChild($domtree->createElement('BIO',$value['bio']));
}
$currentTrack->appendChild($domtree->createElement('loaded','alladms'));
}
}

private function forallusersmount(array $value,basecontent $obj,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($tipo=="administrador"){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
if($value['foto']!=="Sem Foto.png"){
$currentTrack->appendChild($domtree->createElement('FOTO','storage/data/images/users/'.$value['login'].'/'.$value['foto']));
}else{
$currentTrack->appendChild($domtree->createElement('FOTO','storage/data/images/default/Sem Foto.png'));
}
$currentTrack->appendChild($domtree->createElement('LOGIN',$value['login']));
$currentTrack->appendChild($domtree->createElement('TIPO',$value['tipo']));
$currentTrack->appendChild($domtree->createElement('NOME',$value['nome']));
$currentTrack->appendChild($domtree->createElement('EMAIL',$value['email']));
if($value['telefone']!==null && $value['telefone']!==""){
$unmasktel=["(",")"," ","-"];
$currentTrack->appendChild($domtree->createElement('TEL',str_replace($unmasktel,"",$value['telefone'])));
}else{
$currentTrack->appendChild($domtree->createElement('TEL','0'));
}
if($value['cpf']!==null && $value['cpf']!==""){
$currentTrack->appendChild($domtree->createElement('CPF',$value['cpf']));
}else{
$currentTrack->appendChild($domtree->createElement('CPF','0'));
}
if($value['rg']!==null && $value['rg']!==""){
$currentTrack->appendChild($domtree->createElement('RG',$value['rg']));
}else{
$currentTrack->appendChild($domtree->createElement('RG','0'));
}
if($value['bio']!==null && $value['bio']!==""){
$currentTrack->appendChild($domtree->createElement('BIO',$value['bio']));
}else{
$currentTrack->appendChild($domtree->createElement('BIO','Biografia'));
}
if($value['endereco']!==null && $value['endereco']!==""){
$fulladdress=explode(";",$value['endereco']);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=$fulladdress[7];
$formattedadress=$logradouro." ".$endereco.", ".$numero." ".$complemento.", ".$bairro.", ".$cidade.", ".$estado.", CEP ".$cep;
$currentTrack->appendChild($domtree->createElement('END',$formattedadress));
}else{
$currentTrack->appendChild($domtree->createElement('END','Endereco'));
}
$currentTrack->appendChild($domtree->createElement('loaded','allusers'));
}
}

private function forinfomount(array $value,basecontent $obj,DOMDocument $domtree,$xmlRoot,$currentTrack){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('titulo',$value['titulo']));
$currentTrack->appendChild($domtree->createElement('info',$value['info']));
$currentTrack->appendChild($domtree->createElement('details',$value['details']));
$currentTrack->appendChild($domtree->createElement('privacy',$value['privacy']));
$currentTrack->appendChild($domtree->createElement('subject',$value['subject']));
$currentTrack->appendChild($domtree->createElement('loaded','info'));
}

private function forsystemmount(array $value,basecontent $obj,$authentic,$tipo,DOMDocument $domtree,$xmlRoot,$currentTrack){
if($authentic==$value['superlogin'] && $tipo=="administrador" && $tipo==$value['tipo']){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('senha',$value['supersenha']));
$currentTrack->appendChild($domtree->createElement('empresa',$value['empresa']));
$currentTrack->appendChild($domtree->createElement('cnpj',$value['cnpj']));
$fulladdress=explode(";",$value['superendereco']);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=$fulladdress[7];
if($logradouro!==null && $logradouro!==""){
$currentTrack->appendChild($domtree->createElement('LOG',$logradouro));
}else{
$currentTrack->appendChild($domtree->createElement('LOG',"Rua"));
}
if($endereco!==null && $endereco!==""){
$currentTrack->appendChild($domtree->createElement('END',$endereco));
}else{
$currentTrack->appendChild($domtree->createElement('END',"Endereco"));
}
if($numero!==null && $numero!==""){
$currentTrack->appendChild($domtree->createElement('NUMERO',$numero));
}else{
$currentTrack->appendChild($domtree->createElement('NUMERO',"Numero"));
}
if($complemento!==null && $complemento!==""){
$currentTrack->appendChild($domtree->createElement('COMP',$complemento));
}else{
$currentTrack->appendChild($domtree->createElement('COMP',"Complemento"));
}
if($bairro!==null && $bairro!==""){
$currentTrack->appendChild($domtree->createElement('BAIRRO',$bairro));
}else{
$currentTrack->appendChild($domtree->createElement('BAIRRO',"Bairro"));
}
if($cidade!==null  && $cidade!==""){
$currentTrack->appendChild($domtree->createElement('CIDADE',$cidade));
}else{
$currentTrack->appendChild($domtree->createElement('CIDADE',"Cidade"));
}
if($estado!==null && $estado!==""){
$currentTrack->appendChild($domtree->createElement('ESTADO',$estado));
}else{
$currentTrack->appendChild($domtree->createElement('ESTADO',"SP"));
}
if($cep!==null && $cep!==""){
$currentTrack->appendChild($domtree->createElement('CEP',$cep));
}else{
$currentTrack->appendChild($domtree->createElement('CEP',"CEP"));
}
$currentTrack->appendChild($domtree->createElement('email',$value['superemail']));
$currentTrack->appendChild($domtree->createElement('agencia',$value['agencia']));
$currentTrack->appendChild($domtree->createElement('conta',$value['conta']));
$currentTrack->appendChild($domtree->createElement('digito',$value['digito']));
$currentTrack->appendChild($domtree->createElement('cedente',$value['cedente']));
$currentTrack->appendChild($domtree->createElement('registro',$value['registro']));
$currentTrack->appendChild($domtree->createElement('taxa',$value['taxa']));
$currentTrack->appendChild($domtree->createElement('loaded','system'));
}
}

private function fornotifymount(array $value,basecontent $obj,DOMDocument $domtree,$xmlRoot,$currentTrack){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('notifyId',$value['notifyId']));
$currentTrack->appendChild($domtree->createElement('product',$value['product']));
$currentTrack->appendChild($domtree->createElement('notifyMessage',$value['notifyMessage']));
$currentTrack->appendChild($domtree->createElement('dispatchDate',$value['dispatchDate']));
$currentTrack->appendChild($domtree->createElement('dispatchTime',$value['dispatchTime']));
$currentTrack->appendChild($domtree->createElement('notifyAuthor',$value['notifyAuthor']));
$currentTrack->appendChild($domtree->createElement('loaded','notifications'));
}

private function forerrormount(DOMDocument $domtree,$xmlRoot,$currentTrack){
$currentTrack=$domtree->createElement("contents");
$currentTrack=$xmlRoot->appendChild($currentTrack);
$currentTrack->appendChild($domtree->createElement('ERROR','403 Forbidden Access'));
}

}
	
?>