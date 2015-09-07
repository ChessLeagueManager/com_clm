var clm_minHeight = 0;

function clm_height_set(from) {
var out = from.getElementsByTagName("ul");
if(out.length!=1){return;}
clm_minHeight=window.document.getElementById('clm').style.minHeight;

window.document.getElementById('clm').style.minHeight=(out[0].offsetHeight+(GetScreenCordinates(out[0]).y-GetScreenCordinates(window.document.getElementById('clm')).y))+'px';
};

function clm_height_del(from) {
window.document.getElementById('clm').style.minHeight=clm_minHeight;
};

 function GetScreenCordinates(obj) {
        var p = {};
        p.x = obj.offsetLeft;
        p.y = obj.offsetTop;
        while (obj.offsetParent) {
            p.x = p.x + obj.offsetParent.offsetLeft;
            p.y = p.y + obj.offsetParent.offsetTop;
            if (obj == document.getElementsByTagName("body")[0]) {
                break;
            }
            else {
                obj = obj.offsetParent;
            }
        }
        return p;
}
