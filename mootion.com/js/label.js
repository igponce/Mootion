//==============================================================================
// labels.js 1.0
// lives at : www.thestandardhack.com/components/labels.html (soon my friend)
// born by  : aaron boodman aaron@youngpup.net www.youngpup.net
//==============================================================================
// this file uses the html label element to create one of those chill default 
// label things for form elements that flicks on and off when you focus and 
// blur the element.
//
// some cool features: 
//  - it uses event listening, so it doesn't interfere with anything else.
//  - it's easy to use: just drop this file in the head of your document.
//  - it degrades (users see traditional HTML labels)
//  - it cleans itself up before unload so that labels are never accidentally
//    submitted through forms.
//
// everything would be alot simpler if IE would just let you change the type
// of an input element (as mozilla does and the spec requires)
//==============================================================================

addEvent(window, "load", labels_init);


//==============================================================================
// Setup
//==============================================================================
// - turn display of labels off
// - initialize all labels
// - arrange for uninit to be called before any form submissions
//==============================================================================
function labels_init() {
	if (document.getElementById && document.styleSheets) {
		try {
			var s = document.styleSheets[document.styleSheets.length-1];
			// little hack: display:xxxx; does not work for labels in mozilla
			addStyleRule(s, "label.inside", "position:absolute; visibility:hidden;");

			for (var i = 0, label = null; 
				(label = document.getElementsByTagName("label")[i]); 
				i++) 
			{
				// some may want to check for a special className here if only
				// some fields are to exibit the dhtml label behavior.
				//
				// only work for labels of class 'inside'
				if (label.className == 'inside') {
					label_init(label);
				}
			}

			addEvent(document.forms[0], "submit", labels_uninit);
		} 
		catch (e) { }
	}
}
	


// tear-down.
// - clear all labels so they don't accidentally get submitted to the server
function labels_uninit(e) {
	if (document.getElementById && document.styleSheets) {
		for (var i = 0, label = null; 
			(label = document.getElementsByTagName("label")[i]); 
			i++) 
		{
			var el = document.getElementById(label.htmlFor);
			if (el && el.value == el._labeltext) label_hide(el);
		}
	}
}
	



// initialize a single label.
// - only applicable to textarea and input[text] and input[password]
// - arrange for label_focused and label_blurred to be called for focus and blur
// - show the initial label
// - for other element types, show the default label
function label_init(label) {
	try {
		var el = document.getElementById(label.htmlFor);
		var elName = el.nodeName;
		var elType = el.getAttribute("type");

		if (elName == "TEXTAREA" 
		|| (elType == "text" || elType == "password")) {
			el._labeltext = label.firstChild.nodeValue;
			el._type = el.getAttribute("type");
			addEvent(el, "focus", label_focused);
			addEvent(el, "blur", label_blurred);
			label_blurred({currentTarget:el});
		} else {
			label.style.position = "static";
			label.style.visibility = "visible";
		}
	}
	catch (e) { 
		label.style.position = "static";
		label.style.visibility = "visible";
	}
}




function label_focused(e) {
	e = fix_e(e);
	var el = e.currentTarget;
	if (el.value == el._labeltext) el = label_hide(el)
	el.select();
}

function label_hide(el) {
	if (el._type == "password") el = label_setInputType(el, "password");
	el.value = "";
	return el;
}

function label_blurred(e) {
	e = fix_e(e);
	var el = e.currentTarget;
	if (el.value == "") el = label_show(el);
}

function label_show(el) {
	if (el._type == "password") el = label_setInputType(el, "text");
	el.value = el._labeltext;
	return el;
}



//==============================================================================
// XXX hack:
//==============================================================================
// msie won't let us change the type of an existing input element, so to get this 
// functionality, we need to create the desired element type in an HTML string.
//==============================================================================
function label_setInputType(el, type) {
	if (navigator.appName == "Microsoft Internet Explorer") {
		var newEl = document.createElement("SPAN");
		newEl.innerHTML = '<input type="' + type + '" />';
		newEl = newEl.firstChild;
		var s = '';
		for (prop in el) {
			// some properties are read-only
			try {
				// the craziest browser bug ever: "height" and "width" (which 
				// should not even exist) return totally garbage numbers, like 
				// 458231 for instance, so we need to ignore those.
				if (prop != "type"
				&& prop != "height"
				&& prop != "width") newEl[prop] = el[prop];
			} 
			catch(e) { }
		}
		addEvent(newEl, "focus", label_focused);
		addEvent(newEl, "blur", label_blurred);
		el.parentNode.replaceChild(newEl, el);
		return newEl;
	} else {
		el.setAttribute("type", type);
		return el;
	}
}



//==============================================================================
// utility functions below...
//==============================================================================

// scott andrew (www.scottandrew.com) wrote this function. thanks, scott!
// adds an eventListener for browsers which support it.
function addEvent(obj, evType, fn){
  if (obj.addEventListener){
    obj.addEventListener(evType, fn, true);
    return true;
  } else if (obj.attachEvent){
	var r = obj.attachEvent("on"+evType, fn);
    return r;
  } else {
	return false;
  }
}

// add a style rule in ie or dom
function addStyleRule(stylesheet, selector, rule) {
	if (stylesheet.addRule) stylesheet.addRule(selector, rule);
	else {
		var index = stylesheet.cssRules.length;
		stylesheet.insertRule(selector + "{" + rule + "}", index);
	}
}

// makes ie behave like a sc browser with regard to events
function fix_e(e) {
	if (!e && window.event) e = window.event;
	if (!e.currentTarget && e.srcElement) e.currentTarget = e.srcElement;
	if (!e.originalTarget && e.srcElement) e.originalTarget = e.srcElement;
	// paul:
	// we can put more things in here as we go along, 
	// whenever we come across differences that need to
	// be fixed.
	return e;
}
