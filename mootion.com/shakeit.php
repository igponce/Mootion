<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'localization_en.php');

$offset=(get_current_page()-1)*$page_size;
$globals['ads'] = true;


$search = get_search_clause();
if($search) {
	do_header($msg_search. ' "'.htmlspecialchars($_REQUEST['search']).'"');
	do_navbar($msg_pendinglinks. ' &#187; ' . $msg_search);
	echo '<div id="contents">'."\n";
	echo '<h2>'. $msg_searchpending .': "'. htmlspecialchars($_REQUEST['search']) .'" </h2>';
	$search_where .= $search;
	$order_by = '';
} else {
	do_header($msg_pending);
	do_navbar($msg_queued);
	echo '<div id="contents">'."\n";
	echo '<h2>'. $msg_pendinglinks .'</h2>'."\n";
	$order_by = " ORDER BY link_date DESC ";
}


// tabs

echo '<div class="sub-nav">'."\n";
// echo '<div class="shakeit-nav">'."\n";
echo '<ul class="shakeit-sub-nav">'."\n";

$view = $_REQUEST['view'];
$cat = check_integer('category');

switch ($view) {
	case 'discarded':
		//$from = time() - 86400*4; //Only show discarded in last N days
		$from_where = "FROM links WHERE link_status='discard' and (link_votes >0 || link_author = $current_user->user_id)";
		echo '<li><a href="shakeit.php">'. $msg_all . '</a></li>'."\n";
		if ($current_user->user_id > 0)
			echo '<li><a href="shakeit.php?view=recommended">'. $msg_recommended . '</a></li>'."\n";
		echo '<li class="active"><a href="shakeit.php?view=discarded">'. $msg_discarded .'</a></li>'."\n";
		echo '</ul></div>'."\n";
	break;
	case 'recommended':
		if ($current_user->user_id > 0 && !$search) {
			$threshold = $db->get_var("select friend_value from friends where friend_type='affiliate' and friend_from = $current_user->user_id and friend_to=0");
			if(!$threshold) $threshold = 0;
			else $threshold = $threshold * 0.95;
			
			$from = time() - 86400*7; //Only show recommended in last N days
			$from_where = "FROM links, friends WHERE link_date > FROM_UNIXTIME($from) and link_status='queued' and friend_type='affiliate' and friend_from = $current_user->user_id and friend_to=link_author and friend_value > $threshold";
			$order_by = " ORDER BY link_date DESC ";	
			echo '<li><a href="shakeit.php">'. $msg_all . '</a></li>'."\n";
			echo '<li class="active"><a href="shakeit.php?view=recommended">'. $msg_recommended .'</a></li>'."\n";
			echo '<li><a href="shakeit.php?view=discarded">'. $msg_discarded .'</a></li>'."\n";
			echo '</ul></div>'."\n";
			break;
		}
	case 'all':
	default:
		$from_where = "FROM links WHERE link_status='queued' $search_where";
		echo '<li class="active"><a href="shakeit.php">'. $msg_all .'</a></li>'."\n";
		if ($current_user->user_id > 0)
			echo '<li><a href="shakeit.php?view=recommended">'. $msg_recommended .'</a></li>'."\n";
		echo '<li><a href="shakeit.php?view=discarded">'. $msg_discarded .'</a></li>'."\n";
		echo '</ul></div>'."\n";
	break;
}

// end of tabs

if($cat) {
	$from_where .= " AND link_category=$cat ";
}

$link = new Link;
$rows = $db->get_var("SELECT count(*) $from_where $order_by");
$links = $db->get_col("SELECT link_id $from_where $order_by LIMIT $offset,$page_size");
if ($links) {
	$nlinks = 1;
	foreach($links as $link_id) {
		$link->id=$link_id;
		$link->read();
		$link->print_summary();
		
			
                if (  $nlinks == 1 || $nlinks == 8) {
                        echo '<div class="news-summary" id="robapaginas"><div class="news-body">';
                        echo '<h4>Publi</h4>';
			include ('ads/google-robapaginas.inc');
                        echo '</div></div>';
                }
                $nlinks++;
	}
}

do_pages($rows, $page_size);
echo '</div>'."\n";
do_sidebar_shake();
do_footer();


?>
