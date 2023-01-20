var getbook=(function(){
function getbook(){}
function inprivate(button){
commitgetbook(button);
}
function commitgetbook(button){
var form=button.parentNode.parentNode;
var title=form.gettitle;
var serial=form.getserial;
var parcelas=form.parcelas;
var vencimento=form.vencimento;
var orderinfo=document.querySelector("#orderinfo");
var order=orderinfo.order;
var titlejoin=title.value.replace(/[\s]/g,'');
titlejoin=titlejoin.replace(/\./g,'');
titlejoin=titlejoin.replace(/\,/g,'');
$(document).on('confirmation','.remodal',function(){
form.action="./resources/send.php";
var autor=form.getautor;
var date=form.getdate;
if(title.value!=="" && serial.value!=="" && autor.value!=="" && date.value!==""){
var setparcelas=this.querySelector("#setparcelas").value;
var setvencimento=this.querySelector("#setvencimento").value;
parcelas.value=setparcelas;
vencimento.value=setvencimento;
if(order.value=="true" && parcelas.value!=="" && vencimento.value!==""){
form.submit();
}else{
var msg_div=document.querySelector("#msg");
var msgtext=document.querySelector("#msgtext");
var main=document.getElementById("main");
main.className="negate";
window.setTimeout(function fadeOut(){
main.className="";
},2000);
msg_div.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg_div.className="off";
},3000);
msgtext.innerHTML="Complete the profile to sign up.";
return false;
}
}else{
return false;
}
});
$('[data-remodal-id=modal-'+titlejoin+']').remodal({
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
getbook.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return getbook;
})();
function getbookbridge(button){
var getbookobj=new getbook();
getbookobj.inpublic(button);
}