var post=(function(){
function post(){}
function inprivate(){
acessar();
logout();
}
function acessar(){
var main=document.getElementById("main");
var acesso=document.querySelector("#acesso");
var msg=document.getElementById("msg");
var msgtext=msg.children[0];
if(acesso!==null){
acesso.addEventListener("submit",function(e){
acesso.action="";
e.preventDefault();
var login=acesso.login;
var senha=acesso.senha;
var email=acesso.email;
var logincommit=acesso.enviar;
if(typeof email=="undefined" && login.value!=="" && senha.value!==""){
logincommit.disabled=true;
var logar=new XMLHttpRequest();
var parameters="login="+login.value+"&senha="+senha.value+"";
logar.open("POST","./resources/send.php");
logar.setRequestHeader("Content-type","application/x-www-form-urlencoded");
logar.onload=function(){
if(logar.status==200){
var logarString=JSON.stringify(eval("("+logar.response+")"));
var logarJSON=JSON.parse(logarString);
console.log(logarJSON);
if(logarJSON.ERROR){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
logincommit.disabled=false;
},3000);
msgtext.innerHTML=logarJSON.ERROR;
main.className="negate";
}else if(logarJSON.LOGIN){
console.log(logarJSON);
logincommit.disabled=false;
window.location.replace("./");
}
}
};
try{
logar.send(parameters);
}catch(e){
console.log(e);
}
}
});
}
}
function logout(){
var msg=document.getElementById("msg");
var msgtext=msg.children[0];
var saida=document.querySelector("#saida");
if(saida!==null){
saida.addEventListener("click",function(e){
e.preventDefault();
var logout=document.querySelector("#login").name;
console.log(logout);
var sair=new XMLHttpRequest();
var parameters="logout="+logout+"";
sair.open("POST","./resources/send.php");
sair.setRequestHeader("Content-type","application/x-www-form-urlencoded");
sair.onload=function(){
if(sair.status==200){
var sairString=JSON.stringify(eval("("+sair.response+")"));
var sairJSON=JSON.parse(sairString);
console.log(sairJSON);
if(sairJSON.ERROR){
msg.className="animated fadeIn";
msgtext.innerHTML=sairJSON.ERROR;
}else if(sairJSON.LOGOUT){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML=sairJSON.LOGOUT;
window.location.replace("./");
}
}
};
try{
sair.send(parameters);
}catch(e){
console.log(e);
}
});
}
}
post.prototype.inpublic=function(){
return inprivate.call(this);
};
return post;
})();
var postobj=new post();
postobj.inpublic();