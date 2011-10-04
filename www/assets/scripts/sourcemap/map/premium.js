/* Copyright (C) Sourcemap 2011
 * Proprietary functionality for Sourcemap.com
*/

// All of this code is currently orphaned until we hook up channels 

Sourcemap.Map.Editor.Premium = function(map, o) {
    var o = o || {}
    this.map = map.map ? map.map : map;
    if(map instanceof Sourcemap.Map.Base)
        this.map_view = map;
    Sourcemap.Configurable.call(this);
    this.instance_id = Sourcemap.instance_id("sourcemap-editor");
    this.map.editor = this;
}


// Colorpicker Functionality 
Sourcemap.Map.Editor.Premium.prototype.init = function(){
    $('#color-pick').bind('change',function(){
       changeColor();
    });
   
    var widt=false; 
    $(this.map_view.dialog).find('#color-picker').bind('click',function(){
        $("#color-picker-holder").stop().animate({width:widt ? 0 :250}, 500);
        widt = !widt;
    });

}

Sourcemap.Map.Editor.Premium.prototype.mouseOverColor= function(hex){
}

Sourcemap.Map.Editor.Premium.prototype.mouseOutMap = function(){
}

Sourcemap.Map.Editor.Premium.prototype.clickColor = function(hex,seltop,selleft){
    document.getElementById("color-pick").value = hex;
    if (seltop>-1 && selleft>-1)
    {
        document.getElementById("selectedColor").style.top=seltop + "px";
        document.getElementById("selectedColor").style.left=selleft + "px";
        document.getElementById("selectedColor").style.visibility="visible";
    }
    else
    {
        document.getElementById("divpreview").style.backgroundColor=colorhex;
        document.getElementById("divpreviewtxt").innerHTML=colorhex;
        document.getElementById("selectedColor").style.visibility="hidden";
    }
    changeColor();
}

Sourcemap.Map.Editor.Premium.prototype.changeColor = function(){
   var color = document.getElementById("color-pick").value;
   document.getElementById("color-picker").style.background = color;
}

