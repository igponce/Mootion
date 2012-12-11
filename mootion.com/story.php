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


$globals['ads'] = true;

if ( is_null ($_REQUEST['v'])) {
   $vid = $_REQUEST['id'];
} else {
   $vid = $_REQUEST['v'];
}

//if(is_numeric($_REQUEST['id'])) {
if (is_numeric ($vid) ) {
	$link = new Link;
	$link->id=$vid; // $_REQUEST['id'];
	$link->read();

	if ( is_null ($_REQUEST['redirected_to_permalink']) ) {
		header ("Location: " . $link->get_permalink() );
		exit;
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

	do_header($link->title, 'post');
	do_navbar('<a href="./index.php?category='.$link->category.'">'. $globals['category_name'] . '</a> &#187; '. $link->title);
	echo '<div id="contents">';
	$link->print_summary('story');
	
	echo '<div id="comments">';
   	
	echo '<div id="promo">';
	@include "ads/adsense-bajovideo.inc";
	echo '</div>';
	
	echo '<h2>'. 'comments' .'</h2>';

	$comments = $db->get_col("SELECT comment_id FROM comments WHERE comment_link_id=$link->id ORDER BY comment_date");
	if ($comments) {
		echo '<ol id="comments-list">';
		require_once(mnminclude.'comment.php');
		$comment = new Comment;
		foreach($comments as $comment_id) {
			$comment->id=$comment_id;
			$comment->read();
			$comment->print_summary($link);
		}
		echo "</ol>\n";
	}

	$comments_allowed = false; /* by default */
				   
	if ($link->date < time()-34600) {
		$comments_allowed = true;
	}
	
	if ($link->published_date < time()-34600) {
		$comments_allowed = true;
	}
	
				   
	if( ! $comments_allowed ) {
		// older than 4 days. Second Chance to comment: 4 days after beign published.
		echo '<br/>'."\n";
		echo '<div class="air-with-footer">'."\n";
		echo '<div class="commentform" align="center" >'."\n";
		echo 'comments closed' ."\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
	} elseif ($current_user->authenticated) {
		print_comment_form();
	} else {
		echo '<br/>'."\n";
		echo '<div class="air-with-footer">'."\n";
		echo '<div class="commentform" align="center" >'."\n";
		echo '<a href="/login.php?return='.$_SERVER['REQUEST_URI'].'">'. 'Login first to place your comment' ."</a>\n";
		echo '</div>'."\n";
		echo '</div>'."\n";
	}

	echo '</div>' . "\n";
		
	echo '</div>';
	do_sidebar();
	do_footer();
} else {
	// Non-numeric ID.
	header ('Location: /');
}

function print_comment_form() {
	global $link, $current_user;

	echo '<div class="air-with-footer">'."\n";
	echo '<div id="commentform" align="left">'."\n";
	echo '<form action="" method="POST" id="thisform" style="display:inline;">'."\n";
	echo '<fieldset><legend><span class="sign">'. 'write your comment' .'</span></legend>'."\n";
	//echo '<p>'."\n";
	echo "Be polite, don't insult. If you find this video or any comment out of place, please say it briefly, in an informative manner. Good sense of humor is always appreciated :-).";
	echo '<label for="comment" accesskey="2" style="float:left">'. 'comment text (sorry, no html tags allowed)' ."</label>\n";
	//echo '</p>';
	echo '<p class="l-top-s"><br/>'."\n";
	echo $foo;
	echo '<textarea name="comment_content" id="comment" rows="3" cols="76"/></textarea><br/>'."\n";
	echo '<input class="submitcomment" type="submit" name="submit" value="'. 'submit' .'" />'."\n";
	echo '<input type="hidden" name="process" value="newcomment" />'."\n";
	echo '<input type="hidden" name="randkey" value="'.rand(1000000,100000000).'" />'."\n";
	echo '<input type="hidden" name="link_id" value="'.$link->id.'" />'."\n";
	echo '<input type="hidden" name="user_id" value="'.$current_user->user_id.'" />'."\n";
	echo '</p>'."\n";
	echo '</fieldset>'."\n";
	echo '</form>'."\n";
	echo "</div>\n";
	echo "</div><!--air-with-footer-->\n";
}

function insert_comment () {
	global $link, $db, $current_user;
	// Check if is a POST of a comment
	if($_POST['link_id'] == $link->id && $current_user->authenticated && $_POST['user_id'] == $current_user->user_id &&
		$_POST['randkey'] > 0 && strlen($_POST['comment_content']) > 0 ) {
		require_once(mnminclude.'comment.php');
		$comment = new Comment;
		$comment->link=$link->id;
		$comment->randkey=$_POST['randkey'];
		$comment->author=$_POST['user_id'];
		$comment->content=$_POST['comment_content'];
		$comment->store();
		header('Location: '.$_SERVER['REQUEST_URI']);
		die;
	}
}

?>
