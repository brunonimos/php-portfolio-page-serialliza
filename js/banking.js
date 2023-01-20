var banking=(function(){
function banking(){}
function inprivate(button){
bankingsender(button);
}
function bankingsender(button){
var bankingform=button.parentNode;
var bankingid=bankingform.id;
bankingform.action="./resources/send.php";
if(bankingform.action.value=="remessadownloader"){
if(bankingid.value!==""){
bankingform.submit();
}
}else if(bankingform.action.value=="retornouploader"){
bankingform.enctype="multipart/form-data";
var filesvalidate=false;
var retornos=bankingform.querySelector("#banking-upret");
for(var retorno in retornos){
if(retorno!=="length" && retorno!=="item"){
for(var key=0;key<retornos.files.length;key++){
var filename=retornos.files[key].name;
console.log();
var fileIndex=filename.lastIndexOf('.')+1;
var extension=filename.substr(fileIndex);
if(extension.toLowerCase()=="ret"){
filesvalidate=true;
}else{
filesvalidate=false;
}
}
}
}
if(filesvalidate==true){
bankingform.submit();
}
}else if(bankingform.action.value=="paychecker" || bankingform.action.value=="payunchecker"){
if(bankingid.value!==""){
bankingform.submit();
}
}
}
banking.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return banking;
})();
function bankingbridge(button){
var bankingobj=new banking();
bankingobj.inpublic(button);
}