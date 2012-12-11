<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'localization_en.php');

$globals['ads'] = true;

$min_pts = 10;
$max_pts = 32;
$limit = 50;
$line_height = $max_pts * 0.75;

$range_values = array(604800, 2592000, 31536000, 0);


if(($from = check_integer('range')) >= 0 && $from < count($range_values) && $range_values[$from] > 0 ) {
	$from_time = time() - $range_values[$from];
	$from_where = "FROM blogs, links WHERE  link_published_date > FROM_UNIXTIME($from_time) and link_status = 'published' and link_lang = '$dblang' and link_blog = blog_id and blog_type='blog'";
	$time_query = "&amp;from=$from_time";
} else {
	$from_where = "FROM blogs, links WHERE link_status = 'published' and link_lang = '$dblang' and link_blog = blog_id and blog_type='blog'";
}
$from_where .= " GROUP BY blog_id";

$max = max($db->get_var("select count(*) as count $from_where order by count desc limit 1"), 2);
//echo "MAX= $max\n";

$coef = ($max_pts - $min_pts)/($max-1);


do_header($hdr_blogscloud);
do_navbar($msg_blogs);
echo '<div id="contents">';
echo '<h2>' . $msg_topblogs . '</h2>';

echo '<div style="margin: 20px 0 20px 0; line-height: '.$line_height.'pt; margin-left: 100px;">';
$res = $db->get_results("select blog_url, count(*) as count $from_where order by count desc limit $limit");
if ($res) {
	foreach ($res as $item) {
		$blogs[$item->blog_url] = $item->count;
	}
	ksort($blogs);
	foreach ($blogs as $url => $count) {
		$text = preg_replace('/http:\/\//', '', $url);
		$text = preg_replace('/^www\./', '', $text);
		$text = preg_replace('/\/$/', '', $text);
		$size = intval($min_pts + ($count-1)*$coef);
		echo '<span style="font-size: '.$size.'pt"><a href="'.$url.'">'.$text.'</a></span>&nbsp;&nbsp; ';
	}
}

echo '</div>';
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

	do_standard_links();
	echo '</ul>';
	echo '</div>';

}

?>
