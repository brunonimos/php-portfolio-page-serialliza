var create=(function(){
function create(){}
function inprivate(button){
preview(button);
}
function preview(button){
button.style.display="none";
var backbutton=document.querySelector("#back");
backbutton.style.display="block";
backbutton.addEventListener("click",function(e){
e.preventDefault();
window.location.reload(false);
});
var createDiv=document.querySelector("#createSubdiv");
createDiv.className="swipeup";
createDiv.style.display="inline-block";
var createForm=createDiv.querySelector("#creation");
var formtitle=createForm.title;
var formcontent=createForm.content;
var formcategoria=createForm.categoria;
var formVideos=createForm.videos;
var files=createForm.getElementsByClassName("attach buttons")[0];
var imgList=[];
/*var labelforattach=document.getElementById("labelforattach");*/
var forminicio=createForm.inicio;
var formfim=createForm.fim;
var formhourstart=createForm.hourstart;
var formhourend=createForm.hourend;
var formvalor=createForm.valor;
var formmulta=createForm.multa;
var formparcelas=createForm.parcelas;
var formcarga=createForm.carga;
var formcursodiv=forminicio.parentNode;
var createbutton=createForm.criar;
files.style.display="block";
console.log(files);
createbutton.style.display="block";
formVideos.parentNode.style.display="block";
console.log(formVideos);
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
files.addEventListener("change",function(e){
e.preventDefault();
var images=document.querySelector("#creationImagePreview");
var imgs=document.getElementsByClassName("tempreview");
$(imgs).empty();
for(var multimidia in this.files){
if(multimidia!=="length" && multimidia!=="item"){
if(this.files[multimidia].type.match('image.*')){
var reader=new FileReader();
reader.readAsDataURL(this.files[multimidia]);
reader.onload=function(e){
console.log(e);
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
dad.className="tempreview";
image.className="imagepreviewCreate";
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
createForm.addEventListener("reset",function(){
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
createForm.addEventListener("submit",function(e){
createForm.action="./resources/send.php";
createForm.enctype="multipart/form-data";
e.preventDefault();
if(formtitle.value!=="" && formcontent.value!=="" && formcategoria.value!==""){
var formweekdays=createForm.querySelectorAll(".weekday:checked");
formtitle.value=latinize(formtitle.value);
formcontent.value=latinize(formcontent.value);
formcategoria.value=latinize(formcategoria.value);
if(createForm.categoria.value.toLowerCase()=="curso" || createForm.categoria.value.toLowerCase()=="cursos"){
forminicio.style.display="block";
formfim.style.display="block";
formhourstart.value="";
formhourend.value="";
formvalor.style.display="block";
formparcelas.style.display="block";
formcarga.style.display="block";
formmulta.style.display="block";
formcursodiv.className="animated fadeIn";
Array.prototype.forEach.call(formweekdays,function(el){
if(el.value=="on"){
var hourstart=createForm.querySelector('input[name=hourstart'+el.id+']').value;
var hourend=createForm.querySelector('input[name=hourend'+el.id+']').value;
el.value=el.id;
formhourstart.value+=";"+hourstart;
formhourend.value+=";"+hourend;
}
});
if(formmulta.value==""){
formmulta.value=0;
}
if(formweekdays.length>0 && forminicio.value!=="" && formfim.value!=="" && formhourstart.value!=="" && formhourend.value!=="" && formvalor.value!=="" && formmulta.value!=="" && formparcelas.value!=="" && formcarga.value!==""){
if(formvalor.value!==""){
var n=formvalor.value.replace(",",".");
var c=isNaN(c=Math.abs(c)) ? 2 : c,
d=d==undefined ? "." : d,
t=t==undefined ? "" : t,
s=n<0 ? "-" : "",
i=String(parseInt(n=Math.abs(Number(n) || 0).toFixed(c))),
j=(j=i.length)>3 ? j % 3 : 0;
formvalor.value=s+(j ? i.substr(0, j)+t : "")+i.substr(j).replace(/(\d{3})(?=\d)/g,"$1"+t)+(c ? d+Math.abs(n-i).toFixed(c).slice(2) : "");
}
if(formmulta.value!=="" && formmulta.value!==0){
formmulta.value=formmulta.value / 100;
}
console.log(formtitle.value);
console.log(formcontent.value);
console.log(formcategoria.value);
console.log(forminicio.value);
console.log(formfim.value);
console.log(formhourstart.value);
console.log(formhourend.value);
console.log(formweekdays);
console.log(formvalor.value);
console.log(formmulta.value);
console.log(formparcelas.value);
console.log(formcarga.value);
createForm.submit();
}
}else{
forminicio.value=null;
formfim.value=null;
formhourstart.value=null;
formhourend.value=null;
formweekdays=[null];
formvalor.value=null;
formmulta.value=null;
formparcelas.value=null;
formcarga.value=null;
console.log(formtitle.value);
console.log(formcontent.value);
console.log(formcategoria.value);
console.log(forminicio.value);
console.log(formfim.value);
console.log(formhourstart.value);
console.log(formhourend.value);
console.log(formweekdays);
console.log(formvalor.value);
console.log(formmulta.value);
console.log(formparcelas.value);
console.log(formcarga.value);
createForm.submit();
}
}
});
}
create.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return create;
})();
function createbridge(button){
var createobj=new create();
createobj.inpublic(button);
}
function creatingweekdayhourchanger(checkbox){
var hourdivs=checkbox.parentNode.parentNode.querySelectorAll("#hour");
Array.prototype.forEach.call(hourdivs,function(el){
var hourstart=el.querySelector("input[name=hourstart"+checkbox.id+"]");
var hourend=el.querySelector("input[name=hourend"+checkbox.id+"]");
var startdefaultime='6';
var enddefaultime='6';
if(hourstart!==null && hourstart!==null){
if(checkbox.checked===true){
if(hourstart.name=="hourstart"+checkbox.id && hourend.name=="hourend"+checkbox.id){
console.log(hourstart.name);
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