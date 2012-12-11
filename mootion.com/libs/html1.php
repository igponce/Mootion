<?php

// The source code packaged with this file is Free Software, Copyright (C) 2005 by

// Ricardo Galli <gallir at uib dot es>.

// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.

// You can get copies of the licenses here:

// 		http://www.affero.org/oagpl.html

// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".



// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>



@include('ads-credits-functions.php');

include ('localization-html1_en.php');



function do_navbar($where) {

	if ($where != '') $where = '&#187; '.$where; // benjami: change &#187 order

	echo '<div id="nav-string"><div>&#187; <a href="./"><strong>'.$_SERVER['SERVER_NAME'].'</strong></a>' . $where . '</div></div>' . "\n";

	do_banner_top();

}



// Frame votador: Muestra la URL destino y permite votar

// en la misma pagina...



function do_framevotador ($title, $id='home', $url) {

	global $current_user, $dblang, $globals;



	header("Content-type: text/html; charset=utf-8");

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";

	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$dblang.'" lang="'.$dblang.'">' . "\n";

	echo '<head>' . "\n";

	echo "<title>". $title ." // mOOtion - mOOving pictures</title>\n";

	echo '<meta name="generator" content="mOOtion" />' . "\n";

	echo '<meta name="keywords" content="'.$globals['tags'].'" />' . "\n";

	echo '<link rel="icon" href="/favicon.ico" type="image/x-icon" />' . "\n";

	echo '<frameset rows="125,*" frameborder="no" border="0" framespacing="0" cols="*">' . "\n";

	echo '<frame name="arriba" scroll="no" noresize src="story_mini.php?id='. $id .'">' ." \n";

	echo '<frame name="abajo" src="'. $url .'">' . "\n";

	echo '<noframes><body></body></noframes>' . "\n";

	echo '</frameset></body></html>';

}



function do_header ($title, $id='home') {

	global $current_user, $dblang, $globals;

	// ugly; localization

	global $msg_sourcecode, $msg_about, $msg_profile, $msg_login, $msg_logout, $msg_signup,

	       $msg_search, $fld_search;



	header("Content-type: text/html; charset=utf-8");

	

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";

	//echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";

	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$dblang.'" lang="'.$dblang.'">' . "\n";

	echo '<head>' . "\n";

	echo '<!-- TradeDoubler site verification 1475404 -->';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";

	echo "<title>". $title ." // mOOtion - mOOving pictures</title>\n";

	echo '<meta name="generator" content="e.mootion" />' . "\n";

	echo '<meta name="keywords" content="'.$globals['tags'].'" />' . "\n";

	echo '<style type="text/css" media="screen">@import "/css/en/mnm01.css";</style>' . "\n";

	//echo '<style type="text/css" media="all">@import "./css/es/mootion.css";</style>' . "\n";

	//echo '<style type="text/css" media="all">@import "./css/es/toolbar.css";</style>' . "\n";

	echo '<link rel="alternate" type="application/rss+xml" title="'. 'published' .'" href="http://'.get_server_name().'/rss2.php" />'."\n";

	echo '<link rel="alternate" type="application/rss+xml" title="'. 'pending' .'" href="http://'.get_server_name().'/rss2.php?status=queued" />'."\n";

	echo '<link rel="alternate" type="application/rss+xml" title="'. 'all' .'" href="http://'.get_server_name().'/rss2.php?status=all" />'."\n";

	echo '<link rel="icon" href="/favicon.ico" type="image/x-icon" />' . "\n";

	echo '<script src="./js/xmlhttp01.js" type="text/javascript"></script>' . "\n";

	echo '<script src="./js/mootion-utils.js" type="text/javascript"></script>' . "\n";



	if ( $id == 'embed' ) {

		echo '<script type="application/javascript">

		   <!--

		   window.focus();

		   //-->

		   </script>';

	}



	echo '</head>' . "\n";

	echo "<body id=\"$id\">\n";
	echo '<script type="text/javascript">window.google_analytics_uacct = "UA-58031-3";</script>';

	# Alertbar ads:

	@include "ads/alertbar.inc";



	echo '<div id="container">' . "\n";

	echo '<div id="logo">'  . "\n";

	echo '<a href="/"><img src="/img/es/logo01.png" alt="logo" /></a>';

	echo '</div>'  . "\n";



	echo '<div id="header">' . "\n";

	echo '<ul>' . "\n";

// IGP // 	echo '<li><a href="./archives/meneame-src.tgz">' . $msg_sourcecode . '</a></li>' . "\n";

	echo '<li><a href="http://blog.mootion.com/?page_id=7">' . $msg_about .'</a></li>' . "\n";

	if ($title != "login") {

		if($current_user->authenticated) {

	  		echo '<li><a href="/login.php?op=logout&amp;return='.urlencode($_SERVER['REQUEST_URI']).'">' . $msg_logout . '</a></li>' . "\n";

  			echo '<li><a href="/user.php">' . $msg_profile . ' ' . $current_user->user_login . '</a></li>' . "\n";

		} else {

  			echo '<li><a href="/register.php">' . $msg_signup . '</a></li>' . "\n";

  			echo '<li><a href="/login.php?return='.urlencode($_SERVER['REQUEST_URI']).'">' . $msg_login .'</a></li>' . "\n";

		}

	}

	echo '<li>' . "\n";

		echo '<form action="./" method="get" name="thisform" id="thisform-search">' . "\n";

		echo '<label for="search" accesskey="100" class="inside">'. $msg_search .'</label>' . "\n";

		if (!empty($_REQUEST['search'])) {

			echo '<input type="text" name="search" id="search" value="'.$_REQUEST['search'].'" />' . "\n";

		} else {

		// benjami: onblur and onfocus to this	

			echo '<input name="search" id="search" value="'. $fld_search .'" type="text" onblur="if(this.value==\'\') this.value=\''. 'search...' .'\';" onfocus="if(this.value==\''. $msg_search .'\') this.value=\'\';"/>' . "\n";

		}

		echo '</form>' . "\n";

	echo '</li>' . "\n";

	echo '</ul>' . "\n";

	echo '</div>' . "\n";

}



function do_header_mini ($title, $id='home') {

	global $current_user, $dblang, $globals;



	header("Content-type: text/html; charset=utf-8");

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";

	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$dblang.'" lang="'.$dblang.'">' . "\n";

	echo '<head>' . "\n";

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";	

	echo '<meta name="generator" content="mootion" />' . "\n";

	echo '<meta name="keywords" content="'.$globals['tags'].'" />' . "\n";

	echo "<title>". $title ." // mOOtion - mOOving pictures</title>\n";

	echo '<style type="text/css" media="screen">@import "/css/es/mnm01html";</style>' . "\n";

	//echo '<style type="text/css" media="all">@import "./css/es/meneame.css";</style>' . "\n";

	//echo '<style type="text/css" media="all">@import "./css/es/toolbar.css";</style>' . "\n";

	echo '<script src="./js/xmlhttp01.js" type="text/javascript"></script>' . "\n";

	echo '<script src="./js/mootion-utils.js" type="text/javascript"></script>' . "\n";

	echo '</head>' . "\n";

	echo "<body id=\"$id\">\n";

//	echo '<div id="container">' . "\n";



}



function do_footer() {

	@do_credits(); // see lib/ads-credits-functions

	echo "</div><!--#container closed-->\n";

	@include ('ads/googleanalytics.inc');

	@include ('ads/hittail.inc');

	// Technorati code here...

	echo "</body></html><!--html1:do_footer-->\n";

}



function do_sidebar() {

	global $db, $dblang, $globals;

	// ugly; localization

	global $msg_sitename, $msg_sitedescription, $msg_sitereadmore,

	       $msg_voterecent, $msg_votepending, $msg_submitnew;

	       

	echo '<div id="sidebar">';

	

	if(!empty($_REQUEST['id']) || !empty($_REQUEST['v'])) {

		$doing_story=true;

		do_trackbacks();

		echo '<ul class="main-menu">' . "\n";

	} else {

		$doing_story=false;

		echo '<ul class="main-menu">' . "\n";

		echo '<li>' . "\n";

		echo '<div class="note-temp">' . "\n";

		echo '<strong>' . $msg_sitename . '</strong>' . "\n";

		echo $msg_sitedescription .'&nbsp;<a href="http://blog.mootion.com/?page_id=7">'.  $msg_sitereadmore . '</a>.' . "\n";

		echo '</div>' . "\n";

		echo '</li>' . "\n";

	}





	// Always show the 'submit videos' box.

	// if(empty($_REQUEST['id']) || empty($_REQUEST['v'])) {

		// submit box

		echo '<li id="main-submit"><a href="/video_submit.php">'. $msg_submitnew .'</a></li>' . "\n";

	//}

	

	

	// Menear box

	echo '<li class="main-digg"><a href="/shakeit.php">'. $msg_voterecent .'</a></li>' . "\n";


	if ($globals['do_vote_queue']) {

		echo '<li class="main-digg-moretext"><a href="/shakeit.php?category='.$globals['category_id'].'">'. $msg_votepending .' <strong>'.$globals['category_name'].'</strong></a></li>' . "\n";

	}


	// Antes de la lista de categorias ponemos
	// los anuncios de afiliados con rotacion

	include ('ads/adrotator-sidebar.inc');


	if(empty($_REQUEST['id']) || empty($_REQUEST['v'])) {

		// submit box

		do_categories('index', $_REQUEST['category']);

		// do_tags_box();

		do_standard_links();

	}

	

	// echo '<li><div class="mnu-bugs">'. "Report a bug" .'</a></div></li>' . "\n";

	do_rss_box();

	echo '</ul>';

	do_banner_right_a(); // right side banner

	echo '</div><!--html1:do_sidebar-->' . "\n";

}





function do_sidebar_shake() {

	global $db, $dblang, $globals;

	// localization

	global $msg_yourvoteimportant, $msg_whyvote, $msg_usecategories, $msg_search,

	       $msg_searchpending, $msg_discarded,  $msg_submitnew;



	echo '<div id="sidebar">';

	echo '<ul class="main-menu">';



	echo '<li>' . "\n";

	echo '<div class="note-temp">' . "\n";

	echo '<strong>'. $msg_yourvoteimportant .'</strong><br/><br/>';

	echo $msg_whyvote  . '<br/><br/>';

	echo '<strong>'. $msg_usecategories .'</strong>';

	echo '</div>' . "\n";

	echo '</li>' . "\n";



// 	echo '<li><div class="boxed"><div>';



	// Show the 'submit videos' box.

	echo '<li id="main-submit"><a href="video_submit.php">'. $msg_submitnew .'</a></li>' . "\n";

	



	// Categories box



	do_categories ('shakeit', check_integer('category'));

	echo '<li>'. "\n";

	echo '<div class="shakeit-form">'. "\n";

	echo '<label for="search">'. $msg_searchpending .'</label>'; "\n";

	echo '<form class="shakeit-form" action="">'; "\n";

	echo '<input class="shakeit-form-input" type="text" id="search2" name="search" value="'; "\n";

	if (!empty($_REQUEST['search'])) echo $_REQUEST['search'];

	echo '"/>'; "\n";

	echo '<input class="shakeit-form-submit" type="submit" id="search-button" value="'. $msg_search .'" />'; "\n";

	echo '</form>'. "\n";
@include ('ads/hombrelobo.inc');

	echo '</div>'. "\n";

	echo '</li>'. "\n";



	// echo '<li><div class="mnu-bugs"><a href="http://exocert.com/Articles/mootionbugs">'. "Report a BUG" .'</a></div></li>' . "\n";

	do_rss_box();

	do_banner_right_a(); // right side banner

	

	echo '</ul>'. "\n";

	echo '</div>'. "\n";



}



function do_standard_links () {

	// ugly; localization

	global $msg_lastvoted, $msg_topvoted, $msg_toptags, $msg_blogscloud, $msg_topusers;

	

	//	echo '<li><a href="/lastshaked.php">'. $msg_lastvoted.'</a></li>' . "\n";

		echo '<li><div class="mnu-us"><a href="/cloud.php">'. $msg_toptags .'</a></div></li>' . "\n";

		echo '<li><div class="mnu-us"><a href="/topstories.php">'. $msg_topvoted .'</a></div></li>' . "\n";

		echo '<li><div class="mnu-top"><a href="/blogscloud.php">'. $msg_blogscloud .'</a></div></li>' . "\n";

		echo '<li><div class="mnu-top"><a href="/topusers.php">'. $msg_topusers .'</a></div></li>' . "\n";

}



/********* TODO

function do_tags_box () {

	global $db, $dblang;



	$from_time=time()-86400;

	$res=$db->get_results("select tag_words, count(*) as count FROM tags WHERE tag_lang='$dblang' and tag_date > FROM_UNIXTIME($from_time)  GROUP BY tag_words order by tag_words asc limit 20");

	if ($res) {

		echo '<li><div class="mnu-tags"><aa href="/cloud.php">';

		foreach ($res as $item) {

	        $words = $item->tag_words;

	        $count = $item->count;

	        $size = intval(9+$count);

			echo '<span style="font-size: '.$size.'pt;">'.$words.'</span>&nbsp; ';

			//echo $words.'&nbsp; ';

		}

		echo '</a></div></li>' . "\n";

	}

}

***********/



function do_rss_box() {

	global $globals;

	// ugly; localization;

	global $msg_rsssearch, $msg_rsscategory, $msg_rsspublished, $msg_rssqueued,

	       $msg_rsssuscriptions, $msg_rssall;



	echo '<li>' . "\n"; // It was class="side-boxed"

	echo '<ul class="rss-list">' . "\n";

	echo '<li class="rss-retol">'. $msg_rsssuscriptions .'</li>' . "\n";



	if(!empty($_REQUEST['search'])) {

		$search =  htmlspecialchars($_REQUEST['search']);

		echo '<li>';

		echo '<a href="http://mootion.com/rss2.php?search='.$search.'" rel="rss">'. $msg_rsssearch .': <strong>'. htmlspecialchars($_REQUEST['search'])."</strong></a>\n";

		echo '</li>';



	}



	if(!empty($globals['category_name'])) {

		echo '<li>';

		echo '<a href="http://mootion.com/rss2.php?status=all&amp;category='.$globals['category_id'].'" rel="rss">'. $msg_rsscategory .': <strong>'.$globals['category_name']."</strong></a>\n";

		echo '</li>';



	}



	echo '<li>';

	echo '<a href="http://mootion.com/rss2.php" rel="rss">'. $msg_rsspublished.'</a>';

	echo '</li>' . "\n";

	

	echo '<li>';

	echo '<a href="http://mootion.com/rss2.php?status=queued" rel="rss">'. $msg_rssqueued .'</a>';

	echo '</li>' . "\n";



	echo '<li>';

	echo '<a href="http://mootion.com/rss2.php?status=all" rel="rss">'. $msg_rssall .'</a>';

	echo '</li>' . "\n";





	echo '</ul>' . "\n";

	echo '<br style="clear: both;" />' . "\n";

	echo '</li> <!--html1:do_rss_box()-->' . "\n";



}



function force_authentication() {

	global $current_user;

	// localization ; ugly

	global $err_authmustbelogged, $msg_authlogin;



	if(!$current_user->authenticated) {

		//echo '<div class="instruction"><h2>'. $err_authmustbelogged .'. <a href="login.php">'. $msg_authlogin.'</a>.</h2></div>'."\n";

		header("Location: /login.php?return=".urlencode($_SERVER['REQUEST_URI']));

		die;

	}

	return true;

}



function do_pages($total, $page_size=25) {

	global $db;

	// localization ; ugly

	global $msg_navprev, $msg_navnext, $msg_navgoto;



	$index_limit = 10;



	$query=preg_replace('/page=[0-9]+/', '', $_SERVER['QUERY_STRING']);

	$query=preg_replace('/^&*(.*)&*$/', "$1", $query);

	if(!empty($query)) {

		$query = htmlspecialchars($query);

		$query = "&amp;$query";

	}

	

	$current = get_current_page();

	$total_pages=ceil($total/$page_size);

	$start=max($current-intval($index_limit/2), 1);

	$end=$start+$index_limit-1;

	

	echo '<div class="pages">';



	if($current==1) {

		echo '<span class="nextprev">&#171; '. $msg_navprev. '</span>';

	} else {

		$i = $current-1;

		echo '<a href="?page='.$i.$query.'">&#171; '. $msg_navprev.'</a>';

	}



	if($start>1) {

		$i = 1;

		echo '<a href="?page='.$i.$query.'" title="'. $msg_navgoto ." $i".'">'.$i.'</a>';

		echo '<span>...</span>';

	}

	for ($i=$start;$i<=$end && $i<= $total_pages;$i++) {

		if($i==$current) {

			echo '<span class="current">'.$i.'</span>';

		} else {

			echo '<a href="?page='.$i.$query.'" title="'. $msg_navgoto ." $i".'">'.$i.'</a>';

		}

	}

	if($total_pages>$end) {

		$i = $total_pages;

		echo '<span>...</span>';

		echo '<a href="?page='.$i.$query.'" title="'. $msg_navgoto ." $i".'">'.$i.'</a>';

	}

	if($current<$total_pages) {

		$i = $current+1;

		echo '<a href="?page='.$i.$query.'">&#187; '. $msg_navnext .'</a>';

	} else {

		echo '<span class="nextprev">&#187; '. $msg_navnext . '</span>';

	}

	echo "</div><!--html1:do_pages-->\n";



}



function do_trackbacks() {

	global $db;

	// localization ; ugly

	global $msg_notrackbacks;



	if ( !is_null($_REQUEST['id']) ) {

		$id = $_REQUEST['id'];

	} else {

		$id = $_REQUEST['v'];

	}

	

	echo '<div id="trackback">';

	echo '<h2>trackbacks</h2>';

	$trackbacks = $db->get_col("SELECT trackback_id FROM trackbacks WHERE trackback_link_id=$id AND trackback_type='in' ORDER BY trackback_date DESC");

	if ($trackbacks) {

		echo '<ul>';

		require_once(mnminclude.'trackback.php');

		$trackback = new Trackback;

		foreach($trackbacks as $trackback_id) {

			$trackback->id=$trackback_id;

			$trackback->read();

			echo '<li><a href="'.$trackback->url.'" title="'.htmlspecialchars($trackback->content).'">'.$trackback->title.'</a></li>';

		}

		echo "</ul>\n";

	}

	else {

		echo '<ul>';

		echo '<li>'. $msg_notrackbacks .'</li></ul>';

	}

	echo '<br/></div><!--html1:do_trackbacks-->';

}



function do_categories($what_cat_type, $what_cat_id) {

	

	// $what_cat_type:

	//	index: from index.php

	// 	shakeit: from shakeit.php



	global $db, $dblang, $globals;

	// localization

	global $msg_alltags;



	// Categories Box

	echo '<li>' . "\n"; // It was class="side-boxed"



	// change class id for shakeit page

	if ($what_cat_type == 'shakeit') $categorylist_class = 'column-one-list';

		else $categorylist_class = 'column-list';

	echo '<div class="'.$categorylist_class.'">' . "\n";

	

	echo '<ul>' . "\n";



	// database query

	if ($what_cat_type == 'shakeit') {

		$queued_count = $db->get_var("SELECT count(*) FROM links WHERE link_status = 'queued'");

		$categories = $db->get_results("select category_id, category_name,  count(*) as count from links, categories where category_lang='$dblang' and category_id=link_category AND link_status = 'queued' group by link_category ORDER BY category_name ASC");

	}

	else {

		$categories = $db->get_results("SELECT category_id, category_name FROM categories WHERE category_lang='$dblang' ORDER BY category_name ASC");

	}



	$query=preg_replace('/category=[0-9]*/', '', $_SERVER['QUERY_STRING']);

	// Always return to page 1

	$query=preg_replace('/page=[0-9]*/', '', $query);

	$query=preg_replace('/^&*(.*)&*$/', "$1", $query);

	// IGP: Remove id= or v= arg. (Link to categories, not to singe videos).	

	$query=preg_replace('/id=[0-9]*/', '', $query);

	

	if(!empty($query)) {

		$query = htmlspecialchars($query);

		$query = "&amp;$query";

	}



	// draw first category: all categories

	if (empty($what_cat_id)) $thiscat = ' class="thiscat"';

		else $thiscat = '';

	echo '<li'.$thiscat.'><a href="'.$_SERVER[PHP_SELF].'?'.$query.'">'. $msg_alltags;

	if ($what_cat_type == 'shakeit') echo '&nbsp;('.$queued_count.')';

	echo '</a></li>' . "\n";



	// draw categories

	foreach ($categories as $category) {



		if($category->category_id == $what_cat_id) {

			$globals['category_id'] = $category->category_id;

			$globals['category_name'] = $category->category_name;

			$thiscat = ' class="thiscat"';

		} else {

			$thiscat = '';

		}



		echo '<li'.$thiscat.'><a href="'.$_SERVER[PHP_SELF].'?category='.$category->category_id.$query.'">';

		echo $category->category_name;

		if ($what_cat_type == 'shakeit') echo '&nbsp;('.$category->count.')';

		echo "</a></li>\n";



	}



	echo '</ul>';

	echo '<br style="clear: both;" />' . "\n";

	echo '</div></li><!--html1:do_categories-->' . "\n";



}



// Simple Form Security

// ToDo: A better way to take action against errors.



// encode_formmd5 ( $mess )

// Creates a validation code for 1hour based on the md5'd parameters

// and time limit.

function encode_formmd5 ( $mess ) {

	

   global $site_key;	

	

   $valid = time() + 3600;

   $code = md5 ( $valid . $site_key . $mess );

   return $valid . '::::' . $code;

}



// validate_formmd5 ( $mess, $chk )

// Check id given form validation code ($mess) is right or not...

function validate_formd5 ( $mess, $chk ) {

	

   global $site_key;

   

   echo "\n<!-- validate_formd5():<br/>mess = $mess \n     chk = $chk -->\n\n";

   

   $retval = false;

   $pars = explode ('::::', $mess);

   

   $now = time();

   $valid_until = $pars[0];

   if ( $now < $valid_until ) {

	$code = md5 ( $valid_until . $site_key . $chk );

	if ($code == $pars[1]) {

		$retval = true;

	}

   }

   return $retval;	

}





?>

