var rec=(function(){
function rec(){}
function inprivate(button){
recover(button);
}
function recover(button){
var accessdiv=button.parentNode.parentNode;
var accessform=accessdiv.querySelector("#acesso");
if(accessform==null){
accessform=accessdiv.querySelector("#cadastro");
}
var recoverform=accessdiv.querySelector("#recoverer");
if(recoverform.className=="animated fadeIn"){
recoverform.className="animated fadeOut";
window.setTimeout(function fadeOut(){
recoverform.className="off";
accessform.style.display="block";
},30);
}else{
accessform.style.display="none";
recoverform.className="animated fadeIn";
recuperar(recoverform);
}
}
function recuperar(recoverform){
recoverform.addEventListener("submit",function(e){
e.preventDefault();
recoverform.action="./resources/send.php";
recoverform.enctype="multipart/form-data";
var senha=recoverform.recsenha;
var resenha=recoverform.recresenha;
var email=recoverform.recemail;
if(senha.value!==null && email.value!==null && senha.value==resenha.value){
recoverform.submit();
}
});
}
rec.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return rec;
})();
function recoverbridge(button){
var recobj=new rec();
recobj.inpublic(button);
}