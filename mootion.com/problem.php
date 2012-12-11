<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>

include('config.php');
include(mnminclude.'link.php');
include(mnminclude.'votes.php');
include(mnminclude.'localization_en.php');

global $err_incorrectvote, $err_noanonvotes, $err_wronguserid,
       $err_wrongpass, $err_votedtwice, $err_voteinserterror,
       $msg_willtakecare;


/*
echo $_SERVER['REQUEST_URI'];
exit;
*/


$link = new Link;
$id=$_REQUEST['id'];
$user_id=$_REQUEST['user'];


$value = intval($_REQUEST['value']);
if ($value < -7 || $value > -1)
	error($err_incorrectvote . " $value");

$link->id=$id;
$link->read_basic();

if ($current_user->user_id == 0 && ! $anonnymous_vote) {
	error($err_noanonvotes);
}

if($current_user->user_id != $_REQUEST['user']) {
	error($err_wronguserid . $current_user->user_id . '-'. $_REQUEST['user']);
}

$md5=md5($site_key.$_REQUEST['user'].$link->randkey.$globals['user_ip']);
if($md5 !== $_REQUEST['md5']){
	error( $err_wrongpass );
}

$vote = new Vote;
$vote->link=$link->id;
$vote->type='links';
$vote->user=$user_id;

if($vote->exists())
	error($err_votedtwice);

$vote->value = $value;
if(!$vote->insert()) {
	error($err_voteinserterror);
}

echo $msg_willtakecare;

function error($mess) {
	echo "$mess";
	die;
}
?>
