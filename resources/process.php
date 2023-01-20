<?php

require_once __DIR__.'/basesessao.php';
require_once __DIR__.'/basecadastro.php';
require_once __DIR__.'/baselogin.php';
require_once __DIR__.'/basesessao.php';
require_once __DIR__.'/baseaccount.php';
require_once __DIR__.'/baselogout.php';
require_once __DIR__.'/basecontent.php';
require_once __DIR__.'/basecreation.php';
require_once __DIR__.'/baseedit.php';
require_once __DIR__.'/basedelete.php';
require_once __DIR__.'/baseimage.php';
require_once __DIR__.'/basevideo.php';
require_once __DIR__.'/baseorders.php';
require_once __DIR__.'/baseprofile.php';
require_once __DIR__.'/basemessages.php';
require_once __DIR__.'/basebilling.php';
require_once __DIR__.'/basebanking.php';
require_once __DIR__.'/baseappnotify.php';
require_once __DIR__.'/basesystem.php';
require_once __DIR__.'/frameworks/aes.class.php';

class process {

private $entity;
const decriptpass="Umasenhaenigmatica555444999000";

/*decripted cookie getter*/
public function getPassOfCookie(){
        return self::decriptpass;
    }

/*Attribute entity getter*/
public function getEntity(){
        return $this->entity;
    }

/*Constructor*/
public function __construct(array $objs){
    
        if($objs['object'] instanceof basecadastro ||
           $objs['object'] instanceof baselogin ||
           $objs['object'] instanceof basesessao ||
           $objs['object'] instanceof baseaccount ||
           $objs['object'] instanceof baselogout ||
           $objs['object'] instanceof basecontent ||
           $objs['object'] instanceof basecreation ||
           $objs['object'] instanceof baseedit ||
           $objs['object'] instanceof basedelete ||
           $objs['object'] instanceof baseorders ||
           $objs['object'] instanceof baseprofile ||
           $objs['object'] instanceof basemessages ||
           $objs['object'] instanceof basebilling ||
           $objs['object'] instanceof basebanking ||
           $objs['object'] instanceof baseappnotify ||
           $objs['object'] instanceof basesystem
           ){
        $this->entity=$objs['object'];
        }
    }

public static function acesso(){
    
    $base=array();
    $cont=0;
    
    date_default_timezone_set('America/Sao_Paulo');
    
//Login

        if(isset($_POST['login']) && isset($_POST['senha'])){
        $cont=1;
        $action="logar";
        $login=$_POST['login'];
        $senha=$_POST['senha'];
        $tipo="";
        if($login!==null && $senha!==null){
        $base['object']=new baselogin($login,$senha,$tipo);
        }
        }

//Cadastro

        if(isset($_POST['login']) && isset($_POST['nome']) && isset($_POST['senha']) && isset($_POST['email']) && isset($_POST['conditions'])){
        $cont=1;
        $action="cadastrar";
        $login=$_POST['login'];
        $nome=$_POST['nome'];
        $senha=$_POST['senha'];
        $email=$_POST['email'];
        $conditions=false;
        if($_POST['conditions']=="agreed"){
        $conditions=true;
        }
        if($login!=="" && $nome!=="" && $senha!=="" && $email!=="" && $conditions==true){
        $base['object']=new basecadastro($login,$nome,$senha,$email,$conditions);
        }
        }
        
//Account Recover Sender

        if(isset($_POST['recsenha']) && isset($_POST['recemail'])){
        $cont=1;
        $action="accountrecoversender";
        $senha=$_POST['recsenha'];
        $email=$_POST['recemail'];
        $base['object']=new baseaccount($action,$email,$senha,null,null,null,null);
        }
        
//Account Recover Receiver

        if(isset($_POST['rectoken'])){
        $cont=1;
        $action="accountrecoverreceiver";
        $rectoken=$_POST['rectoken'];
        $base['object']=new baseaccount($action,null,null,null,null,null,$rectoken);
        }
        
//Account join team

        if(isset($_POST['action']) && $_POST['action']=="accountjointeam"){
        $cont=1;
        $action=$_POST['action'];
        $login=$_POST['accountlogin'];
        $email=$_POST['accountemail'];
        $situacao=$_POST['accountsituacao'];
        $motivo=$_POST['motivo'];
        $base['object']=new baseaccount($action,$email,null,$login,$situacao,$motivo);
        }
        
//Account leave team

        if(isset($_POST['action']) && $_POST['action']=="accountleaveteam"){
        $cont=1;
        $action=$_POST['action'];
        $login=$_POST['accountlogin'];
        $email=$_POST['accountemail'];
        $situacao=$_POST['accountsituacao'];
        $motivo=$_POST['motivo'];
        $base['object']=new baseaccount($action,$email,null,$login,$situacao,$motivo);
        }
        
//Account suspend

        if(isset($_POST['action']) && $_POST['action']=="accountsuspend"){
        $cont=1;
        $action=$_POST['action'];
        $login=$_POST['accountlogin'];
        $email=$_POST['accountemail'];
        $situacao=$_POST['accountsituacao'];
        $motivo=$_POST['motivo'];
        $base['object']=new baseaccount($action,$email,null,$login,$situacao,$motivo);
        }
        
//Account reactivate

        if(isset($_POST['action']) && $_POST['action']=="accountreactivate"){
        $cont=1;
        $action=$_POST['action'];
        $login=$_POST['accountlogin'];
        $email=$_POST['accountemail'];
        $situacao=$_POST['accountsituacao'];
        $motivo=$_POST['motivo'];
        $base['object']=new baseaccount($action,$email,null,$login,$situacao,$motivo);
        }
        
//Account cancel

        if(isset($_POST['action']) && $_POST['action']=="accountcancel"){
        $cont=1;
        $action=$_POST['action'];
        $login=$_POST['accountlogin'];
        $email=$_POST['accountemail'];
        $situacao=$_POST['accountsituacao'];
        $motivo=$_POST['motivo'];
        $base['object']=new baseaccount($action,$email,null,$login,$situacao,$motivo);
        }

//Sessao

        if(isset($_POST['sessao'])){
        $cont=1;
        $action="sessao";
        $check=$_POST['sessao'];
        $sectid=null;
        $sectlogin=null;
        $sectauth=null;
        if($check!==null){
        if(isset($_COOKIE['id']) && isset($_COOKIE['login']) && isset($_COOKIE['auth'])){
        $sectid=$_COOKIE['id'];
        $sectlogin=$_COOKIE['login'];
        $sectauth=$_COOKIE['auth'];
        }
        $base['object']=new basesessao($sectid,$sectlogin,$sectauth);
        }
        }
        
//Logout

        if(isset($_POST['logout'])){
        $cont=1;
        $action="sair";
        $logout=$_POST['logout'];
        if($logout!==null){
        if(isset($_COOKIE['id']) && isset($_COOKIE['login']) && isset($_COOKIE['auth'])){
        $validateid=preg_replace('/\s+/','+',$logout);
        $logoutid=$_COOKIE['id'];
        $logoutlogin=$_COOKIE['login'];
        $logoutauth=$_COOKIE['auth'];
        if($logoutid!==null && $logoutlogin!==null){
        $base['object']=new baselogout($validateid,$logoutid,$logoutlogin,$logoutauth);
        }
        }
        }
        }

//Content loader

        if(isset($_POST['content']) && isset($_POST['search']) && isset($_POST['token'])){
        $cont=1;
        $action="content";
        $content=$_POST['content'];
        $search=$_POST['search'];
        $token=$_POST['token'];
        $base['object']=new basecontent($token,$search,$content);
        }
	
//Content Creator

        if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['categoria']) && isset($_FILES['attach'])){
        $cont=1;
        $action="criar";
        $title=["newTitle"=>$_POST['title']];
        $content=$_POST['content'];
        $categoria=$_POST['categoria'];
        $mediaData=$_FILES['attach'];
        $videoData=$_POST['videos'];
        if($_POST['inicio']=="" && $_POST['fim']=="" && $_POST['hourstart']=="" && $_POST['hourend']=="" && $_POST['weekdays']=="" && $_POST['valor']=="" && $_POST['multa']=="" && $_POST['parcelas']=="" && $_POST['carga']==""){
        $cursodata=["inicio"=>null,"fim"=>null,"horarioini"=>null,"horariofim"=>null,"dias"=>null,"valor"=>null,"multa"=>null,"parcelas"=>null,"carga"=>null];
        }else{
        $cursodata=["inicio"=>$_POST['inicio'],"fim"=>$_POST['fim'],"horarioini"=>$_POST['hourstart'],"horariofim"=>$_POST['hourend'],"dias"=>$_POST['weekdays'],"valor"=>$_POST['valor'],"multa"=>$_POST['multa'],"parcelas"=>$_POST['parcelas'],"carga"=>$_POST['carga']];
        }
        if($title['newtitle']!=="" && $content!=="" && $categoria!==""){
        $base['object']=new basecreation($title,$content,$categoria,$mediaData,$videoData,$cursodata);
        }
        }

//Content Editor

        if(isset($_POST['newtitle']) && isset($_POST['newcontent']) && isset($_POST['newcategoria']) && isset($_POST['serial'])){
        $cont=1;
        $action="editar";
        if(isset($_COOKIE['tokenid'])){
        $tokenid=$_COOKIE['tokenid'];
        $title=["newTitle"=>$_POST['newtitle']];
        $content=$_POST['newcontent'];
        $categoria=$_POST['newcategoria'];
        $mediaData=$_FILES['files'];
        $videoData=$_POST['newvideos'];
        if($_POST['newinicio']=="" && $_POST['newfim']=="" && $_POST['newhourstart']=="" && $_POST['newhourend']=="" && $_POST['newweekdays']=="" && $_POST['newvalor']=="" && $_POST['newmulta']=="" && $_POST['newparcelas']=="" && $_POST['newcarga']==""){
        $cursodata=["inicio"=>null,"fim"=>null,"horarioini"=>null,"horariofim"=>null,"dias"=>null,"valor"=>null,"multa"=>null,"parcelas"=>null,"carga"=>null];
        }else{
        $cursodata=["inicio"=>$_POST['newinicio'],"fim"=>$_POST['newfim'],"horarioini"=>$_POST['newhourstart'],"horariofim"=>$_POST['newhourend'],"dias"=>$_POST['newweekdays'],"valor"=>$_POST['newvalor'],"multa"=>$_POST['newmulta'],"parcelas"=>$_POST['newparcelas'],"carga"=>$_POST['newcarga']];
        }
        $serial=$_POST['serial'];
        if($tokenid!=="" && $title['newtitle']!=="" && $content!=="" && $categoria!=="" && $serial!==""){
        $base['object']=new baseedit($tokenid,$title,$content,$categoria,$serial,$mediaData,$videoData,$cursodata);
        }
        }
        }
        
//Content Deleter

        if(isset($_POST['action']) && $_POST['action']=="contentdeleter"){
        $cont=1;
        if(isset($_POST['deltitle']) && isset($_POST['serial'])){
        $action=$_POST['action'];
        $title=["delTitle"=>$_POST['deltitle']];
        $serial=$_POST['serial'];
        $base['object']=new basedelete($title,$serial,null,$action);
        }
        }
        
//Multimidia Deleter

        if(isset($_POST['action']) && $_POST['action']=="multimidiadeleter" || isset($_POST['action']) && $_POST['action']=="multimidiatruncater"){
        $cont=1;
        if(isset($_POST['deltitle']) && isset($_POST['medianame'])){
        $action=$_POST['action'];
        $title=["delTitle"=>$_POST['deltitle']];
        $mediaName=["name"=>$_POST['medianame']];
        $base['object']=new basedelete($title,null,$mediaName,$action);
        }
        }
        
//Orders creator

        if(isset($_POST['action']) && $_POST['action']=="orderscreator"){
        if(isset($_POST['gettitle']) && isset($_POST['getserial']) && isset($_POST['getautor']) && isset($_POST['getdate'])){
        $cont=1;
        $action=$_POST['action'];
        $title=$_POST['gettitle'];
        $serial=$_POST['getserial'];
        $autor=$_POST['getautor'];
        $date=$_POST['getdate'];
        $parcelas=$_POST['parcelas'];
        $vencimento=$_POST['vencimento'];
        $validatordata=null;
        if($title!=="" && $serial!=="" && $autor!=="" && $date!=="" && $parcelas!=="" && $vencimento!==""){
        $base['object']=new baseorders($action,$title,$serial,$autor,$date,$parcelas,$vencimento,$validatordata);
        }
        }
        }
        
//Orders validator

        if(isset($_POST['action']) && $_POST['action']=="ordersvalidator"){
        if(isset($_POST['pedido']) && isset($_POST['title']) && isset($_POST['serial']) && isset($_POST['situacao']) && isset($_POST['motivo']) && isset($_POST['cpf']) && isset($_POST['email'])){
        $cont=1;
        $action=$_POST['action'];
        $validatordata=["pedido"=>$_POST['pedido'],"situacao"=>$_POST['situacao'],"motivo"=>$_POST['motivo'],"cpf"=>$_POST['cpf'],"email"=>$_POST['email']];
        $title=$_POST['title'];
        $serial=$_POST['serial'];
        $autor=null;
        $date=null;
        $parcelas=null;
        $vencimento=null;
        if($validatordata["pedido"]!=="" && $title!=="" && $serial!=="" && $validatordata["situacao"]!=="" && $validatordata["motivo"]!=="" && $validatordata["cpf"]!=="" && $validatordata["email"]!==""){
        $base['object']=new baseorders($action,$title,$serial,$autor,$date,$parcelas,$vencimento,$validatordata);
        }
        }
        }
        
//Orders cancel
        
        if(isset($_POST['action']) && $_POST['action']=="orderscancel"){
        if(isset($_POST['pedido']) && isset($_POST['title']) && isset($_POST['serial']) && isset($_POST['situacao']) && isset($_POST['motivo']) && isset($_POST['cpf']) && isset($_POST['email'])){
        $cont=1;
        $action=$_POST['action'];
        $validatordata=["pedido"=>$_POST['pedido'],"situacao"=>$_POST['situacao'],"motivo"=>$_POST['motivo'],"cpf"=>$_POST['cpf'],"email"=>$_POST['email']];
        $title=$_POST['title'];
        $serial=$_POST['serial'];
        $autor=null;
        $date=null;
        $parcelas=null;
        $vencimento=null;
        if($validatordata["pedido"]!=="" && $title!=="" && $serial!=="" && $validatordata["situacao"]!=="" && $validatordata["motivo"]!=="" && $validatordata["cpf"]!=="" && $validatordata["email"]!==""){
        $base['object']=new baseorders($action,$title,$serial,$autor,$date,$parcelas,$vencimento,$validatordata);
        }
        }
        }
        
//Billing loader

        if(isset($_POST['action']) && $_POST['action']=="billingloader"){
        if(isset($_POST['id']) && isset($_POST['demonstrativo'])){
        $cont=1;
        $action=$_POST['action'];
        $id=$_POST['id'];
        $demonstrativo=$_POST['demonstrativo'];
        if($id!=="" && $demonstrativo!==""){
        $base['object']=new basebilling($action,null,null,null,null,null,null,null,null,null,null,null,$id,$demonstrativo);
        }
        }
        }
        
//Banking downloader

        if(isset($_POST['action']) && $_POST['action']=="remessadownloader"){
        $cont=1;
        $action=$_POST['action'];
        if(isset($_POST['id'])){
        $id=$_POST['id'];
        }
        $base['object']=new basebanking($action,$id,null);
        }
        
//Banking uploader

        if(isset($_POST['action']) && $_POST['action']=="retornouploader"){
        if(isset($_FILES['retornos'])){
        $cont=1;
        $action=$_POST['action'];
        $retornos=$_FILES['retornos'];
        $base['object']=new basebanking($action,null,$retornos);
        }
        }
        
//Banking pay checker

        if(isset($_POST['action']) && $_POST['action']=="paychecker" || isset($_POST['action']) && $_POST['action']=="payunchecker"){
        $cont=1;
        $action=$_POST['action'];
        if(isset($_POST['id'])){
        $id=$_POST['id'];
        }
        $base['object']=new basebanking($action,$id,null);
        }
        
//Profile Editor

        if(isset($_POST['action']) && $_POST['action']=="profileditor"){
        if(isset($_POST['nome']) && isset($_POST['cpf']) && isset($_POST['rg']) && isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['bio']) && isset($_FILES['foto']) && isset($_POST['defaultfoto'])){
        $cont=1;
        $action=$_POST['action'];
        $nome=$_POST['nome'];
        $cpf=$_POST['cpf'];
        $rg=$_POST['rg'];
        $email=$_POST['email'];
        $telefone=$_POST['telefone'];
        $endereco=$_POST['fulladdress'];
        $bio=$_POST['bio'];
        if(isset($_FILES['foto'])){
        $foto=$_FILES['foto'];
        }
        if($_FILES['foto']['name']==""){
        $foto['name']=$_POST['defaultfoto'];
        }
        if($nome!=="" && $cpf!=="" && $rg!=="" && $email!=="" && $telefone!=="" && $foto['name']!==""){
        $base['object']=new baseprofile($action,$nome,$cpf,$rg,$email,$telefone,$endereco,$bio,$foto);
        }
        }
        }
        
//Messages creator

        if(isset($_POST['action']) && $_POST['action']=="messagescreator"){
        if(isset($_POST['message']) && isset($_POST['targettext']) && isset($_POST['resumo']) && isset($_POST['assunto']) && isset($_POST['appendix'])){
        $cont=1;
        $action=$_POST['action'];
        $message=$_POST['message'];
        $target=$_POST['targettext'];
        $resumo=$_POST['resumo'];
        $assunto=$_POST['assunto'].". ".$_POST['appendix'];
        $base['object']=new basemessages($action,$target,null,null,$assunto,$message,$resumo,null);
        }
        }
        
//Messages reader

        if(isset($_POST['action']) && $_POST['action']=="messagesreader"){
        if(isset($_POST['id']) && isset($_POST['pedido']) && isset($_POST['resumo'])){
        $cont=1;
        $action=$_POST['action'];
        $id=$_POST['id'];
        $pedido=$_POST['pedido'];
        $resumo=$_POST['resumo'];
        $base['object']=new basemessages($action,$pedido,null,null,null,null,$resumo,$id);
        }
        }
        
//Messages deleter

        if(isset($_POST['action']) && $_POST['action']=="messagesdeleter"){
        if(isset($_POST['id']) && isset($_POST['pedido']) && isset($_POST['resumo'])){
        $cont=1;
        $action=$_POST['action'];
        $id=$_POST['id'];
        $pedido=$_POST['pedido'];
        $resumo=$_POST['resumo'];
        $base['object']=new basemessages($action,$pedido,null,null,null,null,$resumo,$id);
        }
        }
        
//App notify creator
        
        if(isset($_POST['action']) && $_POST['action']=="notifyCreator"){
        if(isset($_POST['notifyText']) && isset($_POST['notifyDate']) && isset($_POST['notifyTime']) && isset($_POST['notifyProduct'])){
        $cont=1;
        $action=$_POST['action'];
        $notifyText=$_POST['notifyText'];
        $notifyDate=$_POST['notifyDate'];
        $notifyTime=$_POST['notifyTime'];
        $notifyProduct=$_POST['notifyProduct'];
        $base['object']=new baseappnotify($action,null,$notifyText,$notifyDate,$notifyTime,$notifyProduct,null);
        }
        }

//App notify deleter
        
        if(isset($_POST['action']) && $_POST['action']=="notifyDeleter"){
        if(isset($_POST['notifyId'])){
        $cont=1;
        $action=$_POST['action'];
        $notifyId=$_POST['notifyId'];
        $base['object']=new baseappnotify($action,$notifyId,null,null,null,null,null);
        }
        }
        
//App notify reader
        
        if(isset($_POST['action']) && $_POST['action']=="notifyReading" && isset($_POST['data'])){
        if(isset($_POST['notifyProduct'])){
        $cont=1;
        $action=$_POST['action'];
        $data=$_POST['data'];
        $notifyProduct=$_POST['notifyProduct'];
        $base['object']=new baseappnotify($action,null,null,null,null,$notifyProduct,$data);
        }
        }
        
//System configurator

        if(isset($_POST['action']) && $_POST['action']=="systemconfig"){
        if(isset($_POST['empresa']) && isset($_POST['cnpj']) && isset($_POST['fulladdress']) && $_POST['email'] && isset($_POST['agencia']) && isset($_POST['conta']) && isset($_POST['digito']) && isset($_POST['cedente']) && isset($_POST['registro']) && isset($_POST['taxa']) && isset($_POST['senha'])){
        $cont=1;
        $action=$_POST['action'];
        $empresa=$_POST['empresa'];
        $cnpj=$_POST['cnpj'];
        $endereco=$_POST['fulladdress'];
        $email=$_POST['email'];
        $agencia=$_POST['agencia'];
        $conta=$_POST['conta'];
        $digito=$_POST['digito'];
        $cedente=$_POST['cedente'];
        $registro=$_POST['registro'];
        $taxa=$_POST['taxa'];
        $senha=$_POST['senha'];
        $base['object']=new basesystem($action,$empresa,$cnpj,$endereco,$email,$agencia,$conta,$digito,$cedente,$registro,$taxa,$senha);
        }
        }

//Call process

        if($cont==0){
        http_response_code(403);
        }
        
        $obj=new process($base);
        $resposta=$obj->getEntity()->chama($obj->getEntity());
        
        return $resposta;

    }

/*Process tool*/
public function getNum_gen($tamanho,$force){
$resposta=$this->num_gen($tamanho,$force);
return $resposta;
}

/*Process tool*/
public function getGetDatetimeNow($complexity){
$resposta=$this->datetimeNow($complexity);
return $resposta;
}

/*Process tool*/
public function getrealval($Num1,$Num2,$Scale){
$resposta=$this->realval($Num1,$Num2,$Scale);
return $resposta;
}

/*Process tool*/
private function num_gen($tamanho,$force){
$bytes=openssl_random_pseudo_bytes($tamanho,$force);
$hex=bin2hex($bytes);
return $hex;
}

/*Process tool*/
private function datetimeNow($complexity){
$retornar=0;
$tz_object=new DateTimeZone('Brazil/East');
$datetime=new DateTime();
$datetime->setTimezone($tz_object);
if($complexity=="simple"){
$retornar=$datetime->format('d-m-Y');
}
else{
$retornar=$datetime->format('d-m-Y H:i:s');
}
return $retornar;
}

/*Process tool*/
private function realval($Num1,$Num2,$Scale=null){
if(!preg_match("/^\+?(\d+)(\.\d+)?$/",$Num1,$Tmp1) || !preg_match("/^\+?(\d+)(\.\d+)?$/",$Num2,$Tmp2)) return('0'); 
$Output=array(); 
$Dec1=isset($Tmp1[2])?rtrim(substr($Tmp1[2],1),'0'):''; 
$Dec2=isset($Tmp2[2])?rtrim(substr($Tmp2[2],1),'0'):''; 
$DLen=max(strlen($Dec1),strlen($Dec2)); 
if($Scale==null) $Scale=$DLen; 
$Num1=strrev(ltrim($Tmp1[1],'0').str_pad($Dec1,$DLen,'0')); 
$Num2=strrev(ltrim($Tmp2[1],'0').str_pad($Dec2,$DLen,'0')); 
$MLen=max(strlen($Num1),strlen($Num2)); 
$Num1=str_pad($Num1,$MLen,'0'); 
$Num2=str_pad($Num2,$MLen,'0'); 
for($i=0;$i<$MLen;$i++){ 
$Sum=((int)$Num1[$i]+(int)$Num2[$i]); 
if(isset($Output[$i])) $Sum+=$Output[$i]; 
$Output[$i]=$Sum%10; 
if($Sum>9) $Output[$i+1]=1; 
} 
$Output=strrev(implode($Output)); 
$Decimal=str_pad(substr($Output,-$DLen,$Scale),$Scale,'0'); 
$Output=(($MLen-$DLen<1)?'0':substr($Output,0,-$DLen)); 
$Output.=(($Scale>0)?".{$Decimal}":''); 
return($Output);
}

/*Process tool*/
public function cleanstring($string){
if(!preg_match('/[\x80-\xff]/',$string)){
return $string;
}
$chars=array(
chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
chr(195).chr(191) => 'y',
chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
);
$string=strtr($string,$chars);
return $string;
}

}

?>