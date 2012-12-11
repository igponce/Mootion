// The source code packaged with this file is Free Software, Copyright (C) 2006
// by Inigo Gonzalez <igponce at corp dot mootion dot com>
//
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// mootion-utils.js

function selectInputText (id) {
   id.focus();
   id.select();
}

function simplepopup (uri) {

   var href;
   if (! window.focus)return true;
   if (typeof(uri) == 'string')
      href=mylink;
   else
      href=mylink.href;
   window.open(href, 'mOOtion', 'width=550,height=300,scrollbars=yes');
   return false;
}

//EOF//