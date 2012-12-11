// The source code packaged with this file is Free Software, Copyright (C) 2006
// by Inigo Gonzalez <igponce at corp dot mootion dot com>
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

<?
header('Content-Type: text/javascript; charset=UTF-8');
header('Cache-Control: max-age=3600');

if ( is_null ($_REQUEST['v'])) {
   $vid = $_REQUEST['id'];
} else {
   $vid = $_REQUEST['v'];
}

if ( is_numeric ( $vid) ) { // okay, print ad code.
?>

function write_iframe (span) {
	var span = document.getElementById('mOOtionXternal');
	span.innerHTML='<iframe width="198" height="117" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" vspace="0" hspace="0" allowtransparency="true" src="<? echo 'http://'.$_SERVER['SERVER_NAME']; if ($_SERVER['SERVER_PORT']!=80) { echo ':'.$_SERVER['SERVER_PORT']; } echo '/api/moovexternal.php?v=' . $vid;?>"></iframe>';
}

document.write('<span id="mOOtionXternal" style="width: 198px; height: 117px; border: none; padding: 0; margin: 0; background: transparent ; "><script type="text/javascript">setTimeout("write_iframe()", 200)</script></span>');

<?
} else {
?>
document.write('<h1>BAD CODE</h1>');

<?
}
?>