<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('config.php');
/*
include(mnminclude.'html1.php');
*/
include(mnminclude.'link.php');
include(mnminclude.'localization_en.php');
	
if(!empty($_REQUEST['rows'])) {
	$rows = $_REQUEST['rows'];
	if ($rows > 100) $rows = 30; //avoid abuses
} else $rows = 30;
	
$if_modified = get_if_modified();

if(!empty($_REQUEST['time'])) {
	// Prepare for times
	if(!($time = check_integer('time')))
		die;
	$sql = "SELECT link_id, count(*) as votes FROM votes, links WHERE  ";	
	if ($time > 0) {
		$from = time()-$time;
		$sql .= "vote_date > FROM_UNIXTIME($from) AND ";
	}
	$sql .= "vote_link_id=link_id  AND link_status != 'discard' GROUP BY vote_link_id  ORDER BY votes DESC LIMIT $rows";
	$last_modified = time();
	$title = $rss_mostvotedon . ' ' . txt_time_diff($from);
	//$link_date = "modified";
	$link_date = "";
} else {
	// All the others
	$search = get_search_clause('boolean');
	// The link_status to search
	if(!empty($_REQUEST['status'])) {
		$status = $_REQUEST['status'];
	} else {
		// By default it searches on all
		if($search) $status = 'all';
		else $status = 'published';
	}
	/*****  WARNING
		this function is to redirect to feed burner
		comment it out
		You have been warned ******/

	if (!$search && empty($_REQUEST['category'])) {
		check_redirect_to_feedburner($status);
	}
	
	/*****  END WARNING ******/
	
	
	switch ($status) {
		case 'published':
			$order_field = 'link_published_date';
			$link_date = 'published_date';
			$title = $rss_title_published;
			break;
		case 'queued':
			$title = $rss_title_queued;
			$order_field = 'link_date';
			$link_date = "date";
			$home = "/shakeit.php";
			break;
		case 'all':
			$title = $rss_title_all;
			$order_field = 'link_date';
			$link_date = "date";
			break;
	}
	
	
	if($status == 'all') {
		$from_where = "FROM links WHERE link_status!='discard' ";
	} else {
		$from_where = "FROM links WHERE link_status='$status' ";
	}
	if(($cat=check_integer('category'))) {
		$from_where .= " AND link_category=$cat ";
		$category_name = $db->get_var("SELECT category_name FROM categories WHERE category_id = $cat AND category_lang='$dblang'");
		$title .= " -$category_name-";
	}
	
	if($search) {
		$from_where .= $search;
		$title = 'mOOtion ' . ": " . htmlspecialchars($_REQUEST['search']);
	}
	
	$order_by = " ORDER BY $order_field DESC ";
	$last_modified = $db->get_var("SELECT UNIX_TIMESTAMP(max($order_field)) links $from_where");
	if ($if_modified > 0) {
		$from_where .= " AND $order_field > FROM_UNIXTIME($if_modified)";
	}
	$sql = "SELECT link_id $from_where $order_by LIMIT $rows";
}

if ($last_modified <= $if_modified) {
	header('HTTP/1.1 304 Not Modified');
	exit();
}



do_header($title);

$link = new Link;
$links = $db->get_col($sql);
if ($links) {
	foreach($links as $link_id) {
		$link->id=$link_id;
		$link->read();
		$category_name = $db->get_var("SELECT category_name FROM categories WHERE category_id = $link->category AND category_lang='$dblang'");
		$content = text_to_html($link->content);
		echo "	<item>\n";
		echo "		<title><![CDATA[$link->title]]></title>\n";
		echo "		<link>".get_permalink($link->id)."</link>\n";
		echo "		<comments>".get_permalink($link->id)."</comments>\n";
		if (!empty($link_date))
			echo "		<pubDate>".date("r", $link->$link_date)."</pubDate>\n";
		else echo "      <pubDate>".date("r", time())."</pubDate>\n";
		echo "		<dc:creator>$link->username</dc:creator>\n";
		echo "		<category>$category_name</category>\n";
		echo "		<guid>".get_permalink($link->id)."</guid>\n";
		echo "		<description><![CDATA[$content";
		//echo "&nbsp;&#187;&nbsp;<a href='".htmlspecialchars($link->url)."'>".'noticia original'."</a>";
		echo "]]></description>\n";
		// echo "		<trackback:ping>".get_trackback($link->id)."</trackback:ping>\n";  // no standard
		//echo "<content:encoded><![CDATA[ ]]></content:encoded>\n";
		echo "	</item>\n\n";
	}
}

do_footer();

function do_header($title) {
	global $last_modified, $dblang, $home;

	header('Last-Modified: ' .  gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
	header('Content-type: text/xml; charset=UTF-8', true);
	echo '<?xml version="1.0" encoding="UTF-8"?'.'>' . "\n";
	echo '<rss version="2.0" '."\n";
	echo '     xmlns:content="http://purl.org/rss/1.0/modules/content/"'."\n";
	echo '     xmlns:wfw="http://wellformedweb.org/CommentAPI/"'."\n";
	echo '     xmlns:dc="http://purl.org/dc/elements/1.1/"'."\n";
	echo ' >'. "\n";
	echo '<channel>'."\n";
	echo'	<title>'.$title.'</title>'."\n";
	echo'	<link>http://'.get_server_name().$home.'</link>'."\n";
	echo"	<image><title>".get_server_name()."</title><link>http://".get_server_name()."</link><url>http://".get_server_name()."/img/common/mnm-rss.gif</url></image>\n";
	echo'	<description>'. $msg_rssdescription .'</description>'."\n";
	echo'	<pubDate>'.date("r", $last_modified).'</pubDate>'."\n";
	echo'	<generator>'. $rss_generator .'</generator>'."\n";
	echo'	<language>'. $rss_language .'</language>'."\n";
}

function do_footer() {
	echo "</channel>\n</rss>\n";
}

function get_if_modified() {
	// Bug in FeedBurner, it needs all items
	if (preg_match('/feedburner/i', $_SERVER['HTTP_USER_AGENT'])) return 0;
	//

	// Get client headers - Apache only 
	$request = apache_request_headers(); 
	if (isset($request['If-Modified-Since'])) { 
  		// Split the If-Modified-Since (Netscape < v6 gets this wrong) 
    	$modifiedSince = explode(';', $request['If-Modified-Since']); 
		return strtotime($modifiedSince[0]); 
	} else { 
		return 0;
	}
}

function check_redirect_to_feedburner($status) {
	global $globals; 

	if (!$globals['redirect_feedburner'] || preg_match('/feedburner/', $_SERVER['PHP_SELF']) || preg_match('/feedburner/i', $_SERVER['HTTP_USER_AGENT'])) return;

	switch ($status) {
		case 'published':
			header("Location: http://feeds.feedburner.com/mootion/published");
			exit();
			break;
		/****
			FeedBurner is not enough fast updating it
		case 'queued':
			header("Location: http://feeds.feedburner.com/meneame/queued");
			exit();
			break;
		****/
		case 'all':
			header("Location: http://feeds.feedburner.com/mootion/all");
			exit();
			break;
	}
	
}
?>
