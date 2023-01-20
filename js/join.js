var cad=(function(){
function cad(){}
function inprivate(){
join();
}
function join(){
var senha=document.querySelector("#send_senha");
var email=document.querySelector("#send_email");
var nome=document.querySelector("#send_nome");
var cadbutton=document.querySelector("#cadon");
var conditions=document.querySelector("#condContainer");
if(cadbutton!==null){
cadbutton.addEventListener("click",function(e){
e.preventDefault();
if(cadbutton.innerHTML=="Sign Up"){
var acesso=document.querySelector("#acesso");
var breakline8=document.createElement("br");
breakline8.id="breakline8";
var breakline9=document.createElement("br");
breakline9.id="breakline9";
var breaklineA=document.createElement("br");
breaklineA.id="breaklineA";
var breaklineB=document.createElement("br");
breaklineB.id="breaklineB";
var breaklineC=document.createElement("br");
breaklineC.id="breaklineC";
var breaklineD=document.createElement("br");
breaklineD.id="breaklineD";
acesso.id="cadastro";
cadbutton.innerHTML="Sign in";
email.className="animated fadeIn";
conditions.className="animated fadeIn";
window.setTimeout(function fadeIn(){
email.className="twinAccess";
$(acesso).prepend(nome);
senha.parentNode.insertBefore(breakline8,nome.nextSibling);
senha.parentNode.insertBefore(breakline9,breakline8.nextSibling);
senha.parentNode.insertBefore(breaklineA,senha.nextSibling);
senha.parentNode.insertBefore(breaklineB,breaklineA.nextSibling);
senha.parentNode.insertBefore(email,breaklineB.nextSibling);
senha.parentNode.insertBefore(breaklineC,email.nextSibling);
senha.parentNode.insertBefore(breaklineD,breaklineC.nextSibling);
senha.parentNode.insertBefore(conditions,breaklineD.nextSibling);
var acessodiv=acesso.parentNode.parentNode;
acessodiv.style.marginBottom="0.0em";
acessodiv.style.marginTop="0.0em";
cadastrar();
},300);
}else{
var cadastro=document.querySelector("#cadastro");
cadastro.id="acesso";
cadbutton.innerHTML="Sign Up";
email.className="animated fadeOut";
conditions.className="animated fadeOut";
window.setTimeout(function fadeOut(){
email.className="twinAccess";
senha.parentNode.removeChild(nome);
senha.parentNode.removeChild(conditions);
senha.parentNode.removeChild(email);
var br8=document.getElementById("breakline8");
var br9=document.getElementById("breakline9");
var bra=document.getElementById("breaklineA");
var brb=document.getElementById("breaklineB");
var brc=document.getElementById("breaklineC");
var brd=document.getElementById("breaklineD");
br8.outerHTML="";
br9.outerHTML="";
bra.outerHTML="";
brb.outerHTML="";
brc.outerHTML="";
brd.outerHTML="";
var cadastrodiv=cadastro.parentNode.parentNode;
cadastrodiv.style.marginBottom="-3em";
cadastrodiv.style.marginTop="0em";
},300);
}
});
}
}
function cadastrar(){
var msg=document.getElementById("msg");
var msgtext=msg.children[0];
acesso=document.querySelector("#cadastro");
acesso.addEventListener("submit",function(e){
acesso.action="";
e.preventDefault();
var nome=acesso.nome;
var login=acesso.login;
var senha=acesso.senha;
var email=acesso.email;
var cadcommit=acesso.enviar;
var agreed=document.querySelector("#conditions");
if(nome!==null && email!==null && agreed!==null){
cadcommit.disabled=true;
var cadastrar=new XMLHttpRequest();
var contract;
if(agreed.checked){
contract="agreed";
}else{
contract="notagreed";
}
if(nome.value!==null && login.value!==null && senha.value!==null && email.value!==null){
/*
var parameters="nome="+nome.value+"&login="+login.value+"&senha="+senha.value+"&email="+email.value+"&conditions="+contract+"";
cadastrar.open("POST","./resources/send.php");
cadastrar.setRequestHeader("Content-type","application/x-www-form-urlencoded");
cadastrar.onload=function(){
if(cadastrar.status==200){
console.log(cadastrar.response);
var cadastrarString=JSON.stringify(eval("("+cadastrar.response+")"));
var cadastrarJSON=JSON.parse(cadastrarString);
if(cadastrarJSON.ERROR){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
cadcommit.disabled=false;
},3000);
msgtext.innerHTML=cadastrarJSON.ERROR;
}else if(cadastrarJSON.LOGIN){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
cadcommit.disabled=false;
window.location.replace("./");
},1000);
msgtext.innerHTML="Accessing...";
}
}
};
try{
cadastrar.send(parameters);
}catch(e){
console.log(e);
}
*/
}
}
});
}
cad.prototype.inpublic=function(){
return inprivate.call(this);
};
return cad;
})();
var cadobj=new cad();
cadobj.inpublic();