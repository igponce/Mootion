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

include(mnminclude.'tags.php');



// Must be autenticated first !!

force_authentication();



do_header('edit video', "post");

do_navbar('edit video');



echo '<div id="genericform-contents">'."\n";



if (!empty($_REQUEST['id'])) { 

	$linkres=new Link;

	$linkres->id=$link_id = $_REQUEST['id'];

	$linkres->read(); // full read!



	// from meneame.net editlink.php - post security bugfix.

	if (!$linkres->is_editable()) {

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. 'not editable' .'</div>'."\n";

		return;

	}



	if ($_POST['phase'] == "1") 

		do_save();

	else 

		do_edit();

} else {

	echo '<div class="form-error-submit">&nbsp;&nbsp;' ."Duh?". '</div>';

}





echo "</div>";



do_footer();



function do_edit() {

	global $linkres, $dblang, $db;



	$link_title = htmlspecialchars($linkres->title);

	$link_content = htmlspecialchars($linkres->content);

	$link_tags = htmlspecialchars($linkres->tags);



	echo '<h2>'. 'edit video' .'</h2>'."\n";

	echo '<div id="genericform">'."\n";

	echo '<form action="/editlink.php" method="post" id="thisform">'."\n";

	$now = time();



	echo '<input type="hidden" name="key" value="'.md5($now.$linkres->randkey.$site_key).'" />'."\n";

	echo '<input type="hidden" name="timestamp" value="'.$now.'" />'."\n";

	echo '<input type="hidden" name="phase" value="1" />'."\n";

	echo '<input type="hidden" name="id" value="'.$linkres->id.'" />'."\n";

	

	// Security Check: formvalidation

	echo '<input type="hidden" name="formvalidation" value="' . encode_formmd5($url . $_POST['randkey'] . $linkres->id ) . '">'."\n";



	echo '<fieldset><legend><span class="sign">'. 'Video Details' .'</span></legend>'."\n";



	echo '<label for="title" accesskey="1">'. 'video title' .':</label>'."\n";

	echo '<p><span class="genericformnote">'. 'video title (120 max. chars.)' .'</span>'."\n";

	echo '<br/><input type="text" id="title" name="title" value="'.$link_title.'" size="60" maxlength="120" /></p>'."\n";



	echo '<label for="tags" accesskey="2">'. 'tags' .':</label>'."\n";

	echo '<p><span class="genericformnote"><strong>'. 'Use a few words, separated by commas ",". Please keep ti as generic and short as possible. </strong>Example: <em>humor,atlanta,commercial</em></span>'."\n";

	echo '<br/><input type="text" id="tags" name="tags" value="'.$link_tags.'" size="50" maxlength="50" /></p>'."\n";



	echo '<p><label for="bodytext" accesskey="3">'. 'Video Description' .':</label>'."\n";

	echo '<br /><span class="genericformnote">'. 'Describe this video with your own words. Two to five paragraphs are enough. Please be clear, polite, and don\'t insult.' .'</span>'."\n";

	echo '<br/><textarea name="bodytext"  rows="10" cols="60" id="bodytext" >'.$link_content.'</textarea></p>'."\n";

	echo '<p><label accesskey="4">'. 'category' .':</label><br />'."\n";

	echo '<span class="genericformnote">'. 'choose the category you find most accurate'.'</span></p>'."\n";

	echo '<div class="column-list categorylist">'."\n";

	echo '<ul>'."\n";

	$categories = $db->get_results("SELECT category_id, category_name FROM categories WHERE category_lang='$dblang' ORDER BY category_name ASC");

	foreach ($categories as $category) {

	 	echo '<li><input name="category" type="radio" '; 

		if ($linkres->category == $category->category_id) echo '  checked="true" ';

		echo 'value="'.$category->category_id.'"/>'. $category->category_name .'</li>'."\n";

		echo "\n<!-- category: $linkres->category, $category->category_id -->\n";

	}



	// TODO: no standard

	// echo '<br style="clear: both;" /></ul>' . "\n";



	echo "<br />\n";

	echo '</div>'."\n";



	echo '<p><label for="trackback">'. 'trackback' .':</label><br />'."\n";

	echo '<span class="genericformnote">'. 'Add a trackback'.'</span>'."\n";

	echo '<input type="text" name="trackback" id="trackback" value="'.$trackback.'" class="form-full" /></p>'."\n";



	echo '<input class="genericsubmit" type="submit" value="'. 'save &#187;' .'" />'."\n";

	echo '</fieldset>'."\n";

	echo '</form>'."\n";

	echo '</div>'."\n";

}





function do_save() {

	global $linkres, $dblang;

	

	// Security Check

	// ToDo: Better Error handling

	if ( ! validate_formd5 ( $_POST['formvalidation'], $_POST['url'] . $_POST['randkey'] . $_POST['id'] ) ) {

		echo '<h1>Validation Error</h1>';

		echo '<hr><p>Some form parameters were modified. This request is beign logged.</p>';

		echo '<p><a href="http://mootion.com">Back to mOOtion</a></p>' . "\n";

		echo '<!-- do_submit3(). Code: ' . $_POST['formvalidation'] . ' -->';

		return;

	}



	$linkres->category=$_POST['category'];

	$linkres->title = stripslashes(strip_tags(trim($_POST['title'])));

	$linkres->content = stripslashes(strip_tags(trim($_POST['bodytext'])));

	$linkres->tags = tags_normalize_string(stripslashes(strip_tags(trim($_POST['tags']))));

	if (!link_edit_errors($linkres)) {

		$linkres->store();

		tags_insert_string($linkres->id, $dblang, $linkres->tags, $linkres->date);

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. "video entry updated" .'</div>'."\n";

	} 



	echo '<div class="formnotice">'."\n";

	$linkres->print_summary('preview');

	echo '</div>'."\n";



	echo '<form id="genericform" method="GET" action="story.php" >';

	echo '<input type="hidden" name="id" value="'.$linkres->id.'" />'."\n";

	echo '<input class="genericsubmit" type="button" onclick="window.history.go(-1)" value="'. '&#171; modify' .'">&nbsp;&nbsp;'."\n";;

	echo '<input class="genericsubmit" type="submit" value="'. 'go to entry' .'" />'."\n";

	echo '</form>'. "\n";



}



function link_edit_errors($linkres) {



	$error = false;

	

	if($_POST['key'] !== md5($_POST['timestamp'].$linkres->randkey.$site_key)) {

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. 'incorrect key' .'</div>';

		$error = true;

	}

	if(time() - $_POST['timestamp'] > 900) {

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. 'time exceeded' .'</div>';

		$error = true;

	}

	if(strlen($linkres->title) < 10  || strlen($linkres->content) < 30 ) {

		//echo '<br style="clear: both;" />';

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. "incomplete title or text" .'</div>';

		$error = true;

	}

	if(strlen($linkres->tags) < 3 ) {

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. "not tagged" .'</div>';

		$error = true;

	}

	if(preg_match('/.*http:\//', $linkres->title)) {

		//echo '<br style="clear: both;" />';

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. "Don't put URLs in the title. URLs don't give information" .'</div>';

		$error = true;

	}

	if(!$linkres->category > 0) {

		//echo '<br style="clear: both;" />';

		echo '<div class="form-error-submit">&nbsp;&nbsp;'. "Must select a category" .'</div>';

		$error = true;

	}

	return $error;

}



?>