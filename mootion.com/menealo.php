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


if(!($id=check_integer('id'))) {
	error('Video ID missing');
}

if(empty($_REQUEST['user']) && $_REQUEST['user'] !== '0' ) {
	error('User Code missing');
}

if (empty($_REQUEST['md5'])) {
	error('Control Key missing');
}

$link = new Link;
$link->id=$id;
if(!$link->read_basic()) {
	error( 'No such video'. $current_user->user_id . '-'. $_REQUEST['user']);
}

if ($current_user->user_id == 0) {
	if (! $anonnymous_vote) {
		error('anonymous voting disabled');
	} else {
		// Check that there are not too much annonymous votes in 1 hour
		$from = time() - 3600;
		$anon_votes = $db->get_var("select count(*) from votes where vote_type = 'links' and vote_link_id = $id and vote_user_id = 0 and vote_date > from_unixtime($from)");
		if ($anon_votes > $anon_to_user_votes) {
			$user_votes = $anon_to_user_votes * $db->get_var("select count(*) from votes where vote_type = 'links' and vote_link_id = $id and vote_user_id > 0 and vote_date > from_unixtime($from)");
			if ($anon_votes > $user_votes) {
				error( 'too much anonymous vOOtes for this video, try again later' );
			}
		}
	}
}

if($current_user->user_id != $_REQUEST['user']) {
	error('invalid user'. $current_user->user_id . '-'. $_REQUEST['user']);
}

$md5=md5($site_key.$_REQUEST['user'].$id.$link->randkey.$globals['user_ip']);
if($md5 !== $_REQUEST['md5']){
	error('invalid control key');
}


if (!$link->insert_vote($current_user->user_id)) {
	error('already voted');
}
// TODO

if ($link->status == 'discard') {
	$sum = $db->get_var("select sum(vote_value) from votes where vote_link_id = $link->id");
	if ($sum > 0 ) {
		$link->read();
		$link->status = 'queued';
		$link->store();
	}
}
	
$count=$link->votes;
echo "$count mOOves~--~".$_REQUEST['id'];

function error($mess) {
	header('Content-Type: text/plain; charset=UTF-8');
	echo "ERROR: $mess";
	die;
}
?>
