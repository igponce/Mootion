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
include(mnminclude.'localization_en.php');

$globals['ads'] = true;

$range_values = array(86400, 604800, 2592000, 31536000, 0);

$offset=(get_current_page()-1)*$page_size;

if(($from = check_integer('range')) >= 0 && $from < count($range_values) && $range_values[$from] > 0 ) {
	$from_time = time() - $range_values[$from];
	$from_where = "FROM votes, links WHERE  vote_date > FROM_UNIXTIME($from_time) AND vote_link_id=link_id AND";
	$time_link = "link_modified > FROM_UNIXTIME($from_time) AND";
} else {
	$from_where = "FROM votes, links WHERE vote_link_id=link_id AND vote_value > 0 AND";
	$time_link = '';
}
$from_where .= " link_status != 'discard'";
$from_where .= " GROUP BY vote_link_id";


do_header( $hdr_mostpopular );
do_navbar($msg_videos .' &#187; '. $msg_stats);
echo '<div id="contents">';
echo '<h2>'. $msg_mostpopular .'</h2>';
$order_by = " ORDER BY votes DESC ";

$link = new Link;

//$rows = $db->get_var("SELECT count(*) as votes $from_where $order_by");
$rows = $db->get_var("SELECT count(*) FROM links WHERE $time_link link_status != 'discard'");

$links = $db->get_results("SELECT link_id, count(*) as votes $from_where $order_by LIMIT $offset,$page_size");
if ($links) {
	foreach($links as $dblink) {
		$link->id=$dblink->link_id;
		$link->read();
		$link->print_summary('short');
	}
}
do_pages($rows, $page_size);
echo '</div>';
do_sidebar_top();
do_footer();


function do_sidebar_top() {
	global $db, $dblang, $range_values, $range_names;

	echo '<div id="sidebar">'."\n";
	echo '<ul class="main-menu">'."\n";
	echo '<li>'."\n";
	echo '<div class="column-select-us">'."\n";
	echo '<ul>'."\n";

	if(!($current_range = check_integer('range')) || $current_range < 1 || $current_range >= count($range_values)) $current_range = 0;
	for($i=0; $i<count($range_values); $i++) {	
		if($i == $current_range)  {
			echo '<li class="thiscat">' .$range_names[$i]. '</li>'."\n";
		} else {
			echo '<li><a href="'.$_SERVER['PHP_SELF'].'?range='.$i.'">' .$range_names[$i]. '</a></li>'."\n";
		}
		
	}
	echo '</ul>'."\n";
	echo '</div>'."\n";
	echo '</li>'."\n";

	do_top_rss_box();

	echo '</ul>';
	echo '</div>';

}

function do_top_rss_box() {
	global $globals, $range_values, $range_names;
	global $msg_subscribepopular;

	echo '<li>' . "\n";
	echo '<ul class="rss-list">' . "\n";
	echo '<li class="rss-retol">'. $msg_subscribepopular .'</li>'."\n";

	for($i=0; $i<count($range_values); $i++) {	
		echo '<li><a href="/rss2.php?time='.$range_values[$i].'" rel="rss">' .$range_names[$i]. '</a></li>'."\n";
	}
	echo '</ul>' . "\n";
	echo '<br style="clear: both;" />' . "\n";
	echo '</li> <!--topstories:do_top_rss_box()-->' . "\n";
}
?>
