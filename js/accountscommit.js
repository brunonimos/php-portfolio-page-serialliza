var accountcommit=(function(){
function accountcommit(){}
function inprivate(button){
var user=button.id;
$(document).on('confirmation','.remodal',function(){
var divValidator=this.children[1];
var form=divValidator.children[0];
form.action="./resources/send.php";
if(divValidator.id=="jointeam" || divValidator.id=="leaveteam" || divValidator.id=="suspend" || divValidator.id=="reactivate" || divValidator.id=="cancelaccount"){
var accountlogin=form.accountlogin;
var accountemail=form.accountemail;
var accountsituacao=form.accountsituacao;
var motivo=form.motivo;
var action=form.action;
if(accountlogin.value!=="" && accountemail.value!=="" && accountsituacao.value!=="" && motivo.value!=="" && action.value!==""){
console.log(accountlogin.value);
console.log(accountemail.value);
console.log(accountsituacao.value);
console.log(motivo.value);
console.log(action.value);
form.submit();
}
}
});
if(button.name=="acconuntcommit"){
$('[data-remodal-id='+user+']').remodal({
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
accountcommit.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return accountcommit;
})();
function accountcommiter(button){
var accountcommiterobj=new accountcommit();
accountcommiterobj.inpublic(button);
}