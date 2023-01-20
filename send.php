<?php

require_once __DIR__.'/process.php';

$resposta=process::acesso();

if(isset($resposta['APPRESPONSE'])){
$status=200;
$status_header='HTTP/1.1 '.$status.''.getStatusCodeMessage($status);
//$content_type='text/html';
//$content_type='application/json';
header($status_header);
//header('Content-type: '.$content_type);
//$msg=$resposta["APPRESPONSE"];
//$msg=json_encode($resposta);
$aaa=new stdClass();
$aaa->aaas="aaa";
$msg=json_encode($aaa);
}else{
$msg=json_encode($resposta);
}
if(isset($resposta['RESPONSE']) && $resposta['RESPONSE']!==""){
include_once(__DIR__.'/response.php');
}else if(isset($resposta['BOLETO'])){
$data_venc=date("d/m/Y",$resposta['BOLETO']["data_vencimento"]);
$dadosboleto=$resposta['BOLETO'];
include_once("frameworks/boletophp/include/funcoes_cef_sigcb.php");
include_once("frameworks/boletophp/include/layout_cef.php");
}else{
print_r($msg);
}

function getStatusCodeMessage($status){
    $codes = Array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );
    return (isset($codes[$status])) ? $codes[$status]:'';
}

?>