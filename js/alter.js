var alter=(function(){
function alter(){}
function inprivate(trigger){
alterar(trigger);
}
function alterar(trigger){
var major=trigger.parentNode;
var form=$(major)[0].getElementsByClassName("editar")[0];
var backbutton=document.querySelector("#back");
backbutton.addEventListener("click",function(e){
e.preventDefault();
window.location.reload(false);
});
var formOldTitle=form.oldtitle;
var files=form.getElementsByClassName("files buttons")[0];
/*var labelforfiles=document.getElementById(""+formOldTitle.value+"labelforfiles");*/
var formNewTitle=form.newtitle;
var formContent=form.newcontent;
var formCategoria=form.newcategoria;
var formNewVideos=form.newvideos;
var forminicio=form.newinicio;
var formfim=form.newfim;
var formhourstart=form.newhourstart;
var formnewhourend=form.newhourend;
var formnewvalor=form.newvalor;
var formnewmulta=form.newmulta;
var formnewparcelas=form.newparcelas;
var formnewcarga=form.newcarga;
var formcursodiv=forminicio.parentNode;
var formSerial=form.serial;
var formCommit=form.commit;
var delbutton=major.getElementsByClassName("delbutton")[0];
var imgContainer=document.getElementById(""+formOldTitle.value+"imagesPreview");
var oldContentDivHeader=document.getElementById(formOldTitle.value+"descriptionHeader");
var oldContentDivCarousel=document.getElementById(formOldTitle.value+"descriptionCarousel");
var oldContentDivBody=document.getElementById("descriptionBody-"+formSerial.value);
var imgList=[];
trigger.style.display="none";
delbutton.style.display="none";
formNewTitle.type="text";
formNewTitle.focus();
formContent.style.display="block";
formCategoria.style.display="block";
formNewVideos.parentNode.style.display="block";
$(forminicio).datepicker({
dateFormat:'dd-mm-yy',
monthNames: ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
dayNames: ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sabado"],
dayNamesShort: ["Dom","Seg","Ter","Qua","Qui","Sex","Sab"],
dayNamesMin: ["Do","Se","Te","Qa","Qi","Se","Sa"],
minDate: "+1d"
});
$(formfim).datepicker({
dateFormat:'dd-mm-yy',
monthNames: ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
dayNames: ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sabado"],
dayNamesShort: ["Dom","Seg","Ter","Qua","Qui","Sex","Sab"],
dayNamesMin: ["Do","Se","Te","Qa","Qi","Se","Sa"],
minDate: "+1d"
});
if(form.newcategoria.value.toLowerCase()=="curso" || form.newcategoria.value.toLowerCase()=="cursos"){
forminicio.style.display="block";
formfim.style.display="block";
formnewvalor.style.display="block";
formnewparcelas.style.display="block";
formnewcarga.style.display="block";
formnewmulta.style.display="block";
formcursodiv.className="animated fadeIn";
var formweekdays=form.querySelectorAll('input[name="newweekdays[]"]:checked');
Array.prototype.forEach.call(formweekdays,function(el){
if(el.value=="on"){
weekdayhourchanger(el);
}
});
}
files.style.display="block";
formCommit.style.display="block";
oldContentDivHeader.style.display="none";
formNewTitle.parentNode.appendChild(oldContentDivCarousel);
oldContentDivBody.style.display="none";
imgContainer.style.display="table-header-group";
imgContainer.style.minHeight="8em";
files.addEventListener("change",function(e){
e.preventDefault();
var images=document.getElementById(""+formOldTitle.value+"imagesPreview");
images.innerHTML="";
for(var multimidia in this.files){
if(multimidia!=="length" && multimidia!=="item"){
if(this.files[multimidia].type.match('image.*')){
var reader=new FileReader();
reader.readAsDataURL(this.files[multimidia]);
reader.onload=function(e){
var filePath=files.value.lastIndexOf('\\')+1;
var name=files.value.substr(filePath);
name=name.replace(/[&\/\\#,+$~%'":*?<>={}]/gi,"");
name=name.replace(/ /gi,"");
var fileIndex=files.value.lastIndexOf('.')+1;
var extension=files.value.substr(fileIndex);
if(extension.toLowerCase()=="jpg" || extension.toLowerCase()=="png" || extension.toLowerCase()=="jpeg"){
var dad=document.createElement("div");
var image=document.createElement("img");
dad.style.display="inline-block";
image.className="imagepreviewEdit";
image.id=name;
image.src=e.target.result;
images.appendChild(dad);
dad.appendChild(image);
imgList.push(name);
}
};
}
}
}
});
form.addEventListener("reset",function(){
for(var key in imgList){
if(imgList[key]!==null){
console.log(imgList[key]);
var image=document.getElementById(""+imgList[key]+"");
if(image!==null){
image.parentNode.removeChild(image);
}
}
}
});
form.addEventListener("submit",function(e){
e.preventDefault();
form.action="./resources/send.php";
form.enctype="multipart/form-data";
if(form.newtitle.value!=="" && form.newcontent.value!=="" && form.newcategoria.value!=="" && form.serial.value!==""){
var formweekdays=form.querySelectorAll('input[name="newweekdays[]"]:checked');
form.newtitle.value=latinize(form.newtitle.value);
form.newcontent.value=latinize(form.newcontent.value);
form.newcategoria.value=latinize(form.newcategoria.value);
form.serial.value=latinize(form.serial.value);
if(form.newcategoria.value.toLowerCase()=="curso" || form.newcategoria.value.toLowerCase()=="cursos"){
forminicio.style.display="block";
formfim.style.display="block";
formhourstart.value="";
formnewhourend.value="";
formnewvalor.style.display="block";
formnewparcelas.style.display="block";
formnewcarga.style.display="block";
formnewmulta.style.display="block";
formcursodiv.className="animated fadeIn";
Array.prototype.forEach.call(formweekdays,function(el){
if(el.value=="on"){
var hourstart=form.querySelector('input[name=newhourstart'+el.id+']').value;
var hourend=form.querySelector('input[name=newhourend'+el.id+']').value;
el.value=el.id;
formhourstart.value+=";"+hourstart;
formnewhourend.value+=";"+hourend;
}
});
if(formnewmulta.value==""){
formnewmulta.value=0;
}
if(formweekdays.length>0 && forminicio.value!=="" && formfim.value!=="" && formhourstart.value!=="" && formnewhourend.value!=="" && formnewvalor.value!=="" && formnewmulta.value!=="" && formnewparcelas.value!=="" && formnewcarga.value!==""){
if(formnewvalor.value!==""){
var n=formnewvalor.value.replace(",",".");
var c=isNaN(c=Math.abs(c)) ? 2 : c,
d=d==undefined ? "." : d,
t=t==undefined ? "" : t,
s=n<0 ? "-" : "",
i=String(parseInt(n=Math.abs(Number(n) || 0).toFixed(c))),
j=(j=i.length)>3 ? j % 3 : 0;
formnewvalor.value=s+(j ? i.substr(0, j)+t : "")+i.substr(j).replace(/(\d{3})(?=\d)/g,"$1"+t)+(c ? d+Math.abs(n-i).toFixed(c).slice(2) : "");
}
if(formnewmulta.value!=="" && formnewmulta.value!==0){
formnewmulta.value=formnewmulta.value / 100;
}
form.submit();
}
}else{
forminicio.value=null;
formfim.value=null;
formhourstart.value=null;
formnewhourend.value=null;
formweekdays=[null];
formnewvalor.value=null;
formnewmulta.value=null;
formnewparcelas.value=null;
formnewcarga.value=null;
form.submit();
}
}
});
}
alter.prototype.inpublic=function(trigger){
return inprivate.call(this,trigger);
};
return alter;
})();
function alterbridge(trigger){
var altobj=new alter();
altobj.inpublic(trigger);
}
function weekdayhourchanger(checkbox){
var hourdivs=checkbox.parentNode.parentNode.querySelectorAll("#hour");
Array.prototype.forEach.call(hourdivs,function(el){
var hourstart=el.querySelector("input[name=newhourstart"+checkbox.id+"]");
var hourend=el.querySelector("input[name=newhourend"+checkbox.id+"]");
var startdefaultime='6';
var enddefaultime='6';
if(hourstart!==null && hourstart!==null){
if(checkbox.checked===true){
if(hourstart.name=="newhourstart"+checkbox.id && hourend.name=="newhourend"+checkbox.id){
el.className="animated fadeIn";
if(hourstart.value!==""){
startdefaultime=hourstart.value.replace("-",":");
}
if(hourend.value!==""){
enddefaultime=hourend.value.replace("-",":");
}
$(hourstart).timepicker({
interval: 15,
defaultTime: startdefaultime,
minTime: '6',
maxTime: '8:00pm',
});
$(hourend).timepicker({
interval: 15,
defaultTime: enddefaultime,
minTime: '6',
maxTime: '8:00pm',
});
}
}else{
el.className="animated fadeOut";
window.setTimeout(function fadeOut(){
el.className="off";
},500);
}
}
});
}