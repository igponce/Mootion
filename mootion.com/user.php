<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
//              http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp mootion com> for mOOtion

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'user.php');
include(mnminclude.'localization_en.php');

$offset=(get_current_page()-1)*$page_size;
$login = $_REQUEST['login'];
if(empty($login)){
	if ($current_user->user_id > 0) {
		$login=$current_user->user_login;
	} else {
		header('Location: ./');
		die;
	}
}
$user=new User();
$user->username = $login;
if(!$user->read()) {
	echo "error 2";
	die;
}

$view = $_REQUEST['view'];
if(empty($view)) $view = 'profile';

do_header($hdr_userprofile .': ' . $login);
do_navbar('<a href="/topusers.php">'. $top_users . '</a> &#187; ' . $user->username);
echo '<div id="genericform-contents">'."\n";

// Tabbed navigation
if (strlen($user->names) > 0) {
	$display_name = $user->names;
}
else {
	$display_name = $user->username;
}
echo '<h2>'.$display_name.'</h2>'."\n";
echo '<div class="sub-nav">'."\n";
echo '<ul>'."\n";

switch ($view) {

	case 'history':
		echo '<li><a href="user.php?login='.$login.'&amp;view=profile">'. $personal_data .'</a></li>';
		echo '<li class="active"><span>'. $sent_videos .'</span></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=published">'. $published .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=commented">'. $comments .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=shaken">'. $voted .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=preferred">'. $prefered_editors .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=voters">'. $voted_by .'</a></li>';
		echo '</ul><br /></div>';
		do_history();
		break;
	case 'published':
		echo '<li><a href="user.php?login='.$login.'&amp;view=profile">'. $personal_data .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=history">'. $sent_videos .'</a></li>';
		echo '<li class="active"><span>'. $published .'</span></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=commented">'. $comments .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=shaken">'. $voted .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=preferred">'. $prefered_editors .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=voters">'. $voted_by .'</a></li>';
		echo '</ul><br /></div>';
		do_published();
		break;
	case 'commented':
		echo '<li><a href="user.php?login='.$login.'&amp;view=profile">'. $personal_data .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=history">'. $sent_videos .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=published">'. $published .'</a></li>';
		echo '<li class="active"><span>'. $comments .'</span></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=shaken">'. $voted .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=preferred">'. $prefered_editors .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=voters">'. $voted_by .'</a></li>';
		echo '</ul><br /></div>';
		do_commented();
		break;
	case 'shaken':
		echo '<li><a href="user.php?login='.$login.'&amp;view=profile">'. $personal_data .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=history">'. $sent_videos .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=published">'. $published .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=commented">'. $comments .'</a></li>';
		echo '<li class="active"><span>'. $voted .'</span></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=preferred">'. $prefered_editors . '</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=voters">'. $voted_by .'</a></li>';
		echo '</ul><br /></div>';
		do_shaken();
		break;
		
	case 'preferred':
		echo '<li><a href="user.php?login='.$login.'&amp;view=profile">'. $personal_data. '</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=history">'. $sent_videos .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=published">'. $published .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=commented">'. $comments .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=shaken">'. $voted .'</a></li>';
		echo '<li class="active"><span>'. $prefered_editors .'</span></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=voters">'. $voted_by .'</a></li>';
		echo '</ul><br /></div>';
		do_preferred();
		break;
		
	case 'voters':
		echo '<li><a href="user.php?login='.$login.'&amp;view=profile">'. $personal_data .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=history">'. $sent_videos .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=published">'. $published .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=commented">'. $comments .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=shaken">'. $voted .'</a></li>';
		echo '<li class="active"><span>'. $voted_by .'</span></li>';
		echo '</ul><br /></div>';
		do_voters();
		break;
		
	case 'profile':
	default:
		echo '<li><a href="user.php?login='.$login.'&amp;view=profile">'. $personal_data .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=history">'. $sent_videos .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=published">'. $published .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=commented">'. $comments .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=shaken">'. $voted .'</a></li>';
		echo '<li><a href="user.php?login='.$login.'&amp;view=voters">'. $voted_by .'</a></li>';
		echo '</ul><br /></div>';
		do_profile();
		break;
}


do_pages($rows, $page_size);
echo '</div>'."\n";

do_footer();

//echo '<div id="contents">';
//echo '</div>';



function do_profile() {
	global $user, $current_user, $login, $personal_info,
	       $msg_user, $msg_name, $msg_website, $msg_karma,
	       $msg_sentvideos, $msg_published, $msg_allcomments,
	       $msg_votes, $msg_from, $msg_timesvoted, $msg_votingstats;

// 	echo '<div id="contents-wide">';
	echo '<fieldset><legend>';
	echo $personal_info;
	
	if($login===$current_user->user_login) {
		echo ' (<a href="profile.php">'. 'modify' .'</a>)';
	}
	echo '</legend>';

	echo '<dl>';	
	if(!empty($user->username))
		echo '<dt>'. $msg_user.':</dt><dd>'.$user->username.'</dd>';
	if(!empty($user->names))
		echo '<dt>'. $msg_name .':</dt><dd>'.$user->names.'</dd>';
	if(!empty($user->url)) {
	        if ( preg_match ('#^http://(.+)#',$user->url,$match) )
		   $user->url = $match[1];
		echo '<dt>'. $msg_website.':</dt><dd><a href="http://'.$user->url.'" target="_blank">'.$user->url.'</a></dd>';
	}
	echo '<dt>'. $msg_from .':</dt><dd>'.get_date($user->date).'</dd>';
	if(!empty($user->karma))
		echo '<dt>'. $msg_karma.':</dt><dd>'.$user->karma.'</dd>';
	echo '</dl></fieldset>';

	$user->all_stats();
	echo '<fieldset><legend>'. $msg_votingstats.'</legend><dl>';

        echo '<dt>'. $msg_sentvideos .':</dt><dd>'.$user->total_links.'</dd>';
        echo '<dt>'. $msg_published .':</dt><dd>'.$user->published_links.'</dd>';
        echo '<dt>'. $msg_allcomments .':</dt><dd>'.$user->total_comments.'</dd>';
        echo '<dt>'. $msg_votes .':</dt><dd>'.$user->total_votes.'</dd>';
        echo '<dt>'. $msg_timesvoted .':</dt><dd>'.$user->published_votes.'</dd>';

	echo '</dl></fieldset>';
// 	echo '</div>';
}


function do_history () {
	global $db, $rows, $user, $offset, $page_size;
	global $msg_sentvideos;

	$link = new Link;
// 	echo '<div id="contents-wide">';
	echo '<h2>'. $msg_sentvideos .'</h2>';
	//$rows = $db->get_var("SELECT count(*) FROM links WHERE link_author=$user->id AND link_status!='discard'");
	//$links = $db->get_col("SELECT link_id FROM links WHERE link_author=$user->id AND link_status!='discard' ORDER BY link_date DESC LIMIT $offset,$page_size");
	$rows = $db->get_var("SELECT count(*) FROM links WHERE link_author=$user->id AND link_votes > 0");
	$links = $db->get_col("SELECT link_id FROM links WHERE link_author=$user->id AND link_votes > 0 ORDER BY link_date DESC LIMIT $offset,$page_size");
	if ($links) {
		foreach($links as $link_id) {
			$link->id=$link_id;
			$link->read();
			$link->print_summary('short');
		}
	}
// 	echo '</div>';
}

function do_published () {
	global $db, $rows, $user, $offset, $page_size;
	global $msg_published;

	$link = new Link;
// 	echo '<div id="contents-wide">';
	echo '<h2>'. $msg_published .'</h2>';
	$rows = $db->get_var("SELECT count(*) FROM links WHERE link_author=$user->id AND link_status='published'");
	$links = $db->get_col("SELECT link_id FROM links WHERE link_author=$user->id AND link_status='published'  ORDER BY link_published_date DESC LIMIT $offset,$page_size");
	if ($links) {
		foreach($links as $link_id) {
			$link->id=$link_id;
			$link->read();
			$link->print_summary('short');
		}
	}
// 	echo '</div>';
}

function do_shaken () {
	global $db, $rows, $user, $offset, $page_size;
	global $msg_allvotes;

	$link = new Link;
// 	echo '<div id="contents-wide">';
	echo '<h2>'. $msg_allvotes .'</h2>';
	$rows = $db->get_var("SELECT count(*) FROM links, votes WHERE vote_type='links' and vote_user_id=$user->id AND vote_link_id=link_id and vote_value > 0");
	$links = $db->get_col("SELECT link_id FROM links, votes WHERE vote_type='links' and vote_user_id=$user->id AND vote_link_id=link_id  and vote_value > 0 ORDER BY link_date DESC LIMIT $offset,$page_size");
	if ($links) {
		foreach($links as $link_id) {
			$link->id=$link_id;
			$link->read();
			$link->print_summary('short');
		}
	}
// 	echo '</div>';
}


function do_commented () {
	global $db, $rows, $user, $offset, $page_size;
	global $msg_votes;

	$link = new Link;
	echo '<h2>'. $msg_votes .'</h2>';
	$rows = $db->get_var("SELECT count(*) FROM links, comments WHERE comment_user_id=$user->id AND comment_link_id=link_id");
	$links = $db->get_col("SELECT DISTINCT link_id FROM links, comments WHERE comment_user_id=$user->id AND comment_link_id=link_id  ORDER BY link_date DESC LIMIT $offset,$page_size");
	if ($links) {
		foreach($links as $link_id) {
			$link->id=$link_id;
			$link->read();
			$link->print_summary('short');
		}
	}
}

function do_preferred () {
	global $db, $user;
	global $prefered_editors;

	$friend = new User;
	echo '<h2>'. $prefered_editors .'</h2>';
	echo '<div class="friends-list">';
	echo "<ol>\n";
	$dbusers = $db->get_results("SELECT friend_to, friend_value FROM friends WHERE friend_type='affiliate' AND friend_from=$user->id AND friend_to !=0 ORDER BY friend_value DESC LIMIT 50");
	if ($dbusers) {
		foreach($dbusers as $dbuser) {
			$friend->id=$dbuser->friend_to;
			$value = $dbuser->friend_value * 100;
			$value = sprintf("%6.2f", $value);
			$friend->read();
			echo '<li><a href="user.php?login='.$friend->username.'">'.$friend->username."</a> ($value %)</li>\n";
		}
	}
	echo '</ol>';
	echo "</div>\n";
}


function do_voters () {
	global $db, $user;
	global $msg_votes;

	$friend = new User;
	echo '<h2>'. $msg_votes .'</h2>';
	echo '<div class="friends-list">';
	echo "<ol>\n";
	$dbusers = $db->get_results("SELECT friend_from, friend_value FROM friends WHERE friend_type='affiliate' AND friend_to=$user->id AND friend_from !=0 ORDER BY friend_value DESC LIMIT 50");
	if ($dbusers) {
		foreach($dbusers as $dbuser) {
			$friend->id=$dbuser->friend_from;
			$value = $dbuser->friend_value * 100;
			$value = sprintf("%6.2f", $value);
			$friend->read();
			echo '<li><a href="user.php?login='.$friend->username.'">'.$friend->username."</a> ($value %)</li>\n";
		}
	}
	echo '</ol>';
	echo "</div>\n";
}

?>
