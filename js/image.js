var img=(function(){
function img(){}
function inprivate(imageMedia){
imageshow(imageMedia);
}
function imageshow(imageMedia){
var imagediv=imageMedia.parentNode.parentNode.parentNode.parentNode;
var images=imagediv.getElementsByTagName("img");
var tilesarray=[];
for(var key=0;key<=images.length;key++){
if(typeof images.item(key)!=="undefined" && images.item(key)!==null){
tilesarray.push(images.item(key).src.replace("image","raw")+".dzi");
}
}
var figurecaption=imageMedia.parentNode.children[0];
var index=parseInt(figurecaption.id);
var path=window.location.pathname;
var fileIndex=path.lastIndexOf('/')+1;
var fileName=path.substr(fileIndex);
if(fileName==figurecaption.className){
var imageView=document.getElementById(""+figurecaption.className+"-imgView");
var view=OpenSeadragon({
id:imageView.id,
prefixUrl:"js/frameworks/images/",
tileSources:tilesarray,
initialPage:index,
sequenceMode:true,
immediateRender:true,
defaultZoomLevel:0.2,
preserveViewport:true,
iOSDevice:true
});
view.setFullPage(true);
view.addOnceHandler("tile-load-failed",function(){
view.setFullPage(false);
$(imageView).html("");
imageView.className="viewOff";
view.destroy();
});
view.fullPageButton.addOnceHandler("click",function(){
view.destroy();
$(imageView).html("");
imageView.className="viewOff";
});
$(imageView).css({
position:'absolute',
margin:'auto',
width:'100%',
height:'100%',
maxWidth:'100%',
maxHeight:'100%',
top:0,
bottom:0,
left:0,
right:0
});
imageView.className="animated fadeIn";
}else{
window.location.replace("./"+figurecaption.className);
}
}
img.prototype.inpublic=function(imageMedia){
return inprivate.call(this,imageMedia);
};
return img;
})();
function imgshowbridge(imageMedia){
var imgobj=new img();
imgobj.inpublic(imageMedia);
}