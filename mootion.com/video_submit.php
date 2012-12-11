<?

// The source code packaged with this file is Free Software, Copyright (C) 2005 by

// Ricardo Galli <gallir at uib dot es>.

//

// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.

// You can get copies of the licenses here:

// 		http://www.affero.org/oagpl.html

// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".



// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp mootion com> for mOOtion



include('config.php');

include(mnminclude.'html1.php');

include(mnminclude.'ts.php');

include(mnminclude.'link.php');

include(mnminclude.'tags.php');

include(mnminclude.'videofarm.php');

include(mnminclude.'localization_en.php');



force_authentication();



//this is for direct links from weblogs

/***

if(empty($_POST['phase']) && !empty($_GET['url'])) {

	$_POST['phase'] = 1;

	$_POST['url'] = $_GET['url'];

	$_POST['randkey'] = rand(10000,10000000);

	if(!empty($_GET['trackback'])) 

		$_POST['trackback'] = $_GET['trackback'];

}

**/



do_header($title_submitvideos, "post");



if(isset($_POST["phase"])) {

	switch ($_POST["phase"]) {

		case 1:

			do_submit1();

			break;

		case 2:

			do_submit2();

			break;

		case 3:

			do_submit3();

			break;

	}

} else {

	do_submit0();

//	do_sidebar();

}

do_footer();

exit;



function print_empty_submit_form() {

	// Localization

	global $msg_videoaddress, $btn_continue;



	

	if (!empty($_GET['url'])) {
		$url = $_GET['url'];
	} else {
		$url = '';
	}

	echo '<div id="genericform">';

	echo '<fieldset><legend><span class="sign">'. $msg_videoaddress .'</span></legend>';

	echo '<form action="/video_submit.php" method="post" id="thisform">';

	echo '<p class="l-top"><label for="url">'. 'url' .':</label><br />';

	echo '<b><input type="text" name="url" id="url" value="'.$url.'" class="form-full" /></p>';

	echo '<input type="hidden" name="phase" value=1>';

	echo '<input type="hidden" name="randkey" value="'.rand(10000,10000000).'">';

	echo '<input type="hidden" name="id" value="c_1">';

	echo '<p class="l-bottom"><input class="genericsubmit" type="submit" value="'. $btn_continue .'" /></p>';

	echo '</form>';

	echo '</fieldset>';

	echo '</div>';

}



function do_submit0() {

	// Localization

	global $title_submitvideos, $msg_step1, $title_submitvideo1, $msg_instructionlist;

	

	do_navbar( $title_submitvideos . ' &#187; '. $msg_step1 );

	echo '<div id="genericform-contents">'."\n";

	echo '<h2>'. $title_submitvideo1 .'</h2>';

	echo '<div class="instruction">';

	echo '<ul class="instruction-list">';

	echo $msg_instructionlist;

	echo '</ul></div>'."\n";

	print_empty_submit_form();

	echo '</div>';

}



function do_submit1() {

	global $db, $dblang, $current_user;

	// Localization

	global $err_needsmorevotes, $err_neededtosubmit, $msg_videos, $msg_govoting,

           $err_invalidurl, $msg_tryanother, $err_recentsubmitted, $msg_youmustwait,

           $msg_whywait, $msg_RTFM, $err_duplicated, $msg_weresorry, $msg_voteduplicated,

           $btn_goback, $msg_videoinfo, $msg_pagetitle, $msg_itsablog, $msg_details,

           $msg_videotitle, $msg_interttitle, $msg_tags, $msg_taghlp, $msg_description,

           $msg_descriptionhlp, $msg_categories, $msg_categorieshlp, $msg_trackback,

           $msg_trackbackhlp, $msg_step2, $msg_submitvideo2, $msg_filesize;



	// check that the user also votes, not only sends links

	// We (mOOtion) check user votes before reading data form the 'net (saves bandwidth)

	// Original meneame.net source did it after getting the URL !

	

	$from = time() - 3600*24;

	$user_votes = $db->get_var("select count(*) from votes where vote_type='links' and vote_date > from_unixtime($from) and vote_user_id=$current_user->user_id");

	$user_links = 1 + $db->get_var("select count(*) from links where link_author=$current_user->user_id and link_date > from_unixtime($from) and link_status != 'discard'");

	$total_links = $db->get_var("select count(*) from links where  link_date > from_unixtime($from) and link_status = 'queued'");

	$min_votes = intval($total_links/15) * $user_links;

	

	//echo "$total_links, $user_links, $user_votes, $min_votes\n";

	if ($user_votes < $min_votes) {

		$needed = $min_votes - $user_votes;

		echo '<p class="error"><strong>'. $err_needsmorevotes .'</strong></p>';

		echo '<p class="error-text">'. $err_neededtosubmit . $needed .  $msg_videos .', ';

		echo '<a href="shakeit.php" target="_blank">'. $msg_govoting .'</a></p>';

		echo '<br style="clear: both;" />' . "\n";

		echo '</div>'. "\n";

		return;

	}



	// Okay, user has enough votes to send videos.



	$url = trim($_POST['url']);

	$linkres=new Link;

	$edit = false;



	$linkres->url = $url;	

	$mimetype = $linkres->mimetype ($url);

	$linkres->randkey = $_POST['randkey'];



	do_navbar($title_submitvideos . '&#187;'.  $msg_step2);

	echo '<div id="genericform-contents">'."\n";



	if(!$linkres->valid) {

		echo '<p class="error"><strong>'. $err_invalidurl .':</strong> ('.$url.')</p>';

		echo '<code>' ;

		echo $linkres->html ;

		echo '</code>';

		echo '<p>'. $msg_tryanother .'</p>';



		echo '<code>'.$linkres->html.'</code>';

		print_empty_submit_form();

		return;

	}

	

	// Video Specific

	// URL from a videofarm ??

	

	if ( $linkres->type != 'video' ) {

	   if ( ! process_videofarm ( $linkres ) ) {

			// Not a VideoFarm... should be a Blog.

			// Warning !!! Misconfigured web serwers will make us choke here !!!

			if ( ! getvideofromblog ( $linkres ) ) {			// $linkres->get ($url);

			   ?>

			   <p class="error"><strong>No video found</strong><p>

			   <p class="error-text>The URL submitted has no video.</p>

			   <p class="error-text>Put a video on your blog, and then come back here for all us to see.</p>

			   <?php

			   

			   echo '<input class="genericsubmit" type=button onclick="window.history.go(-1)" value="'. $btn_goback .'">';

			   echo '</div></body></html>';			   

			   exit;

			   

			} else {



				$trackback=$linkres->trackback; // should be 'via URL';

				$linkres->get;

			     

			     // avoid auto-promotion (autobombo) -- only for non-videofarms.

			     

			     $hours = 2;

			     $from = time() - 3600*$hours;

			     $sqlcmd = "select count(*) from links where link_date > from_unixtime($from) and link_author=$current_user->user_id and link_blog='".$db->escape($linkres->blog)."' and link_status != 'discard' and link_votes > 0";

			     $same_blog = $db->get_var($sqlcmd);

			     // DEBUG // echo "<br/><tt>Escapando la BBDD:\n$sqlcmd\n</tt>";

			     // DEBUG// return;

			     

			     if ($same_blog > 0) {

				echo '<p class="error"><strong>'. $err_recentsubmitted .'</strong></p>';

				echo '<p class="error-text">'. $msg_youmustwait .  $hours .  $msg_whywait . "</p>\n";

				echo '<a href="faq-' . $dblang . '.php">' . $msg_RTFM .'</a></p>';

				echo '<br style="clear: both;" />' . "\n";

				echo '</div>'. "\n";

				return;

				}

			}

		}	

	}

	

	if($linkres->duplicates($url) > 0) {

		echo '<p class="error"><strong>'. $err_duplicated .'</strong></p> ';

		echo '<p class="error-text">'. $msg_weresorry .'</p>';

		echo '<p class="error-text"><a href="index.php?search='.htmlentities($url).'">'. $msg_voteduplicated .'</a>';

		echo '<br style="clear: both;" /><br style="clear: both;" />' . "\n";

		echo '<form id="genericform">';

		echo '<input class="genericsubmit" type=button onclick="window.history.go(-1)" value="'. $btn_goback .'">';

		echo '</form>'. "\n";

		echo '</div>'. "\n";

		return;

	}

	

	$linkres->status='discard';

	$linkres->author=$current_user->user_id;

	$linkres->store();

	

	echo '<h2>'. $msg_submitvideo2 .'</h2>'."\n";

	echo '<div id="genericform">'."\n";

	echo '<form action="/video_submit.php" method="post" id="thisform">'."\n";

	echo '<input type="hidden" name="url" id="url" value="'.$url.'" />'."\n";

	echo '<input type="hidden" name="phase" value="2" />'."\n";

	echo '<input type="hidden" name="randkey" value="'.$_POST['randkey'].'" />'."\n";

	echo '<input type="hidden" name="id" value="'.$linkres->id.'" />'."\n";

	echo '<input type="hidden" name="formvalidation" value="' . encode_formmd5($url . $_POST['randkey'] . $linkres->id ) . '">'."\n";

	

	// incorpora el 'embedhtml' necesario para incrustar el video en una pagina

	// echo '<input type="hidden" name="embedhtml" value="'.htmlentities($linkres->embedhtml,ENT_COMPAT).'" />'."\n";

	

	// Mostramos el video en el formulario.

	echo html_entity_decode ($linkres->embedhtml,ENT_COMPAT)."\n";

	

	echo '<fieldset><legend><span class="sign">'. $msg_videoinfo .'</span></legend>'."\n";

	

	if ($linkres->type() == 'video' && $linkres->encoding != 'web') {

		echo '<p class="genericformtxt"><label for="url_title" accesskey="1">';

		echo get_video_filetype ($linkres) . "<br/>\n";

		echo $msg_filesize .': '. $linkres->size . " bytes <br/>\n";

	} else {

		echo '<p class="genericformtxt"><label for="url_title" accesskey="1">'. $msg_pagetitle .': </label> '."\n";

		$link_title = $linkres->url_title;

		echo $link_title;

	}

	

	if($linkres->type() === 'blog') {

		echo '<br /> ('. $msg_itsablog .'</p>'."\n";

	} else {

		echo "</p>\n";

	}

	echo '</fieldset>'."\n";

	

	echo '<fieldset><legend><span class="sign">'. $msg_details .'</span></legend>'."\n";

	

	echo '<label for="title" accesskey="2">'. $msg_videotitle .':</label>'."\n";

	echo '<p><span class="genericformnote">'. $msg_interttitle .'</span>'."\n";

	

	echo '<br/><input type="text" id="title" name="title" value="'.$link_title.'" size="60" maxlength="120" /></p>'."\n";

	

	echo '<label for="tags" accesskey="4">'. $msg_tags .':</label>'."\n";

	echo '<p><span class="genericformnote"><strong>'. $msg_taghlp ."\n";

	echo '<br/><input type="text" id="tags" name="tags" value="'. $link_tags .'" size="40" maxlength="60" /></p>'."\n";

	

	echo '<p><label for="bodytext" accesskey="3">'. $msg_description .':</label>'."\n";

	echo '<br /><span class="genericformnote">'. $msg_descriptionhlp.'</span>'."\n";

	echo '<br/><textarea name="bodytext"  rows="10" cols="60" id="bodytext" >'.$link_content.'</textarea></p>'."\n";

	echo '<p><label accesskey="5">'. $msg_categories .':</label><br />'."\n";

	echo '<span class="genericformnote">'. $msg_categorieshlp.'</span></p>'."\n";

	echo '<div class="column-list">'."\n";

	echo '<div class="categorylist">'."\n";

	echo '<ul>'."\n";

	$categories = $db->get_results("SELECT category_id, category_name FROM categories WHERE category_lang='$dblang' ORDER BY category_name ASC");

	foreach ($categories as $category) {

		echo '<li><input name="category" type="radio" value="'.$category->category_id.'"/>'. $category->category_name .'</li>'."\n";

	}

	// TODO: no standard

	echo '<br style="clear: both;" />' . "\n";

	echo '</ul></div></div>'."\n";

	echo '<p><label for="trackback">'. $msg_trackback .':</label><br />'."\n";

	echo '<span class="genericformnote">'. $msg_trackbackhlp .'</span>'."\n";

	echo '<input type="text" name="trackback" id="trackback" value="'.$trackback.'" class="form-full" /></p>'."\n";

	echo '<input class="genericsubmit" type="button" onclick="window.history.go(-1)" value="'. $btn_goback .'">&nbsp;&nbsp;'."\n";

	echo '<input class="genericsubmit" type="submit" value="'. 'next &#187;' .'" />'."\n";

	echo '</fieldset>'."\n";

	echo '</form>'."\n";

	echo '</div>'."\n";

	echo '</div>'."\n";

}



function do_submit2() {

	global $db, $dblang;

	global $title_submitvideos, $btn_goback, $btn_submit, $msg_step3, $video_details, 

	       $warn_thisisademo, $msg_nowyoucan, $msg_sendqueue, $msg_dogfoodhlp,

	       $msg_submitvideo3;



	// Before we read anything: check the form parameters are okay.

	// ToDo: Better Error handling

	if ( ! validate_formd5 ( $_POST['formvalidation'], $_POST['url'] . $_POST['randkey'] . $_POST['id'] ) ) {

		echo '<h1>Validation Error</h1>';

		echo '<hr><p>Some form parameters were modified. This request is beign logged.</p>';

		echo '<p><a href="http://mootion.com">Back to mOOtion</a></p>' . "\n";

		echo '<!-- do_submit2(). Code: ' . $_POST['formvalidation'] . ' -->';

		return;

	}

    

	$linkres=new Link;

	$linkres->id=$link_id = $_POST['id'];

	$linkres->read();

	$linkres->category=$_POST['category'];

	$linkres->title = strip_tags(trim($_POST['title']));

	$linkres->tags = tags_normalize_string(strip_tags(trim($_POST['tags'])));

	$linkres->content = strip_tags(trim($_POST['bodytext']));

	// $linkres->embedhtml= $_POST['embedhtml'];



	if (link_errors($linkres)) {

		echo '<form id="genericform">'."\n";

		echo '<p><input class="genericsubmit" type=button onclick="window.history.go(-1)" value="'. $btn_goback .'"></p>'."\n";

		echo '</form>'."\n";

		echo '</div>'."\n"; // opened in print_form_submit_error

		return;

	}

	

	$linkres->store();

	tags_insert_string($linkres->id, $dblang, $linkres->tags);

	$linkres->read();

	$edit = true;

	$link_title = $linkres->title;

	$link_content = $linkres->content;

	do_navbar($title_submitvideos .'&#187;'. $msg_step3);

	echo '<div id="genericform-contents">'."\n";

	

	echo '<h2>'. $msg_submitvideo3 .'</h2>'."\n";



	echo '<form action="./video_submit.php" method="post" id="genericform">'."\n";

	echo '<fieldset><legend><span class="sign">'. $video_details .'</span></legend>'."\n";

	echo '<div class="genericformtxt"><label>'. $warn_thisisademo .'</label>&nbsp;&nbsp;<br/>'. $msg_nowyoucan .'<label>'. $btn_goback .'</label>'.' or 2)  '.'<label>'. $msg_sendqueue .'</label>'. $msg_dogfoodhlp.'</div>';

	echo '<div class="formnotice">'."\n";

	$linkres->print_summary('preview');

	echo '</div>'."\n";



	echo '<input type="hidden" name="phase" value="3" />'."\n";

	echo '<input type="hidden" name="randkey" value="'.$_POST['randkey'].'" />'."\n";

	echo '<input type="hidden" name="id" value="'.$linkres->id.'" />'."\n";

	echo '<input type="hidden" name="trackback" value="'.trim($_POST['trackback']).'" />'."\n";

	

	// Security Check: formvalidation

	

	echo '<input type="hidden" name="formvalidation" value="' . encode_formmd5($url . $_POST['randkey'] . $linkres->id . $_POST['trackback']) . '">'."\n";

	echo '<br style="clear: both;" /><br style="clear: both;" />'."\n";

	echo '<input class="genericsubmit" type="button" onclick="window.history.go(-1)" value="'. $btn_goback .'">&nbsp;&nbsp;'."\n";

	echo '<input class="genericsubmit" type="submit" value="'. $btn_submit .'" />'."\n";

	echo '</form>'."\n";

	echo '</fieldset>'."\n";

	echo '</div>'."\n";



}



function do_submit3() {

	global $db, $current_user;



	// ToDo: Better Error handling

	if ( ! validate_formd5 ( $_POST['formvalidation'], $_POST['url'] . $_POST['randkey'] . $_POST['id'] . $_POST['trackback'] ) ) {

		echo '<h1>Validation Error</h1>';

		echo '<hr><p>Some form parameters were modified. This request is beign logged.</p>';

		echo '<p><a href="http://mootion.com">Back to mOOtion</a></p>' . "\n";

		echo '<!-- do_submit3(). Code: ' . $_POST['formvalidation'] . ' -->';

		return;

	}



	$linkres=new Link;



	$linkres->id=$link_id = $_POST['id'];

	$linkres->read();



	/*** TODO

	if (link_errors($linkres)) {

		echo '<form id="thisform">';

		echo '<input type=button onclick="window.history.go(-2)" value="'. 'modify' .'">';

		return;

	}

	********************/



	$linkres->status='queued';

	$linkres->date=time();

	$linkres->store_basic();

	$linkres->insert_vote($current_user->user_id);

	$db->query("delete from links where link_author = $linkres->author and link_status='discard' and link_votes=0");

	

	// quedaría bien un efecto de temporizador.



	if(!empty($_POST['trackback'])) {

		

		// DEBUG:

		echo '<br/><em>Got Trackback:</em> <tt>' . $_POST['trackback'] .'</tt><br/>';

		require_once(mnminclude.'trackback.php');

		$trackres = new Trackback;

		$trackres->url=trim($_POST['trackback']);

		$trackres->link=$linkres->id;

		$trackres->title=$linkres->title;

		$trackres->author=$linkres->author;

		$trackres->content=$linkres->content;

		

		echo '<br/>CONTENT: ' . $trackres->content . "\n";

		$res = $trackres->send();

		sleep (20);

		

	}

	

	



	header("Location: ./shakeit.php");

	die;

	

	/* Reducido una pantalla

	echo '<fieldset><legend>'. 'in queue' .'</legend>';

	$linkres->print_summary();

	echo '<br style="clear: both;" /><br style="clear: both;" />' . "\n";

	echo '<form action="/shakeit.php" method="get" id="thisform">';

	echo '<input type="submit" value="'. 'Let's mOOve videos' .'" />';

	echo '</form>';

	echo '</fieldset>';

	*/

}



function link_errors($linkres) {

    global $err_incorrectkey, $err_alreadyqueued, $err_incompletetitltxt, $err_toolongtitltxt,

           $err_untagged, $err_titleisurl, $err_nocategory;

	$error = false;

	// Errors

	if($_POST['randkey'] !== $linkres->randkey) {

		//echo '<br style="clear: both;" />';

		print_form_submit_error( $err_incorrectkey);

		$error = true;

	}

	if($linkres->status != 'discard') {

		//echo '<br style="clear: both;" />';

		print_form_submit_error( $msg_alreadyqueued.": $linkres->status");

		$error = true;

	}

	if(strlen($linkres->title) < 10  || strlen($linkres->content) < 30 ) {

		print_form_submit_error($err_incompletetitltxt);

		$error = true;

	}

	if(strlen($linkres->title) > 120  || strlen($linkres->content) > 550 ) {

		print_form_submit_error($err_toolongtitltxt);

		$error = true;

	}

	if(strlen($linkres->tags) < 3 ) {

		print_form_submit_error($err_untagged);

		$error = true;

	}



	if(preg_match('/.*http:\//', $linkres->title)) {

		//echo '<br style="clear: both;" />';

		print_form_submit_error($err_titleisurl);

		$error = true;

	}

	if(!$linkres->category > 0) {

		//echo '<br style="clear: both;" />';

		print_form_submit_error($err_nocategory);

		$error = true;

	}

	return $error;

}



function print_form_submit_error($mess) {

	static $previous_error=false;

    global $title_submitvideos, $msg_oops;

	

	if (!$previous_error) {

		do_navbar($title_submitvideos . ' &#187; '. $msg_oops);

		echo '<div id="genericform-contents">'."\n"; // this div MUST be closed after function call!

		echo '<h2>'. $msg_oops .'</h2>'."\n";

		$previous_error = true;

	}

	echo '<div class="form-error-submit">&nbsp;&nbsp;'. $mess .'</div>'."\n";

}

	

?>
