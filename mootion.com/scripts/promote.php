<?
include('../config.php');
include(mnminclude.'link.php');

header("Content-Type: text/plain");

$range_names  = array( 'todas', '24 horas', 'última semana', 'último mes', 'último año');
$range_values = array(0, 86400, 604800, 2592000, 31536000);

$now = time();
echo "BEGIN: ".get_date_time($now)."\n";
if($_GET['period'] )
	$period = $_GET['period'];
else $period = 24;

echo "Period (h): $period\n";

$from_time = $now - $period*3600;
#$from_where = "FROM votes, links WHERE  



$continue = true;
while ($continue) {
	$last_published = $db->get_var("SELECT UNIX_TIMESTAMP(max(link_published_date)) from links WHERE link_status='published'");
	if (!$last_published) $last_published = time() - 24*3600*30;
	$d = min(5, 1/(($now-$last_published)/3600));
	$d = max(0.75, $d);
	print "Last published at: " . get_date_time($last_published) ."\n";
	echo "Decay: $d\n";

	echo "Period: $period\n";
	$past_karma = $db->get_var("SELECT avg(link_karma) from links WHERE link_published_date > FROM_UNIXTIME($last_published-$from_time) and link_status='published'");
	echo "Past karma: $past_karma\n";
//////////////
	$min_karma = max($past_karma * $d, 20);
	$min_votes = 5;
/////////////
	
	echo "MIN karma: $min_karma\n    votes: $min_votes\n";
	$where = "vote_date > FROM_UNIXTIME($from_time) AND vote_link_id=link_id AND link_status = 'queued' AND link_votes>=$min_votes";
	$group =  "GROUP BY vote_link_id";
	$sort = "ORDER BY karma DESC";


	$link = new Link;
	$votes = $db->get_var("SELECT count(*) from links, votes where $where");
	$karma_total = $db->get_var("SELECT sum(vote_value) from links, votes where $where");

	$links = $db->get_results("SELECT link_id, sum(vote_value) as karma, count(*) as votes from links, votes where $where $group $sort");
	$rows = $db->num_rows;
	if (!$rows) {
		echo "There is no articles\n";
		echo "--------------------------\n";
		die;
	}
	
	$karma_avg = $karma_total / $rows;
	echo "Votes: $votes Karma_total: $karma_total Media: $karma_avg\n";

	if ($links) {
		$dblink = current($links);
//		foreach($links as $dblink) {
		$i++;
		$link->id=$dblink->link_id;
		$link->read();
			//$karma = $dblink->karma/sqrt($period);
		echo "$link->id:  $dblink->votes, $dblink->karma _" . $link->title . "_";
		echo "\n";
		if ($dblink->votes >= $min_votes && $dblink->karma > $min_karma) {
			$link->karma=$dblink->karma;
			$link->status='published';
			$link->published_date=time();
			$link->store();
			echo "Published: $link->title \n";
		} else {
			$future = $past_karma * 3600 / $dblink->karma;
			echo "Estimated: " . get_date_time(time()+$future) ."\n";
		}
		$continue = false;
	} 
	echo "--------------------------\n";
	$continue = false;
}
?>
