var filter=(function(){
function filter(){}
function inprivate(){
addEventListener("click",function(){
var formulario=document.forms;
/*
for(i=0;formulario.length>i;i++){
for(j=0;formulario[i].elements.length>j;j++){
if(formulario[i].elements[j].type!=="file"){
formulario[i].elements[j].value=formulario[i].elements[j].value.replace(/[&\#*<>{}]/gi,"");
formulario[i].elements[j].value=formulario[i].elements[j].value.replace(/math/gi,"");
formulario[i].elements[j].value=formulario[i].elements[j].value.replace(/svg/gi,"");
formulario[i].elements[j].value=formulario[i].elements[j].value.replace(/include/gi,"");
formulario[i].elements[j].value=formulario[i].elements[j].value.replace(/src/gi,"");
formulario[i].elements[j].value=formulario[i].elements[j].value.replace(/script/gi,"");
formulario[i].elements[j].value=formulario[i].elements[j].value.replace(/url/gi,"");
}
}
}
*/
return formulario.submit;
});
}
filter.prototype.inpublic=function(){
return inprivate.call(this);
};
return filter;
})();
var filterobj=new filter();
filterobj.inpublic();