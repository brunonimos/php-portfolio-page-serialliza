var del=(function(){
function del(){}
function inprivate(trigger){
deletar(trigger);
}
function deletar(trigger){
var major=trigger.parentNode;
var form=$(major)[0].getElementsByClassName("apagar")[0];
if(typeof form=="undefined"){
major=trigger.parentNode.parentNode;
form=$(major)[0].getElementsByClassName("apagarImagem")[0];
}
var backbutton=document.querySelector("#back");
backbutton.addEventListener("click",function(e){
e.preventDefault();
window.location.reload(false);
});
var images=major.parentNode.getElementsByClassName("imageMedia");
var formTitle=form.deltitle;
var formSerial=form.serial;
var formMediaDel=form.medianame;
if(form.action.value=="multimidiadeleter"){
form.action="./resources/send.php";
if(formTitle.value!=="" && formMediaDel.value!==""){
form.submit();
}
}else{
var formDeleteAll=form.deleteall;
formDeleteAll.style.display="block";
formDeleteAll.addEventListener("click",function(e){
e.preventDefault();
form.action="./resources/send.php";
form.action.value="contentdeleter";
if(form.action.value=="contentdeleter" && formTitle.value!=="" && formSerial.value!==""){
console.log(formTitle.value);
console.log(formSerial.value);
form.submit();
}
});
var altbutton=major.getElementsByClassName("editbutton")[0];
trigger.style.display="none";
altbutton.style.display="none";
for(var k=0;k<=images.length;k++){
if(typeof images.item(k)!=="undefined" && images.item(k)!==null){
var deleterbutton=document.createElement("button");
deleterbutton.name=images.item(k).alt;
deleterbutton.type="button";
deleterbutton.title="Deletar Multimidia";
deleterbutton.className="deleterbutton";
deleterbutton.style.marginTop="-6.6em";
deleterbutton.style.marginRight="-1.3em";
images.item(k).parentNode.insertBefore(deleterbutton,images.item(k).nextSibling);
deleterbutton.addEventListener("click",function(e){
e.preventDefault();
form.action="./resources/send.php";
form.action.value="multimidiatruncater";
formMediaDel.value=this.name;
var formTitle=form.deltitle;
if(form.action.value=="multimidiatruncater" && formTitle.value!=="" && formMediaDel.value!==""){
console.log(formTitle.value);
console.log(formMediaDel.value);
form.submit();
}
});
}
}
}
}
del.prototype.inpublic=function(trigger){
return inprivate.call(this,trigger);
};
return del;
})();
function deletebridge(trigger){
var delobj=new del();
delobj.inpublic(trigger);
}