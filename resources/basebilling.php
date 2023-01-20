<?php

require realpath('../vendor/autoload.php');
require_once __DIR__.'/base.php';
require_once __DIR__.'/process.php';
require_once __DIR__.'/frameworks/aes.class.php';

class basebilling {
	
private $query;

private $login;

private $pedido;

private $serial;

private $titulo;

private $vencimento;

private $limite;

private $valor;

private $parcelas;

private $nome;

private $cpf;

private $cnpj;

private $endereco;

private $email;

private $id;

private $remessa;

private $emissao;

private $action;

public function __construct($action,$pedido,$serial,$titulo,$vencimento,$valor,$parcelas,$login,$nome,$cpf,$endereco,$email,$id,$demonstrativo){
$this->action=$action;
$this->pedido=$pedido;
$this->serial=$serial;
$this->titulo=$titulo;
$this->vencimento=$vencimento;
$this->valor=$valor;
$this->parcelas=(int)$parcelas;
$this->nome=$nome;
$this->login=$login;
$this->cpf=$cpf;
$this->endereco=$endereco;
$this->email=$email;
$this->id=$id;
$this->demonstrativo=$demonstrativo;
}

private function setPreSearch(){
$query="SELECT * FROM contas WHERE numero=:id";
return $query;
}

private function setParamPreSearch(basebilling $obj){
$params=array();
$params["id"]=$obj->id;
return $params;
}

private function setInsert(){
$query="INSERT INTO contas (numero,pedido,serial,vencimento,valor,login,nome,cpf,endereco,email,demonstrativo,emissao) VALUES (:id,:pedido,:serial,:vencimento,:valor,:login,:nome,:cpf,:endereco,:email,:demonstrativo,:emissao)";
return $query;
}

private function setParamInsert(basebilling $obj){
$params=array();
$params["id"]=$obj->id;
$params["pedido"]=$obj->pedido;
$params["serial"]=$obj->serial;
$params["vencimento"]=$obj->limite;
$params["valor"]=$obj->valor;
$params["login"]=$obj->login;
$params["nome"]=$obj->nome;
$params["cpf"]=$obj->cpf;
$params["endereco"]=$obj->endereco;
$params["email"]=$obj->email;
$params["demonstrativo"]=$obj->demonstrativo;
$params["emissao"]=$obj->emissao;
return $params;
}

private function setPostSearch(){
$query="SELECT * FROM contas WHERE demonstrativo=:demonstrativo";
return $query;
}

private function setParamPostSearch(basebilling $obj){
$params=array();
$params["demonstrativo"]=$obj->demonstrativo;
return $params;
}

private function setPostUpdate(){
$query="UPDATE contas SET remessa=:remessa WHERE pedido=:pedido AND numero=:id AND demonstrativo=:demonstrativo";
return $query;
}

private function setParamPostUpdate(basebilling $obj){
$params=array();
$params["remessa"]=$obj->remessa;
$params["pedido"]=$obj->pedido;
$params["id"]=$obj->id;
$params["demonstrativo"]=$obj->demonstrativo;
return $params;
}

private function setUpdate(){
$query="UPDATE contas SET status=:status WHERE pedido=:pedido AND serial=:serial AND cpf=:cpf AND status=:returned";
return $query;
}

private function setParamUpdate(basebilling $obj){
$params=array();
if($obj->action=="billingcancel"){
$params["status"]="Cancelado";
}
$params["pedido"]=$obj->pedido;
$params["serial"]=$obj->serial;
$params["cpf"]=$obj->cpf;
$params["returned"]="Pendente";
return $params;
}

private function setSearch(){
$query="SELECT * FROM internal WHERE tipo=:tipo";
return $query;
}

private function setParamSearch(basebilling $obj){
$params=array();
$params["tipo"]="administrador";
return $params;
}
    	
public function chama(basebilling $obj){
$resposta=array();
if($obj->action=="billingloader"){
$resposta=$obj->billing_loader($obj);
}else if($obj->action=="billingcreator"){
$resposta=$obj->billing_creator($obj);
}else if($obj->action=="billingcancel"){
$resposta=$obj->billing_cancel($obj);
}
return $resposta;
}

private function billing_loader(basebilling $obj){
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
if($decryptlogin==$gotlogin){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$this->setPreSearch();
$base->commando($this->setParamPreSearch($obj));
$resultsetA=$base->fetch();
$base->sql=$this->setSearch();
$base->commando($this->setParamSearch($obj));
$resultsetB=$base->fetch();
$fulladdress=explode(";",$resultsetA['endereco']);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=$fulladdress[7];
$superfulladdress=explode(";",$resultsetB['superendereco']);
$superlogradouro=$superfulladdress[0];
$superendereco=$superfulladdress[1];
$supernumero=$superfulladdress[2];
$supercomplemento=$superfulladdress[3];
$superbairro=$superfulladdress[4];
$supercidade=$superfulladdress[5];
$superestado=$superfulladdress[6];
$supercep=$superfulladdress[7];
$emissao=new \DateTime($resultsetA['emissao']);
$vencimento=new \DateTime($resultsetA['vencimento']);

$dadosboleto["nosso_numero1"]="000";
$dadosboleto["nosso_numero_const1"]="1";
$dadosboleto["nosso_numero2"]="000";
$dadosboleto["nosso_numero_const2"]="4";
$dadosboleto["nosso_numero3"]=$resultsetA['numero'];

$dadosboleto["numero_documento"]="PEDIDO".$resultsetA['pedido'];
$dadosboleto["data_vencimento"]=date($vencimento->format('d/m/Y'));
$dadosboleto["data_documento"]=date($emissao->format('d/m/Y'));
$dadosboleto["data_processamento"]=date($emissao->format('d/m/Y'));
$dadosboleto["valor_boleto"]=number_format($resultsetA['valor']+$resultsetB['taxa'],2,',','');

$dadosboleto["sacado"]=$resultsetA['nome'];
$dadosboleto["endereco1"]=$logradouro." ".$endereco." ".$numero." ".$complemento." ".$bairro;
$dadosboleto["endereco2"]=$cidade." - ".$estado." - CEP: ".$cep;

$dadosboleto["demonstrativo1"]=$resultsetA['demonstrativo'];
$dadosboleto["demonstrativo2"]="Taxa do banco - R$ ".number_format($resultsetB['taxa'],2,',','');
$dadosboleto["demonstrativo3"]="";

$dadosboleto["instrucoes1"]="- Sr. Caixa, cobrar multa de 0% depois do vencimento";
$dadosboleto["instrucoes2"]="- Receber 45 dias depois do vencimento";
$dadosboleto["instrucoes3"]="- Em caso de duvidas entre em contato conosco: ".$resultsetB['superemail'];
$dadosboleto["instrucoes4"]="";

$dadosboleto["quantidade"]="";
$dadosboleto["valor_unitario"]="";
$dadosboleto["aceite"]="N";		
$dadosboleto["especie"]="R$";
$dadosboleto["especie_doc"]="DM";

$dadosboleto["agencia"]=$resultsetB['agencia'];
$dadosboleto["conta"]=$resultsetB['conta'];
$dadosboleto["conta_dv"]=$resultsetB['digito'];

$dadosboleto["conta_cedente"]=$resultsetB['cedente'];
$dadosboleto["carteira"]="CR";

$dadosboleto["identificacao"]=$resultsetB['empresa'];
$dadosboleto["cpf_cnpj"]=$resultsetB['cnpj'];
$dadosboleto["endereco"]=$superlogradouro." ".$superendereco." ".$supernumero." ".$supercomplemento." ".$superbairro;
$dadosboleto["cidade_uf"]=$supercidade." - ".$superestado." - CEP: ".$supercep;
$dadosboleto["cedente"]=$resultsetB['empresa'];

$resposta['BOLETO']=$dadosboleto;
}else{
$resposta['RESPONSE']="Acesso negado";
}
}else{
$resposta['RESPONSE']="Acesso negado";
}
return $resposta;
}

private function billing_creator(basebilling $obj){
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
if($decryptlogin==$gotlogin){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$this->setSearch();
$base->commando($this->setParamSearch($obj));
$resultset=$base->fetch();
$unmaskcnpj=[".","/","-"];
$fullsuperaddress=explode(";",$resultset['superendereco']);
$superlogradouro=$fullsuperaddress[0];
$superendereco=$fullsuperaddress[1];
$supernumero=$fullsuperaddress[2];
$supercomplemento=$fullsuperaddress[3];
$superbairro=$fullsuperaddress[4];
$supercidade=$fullsuperaddress[5];
$superestado=$fullsuperaddress[6];
$supercep=str_replace("-","",$fullsuperaddress[7]);
$codigoBanco=\Cnab\Banco::CEF;
$cnabFactory=new \Cnab\Factory();
$arquivo=$cnabFactory->createRemessa($codigoBanco,'cnab240','sigcb');
$boletoconfig=array();
$boletoconfig['data_geracao']=new \DateTime();
$boletoconfig['data_gravacao']=new \DateTime();
$boletoconfig['nome_fantasia']=$resultset['empresa'];
$boletoconfig['razao_social']=$resultset['empresa'];
$boletoconfig['cnpj']=str_replace($unmaskcnpj,"",$resultset['cnpj']);
$boletoconfig['banco']=$codigoBanco;
$boletoconfig['logradouro']=$superlogradouro." ".$superendereco;
$boletoconfig['numero']=$supernumero." ".$supercomplemento;
$boletoconfig['bairro']=$superbairro;
$boletoconfig['cidade']=$supercidade;
$boletoconfig['uf']=$superestado;
$boletoconfig['cep']=$supercep;
$boletoconfig['conta']=$resultset['conta'].$resultset['digito'];
$boletoconfig['operacao']='003';
$boletoconfig['agencia']=$resultset['agencia'];
$boletoconfig['agencia_dv']='0';
$boletoconfig['codigo_cedente']=$resultset['cedente'];
$boletoconfig['numero_sequencial_arquivo']=1;
$arquivo->configure($boletoconfig);
$parcela=1;
$obj->vencimento-=1;
$date=new DateTime();
$today=new DateTime();
$date->modify('first day of next month');
$date->modify(''.$obj->vencimento.' day');
$fulladdress=explode(";",$obj->endereco);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=str_replace("-","",$fulladdress[7]);
$obj->id=rand(100000000,999999999);
while($parcela<=$obj->parcelas){
$obj->id+=1;
$obj->demonstrativo="Parcela ".$parcela." de ".$obj->parcelas." do curso ".$obj->titulo." Pedido ".$obj->pedido;
$base->sql=$obj->setPreSearch();
$base->commando($obj->setParamPreSearch($obj));
$resultset=$base->fetch();
if($resultset==null){
if($parcela!==1){
$date->modify('+1 month');
}
$obj->limite=$date->format('d-m-Y');
$obj->emissao=$today->format('d-m-Y');
$base->sql=$obj->setInsert();
$base->commando($obj->setParamInsert($obj));
$parcela+=1;
$base->sql=$obj->setPostSearch();
$base->commando($obj->setParamPostSearch($obj));
$resultset=$base->fetch();
$obj->id=$resultset['numero'];
$fulladdress=explode(";",$resultset['endereco']);
$logradouro=$fulladdress[0];
$endereco=$fulladdress[1];
$numero=$fulladdress[2];
$complemento=$fulladdress[3];
$bairro=$fulladdress[4];
$cidade=$fulladdress[5];
$estado=$fulladdress[6];
$cep=$fulladdress[7];
$boletoinfo=array();
$boletoinfo['codigo_ocorrencia']=1;
$boletoinfo['nosso_numero']=$resultset['numero'];
$boletoinfo['numero_documento']='PEDIDO'.$resultset['pedido'].$resultset['numero'];
$boletoinfo['carteira']='111';
$boletoinfo['especie']=\Cnab\Especie::CNAB240_OUTROS;
$boletoinfo['aceite']='N';
$boletoinfo['registrado']=1;
$boletoinfo['modalidade_carteira']='21';
$boletoinfo['valor']=$resultset['valor'];
$boletoinfo['instrucao1']='Sr. Caixa, cobrar multa de 0% após o vencimento';
$boletoinfo['instrucao2']='Em caso de dúvidas entre em contato conosco: atdesp@gmail.com';
$boletoinfo['sacado_razao_social']=$resultset['valor'];
$boletoinfo['sacado_tipo']='cpf';
$boletoinfo['sacado_cpf']=$resultset['cpf'];
$boletoinfo['sacado_logradouro']=$logradouro." ".$endereco.", ".$numero." ".$complemento;
$boletoinfo['sacado_bairro']=$bairro;
$boletoinfo['sacado_cep']=$cep;
$boletoinfo['sacado_cidade']=$cidade;
$boletoinfo['sacado_uf']=$estado;
$boletoinfo['data_vencimento']=$date;
$boletoinfo['data_cadastro']=$today;
$boletoinfo['juros_de_um_dia']=0.0;
$boletoinfo['data_desconto']=$date;
$boletoinfo['valor_desconto']=10.0;
$boletoinfo['prazo']=45;
$boletoinfo['taxa_de_permanencia']='0';
$boletoinfo['mensagem']=$resultset['demonstrativo'];
$boletoinfo['data_multa']=$date;
$boletoinfo['valor_multa']=0.0;
$boletoinfo['baixar_apos_dias']=30;
$boletoinfo['dias_iniciar_contagem_juros']=1;
$arquivo->insertDetalhe($boletoinfo);
$uploadfolder="../storage/data/billing/remessa/PEDIDO".$obj->pedido;
$uploadfile="../storage/data/billing/remessa/PEDIDO".$obj->pedido."/Pedido".$obj->pedido."-".$resultset['numero'].".txt";
if(!file_exists($uploadfolder)){
mkdir($uploadfolder,0777,true);
}
$arquivo->save($uploadfile);
if(file_exists($uploadfile)){
$obj->remessa="storage/data/billing/remessa/PEDIDO".$obj->pedido."/Pedido".$obj->pedido."-".$resultset['numero'].".txt";
$base->sql=$obj->setPostUpdate();
$base->commando($obj->setParamPostUpdate($obj));
}
}else{
$parcela=$obj->parcelas+1;
$resposta['RESPONSE']="Error performing billing, try again.";
}
}
if($parcela-1==$obj->parcelas){
$resposta['RESPONSE']="Successful billing.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
}else{
$resposta['RESPONSE']="Access denied.";
}
return $resposta;
}

private function billing_cancel(basebilling $obj){
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
if($decryptlogin==$gotlogin){
$resposta['ACTOR']=$decryptlogin;
$base=new database();
$base->sql=$this->setUpdate();
$base->commando($this->setParamUpdate($obj));
if($obj->valor>0 && $obj->parcelas===1){
$obj->vencimento-=1;
$date=new DateTime();
$date->modify('first day of next month');
$date->modify(''.$obj->vencimento.' day');
$obj->demonstrativo="Multa rescisoria ".$obj->parcelas." de ".$obj->parcelas." do curso ".$obj->titulo;
$date->modify('+1 month');
$obj->limite=$date->format('d-m-Y');
$base->sql=$obj->setInsert();
$base->commando($obj->setParamInsert($obj));
$resposta['RESPONSE']="Cancellation of invoices carried out successfully.";
}else{
$resposta['RESPONSE']="Cancellation of invoices carried out successfully.";
}
/*Delete a remessa em storage/billing*/
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