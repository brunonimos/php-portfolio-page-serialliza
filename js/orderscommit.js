var orderscommit=(function(){
function orderscommit(){}
function inprivate(button){
var pedido=button.id;
$(document).on('confirmation','.remodal',function(){
var divValidator=this.children[1];
var form=divValidator.children[0];
form.action="./resources/send.php";
if(divValidator.id=="approve"){
var orderApprove=form.pedido;
var titleApprove=form.title;
var serialApprove=form.serial;
var motivoApprove=form.motivo;
var situacaoApprove=form.situacao;
var cpfApprove=form.cpf;
var emailApprove=form.email;
if(orderApprove.value!=="" && titleApprove.value!=="" && serialApprove.value!=="" && motivoApprove.value!=="" && situacaoApprove.value!=="" && cpfApprove.value!=="" && emailApprove.value!==""){
form.submit();
}
}
if(divValidator.id=="reprove"){
var orderReprove=form.pedido;
var titleReprove=form.title;
var serialReprove=form.serial;
var motivoReprove=form.motivo;
var situacaoReprove=form.situacao;
var cpfReprove=form.cpf;
var emailReprove=form.email;
if(orderReprove.value!=="" && titleReprove.value!=="" && serialReprove.value!=="" && motivoReprove.value!=="" && situacaoReprove.value!=="" && cpfReprove.value!=="" && emailReprove.value!==""){
form.submit();
}
}
});
if(button.name=="ordercommit"){
$('[data-remodal-id='+pedido+']').remodal({
NAMESPACE:'remodal',
DEFAULTS:{
hashTracking:true,
closeOnConfirm:true,
closeOnCancel:true,
closeOnEscape:true,
closeOnOutsideClick:true,
modifier:''
}
});
}
if(button.name=="ordercancel"){
$('[data-remodal-id='+pedido+']').remodal({
NAMESPACE:'remodal',
DEFAULTS:{
hashTracking:true,
closeOnConfirm:true,
closeOnCancel:true,
closeOnEscape:true,
closeOnOutsideClick:true,
modifier:''
}
});
}
}
orderscommit.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return orderscommit;
})();
var postorders=(function(){
function postorders(){}
function inprivate(button){
var pedido=button.id;
$(document).on('confirmation','.remodal',function(){
var divValidator=this.children[1];
var form=divValidator.children[0];
form.action="./resources/send.php";
if(divValidator.id=="finalize" || divValidator.id=="cancele"){
var orderGiveback=form.pedido;
var titleGiveback=form.title;
var serialGiveback=form.serial;
var motivoGiveback=form.motivo;
var situacaoGiveback=form.situacao;
var cpfGiveback=form.cpf;
var emailGiveback=form.email;
if(orderGiveback.value!=="" && titleGiveback.value!=="" && serialGiveback.value!=="" && motivoGiveback.value!=="" && situacaoGiveback.value!=="" && cpfGiveback.value!=="" && emailGiveback.value!==""){
form.submit();
}
}
});
if(button.name=="postordergiveback"){
$('[data-remodal-id='+pedido+']').remodal({
NAMESPACE:'remodal',
DEFAULTS:{
hashTracking:true,
closeOnConfirm:true,
closeOnCancel:true,
closeOnEscape:true,
closeOnOutsideClick:true,
modifier:''
}
});
}
}
postorders.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return postorders;
})();
function orderscommiter(button){
var orderscommitobj=new orderscommit();
orderscommitobj.inpublic(button);
}
function postorder(button){
var postordersobj=new postorders();
postordersobj.inpublic(button);
}