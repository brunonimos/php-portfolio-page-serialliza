var description=(function(){
function description(){}
function inprivate(button){
descript(button);
}
function descript(button){
var buttondiv=button.parentNode;
var descriptdiv=buttondiv.children[1];
if(descriptdiv.style.display=="none"){
if(button.id=="messagebody" && buttondiv.id=="messages"){
var messageform=buttondiv.children[1].children[0];
var msgid=messageform.id;
var msgpedido=messageform.pedido;
var msgresumo=messageform.resumo;
if(msgid.value!=="" && msgpedido.value!=="" && msgresumo.value!==""){
if(msgresumo.value=="Aguarde"){
msgresumo.value=msgresumo.value+" comunicado";
}
if(msgresumo.value=="Aprovação"){
msgresumo.value=msgresumo.value+" comunicada";
}
if(msgresumo.value=="Reprovação"){
msgresumo.value=msgresumo.value+" comunicada";
}
if(msgresumo.value=="Finalização"){
msgresumo.value=msgresumo.value+" comunicada";
}
if(msgresumo.value=="Cancelamento"){
msgresumo.value=msgresumo.value+" comunicada";
}
if(button.className=="qcunread"){
console.log(msgresumo.value);
var messagereading=new XMLHttpRequest();
var parameters="id="+msgid.value+"&pedido="+msgpedido.value+"&resumo="+msgresumo.value+"&action=messagesreader";
messagereading.open("POST","./resources/send.php");
messagereading.setRequestHeader("Content-type","application/x-www-form-urlencoded");
messagereading.onload=function(){
if(messagereading.status==200){
button.className="qcread";
button.children[0].className="qcread";
}else{
console.log(messagereading);
}
};
try{
messagereading.send(parameters);
}catch(e){
console.log(e);
msgtext.innerHTML=e;
}
}
}
messageform.addEventListener("submit",function(e){
e.preventDefault();
messageform.action="./resources/send.php";
if(messageform.id.value!=="" && messageform.pedido.value!=="" && messageform.resumo.value!==""){
messageform.action.value="messagesdeleter";
messageform.submit();
}
});
}
descriptdiv.style.display="block";
descriptdiv.className="animated fadeIn";
}else{
descriptdiv.className="animated fadeOut";
window.setTimeout(function fadeOut(){
descriptdiv.style.display="none";
},500);
}
}
description.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return description;
})();
function descriptbridge(button){
var descriptobj=new description();
descriptobj.inpublic(button);
}