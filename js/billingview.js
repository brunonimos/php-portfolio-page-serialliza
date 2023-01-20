var billingview=(function(){
function billingview(){}
function inprivate(button){
billingviewer(button);
}
function billingviewer(button){
var billingform=button.parentNode;
var billingid=billingform.id;
var billingdemonstrativo=billingform.demonstrativo;
billingform.action="./resources/send.php";
if(billingid.value!=="" && billingdemonstrativo.value!==""){
billingform.submit();
}
}
billingview.prototype.inpublic=function(button){
return inprivate.call(this,button);
};
return billingview;
})();
function billingbridge(button){
var billingviewobj=new billingview();
billingviewobj.inpublic(button);
}