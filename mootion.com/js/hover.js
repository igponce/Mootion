sfHover = function() { 
var sfEls = document.getElementById("dropdownnav").getElementsByTagName("LI");
for (var i=0; i<sfEls.length; i++) {
	sfEls[i].onmouseover=function() {
             this.className+=" sfhover";
}
sfEls[i].onmouseout=function() {
      this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
       }
    }
}
probHover = function() { 
	var probEls = new Array()
	for (j = 0; j<100; j++) {
		probEls[j] = new Array();
		var item = document.getElementById('probdrop'+j);
		if (item) {
			probEls[j] = item.getElementsByTagName("LI");
			for (var i=0; i <probEls[j].length; i++) {
				probEls[j][i].onmouseover=function() {
			             this.className +=" probhover";
				}
				probEls[j][i].onmouseout=function() {
				          this.className=this.className.replace(new RegExp(" probhover\\b"), "");
			       }
	    		}
		}
	}
}
function containsDOM (container, containee) {
  var isParent = false;
  do {
    if ((isParent = container == containee))
      break;
    containee = containee.parentNode;
  }
  while (containee != null);
  return isParent;
}


function checkMouseLeave (element, evt) {

  evt = (evt) ? evt : ((window.event) ? window.event : "");
  window.status = evt;
  if (evt.relatedTarget) {
    return !containsDOM(element, evt.relatedTarget);
  } else {
        if (element.contains(evt.toElement)) {
                return(false);
        } else {
                return(true);
        }
  }
}
function HideandUNhideObj(i){
        nav=document.getElementById('div'+i).style;
        con=document.getElementById('ul'+i);
        if(nav.display=="none"){
                // set mouseout function here
                nav.display = 'block';
                con.onmouseout = function(evt) {
                    if (checkMouseLeave(this, evt)) {
                        i = parseInt(this.id.substr(2));
                        nav=document.getElementById('div'+i).style;
                        nav.display = 'none';
                    }
                }
        } else {
                nav.display='none';
                con.onmouseout = function (evt) {
                    if (checkMouseLeave(this, evt)) {
                        i = parseInt(this.id.substr(2));
                        nav=document.getElementById('div'+i).style;
                        nav.display= 'none';
                    }
                }
        }
}

if (window.attachEvent) {
//    window.attachEvent("onload", sfHover);
    window.attachEvent("onload", probHover);
}
