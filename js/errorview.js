var errorview=(function(){
function errorview(){}
function inprivate(){
var listeningElement=document.querySelector('.listening');
var msg=document.querySelector('#msg');
var header=document.querySelector("#collapsedtop");
var logo=document.querySelector("#logo");
var logotipo=document.querySelector("#logotipo");
var topo=document.querySelector("#toponulled");
var background=document.querySelector("#backgroundContainer");
var precontent=document.querySelector("#precontent");
$(document).ready(function(){
logotipo.style.width="40%";
logotipo.style.height="70%";
header.style.minHeight="0em";
logo.style.marginTop="-5em";
background.style.display="block";
precontent.style.marginTop="1em";
if(listeningElement!==null && msg!==null){
window.setTimeout(function loadwith(){
listeningElement.innerHTML="";
msg.className="animated zoomInLeft";
},4000);
}
if(logo!==null){
logo.id="logosigned";
}
if(topo!==null){
topo.id="toposigned";
}
});
}
errorview.prototype.inpublic=function(){
return inprivate.call(this);
};
return errorview;
})();
var errorviewobj=new errorview();
errorviewobj.inpublic();