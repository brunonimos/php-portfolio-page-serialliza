var router=(function(){
function router(){}
function inprivate(id){
verifier(id);
}
function verifier(id){
console.log(id);
}
router.prototype.inpublic=function(id){
return inprivate.call(this,id);
};
return router;
})();
function routerbridge(id){
var routerobj=new router();
routerobj.inpublic(id);
}