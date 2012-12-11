<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('../config.php');

header('Content-Type: text/html; charset=UTF-8');
if(empty($_GET['url'])) {
	echo 'KO';
	die;
}
$url = $_GET['url'];
$res = $db->get_row("select link_id, link_votes, link_status from links where link_url='$url' and link_status != 'discard'");
if ($res) {
	echo 'OK http://'.get_server_name().'/story.php?id='.$res->link_id.' '.$res->link_votes.' '.$res->link_status;
} else {
	echo 'KO http://'.get_server_name().'/submit.php?url='.$url;
}
?>
