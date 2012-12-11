<?
include('../config.php');
include(mnminclude.'link.php');

header("Content-Type: text/plain");
ob_end_flush();

define(MAX, 2);
define (MIN, 1);

$now = time();
echo "BEGIN: ".get_date_time($now)."\n";
if($_GET['period'] )
	$period = $_GET['period'];
else $period = 200;

echo "Period (h): $period\n";

$from_time = $now - $period*3600;
#$from_where = "FROM votes, links WHERE  


$last_published = $db->get_var("SELECT UNIX_TIMESTAMP(max(link_published_date)) from links WHERE link_status='published'");
if (!$last_published) $last_published = $now - 24*3600*30;
$history_from = $last_published - $period*3600;

$diff = $now - $last_published;

$d = min(MAX, MAX - ($diff/3600)*(MAX-MIN) );
$d = max(0.75, $d);
print "Last published at: " . get_date_time($last_published) ."\n";
echo "Decay: $d\n";

$continue = true;
$i=0;
while ($continue) {
	$continue = false;
	$past_karma = $db->get_var("SELECT avg(link_karma) from links WHERE link_published_date > FROM_UNIXTIME($history_from) and link_status='published'");
	echo "Past karma: $past_karma\n";
//////////////
	$min_karma = 11 ; // max($past_karma * $d, 12);
	$min_votes = 3;
/////////////
	
	echo "MIN karma: $min_karma\n    votes: $min_votes\n";
	$where = "vote_date > FROM_UNIXTIME($from_time) AND vote_link_id=link_id AND link_status = 'queued' AND link_votes>=$min_votes";
	$group =  "GROUP BY vote_link_id";
	$sort = "ORDER BY karma DESC";


	$votes = $db->get_var("SELECT count(*) from links, votes where $where");
	$karma_total = $db->get_var("SELECT sum(vote_value) from links, votes where $where");

	$links = $db->get_results("SELECT link_id, sum(vote_value) as karma, count(*) as votes from links, votes where $where $group $sort LIMIT 30");
	$rows = $db->num_rows;
	if (!$rows) {
		echo "There is no articles\n";
		echo "--------------------------\n";
		die;
	}
	
	$karma_avg = $karma_total / $rows;
	echo "Votes: $votes Karma_total: $karma_total Media: $karma_avg\n";

	$max_karma_found = 0;
	$best_link = 0;
	$best_karma = 0;
	
	if ($links) {
//		$dblink = current($links);
		foreach($links as $dblink) {
			$link = new Link;
			$link->id=$dblink->link_id;
			$link->read();
			//$karma = $dblink->karma/sqrt($period);

			//add to blogs
			$is_blog=$link->type();
			echo "BLOG: $link->blog, $link->type\n";
			if($link->type() == 'blog') {
				$db_link->karma *= 1.1;
			}

			// Aged karma
			$diff = max(0, $now - ($link->date + 12*3600)); // 1 hour without decreasing
			$oldd = 1 - $diff/(3600*144);
			$oldd = max(0.5, $oldd);
			$oldd = min(1, $oldd);
			//echo "Oldness: $oldd ($diff)\n";
			$aged_karma = $dblink->karma * $oldd;

			$dblink->karma=$aged_karma;

			$max_karma_found = max($max_karma_found, $dblink->karma);
			echo "$link->id:  $is_blog, $dblink->votes, $dblink->karma, '" . $link->title . "_"; echo "'\n";
			
			if ($dblink->votes >= $min_votes && $dblink->karma > $min_karma &&
				$dblink->karma > ($max_karma_found - 0.1) ) {
				$best_link = $link->id;
				$best_karma = $dblink->karma;
				echo "Better found: $link->id, $dblink->karma\n";
			}
		}

		//////////
		echo "----------\n";
		echo "Best karma: $max_karma_found \n";
		if ($best_link > 0) {
			$i++;
			$link->id = $best_link;
			$link->read();
			$link->karma=$best_karma;
			$link->status='published';
			$link->published_date=time();
			echo "Best found: $link->id, $link->karma\n";
			echo "$i Published: $link->title \n";

			echo "--- Publicando en Twitter ---\n";

			define('POSTURL', 'http://mootion:galleta@twitter.com/statuses/update.json');
			define('POSTVARS', 'status=http://mootion.com/');

 			// INITIALIZE ALL VARS

 			 $ch = curl_init(POSTURL);
			 curl_setopt($ch, CURLOPT_POST      ,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS    , POSTVARS. $best_link. '-tw '.  htmlspecialchars(wordwrap($link->title, 36, " ", 1)));
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
			curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
			curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
			$Rec_Data = curl_exec($ch);


			echo "$i -------------\n";

			$link->store();
			if ($i < 3 && $d > 1.1) $continue = true;
		} 
		/****else {
			$future = $past_karma * 3600 / $blink->karma;
			echo "Estimated: " . get_date_time(time()+$future) ."\n";
		}
		**********/
	}  
	echo "--------------------------\n";
}
?>
