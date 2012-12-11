<?
include('../config.php');
include(mnminclude.'user.php');

header("Content-Type: text/plain");

$karma_base=8;
$min_karma=6;
$max_karma=20;
$negative_per_day = 0.1;
$history_from = time() - 3600*24*7;
$points_received = 15;
$points_given = 20;


$max_positive_received = $db->get_var("SELECT count(*) as votes from links, votes  where vote_date > FROM_UNIXTIME($history_from) and vote_value>0 and vote_link_id = link_id group by link_author order by votes desc limit 1");

//$max_negative_received = $db->get_var("SELECT count(*) as votes from links, votes  where vote_date > FROM_UNIXTIME($history_from) and vote_value<0 and vote_link_id = link_id group by link_author order by votes desc limit 1");

$max_published_given = $db->get_var("SELECT count(*) as votes from links, votes  where vote_date > FROM_UNIXTIME($history_from) and vote_user_id > 0 and vote_value>0 and vote_link_id = link_id and link_status='published' group by link_author order by votes desc limit 1");

$max_nopublished_given = $db->get_var("SELECT count(*) as votes from links, votes  where vote_date > FROM_UNIXTIME($history_from) and vote_user_id > 0 and vote_value>0 and vote_link_id = link_id and link_status!='published' group by link_author order by votes desc limit 1");


print "Pos: $max_positive_received, Neg: $max_negative_received, Published: $max_published_given No: $max_nopublished_given\n";



/////////////////////////



$users = $db->get_results("SELECT user_id from users order by user_login");

foreach($users as $dbuser) {
	$user = new User;
	$user->id=$dbuser->user_id;
	$user->read();
	//$user->all_stats($history_from);

	$positive_votes_received=$db->get_var("SELECT count(*) FROM links, votes WHERE link_author = $user->id and vote_link_id = link_id and vote_date > FROM_UNIXTIME($history_from) and vote_value > 0");
	$negative_votes_received=$db->get_var("SELECT count(*) FROM links, votes WHERE link_author = $user->id and vote_link_id = link_id and vote_date > FROM_UNIXTIME($history_from) and vote_value < 0");

	$karma1 = $points_received * ($positive_votes_received/$max_positive_received) - $points_received * ($negative_votes_received/$max_positive_received) * 5;
	print "$user->username ($positive_votes_received, $negative_votes_received): $karma1\n";

/////

	$published_given = $db->get_var("SELECT count(*) FROM votes,links WHERE vote_user_id = $user->id and vote_date > FROM_UNIXTIME($history_from)  and vote_value > 0 AND link_id = vote_link_id AND link_status = 'published' AND vote_date < link_published_date");
	$nopublished_given = $db->get_var("SELECT count(*) FROM votes,links WHERE vote_user_id = $user->id and vote_date > FROM_UNIXTIME($history_from)  and vote_value > 0 AND link_id = vote_link_id AND link_status != 'published'");
	
	$karma2 = $points_given * ($published_given/$max_published_given) - $points_given * ($nopublished_given/$max_nopublished_given) / 2;
	print "$user->username ($published_given, $nopublished_given): $karma2\n";

	
	$karma3 = 0;
	if ($user->date < time()-86400) {
		$past_time=time() - $db->get_var("select UNIX_TIMESTAMP(max(vote_date)) from votes where vote_user_id=$dbuser->user_id");
		$karma3 = min($past_time*$negative_per_day/(3600*24), 4);
	}

	$karma = max($karma_base+$karma1+$karma2-$karma3, $min_karma);
	$karma = min($karma, $max_karma);
	if ($karma > $max_karma * 0.8 && $user->level == 'normal') {
		$user->level = 'special';
	} else {
		if ($user->level == 'special' && $karma < $max_karma * 0.75) {
			$user->level = 'normal';
		}
	}
	echo $user->username . ": $karma ($user->level)\n";
	$user->karma = $karma;
	$user->store();


}
?>
