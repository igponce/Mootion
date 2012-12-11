<?
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// Inigo Gonzalez <igponce at corp mootion com> for mOOtion
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Based on meneame by Ricardo Galli

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'localization_en.php');

$globals['ads'] = true;
$http_referer = getenv('HTTP_REFERER');
$goBackURL = 'http://mootion.com/'; // Defaults to mOOtion.

if(is_numeric($_REQUEST['id'])) {
	
	$link = new Link;
	$link->id=$_REQUEST['id'];
	$link->read();
	
	if ( $link->embedhtml != '' ) {
		header ('Location: ./story.php?v='.$link->id);
		die;
	}

	
	if ($_POST['process']=='newcomment') {
		insert_comment();
	}
	// Set globals
	$globals['link_id']=$link->id;
	$globals['category_id']=$link->category;
	$globals['category_name']=$link->category_name();
	if ($link->status != 'published') 
		$globals['do_vote_queue']=true;
	if (!empty($link->tags))
		$globals['tags']=$link->tags;

	do_header_mini ($link->title, 'story_mini');

	echo ' <table width=100% height=100% cellpadding=5 cellspacing=0 border=0> ';
	echo ' <tr>';
	echo '    <td valign=center>';
	$link->print_summary('story_mini');	
	echo '    </td>';
	echo '    <td nowrap width=1% valign=top align=right rowspan=2 border=0>';
	echo '        <a href="' . $link->url .'" target=_top><font color=#0000cc>'. $msg_removeframe .'</font>&nbsp;<img src="img/common/button-x.gif" height=12 width=13 border=0></a><br/>';
	echo '        <img alt="" width=1 height=5 border=0><br/>';

	if ( preg_match ('/mootion.com/i', $http_referer) ) {
		$goBackURL = 'javascript:history.go(-1)"';
	} else {
		$goBackURL = 'http://mootion.com'; 
	}

	echo '<a href="'. $goBackURL .' target=_top>&laquo;&nbsp;'. $msg_gohome .'</a>';

	echo '</td>';
	echo '</tr></table>';
	// Include tracking (analytics / statcounter code here)
	@include('ads/googleanalytics.inc');
	echo '</body></html>';

}

?>
