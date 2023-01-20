var kickout=(function(){
function kickout(){}
function inprivate(){
sessao();
}
function sessao(){
var path=window.location.pathname;
var fileIndex=path.lastIndexOf('/')+1;
var fileName=path.substr(fileIndex);
var msg=document.getElementById("msg");
var msgtext=msg.children[0];
if(fileName=="admin.html" || fileName=="home.html" || fileName=="results.html"){
window.location.replace("./");
}else{
if(fileName!==""){
var back=document.getElementById("back");
back.style.display="block";
}
var app=angular.module('myApp',['angular.filter','ngSanitize']);
app.controller('userCtrl',function($scope,$http){
$http({
method:'POST',
url:'./resources/send.php',
data:$.param({sessao:'sessao'}),
headers:{'Content-Type':'application/x-www-form-urlencoded'}
}).then(
function(returnedData){
$scope.user=returnedData.data;
console.log(returnedData.data);
if(returnedData.data.NOTIFIED=="not"){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},5000);
msgtext.innerHTML="This website uses cookies to give you the best, most relevant experience. Using this website means you are Ok with this.";
}
var searchform=document.querySelector("#search");
var corpinfo=document.querySelector("#corpinfo");
$(searchform).find('input[type=submit]').hide();
$(searchform).find('input').keypress(function(e){
if(e.which==10 || e.which==13){
var termo=searchform.termo;
var preparedtermo=searchform.preparedtermo;
if(searchform.id=="search" && termo.value!==""){
preparedtermo.value=termo.value.replace(/\ /g,'_');
preparedtermo.value=latinize(preparedtermo.value);
if(preparedtermo.value!==""){
searchform.action="./resources/send.php";
window.location.replace("./"+preparedtermo.value+"SearchDo");
}
}
}
});
if(typeof corpinfo!=="undefined" && corpinfo!==null){
corpinfo.style.display="block";
corpinfo.className="animated fadeIn";
}
if(typeof $scope.user!=="undefined" && $scope.user.LOGIN){
signed($scope.user.LOGIN);
if(!$scope.user.ORDER){
$(document).ready(function(){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},5000);
window.setTimeout(function loadwith(){
msgtext.innerHTML=$scope.user.ERROR;
},1000);
});
}
}else{
var searchmethod=fileName.split("Search");
var lastindex=searchmethod.slice(-1)[0];
if(fileName==""){
unsignedwithbar();
}else if(fileName=="join"){
window.location.replace("./join#tojoin");
unsigned();
}else if(fileName=="alladms"){
unsignedwithoutprecontent();
}else if(fileName=="info"){
unsignedwithoutprecontent();
}else if(fileName=="privacy"){
unsignedwithoutprecontent();
}else if(lastindex=="Do"){
unsignedwithoutprecontent();
}else if(fileName=="admin.html" || fileName=="home.html" || fileName=="results.html" || fileName=="profile" || fileName=="allusers" || fileName=="orders" || fileName=="billing" || fileName=="banking" || fileName=="messages" || fileName=="myorders" || fileName=="mybilling" || fileName=="contact" || fileName=="system" || fileName=="notifications"){
window.location.replace("./403.html");
}else if(fileName!==""){
var backbutton=document.querySelector("#back");
var topo=document.querySelector("#topo");
backbutton.style.display="block";
if(topo!==null){
topo.style.position="fixed";
}
unsignedwithoutprecontent();
}
}
},
function(returnedData){
console.log(returnedData);
console.log(returnedData.status);
}
);
});
if(fileName!=="admin.html" && fileName!=="home.html" && fileName!=="results.html"){
app.controller('myCtrl',function($scope,$http,$filter){
var content="all";
var search="";
var token="";
$(document).ready(function(){
var searchform=document.getElementById("search");
if(searchform!==null){
searchform.addEventListener("submit",function(e){
e.preventDefault();
var termo=searchform.termo;
if(termo.value!==""){
termo.value=latinize(termo.value);
searchform.action="./resources/send.php";
window.location.replace("./"+termo.value+"SearchDo");
}
});
}
});
if(fileName!==""){
var method=fileName.split("Search");
var lastindex=method.slice(-1)[0];
var query=method.slice(-2)[0];
if(lastindex=="Do"){
query=query.replace(/\_/g,' ');
query=query.replace(/\./g,'');
query=query.replace(/\#/g,'');
query=query.replace(/\,/g,'');
search=query.toLowerCase();
content="searchmode";
}else{
method=fileName.split("Recovery");
lastindex=method.slice(-1)[0];
query=method.slice(-2)[0];
if(lastindex=="Do"){
content="recoverymode";
token=query;
}else{
content=fileName;
}
}
}
$http({
method:'POST',
url:'./resources/send.php',
data:$.param({content:content,search:search,token:token}),
headers:{'Content-Type':'application/x-www-form-urlencoded'}
}).then(
function(returnedData){
if(content!=="recoverymode" && fileName!=="join" && fileName!=="alladms" && fileName!=="info" && fileName!=="privacy" && fileName!=="profile" && fileName!=="allusers" && fileName!=="orders" && fileName!=="billing" && fileName!=="banking" && fileName!=="messages" && fileName!=="myorders" && fileName!=="mybilling" && fileName!=="contact" && fileName!=="system" && fileName!=="notifications"){
if(typeof returnedData.data.contents!=="undefined" && returnedData.data.contents.length>1){
returnedData.data.contents.splice(-1,1);
}
for(var keycont in returnedData.data.contents){
if(typeof returnedData.data.contents[keycont]!=="undefined"){
if(typeof returnedData.data.contents[keycont].image!=="undefined"){
returnedData.data.contents[keycont].image.shift();
}
if(typeof returnedData.data.contents[keycont].video!=="undefined"){
returnedData.data.contents[keycont].video.shift();
}
}
}
$scope.user=returnedData.data.user;
$scope.date=new Date();
$scope.contents=returnedData.data.contents;
if(typeof $scope.contents[$scope.contents.length-1]!=="undefined"){
if(typeof $scope.contents[$scope.contents.length-1].FIX!=="undefined"){
if($scope.contents[$scope.contents.length-1].FIX!==""){
$scope.contents.splice(-1);
}
}
}
console.log($scope.contents);
$(document).ready(function(){
var editdiv=document.querySelector("#edit-"+fileName);
if(typeof editdiv!=="undefined" && editdiv!==null){
editdiv.style.display="block";
var deleter=editdiv.children[0];
var editor=editdiv.children[2];
deleter.style.display="block";
deleter.style.position="relative";
deleter.style.zIndex="1";
editor.style.display="block";
editor.style.position="relative";
editor.style.zIndex="1";
}
var getbook=document.querySelector("#getbook-"+fileName);
if(typeof getbook!=="undefined" && getbook!==null){
getbook.style.display="inline-block";
}
var description=document.querySelector("#descriptionBody-"+fileName);
if(typeof description!=="undefined" && description!==null){
description.style.display="block";
var showdesc=description.children[0];
var rating=new SimpleStarRating(document.getElementById("bookRating-"+fileName));
if(typeof $scope.user!=="undefined" && $scope.user.client){
document.getElementById("bookRating-"+fileName).addEventListener('rate',function(e){
var ratedid=e.target.id;
var vote=e.detail;
var bookserial=ratedid.replace("bookRating-","");
if(typeof vote!=="undefined" && typeof bookserial!=="undefined"){
rating.disable();
var votar=new XMLHttpRequest();
var parameters="rating="+e.detail+"&book="+bookserial+"";
votar.open("POST","./resources/send.php");
votar.setRequestHeader("Content-type","application/x-www-form-urlencoded");
votar.onload=function(){
if(votar.status==200){
var votarJSON=JSON.parse(votar.responseText);
if(votarJSON.ERROR){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
rating.enable();
},3000);
msgtext.innerHTML=votarJSON.ERROR;
}else if(votarJSON.RATED){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
rating.enable();
window.location.replace("./");
},3000);
msgtext.innerHTML=votarJSON.RATED;
}
}
};
try{
votar.send(parameters);
}catch(e){
console.log(e);
}
}
});
}else{
rating.disable();
}
showdesc.click();
}
var carousels=document.getElementsByClassName("owl-carousel");
for(var key=0;key<=carousels.length;key++){
if(typeof carousels.item(key)!=="undefined" && carousels.item(key)!==null){
var bookscaroulsel=false;
var mediacaroulsel=false;
var optionsBooks={};
var optionsMedia={};
if(carousels.item(key).id=="books"){
if($scope.contents.length===1){
var categTextDiv=document.getElementById("categoriatext");
categTextDiv.style.display="none";
bookscaroulsel=false;
optionsBooks={mouseDrag:false,touchDrag:false,pullDrag:false,responsive:{0:{items:1},333:{items:2},666:{items:3}},animateIn:'flipOutY',animateOut:'flipInY'};
}else{
bookscaroulsel=true;
optionsBooks={mouseDrag:true,touchDrag:true,pullDrag:true,autoplay:true,rewind:true,responsive:{0:{items:1},333:{items:2},666:{items:3}},animateIn:'flipOutY',animateOut:'flipInY'};
}
if(bookscaroulsel===true){
$(carousels.item(key)).owlCarousel(optionsBooks);
}
}
if(carousels.item(key).id=="media"){
if("media-"+fileName==carousels.item(key).parentNode.id){
mediacaroulsel=true;
optionsMedia={responsive:{0:{items:1},333:{items:2},666:{items:3}},animateIn:'zoomInRight',animateOut:'zoomOutLeft',mouseDrag:true,touchDrag:true,pullDrag:true,autoplay:true,rewind:true,autoplayHoverPause:true};
}else{
mediacaroulsel=false;
optionsMedia={items:1,mouseDrag:false,touchDrag:false,pullDrag:false,nav:false,autoplay:false,loop:false};
}
if(mediacaroulsel===true || mediacaroulsel===false){
$(carousels.item(key)).owlCarousel(optionsMedia);
}
}
}
}
});
}else if(content=="recoverymode"){
var recover=document.getElementById("recoversend");
recover.action="./resources/send.php";
var rectoken=recover.rectoken;
rectoken.value=token;
if(rectoken.value!==""){
recover.submit();
}
}else if(fileName=="profile"){
$scope.profile=returnedData.data.contents;
console.log(returnedData.data);
if(typeof $scope.profile[$scope.profile.length-1]!=="undefined"){
if(typeof $scope.profile[$scope.profile.length-1].FIX!=="undefined"){
if($scope.profile[$scope.profile.length-1].FIX!==""){
$scope.profile.splice(-1);
}
}
}
if($scope.profile[0].CPF=="0"){
$scope.profile[0].CPF="";
}
if($scope.profile[0].RG=="0"){
$scope.profile[0].RG="";
}
if($scope.profile[0].TEL=="0"){
$scope.profile[0].TEL="";
}
if($scope.profile[0].BIO=="Biografia"){
$scope.profile[0].BIO="";
}
if($scope.profile[0].END=="Endereco"){
$scope.profile[0].END="";
}
if($scope.profile[0].NUMERO=="Numero"){
$scope.profile[0].NUMERO="";
}
if($scope.profile[0].COMP=="Complemento"){
$scope.profile[0].COMP="";
}
if($scope.profile[0].CEP=="CEP"){
$scope.profile[0].CEP="";
}
if($scope.profile[0].BAIRRO=="Bairro"){
$scope.profile[0].BAIRRO="";
}
if($scope.profile[0].CIDADE=="Cidade"){
$scope.profile[0].CIDADE="";
}
console.log($scope.profile);
$(document).ready(function(){
var profilediv=document.getElementById("profile");
if(typeof profilediv!=="undefined" && profilediv!==null){
new InputMask().Initialize(document.querySelectorAll("#telefone"),{mask:InputMaskDefaultMask.Phone2});
new InputMask().Initialize(document.querySelectorAll("#cpf"),{mask:InputMaskDefaultMask.CPF});
new InputMask().Initialize(document.querySelectorAll("#cep"),{mask:InputMaskDefaultMask.CEP});
var profileEditor=profilediv.querySelector("form");
var foto=profileEditor.foto;
foto.addEventListener("change",function(e){
e.preventDefault();
e.srcElement.files[0].name=e.srcElement.files[0].name.replace(/[&\/\\#,+$~%'":*?<>={}]/gi,"");
e.srcElement.files[0].name=e.srcElement.files[0].name.replace(/ /gi,"");
var fileIndex=e.srcElement.files[0].name.lastIndexOf('.')+1;
var extension=e.srcElement.files[0].name.substr(fileIndex);
if(extension.toLowerCase()=="jpg" || extension.toLowerCase()=="png" || extension.toLowerCase()=="jpeg"){
var fotoview=document.getElementById("profileImg");
fotoview.innerHTML="";
var reader=new FileReader();
reader.readAsDataURL(e.srcElement.files[0]);
reader.onload=function(e){
fotoview.src=e.target.result;
};
}
});
profileEditor.addEventListener("submit",function(e){
e.preventDefault();
profileEditor.action="./resources/send.php";
profileEditor.enctype="multipart/form-data";
var nome=profileEditor.nome;
var cpf=profileEditor.cpf;
var rg=profileEditor.rg;
var email=profileEditor.email;
var telefone=profileEditor.telefone;
var logradouro=profileEditor.logradouro;
var endereco=profileEditor.endereco;
var numero=profileEditor.numero;
var complemento=profileEditor.complemento;
var cep=profileEditor.cep;
var bairro=profileEditor.bairro;
var cidade=profileEditor.cidade;
var estado=profileEditor.estado;
var fulladdress=profileEditor.fulladdress;
var defaultfoto=profileEditor.defaultfoto;
var defaultfotopath=defaultfoto.value;
var defaultfotoindex=defaultfotopath.lastIndexOf('/')+1;
var defaultfotoname=defaultfotopath.substr(defaultfotoindex);
defaultfoto.value=defaultfotoname;
if(nome.value!=="" && cpf.value!=="" && rg.value!=="" && email.value!=="" && telefone.value!=="" && defaultfoto.value!==""){
if(logradouro.value!=="" && endereco.value!=="" && numero.value!=="" && cep.value!=="" && bairro.value!=="" && cidade.value!=="" && estado.value!==""){
fulladdress.value=logradouro.value+";"+endereco.value+";"+numero.value+";"+complemento.value+";"+bairro.value+";"+cidade.value+";"+estado.value+";"+cep.value;
}
profileEditor.submit();
}
});
profilediv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="Profile editor temporarily unavailable, try again later.";
}
});
}else if(fileName=="orders"){
$scope.orders=returnedData.data.contents;
if(typeof $scope.orders[$scope.orders.length-1]!=="undefined"){
if(typeof $scope.orders[$scope.orders.length-1].FIX!=="undefined"){
if($scope.orders[$scope.orders.length-1].FIX!==""){
$scope.orders.splice(-1);
}
}
}
console.log($scope.orders);
if(typeof $scope.orders[0]!=="undefined"){
if(typeof $scope.orders[0].ERROR!=="undefined"){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML=$scope.orders[0].ERROR;
}else{
$(document).ready(function(){
var ordersdiv=document.getElementById("orders");
if(typeof ordersdiv!=="undefined" && ordersdiv!==null){
$scope.viewData={};
$scope.excelgen=function(orders){
for(var keys in orders){
if(typeof orders[keys]!=="undefined" && keys>0){
delete orders[keys].login;
delete orders[keys].foto;
delete orders[keys].consultor;
var entregadate=new Date(orders[keys].starts);
var devolucaodate=new Date(orders[keys].ends);
orders[keys].inicio=orders[keys].starts;
orders[keys].inicio=(entregadate.getDate())+"/"+(entregadate.getMonth()+1)+"/"+(entregadate.getFullYear());
orders[keys].fim=orders[keys].ends;
orders[keys].fim=(devolucaodate.getDate())+"/"+(devolucaodate.getMonth()+1)+"/"+(devolucaodate.getFullYear());
delete orders[keys].date;
delete orders[keys].starts;
delete orders[keys].ends;
}
}
alasql('SELECT * INTO XLSX("pedidos.xlsx",{headers:true}) FROM ?',[orders]);
};
ordersdiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No orders.";
}
});
}
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No orders.";
}
}else if(fileName=="billing"){
$scope.billing=returnedData.data.contents;
if(typeof $scope.billing[$scope.billing.length-1]!=="undefined"){
if(typeof $scope.billing[$scope.billing.length-1].FIX!=="undefined"){
if($scope.billing[$scope.billing.length-1].FIX!==""){
$scope.billing.splice(-1);
}
}
}
console.log($scope.billing);
if(typeof $scope.billing[0]!=="undefined"){
if(typeof $scope.billing[0].ERROR!=="undefined"){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML=$scope.billing[0].ERROR;
}else{
$(document).ready(function(){
var billingdiv=document.getElementById("billing");
if(typeof billingdiv!=="undefined" && billingdiv!==null){
billingdiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No invoices.";
}
});
}
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No invoices.";
}
}else if(fileName=="banking"){
$scope.banking=returnedData.data.contents;
if(typeof $scope.banking[$scope.banking.length-1]!=="undefined"){
if(typeof $scope.banking[$scope.banking.length-1].FIX!=="undefined"){
if($scope.banking[$scope.banking.length-1].FIX!==""){
$scope.banking.splice(-1);
}
}
}
console.log($scope.banking);
$(document).ready(function(){
var bankingdiv=document.getElementById("banking");
if(typeof bankingdiv!=="undefined" && bankingdiv!==null){
bankingdiv.className="animated fadeIn";
var retupload=bankingdiv.querySelector("#banking-upret");
retupload.addEventListener("change",function(e){
e.preventDefault();
var confirmbutton=bankingdiv.querySelector("#retcommit");
confirmbutton.style.display="inline-block";
});
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No docs.";
}
});
}else if(fileName=="myorders"){
$scope.myorders=returnedData.data.contents;
if(typeof $scope.myorders[$scope.myorders.length-1]!=="undefined"){
if(typeof $scope.myorders[$scope.myorders.length-1].FIX!=="undefined"){
if($scope.myorders[$scope.myorders.length-1].FIX!==""){
$scope.myorders.splice(-1);
}
}
}
console.log($scope.myorders);
$(document).ready(function(){
var myordersdiv=document.getElementById("myorders");
if(typeof myordersdiv!=="undefined" && myordersdiv!==null){
var myordersOrders=document.getElementById("myorders-orders");
myordersdiv.className="animated fadeIn";
myordersOrders.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No orders";
}
});
}else if(fileName=="contact"){
$scope.contact=returnedData.data.contents;
if(typeof $scope.contact[$scope.contact.length-1]!=="undefined"){
if(typeof $scope.contact[$scope.contact.length-1].FIX!=="undefined"){
if($scope.contact[$scope.contact.length-1].FIX!==""){
$scope.contact.splice(-1);
}
}
}
console.log($scope.contact);
$(document).ready(function(){
var mycontactdiv=document.getElementById("contact");
var contactForm=mycontactdiv.children[0];
var assunto=contactForm.assunto;
var targetselector=contactForm.targetselector;
var target=contactForm.targettext;
var resumo=contactForm.resumo;
var message=contactForm.message;
if($scope.contact[0].pedido=="0"){
targetselector.parentNode.style.display="none";
target.required=true;
target.parentNode.style.display="inline-block";
target.parentNode.className="animated fadeIn";
}
assunto.addEventListener("change",function(){
if(this.value=="Pedido"){
target.parentNode.className="animated fadeOut";
window.setTimeout(function fadeOut(){
target.required=false;
target.parentNode.style.display="none";
targetselector.parentNode.style.display="inline-block";
targetselector.parentNode.className="animated fadeIn";
},1000);
}else if(this.value=="Outro"){
targetselector.parentNode.className="animated fadeOut";
window.setTimeout(function fadeOut(){
targetselector.parentNode.style.display="none";
target.required=true;
target.parentNode.style.display="inline-block";
target.parentNode.className="animated fadeIn";
},1000);
}
});
contactForm.addEventListener("submit",function(e){
contactForm.action="./resources/send.php";
e.preventDefault();
if(target.value==="" || assunto.value=="Pedido"){
contactForm.appendix.value=contactForm.targetselector.value;
target.value=targetselector.value;
}
if(target.value==="" || assunto.value=="Outro"){
contactForm.appendix.value=contactForm.targettext.value;
target.value="0";
}
if(assunto.value!=="" && target.value!=="" && resumo.value!=="" && message.value!==""){
contactForm.submit();
}
});
mycontactdiv.className="animated fadeIn";
});
}else if(fileName=="mybilling"){
$scope.mybilling=returnedData.data.contents;
if(typeof $scope.mybilling[$scope.mybilling.length-1]!=="undefined"){
if(typeof $scope.mybilling[$scope.mybilling.length-1].FIX!=="undefined"){
if($scope.mybilling[$scope.mybilling.length-1].FIX!==""){
$scope.mybilling.splice(-1);
}
}
}
console.log($scope.mybilling);
$(document).ready(function(){
var mybillingdiv=document.getElementById("mybilling");
if(typeof mybillingdiv!=="undefined" && mybillingdiv!==null){
mybillingdiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No invoices.";
}
});
}else if(fileName=="messages"){
$scope.msgs=returnedData.data.contents;
if(typeof $scope.msgs[$scope.msgs.length-1]!=="undefined"){
if(typeof $scope.msgs[$scope.msgs.length-1].FIX!=="undefined"){
if($scope.msgs[$scope.msgs.length-1].FIX!==""){
$scope.msgs.splice(-1);
}
}
}
console.log($scope.msgs);
$(document).ready(function(){
var qcdiv=document.querySelector("#qc");
if(typeof qcdiv!=="undefined" && qcdiv!==null){
$scope.currentPage=0;
$scope.pageSize=10;
$scope.data=[];
$scope.comframe='';
$scope.getData=function(){
return $filter('filter')($scope.data,$scope.comframe);
};
$scope.numberOfPages=function(){
return Math.ceil($scope.getData().length/$scope.pageSize);                
};
for(var i=0;i<65;i++){
$scope.data.push($scope.msgs[i]);
}
qcdiv.className="animated fadeIn";
qcdiv.style.display="block";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No messages.";
}
});
}else if(fileName=="recovery"){

}else if(fileName=="allusers"){
$scope.allusers=returnedData.data.contents;
if(typeof $scope.allusers[$scope.allusers.length-1]!=="undefined"){
if(typeof $scope.allusers[$scope.allusers.length-1].FIX!=="undefined"){
if($scope.allusers[$scope.allusers.length-1].FIX!==""){
$scope.allusers.splice(-1);
}
}
}
for(var keyallusers in $scope.allusers){
if(typeof $scope.allusers[keyallusers]!=="undefined"){
if($scope.allusers[keyallusers].CPF=="0"){
$scope.allusers[keyallusers].CPF="";
}
if($scope.allusers[keyallusers].RG=="0"){
$scope.allusers[keyallusers].RG="";
}
if($scope.allusers[keyallusers].TEL=="0"){
$scope.allusers[keyallusers].TEL="";
}
if($scope.allusers[keyallusers].BIO=="Biografia"){
$scope.allusers[keyallusers].BIO="";
}
if($scope.allusers[keyallusers].END=="Endereco"){
$scope.allusers[keyallusers].END="";
}
}
}
console.log($scope.allusers);
$(document).ready(function(){
var allusersdiv=document.getElementById("allusers");
if(typeof allusersdiv!=="undefined" && allusersdiv!==null){
allusersdiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No clients.";
}
});
}else if(fileName=="alladms"){
$scope.alladms=returnedData.data.contents;
if(typeof $scope.alladms[$scope.alladms.length-1]!=="undefined"){
if(typeof $scope.alladms[$scope.alladms.length-1].FIX!=="undefined"){
if($scope.alladms[$scope.alladms.length-1].FIX!==""){
$scope.alladms.splice(-1);
}
}
}
console.log($scope.alladms);
$(document).ready(function(){
var alladmsdiv=document.getElementById("alladms");
var searchfield=document.getElementById("search");
if(typeof searchfield!=="undefined" && searchfield!==null){
searchfield.style.display="none";
}
if(typeof alladmsdiv!=="undefined" && alladmsdiv!==null){
alladmsdiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No adms.";
}
});
}else if(fileName=="info"){
$scope.info=returnedData.data.contents;
if(typeof $scope.info[$scope.info.length-1]!=="undefined"){
if(typeof $scope.info[$scope.info.length-1].FIX!=="undefined"){
if($scope.info[$scope.info.length-1].FIX!==""){
$scope.info.splice(-1);
}
}
}
console.log($scope.info);
$(document).ready(function(){
var infodiv=document.getElementById("morecorpinfo");
var searchfield=document.getElementById("search");
if(typeof searchfield!=="undefined" && searchfield!==null){
searchfield.style.display="none";
}
if(typeof infodiv!=="undefined" && infodiv!==null){
infodiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No info.";
}
});
}else if(fileName=="privacy"){
$scope.privacy=returnedData.data.contents;
if(typeof $scope.privacy[$scope.privacy.length-1]!=="undefined"){
if(typeof $scope.privacy[$scope.privacy.length-1].FIX!=="undefined"){
if($scope.privacy[$scope.privacy.length-1].FIX!==""){
$scope.privacy.splice(-1);
}
}
}
console.log($scope.privacy);
$(document).ready(function(){
var privacydiv=document.getElementById("privacyinfo");
var searchfield=document.getElementById("search");
if(typeof searchfield!=="undefined" && searchfield!==null){
searchfield.style.display="none";
}
if(typeof privacydiv!=="undefined" && privacydiv!==null){
privacydiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="No info.";
}
});
}else if(fileName=="system"){
$scope.system=returnedData.data.contents;
if(typeof $scope.system[$scope.system.length-1]!=="undefined"){
if(typeof $scope.system[$scope.system.length-1].FIX!=="undefined"){
if($scope.system[$scope.system.length-1].FIX!==""){
$scope.system.splice(-1);
}
}
}
if(typeof $scope.system[0].ERROR!=="undefined"){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML=$scope.system[0].ERROR;
}else{
if($scope.system[0].END=="Endereco"){
$scope.system[0].END="";
}
if($scope.system[0].NUMERO=="Numero"){
$scope.system[0].NUMERO="";
}
if($scope.system[0].COMP=="Complemento"){
$scope.system[0].COMP="";
}
if($scope.system[0].CEP=="CEP"){
$scope.system[0].CEP="";
}
if($scope.system[0].BAIRRO=="Bairro"){
$scope.system[0].BAIRRO="";
}
if($scope.system[0].CIDADE=="Cidade"){
$scope.system[0].CIDADE="";
}
console.log($scope.system);
$(document).ready(function(){
var systemdiv=document.getElementById("system");
if(typeof systemdiv!=="undefined" && systemdiv!==null){
new InputMask().Initialize(document.querySelectorAll("#cep"),{mask:InputMaskDefaultMask.CEP});
new InputMask().Initialize(document.querySelectorAll("#cnpj"),{mask:InputMaskDefaultMask.CNPJ});
var systemConfig=systemdiv.querySelector("form");
systemConfig.addEventListener("submit",function(e){
e.preventDefault();
systemConfig.action="./resources/send.php";
var empresa=systemConfig.empresa;
var cnpj=systemConfig.cnpj;
var logradouro=systemConfig.logradouro;
var endereco=systemConfig.endereco;
var numero=systemConfig.numero;
var complemento=systemConfig.complemento;
var cep=systemConfig.cep;
var bairro=systemConfig.bairro;
var cidade=systemConfig.cidade;
var estado=systemConfig.estado;
var fulladdress=systemConfig.fulladdress;
var email=systemConfig.email;
var agencia=systemConfig.agencia;
var conta=systemConfig.conta;
var digito=systemConfig.digito;
var cedente=systemConfig.cedente;
var registro=systemConfig.registro;
var taxa=systemConfig.taxa;
var senha=systemConfig.senha;
if(empresa.value!=="" && cnpj.value!=="" && email.value!=="" && agencia.value!=="" && conta.value!=="" && digito!=="" && cedente.value!=="" && registro.value!=="" && taxa.value!=="" && senha.value!==""){
if(taxa.value!==""){
var n=taxa.value.replace(",",".");
var c=isNaN(c=Math.abs(c)) ? 2 : c,
d=d==undefined ? "." : d,
t=t==undefined ? "" : t,
s=n<0 ? "-" : "",
i=String(parseInt(n=Math.abs(Number(n) || 0).toFixed(c))),
j=(j=i.length)>3 ? j % 3 : 0;
taxa.value=s+(j ? i.substr(0, j)+t : "")+i.substr(j).replace(/(\d{3})(?=\d)/g,"$1"+t)+(c ? d+Math.abs(n-i).toFixed(c).slice(2) : "");
}
if(logradouro.value!=="" && endereco.value!=="" && numero.value!=="" && cep.value!=="" && bairro.value!=="" && cidade.value!=="" && estado.value!==""){
fulladdress.value=logradouro.value+";"+endereco.value+";"+numero.value+";"+complemento.value+";"+bairro.value+";"+cidade.value+";"+estado.value+";"+cep.value;
systemConfig.submit();
}
}
});
systemdiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="System data not located, try again later.";
}
});
}
}else if(fileName=="notifications"){
$scope.notifications=returnedData.data.contents;
if(typeof $scope.notifications[$scope.notifications.length-1]!=="undefined"){
if(typeof $scope.notifications[$scope.notifications.length-1].FIX!=="undefined"){
if($scope.notifications[$scope.notifications.length-1].FIX!==""){
$scope.notifications.splice(-1);
}
}
}
if(typeof $scope.notifications[0].ERROR!=="undefined"){
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML=$scope.system[0].ERROR;
}else{
console.log($scope.notifications);
$(document).ready(function(){
var notifydiv=document.getElementById("notifications");
console.log(notifydiv);
if(typeof notifydiv!=="undefined" && notifydiv!==null){
notifydiv.className="animated fadeIn";
}else{
msg.className="animated fadeIn";
window.setTimeout(function fadeOut(){
msg.className="off";
},3000);
msgtext.innerHTML="Notification data not located, try again later.";
}
});
}
}
},
function(returnedData){
console.log(returnedData);
console.log(returnedData.status);
}
);
});
app.filter('startFrom',[function(){
return function(input,start){
if(!input||!input.length){
return;
}
start=+start;
return input.slice(start);
};
}]);
app.filter('removeSpaces',[function(){
return function(string){
if (!angular.isString(string)){
return string;
}
var stringjoin=string.replace(/[\s]/g,'');
stringjoin=stringjoin.replace(/\./g,'');
stringjoin=stringjoin.replace(/\,/g,'');
return stringjoin;
};
}]);
app.filter('replaceSpacesforSearchEngine',[function(){
return function(string){
if (!angular.isString(string)){
return string;
}
var searchEngine=string.replace(/\ /g,'_');
searchEngine=latinize(searchEngine);
return searchEngine;
};
}]);
app.filter('trusted',['$sce',function($sce){
return function(url){
return $sce.trustAsResourceUrl(url);
};
}]);
}else{
window.location.replace("./");
}
}
}
function signed(signedlogin){
var listeningElement=document.querySelector('.listening');
var path=window.location.pathname;
var fileIndex=path.lastIndexOf('/')+1;
var fileName=path.substr(fileIndex);
var login=document.querySelector("#login");
var login_div=document.querySelector("#login_div");
var login_bot=document.querySelector("#login_bot");
var header=document.querySelector("#collapsedtop");
var topo=document.querySelector("#toponulled");
var logo=document.querySelector("#logo");
var container=document.querySelector("#contentContainer");
var background=document.querySelector("#backgroundContainer");
var article=document.querySelector("#article");
var precontent=document.querySelector("#precontent");
precontent.style.marginTop="-28em";
$(document).ready(function(){
container.style.display="block";
background.style.display="block";
if(article!==null){
article.style.display="block";
article.className="animated fadeIn";
}
listeningElement.parentNode.parentNode.style.display="none";
listeningElement.innerHTML="";
$(document).scroll(function(){
var documentTop=$(this).scrollTop();
if(documentTop<101){
if(header!==null){
if(header.className=="animated fadeOut"){
header.className="animated fadeIn";
window.setTimeout(function fadeIn(){
header.className="on";
},3000);
}
}
}else if(documentTop>100){
if(header!==null){
if(header.className=="" || header.className=="animated fadeIn" || header.className=="on"){
header.className="animated fadeOut";
}
}
}
});
});
window.setTimeout(function loadwith(){
header.style.minHeight="0em";
header.style.marginLeft="-0.5em";
login_bot.className="on";
$("#top").prepend(topo);
if(login!==null){
login.innerHTML=signedlogin;
}
if(topo!==null){
topo.id="toposigned";
topo.style.position="fixed";
}
if(logo!==null){
logo.id="logosigned";
}
if(login_div!==null && login_div.parentNode!==null){
login_div.parentNode.removeChild(login_div);
}
if(fileName!==""){
var backbutton=document.querySelector("#back");
backbutton.style.display="block";
}
},100);
}
function unsigned(){
var listeningElement=document.querySelector('.listening');
var login_div=document.querySelector("#login_div");
var loginmodal=$('[data-remodal-id=tojoin]');
var topo=document.querySelector("#toponulled");
$(loginmodal).prepend(login_div);
var logo=document.querySelector("#logo");
var container=document.querySelector("#contentContainer");
var background=document.querySelector("#backgroundContainer");
var header=document.querySelector("#collapsedtop");
var precontent=document.querySelector("#precontent");
var footer=document.querySelector("#footer");
logo.style.marginTop="-39em";
header.style.marginLeft="-0.5em";
header.style.marginTop="9em";
footer.style.marginTop="2em";
container.style.display="none";
container.style.marginTop="-28em";
precontent.style.marginTop="12em";
$(document).ready(function(){
background.style.display="block";
if(logo!==null){
logo.style.display="block";
}
if(login_div!==null){
login_div.className="animated bounceInDown";
}
if(topo!==null){
/*topo.id="topo";*/
topo.style.visibility="hidden";
}
if(listeningElement!==null){
listeningElement.parentNode.parentNode.style.display="none";
listeningElement.innerHTML="";
}
$(document).on('closing','.remodal',function(){
window.location.replace("./");
});
console.log(login_div);
loginmodal.remodal({NAMESPACE:'remodal',DEFAULTS:{hashTracking:true,closeOnConfirm:true,closeOnCancel:true,closeOnEscape:true,closeOnOutsideClick:true,modifier:''}});
});
}
function unsignedwithbar(){
var listeningElement=document.querySelector('.listening');
var login_div=document.querySelector("#login_div");
var header=document.querySelector("#collapsedtop");
var topo=document.querySelector("#toponulled");
var logo=document.querySelector("#logo");
var container=document.querySelector("#contentContainer");
var background=document.querySelector("#backgroundContainer");
var maininfo=document.querySelector("#maininfo");
var precontent=maininfo.parentNode;
var accessbutton=document.querySelector("#joinbutton");
var top=document.querySelector("#top");
logo.id="logosigned";
$(document).ready(function(){
container.style.marginTop="0.0em";
precontent.style.marginTop="-25.0em";
top.style.position="fixed";
if(accessbutton!==null){
accessbutton.style.display="block";
}
window.setTimeout(function bounceIn(){
maininfo.className="animated fadeIn";
maininfo.style.display="block";
},200);
window.setTimeout(function bounceIn(){
container.className="animated fadeIn";
container.style.display="block";
},500);
window.setTimeout(function bounceIn(){
background.className="animated fadeIn";
background.style.display="block";
},500);
if(listeningElement!==null){
listeningElement.parentNode.parentNode.style.display="none";
listeningElement.innerHTML="";
}
$(document).scroll(function(){
var documentTop=$(this).scrollTop();
console.log(documentTop);
if(documentTop<101){
if(header!==null){
if(header.className=="animated fadeOut"){
header.className="animated fadeIn";
window.setTimeout(function fadeIn(){
header.className="on";
},3000);
}
}
}else if(documentTop>100){
if(header!==null){
if(header.className=="" || header.className=="animated fadeIn" || header.className=="on"){
header.className="animated fadeOut";
}
}
}
});
});
window.setTimeout(function loadwith(){
header.style.minHeight="0em";
header.style.marginLeft="-0.5em";
$("#top").prepend(topo);
topo.id="toposigned";
if(login_div!==null){
login_div.parentNode.removeChild(login_div);
}
},100);
}
function unsignedwithoutprecontent(){
var listeningElement=document.querySelector('.listening');
var login_div=document.querySelector("#login_div");
var header=document.querySelector("#collapsedtop");
var logo=document.querySelector("#logo");
var topo=document.querySelector("#toponulled");
var container=document.querySelector("#contentContainer");
var background=document.querySelector("#backgroundContainer");
var accessbutton=document.querySelector("#joinbutton");
var top=document.querySelector("#top");
var precontent=document.querySelector("#precontent");
$(document).ready(function(){
precontent.style.marginTop="-28em";
if(logo!==null){
logo.id="logosigned";
}
container.style.marginTop="-12.0em";
if(accessbutton!==null){
accessbutton.style.marginTop="-1.4em";
accessbutton.style.display="block";
}
top.style.position="fixed";
window.setTimeout(function bounceIn(){
container.className="animated fadeIn";
container.style.display="block";
},500);
window.setTimeout(function bounceIn(){
background.className="animated fadeIn";
background.style.display="block";
},500);
if(listeningElement!==null){
listeningElement.parentNode.parentNode.style.display="none";
listeningElement.innerHTML="";
}
$(document).scroll(function(){
var documentTop=$(this).scrollTop();
console.log(documentTop);
if(documentTop<101){
if(header!==null){
if(header.className=="animated fadeOut"){
header.className="animated fadeIn";
window.setTimeout(function fadeIn(){
header.className="on";
},3000);
}
}
}else if(documentTop>100){
if(header!==null){
if(header.className=="" || header.className=="animated fadeIn" || header.className=="on"){
header.className="animated fadeOut";
}
}
}
});
});
window.setTimeout(function loadwith(){
header.style.minHeight="0em";
header.style.marginLeft="-0.5em";
$("#top").prepend(topo);
if(topo!==null){
topo.id="toposigned";
}
if(login_div!==null){
login_div.parentNode.removeChild(login_div);
}
},100);
}
kickout.prototype.inpublic=function(){
return inprivate.call(this);
};
return kickout;
})();
var kickoutobj=new kickout();
kickoutobj.inpublic();