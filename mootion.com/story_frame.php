<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp mootion com> for mOOtion

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');


$globals['ads'] = true;

if(is_numeric($_REQUEST['id'])) {
	$link = new Link;
	$link->id=$_REQUEST['id'];
	$link->read();
	
	if ( $link->embedhtml != '' ) {
		header ('Location: ./story.php?v='.$link->id);
	} else {

	   // Set globals
	   $globals['link_id']=$link->id;
	   $globals['category_id']=$link->category;
	   $globals['category_name']=$link->category_name();
	
   	   // Preparamos el frame en el que mostraremos la
	   // entrada en mootion.com y la URL destino.

	   do_framevotador($link->title, $link->id, $link->url);
	}
	
} else {

   header('Location: /');
   die;
}

?>
