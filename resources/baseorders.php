<?php

require realpath('../vendor/autoload.php');
require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class baseorders {
	
private $query;

private $action;

private $login;

private $tipo;

private $title;

private $serial;

private $autor;

private $categoria;

private $date;

private $parcelas;

private $vencimento;

private $validatordata;

public function __construct($action,$title,$serial,$autor,$date,$parcelas,$vencimento,$validatordata){
$this->action=$action;
$this->title=$title;
$this->serial=$serial;
$this->autor=$autor;
$this->date=$date;
$this->parcelas=$parcelas;
$this->vencimento=$vencimento;
$this->validatordata=$validatordata;
}

private function setPreSelect(baseorders $obj){
if($obj->action=="orderscreator"){
$query="SELECT pedidos.numero,pedidos.situacao,pedidos.motivo FROM pedidos WHERE hashtag=:hashtag";
}else if($obj->action=="ordersvalidator"){
$query="SELECT pedidos.numero,pedidos.situacao FROM pedidos WHERE numero=:pedido";
}else if($obj->action=="orderscancel"){
$query="SELECT pedidos.numero,pedidos.serial,pedidos.titulo,pedidos.login,pedidos.cpf,pedidos.nome,pedidos.endereco,pedidos.email,pedidos.situacao,pedidos.valor,pedidos.parcelas,pedidos.multa,pedidos.vencimento FROM pedidos WHERE numero=:pedido";
}
return $query;
}

private function setParamPreCreatorSearch(baseorders $obj){
$params=array();
if($obj->action=="orderscreator"){
$params["hashtag"]=$obj->login."#".$obj->serial;
}
return $params;
}

private function setParamPreValidatorSearch(baseorders $obj){
$params=array();
if($obj->action=="ordersvalidator"){
$params["pedido"]=$obj->validatordata["pedido"];
}
return $params;
}

private function setParamPreCancelSearch(baseorders $obj){
$params=array();
if($obj->action=="orderscancel"){
$params["pedido"]=$obj->validatordata["pedido"];
}
return $params;
}

private function setSearch(baseorders $obj){
$query="SELECT users.login,users.nome,users.cpf,users.rg,users.email,users.telefone,users.endereco,content.titulo,content.serial,content.autor,content.startdate,content.enddate,content.weekdays,content.starttime,content.endtime,content.valor,content.multa,content.carga FROM users INNER JOIN content WHERE users.login=:login AND users.tipo=:tipo AND content.titulo=:title AND content.serial=:serial AND content.autor=:autor AND LOWER(content.categoria) LIKE :categoria";
return $query;
}

private function setParamSearch(baseorders $obj){
$params=array();
$params["login"]=$obj->login;
$params["tipo"]=$obj->tipo;
$params["title"]=$obj->title;
$params["serial"]=$obj->serial;
$params["autor"]=$obj->autor;
$params["categoria"]='%'.$obj->categoria.'%';
return $params;
}

private function setPostSelect(baseorders $obj){
if($obj->action=="orderscreator"){
$query="SELECT pedidos.numero,pedidos.serial,pedidos.titulo,pedidos.vencimento,pedidos.valor,pedidos.parcelas,pedidos.nome,pedidos.cpf,pedidos.endereco,pedidos.email FROM pedidos WHERE hashtag=:hashtag";
}else if($obj->action=="ordersvalidator"){
$query="SELECT pedidos.login,pedidos.numero,pedidos.serial,pedidos.titulo,pedidos.vencimento,pedidos.valor,pedidos.parcelas,pedidos.nome,pedidos.cpf,pedidos.endereco,pedidos.email FROM pedidos WHERE numero=:pedido";
}else if($obj->action=="orderscommunication"){
$query="SELECT pedidos.login,pedidos.situacao,pedidos.feedback FROM pedidos WHERE numero=:pedido AND login=:login";
}
return $query;
}

private function setParamPostCreatorSearch(baseorders $obj){
$params=array();
$params["hashtag"]=$obj->login."#".$obj->serial;
return $params;
}

private function setParamPostValidatorSearch(baseorders $obj){
$params=array();
$params["pedido"]=$obj->validatordata["pedido"];
return $params;
}

private function setParamPostCommunicatorSearch(baseorders $obj){
$params=array();
$params["pedido"]=$obj->validatordata["pedido"];
$params["login"]=$obj->login;
return $params;
}

private function setInsert(){
$query="INSERT INTO pedidos (hashtag,titulo,serial,situacao,motivo,consultor,login,nome,cpf,rg,email,telefone,endereco,startdate,enddate,weekdays,starttime,endtime,valor,multa,parcelas,vencimento,carga) VALUES (:hashtag,:title,:serial,:situacao,:motivo,:consultor,:login,:nome,:cpf,:rg,:email,:telefone,:endereco,:startdate,:enddate,:weekdays,:starttime,:endtime,:valor,:multa,:parcelas,:vencimento,:carga)";
//INSERT INTO `pedidos` (hashtag,titulo,serial,situacao,motivo,consultor,login,nome,cpf,rg,email,telefone,endereco,startdate,enddate,weekdays,starttime,endtime,valor,multa,parcelas,vencimento,carga) VALUES ("aaauser#302793049026","Curso teste.","302793049026","Aguardando","Aguardando analise desde 12-05-2019","admin","aaauser","AAA","122.431.755-68","454545904","aaa@aaa.com","(11) 99999-9777","Rua;Um;1;;Jardim;Suzano;SP;08345-589","30-04-2019","23-05-2019",";terca;quinta",";09-00 AM;12-30 PM ",";11-00 AM;01-45 PM","515","0","2","10","120")
return $query;
}

private function setParamInsert(baseorders $obj){
$params=array();
$params["hashtag"]=$obj->login."#".$obj->serial;
$params["title"]=$obj->title;
$params["serial"]=$obj->serial;
$params["situacao"]="Aguardando";
$params["motivo"]="Aguardando analise desde ".$obj->date;
$params["consultor"]=$obj->autor;
$params["login"]=$obj->login;
$params["nome"]=$obj->nome;
$params["cpf"]=$obj->cpf;
$params["rg"]=$obj->rg;
$params["email"]=$obj->email;
$params["telefone"]=$obj->telefone;
$params["endereco"]=$obj->endereco;
$params["startdate"]=$obj->startdate;
$params["enddate"]=$obj->enddate;
$params["weekdays"]=$obj->weekdays;
$params["starttime"]=$obj->starttime;
$params["endtime"]=$obj->endtime;
$params["valor"]=$obj->valor;
$params["multa"]=$obj->multa;
$params["parcelas"]=$obj->parcelas;
$params["vencimento"]=$obj->vencimento;
$params["carga"]=$obj->carga;
return $params;
}

private function setUpdate(){
$query="UPDATE pedidos SET situacao=:situacao, motivo=:motivo, feedback=:feedback WHERE numero=:pedido AND serial=:serial AND cpf=:cpf AND email=:email";
return $query;
}

private function setParamUpdate(baseorders $obj){
$params=array();
$params["situacao"]=$obj->validatordata["situacao"];
$params["motivo"]=$obj->validatordata["motivo"];
$params["feedback"]="Aguardando leitura";
$params["pedido"]=$obj->validatordata["pedido"];
$params["serial"]=$obj->serial;
$params["cpf"]=$obj->validatordata["cpf"];
$params["email"]=$obj->validatordata["email"];
return $params;
}

private function setPostCommunicationUpdate(){
$query="UPDATE pedidos SET feedback=:feedback WHERE numero=:pedido AND login=:login";
return $query;
}

private function setParamPostCommunicationUpdate(baseorders $obj){
$params=array();
$params["feedback"]=$obj->validatordata["feedback"];
$params["pedido"]=$obj->validatordata["pedido"];
$params["login"]=$obj->login;
return $params;
}

private function setDelete(){
$query="DELETE FROM pedidos WHERE numero=:pedido AND serial=:serial AND cpf=:cpf AND email=:email";
return $query;
}

private function setParamDelete(baseorders $obj){
$params=array();
$params["pedido"]=$obj->validatordata["pedido"];
$params["serial"]=$obj->serial;
$params["cpf"]=$obj->validatordata["cpf"];
$params["email"]=$obj->validatordata["email"];
return $params;
}

public function chama(baseorders $obj){
$resposta=array();
$resposta['RESPONSE']="Access denied.";
if($obj->action=="orderscreator" && $obj->title!=="" && $obj->serial!=="" && $obj->autor!=="" && $obj->date!=="" && $obj->parcelas!==""){
$resposta=$obj->orders_creator($obj);
}else if($obj->action=="ordersvalidator" && $obj->validatordata["pedido"]!=="" && $obj->title!=="" && $obj->serial!=="" && $obj->validatordata["situacao"]!=="" && $obj->validatordata["motivo"]!=="" && $obj->validatordata["cpf"]!=="" && $obj->validatordata["email"]!==""){
$resposta=$obj->orders_validator($obj);
}if($obj->action=="orderscommunication" && $obj->validatordata["pedido"]!=="" && $obj->validatordata["feedback"]!==""){
$resposta=$obj->orders_communicator($obj);
}else if($obj->action=="orderscancel" && $obj->validatordata["pedido"]!=="" && $obj->title!=="" && $obj->serial!=="" && $obj->validatordata["situacao"]!=="" && $obj->validatordata["motivo"]!=="" && $obj->validatordata["cpf"]!=="" && $obj->validatordata["email"]!==""){
$resposta=$obj->orders_cancel($obj);
}
return $resposta;
}

private function orders_creator(baseorders $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$gotlogin=$loginexplode[0];
if($decryptlogin==$gotlogin){
$resposta['ACTOR']=$decryptlogin;
$obj->login=$decryptlogin;
}
$obj->tipo=$tipoexplode[1];
$obj->categoria="curso";
if($obj->login!=="" && $obj->tipo=="administrador" || $obj->tipo=="gestor" || $obj->tipo=="usuario"){
$base=new database();
$base->sql=$obj->setPreSelect($obj);
$base->commando($obj->setParamPreCreatorSearch($obj));
$resultset=$base->fetch();
if($resultset['numero']=="" || $resultset['situacao']=="Reprovado" || $resultset['situacao']=="Finalizado"){
$base->sql=$obj->setSearch($obj);
$base->commando($obj->setParamSearch($obj));
$resultset=$base->fetchAll();
if($resultset!==null && is_array($resultset)){
$obj->nome=$resultset[0]['nome'];
$obj->cpf=$resultset[0]['cpf'];
$obj->rg=$resultset[0]['rg'];
$obj->email=$resultset[0]['email'];
$obj->telefone=$resultset[0]['telefone'];
$obj->endereco=$resultset[0]['endereco'];
$obj->startdate=$resultset[0]['startdate'];
$obj->enddate=$resultset[0]['enddate'];
$obj->weekdays=$resultset[0]['weekdays'];
$obj->starttime=$resultset[0]['starttime'];
$obj->endtime=$resultset[0]['endtime'];
$obj->valor=round($resultset[0]['valor'] / $obj->parcelas,2);
$obj->multa=$resultset[0]['multa'];
$obj->carga=$resultset[0]['carga'];
$base->sql=$obj->setInsert();
$base->commando($obj->setParamInsert($obj));
$base->sql=$obj->setPostSelect($obj);
$base->commando($obj->setParamPostCreatorSearch($obj));
$resultset=$base->fetch();
$assunto="Order ".$resultset['numero'].", ".$obj->title.": Waiting for analysis.";
$conteudo="Your request ".$resultset['numero']." was successfully created. Wait for the course creator review to respond by message in the app.";
$resumo=$obj->getResumo("Aguardando");
$msgobj=new basemessages("messagescreator",$resultset['numero'],$obj->autor,$obj->login,$assunto,$conteudo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if($msgobjresp['RESPONSE']=="Message sent successfully."){
$resposta['RESPONSE']="Order ".$resultset['numero']. " successfully created.";
}else{
$resposta['RESPONSE']="Failed to create the request ".$resultset['numero'];
}
}else{
$resposta['RESPONSE']="Non-existent contract.";
}
}else{
$resposta['RESPONSE']="Order ".$resultset['numero']." already exists and ".strtolower($resultset['motivo']).".";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function orders_validator(baseorders $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$gotlogin=$loginexplode[0];
if($decryptlogin==$gotlogin){
$resposta['ACTOR']=$decryptlogin;
$obj->login=$decryptlogin;
}
$obj->tipo=$tipoexplode[1];
if($obj->login!=="" && $obj->tipo=="administrador" || $obj->login!=="" && $obj->tipo=="gestor"){
if($obj->validatordata["situacao"]=="Aprovado" || $obj->validatordata["situacao"]=="Finalizado"){
$obj->validatordata["motivo"]=$obj->validatordata["motivo"]." ".$process->getGetDatetimeNow("complex");
}
$base=new database();
$base->sql=$obj->setPreSelect($obj);
$base->commando($obj->setParamPreValidatorSearch($obj));
$resultset=$base->fetch();
if($resultset['situacao']!==$obj->validatordata["situacao"]){
$base->sql=$obj->setUpdate();
$base->commando($obj->setParamUpdate($obj));
$base->sql=$obj->setPostSelect($obj);
$base->commando($obj->setParamPostValidatorSearch($obj));
$resultset=$base->fetch();
$assunto="Pedido ".$obj->validatordata["pedido"].", ".$obj->title.": ".$obj->validatordata["situacao"];
$conteudo="Seu pedido foi ".$obj->validatordata["pedido"]." foi ".strtolower($obj->validatordata["situacao"]).". Motivo: ".$obj->validatordata["motivo"]."";
$resumo=$obj->getResumo($obj->validatordata["situacao"]);
$msgobj=new basemessages("messagescreator",$obj->validatordata["pedido"],$obj->login,$resultset['login'],$assunto,$conteudo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if($msgobjresp['RESPONSE']=="Message sent successfully."){
if($obj->validatordata["situacao"]=="Aprovado"){
$billingobj=new basebilling("billingcreator",$resultset['numero'],$resultset['serial'],$resultset['titulo'],$resultset['vencimento'],$resultset['valor'],$resultset['parcelas'],$resultset['login'],$resultset['nome'],$resultset['cpf'],$resultset['endereco'],$resultset['email'],null,null);
$billingobjresp=$billingobj->chama($billingobj);
if($billingobjresp['RESPONSE']=="Successful billing."){
$resposta['RESPONSE']="Order ".$obj->validatordata["pedido"]." ".strtolower($obj->validatordata["situacao"])." successfully.";
}else{
$resposta['RESPONSE']="Order ".$obj->validatordata["pedido"]." ".strtolower($obj->validatordata["situacao"])." successfully, but there was an error in billing.";
}
}else{
$resposta['RESPONSE']="Order ".$obj->validatordata["pedido"]." ".strtolower($obj->validatordata["situacao"])." successfully.";
}
}else{
$resposta['RESPONSE']="Failed to validate the request ".$obj->validatordata["pedido"];
}
}else{
$resposta['RESPONSE']="Order ".$resultset['numero']." was already ".strtolower($obj->validatordata["situacao"]);
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function orders_communicator(baseorders $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
if($decryptlogin!==""){
$resposta['ACTOR']=$decryptlogin;
$obj->login=$decryptlogin;
$base=new database();
$base->sql=$obj->setPostSelect($obj);
$base->commando($obj->setParamPostCommunicatorSearch($obj));
$resultset=$base->fetch();
if($resultset['situacao']!=="" && $obj->validatordata["pedido"]!=="" && $obj->validatordata["feedback"]!==""){
if($resultset['feedback']=="Aguardando leitura"){
$verbs=["ção","do","mento","de","dando"];
$resumocheck=explode(" ",$obj->validatordata["feedback"]);
$resumo=str_replace($verbs,"",$resumocheck[0]);
$situacao=str_replace($verbs,"",$resultset['situacao']);
if($resumo==$situacao){
$base->sql=$obj->setPostCommunicationUpdate();
$base->commando($obj->setParamPostCommunicationUpdate($obj));
$resposta['RESPONSE']="Order updated.";
}else{
$resposta['RESPONSE']="Incompatible request status with the message.";
}
}else{
$resposta['RESPONSE']="Request was already updated.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}
}
return $resposta;
}

private function orders_cancel(baseorders $obj){
$resposta=array();
$object=["object"=>$obj];
$process=new process($object);
if(isset($_COOKIE['login']) && isset($_COOKIE['id']) && isset($_COOKIE['auth'])){
$decryptlogin=AesCtr::decrypt($_COOKIE['login'],$process->getPassOfCookie(),256);
$decryptauth=AesCtr::decrypt($_COOKIE['auth'],$process->getPassOfCookie(),256);
$tipoexplode=explode("#",$decryptauth);
$loginexplode=explode("*",$decryptauth);
$gotlogin=$loginexplode[0];
if($decryptlogin==$gotlogin){
$resposta['ACTOR']=$decryptlogin;
$obj->login=$decryptlogin;
}
$obj->tipo=$tipoexplode[1];
if($obj->login!=="" && $obj->tipo=="administrador" || $obj->login!=="" && $obj->tipo=="gestor"){
$base=new database();
$base->sql=$obj->setPreSelect($obj);
$base->commando($obj->setParamPreCancelSearch($obj));
$resultset=$base->fetch();
if($resultset['situacao']!=="Aguardando"){
$rescisaovalor=0;
$rescisaoparcelas=0;
$rescmsg="";
if($obj->validatordata["situacao"]=="Cancelado" && $resultset['situacao']!=="Reprovado" && $resultset['multa']>0){
$rescisaovalor=$resultset['valor'] * $resultset['parcelas'];
$rescisaovalor=$rescisaovalor * $resultset['multa'];
if($rescisaovalor===0){
$rescmsg="The remaining portions of the order were cancelled and there was no termination fine.";
}else if($rescisaovalor>0){
$rescmsg="The remaining portions of the order were cancelled, but there was a termination fine in the amount of R$ ".$rescisaovalor." for payment via boleto.";
$rescisaoparcelas=1;
}
}
$assunto="Order ".$obj->validatordata["pedido"].", ".$obj->title.": ".$obj->validatordata["situacao"];
$conteudo="Your request was ".$obj->validatordata["pedido"]." was ".strtolower($obj->validatordata["situacao"]).". Cause: ".$obj->validatordata["motivo"].". ".$rescmsg;
$resumo=$obj->getResumo($obj->validatordata["situacao"]);
$msgobj=new basemessages("messagescreator",$obj->validatordata["pedido"],$obj->login,$resultset['login'],$assunto,$conteudo,$resumo,null);
$msgobjresp=$msgobj->chama($msgobj);
if($msgobjresp['RESPONSE']=="Message sent successfully."){
if($obj->validatordata["situacao"]=="Cancelado"){
$billingobj=new basebilling("billingcancel",$resultset['numero'],$resultset['serial'],$resultset['titulo'],$resultset['vencimento'],$rescisaovalor,$rescisaoparcelas,$resultset['login'],$resultset['nome'],$resultset['cpf'],$resultset['endereco'],$resultset['email'],null,null);
$billingobjresp=$billingobj->chama($billingobj);
if($billingobjresp['RESPONSE']=="Cancellation of invoices carried out successfully."){
$base->sql=$obj->setDelete($obj);
$base->commando($obj->setParamDelete($obj));
$resposta['RESPONSE']="Order ".$obj->validatordata["pedido"]." ".strtolower($obj->validatordata["situacao"])." successfully.";
}
}else if($obj->validatordata["situacao"]=="Finalizado"){
$base->sql=$obj->setDelete($obj);
$base->commando($obj->setParamDelete($obj));
$resposta['RESPONSE']="Order ".$obj->validatordata["pedido"]." ".strtolower($obj->validatordata["situacao"])." successfully.";
}
}else{
$resposta['RESPONSE']="Failed to validate the request ".$obj->validatordata["pedido"];
}
}else{
$resposta['RESPONSE']="Order ".$resultset['numero']." ".strtolower($resultset['situacao'])." feedback. Please give feedback on the order before cancelling.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function getResumo($situacao){
switch($situacao){
case "Aprovado":
$resumo="Aprovação";
break;
case "Reprovado":
$resumo="Reprovação";
break;
case "Finalizado":
$resumo="Finalização";
break;
case "Cancelado":
$resumo="Cancelamento";
break;
default: $resumo="Aguarde";
}
return $resumo;
}

}

?>