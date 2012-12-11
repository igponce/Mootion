<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006
// by Inigo Gonzalez <igponce at corp dot mootion dot com>
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('../config.php');

header('Content-Type: text/html; charset=UTF-8');
header('Pragma: no-cache');
header('Cache-Control: max-age=10, must-revalidate');

/* Conseguir el Video en cuestion */

if ( ! is_null ($_REQUEST['v']) && is_numeric ($_REQUEST['v']) ) {
   $vid = $_REQUEST['v'];
   echo '<br>Buscando Video con ID:'. $vid . "<br/>\n";
   $res = $db->get_row("select link_id, link_title, link_randkey, link_embedhtml, count(*) as votes from links, votes where link_id = vote_link_id and link_id = $vid group by link_id");
   if ( $res ) {
      echo '<br>Title: ' . $res->link_title;
      echo '<br>Votes: ' . $res->votes;
      echo '<br>link_random: ' . $res->link_randkey;
   }
   
   
   
} else {
   // deberiamos lanzar un mensaje de error...
}


?>