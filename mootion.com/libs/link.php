<?

// The source code packaged with this file is Free Software, Copyright (C) 2005 by

// Ricardo Galli <gallir at uib dot es>.

// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.

// You can get copies of the licenses here:

// 		http://www.affero.org/oagpl.html

// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".



// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>



require (mnminclude.'localization-utils_en.php');

require (mnminclude.'blog.php');



class Link {

	var $id = 0;

	var $author = -1;

	var $blog = 0;

	var $username = false;

	var $randkey = 0;

	var $karma = 0;

	var $valid = false;

	var $date = false;

	var $published_date = 0;

	var $modified = 0;

	var $uri = false;

	var $url = false;

	var $url_title = false;

	var $encoding = false;

	var $status = 'discard';

	var $type = '';

	var $category = 0;

	var $votes = 0;

	var $title = '';

	var $tags = '';

	var $content = '';

	var $html = false;

	var $trackback = false;

	var $viaurl = false ;

	var $read = false;

	var $fullread = false;

	var $voted = false;

	var $size = 0;

	var $embedhtml = false;



	function print_html() {

		echo "Valid: " . $this->valid . "<br>\n";

		echo "Url: " . $this->url . "<br>\n";

		echo "Title: " . $this->url_title . "<br>\n";

		echo "encoding: " . $this->encoding . "<br>\n";

		echo "ViaUrl: " . $this->viaurl . "<br/>\n";

		echo "URI: " . $this->uri . "<br/>\n";

	}

	

        function mimetype ($url) {

		

		// Obtiene la cabecera y averigua el tipo de contenido MIME

		

		$video_type = false;

		$matches = false;

		$url=trim($url);

		$size=0;



		$timeout = 5; // set to zero for no timeout

		

		$ch = curl_init();

		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		curl_setopt ($ch, CURLOPT_NOBODY, 1);

		curl_setopt ($ch, CURLOPT_HEADER, 1);

		$this->html = curl_exec($ch);

		curl_close($ch);

		

		// IGP // if(!($this->html = file_get_contents($url))) {



		$this->url=$url;



		// Comprobamos el tipo MIME. Aceptamos video/*, x-video/*



		if(preg_match('/Content-Type:\s+video\/(.*)/i', $this->html, $matches)) {

				$this->valid = true;

				$this->type = 'video';

				$this->encoding = strtolower ( $matches[1] );

				

				preg_match ('/Content-Length:\s+([1-9][0-9]+)/', $this->html, $matches);

				$this->size = $matches[1];

			

		} else

		if(preg_match('/Content-Type:\s+text\/html/i', $this->html, $matches)) {

			// Tipos MIME aceptables: text/html		

			$this->valid = true;

			$this->type = 'blog'; // maybe it's a blog...

			$this->encoding = $matches[1];

		}



		// DEBUG:

		echo "\n<!-- (link.php) Cabecera HTTP Capturada:\n\n" . $this->html;

		echo "\n\nlink.php-Content-Type:" . $video_type . "\n-->";



		return $video_type;

	}





	function get($url) {

		$url=trim($url);

		$timeout = 5; // set to zero for no timeout



		$ch = curl_init();

		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$this->html = curl_exec($ch);

		curl_close($ch);

		

		// IGP: Original en meneame // if(!($this->html = file_get_contents($url))) {

		 if(! $this->html ) {

			return;

		}

		$this->valid = true;

		$this->url=$url;

		if(preg_match('/charset=([a-zA-Z0-9-_]+)/i', $this->html, $matches)) {

			$this->encoding=trim($matches[1]);

			if(strcasecmp($this->encoding, 'utf-8') != 0) {

				@$this->html=iconv($this->encoding, 'UTF-8//IGNORE', $this->html);

			}

		}

		if(preg_match('/<title>([^<>]*)<\/title>/i', $this->html, $matches)) {

			$this->url_title=trim($matches[1]);

		}

		require_once(mnminclude.'blog.php');

		$blog = new Blog();

		$blog->analyze_html($this->url, $this->html);

		if(!$blog->read('key')) {

			$blog->store();

		}

		$this->blog=$blog->id;

		$this->type=$blog->type;



		// Detect trackbacks

		if (!empty($_POST['trackback'])) {

			$this->trackback=trim($_POST['trackback']);

		} elseif (preg_match('/trackback:ping="([^"]+)"/i', $this->html, $matches) ||

			preg_match('/trackback:ping +rdf:resource="([^>]+)"/i', $this->html, $matches) || 

			preg_match('/<trackback:ping>([^<>]+)/i', $this->html, $matches)) {

			$this->trackback=trim($matches[1]);

		} elseif (preg_match('/<a[^>]+rel="trackback"[^>]*>/i', $this->html, $matches)) {

			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {

				$this->trackback=trim($matches2[1]);

			}

		} elseif (preg_match('/<a[^>]+href=[^>]+>trackback<\/a>/i', $this->html, $matches)) {

			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {

				$this->trackback=trim($matches2[1]);

			}

		}  elseif (preg_match('/(http:\/\/[^\s]+\/trackback\/*)/i', $this->html, $matches)) {

			$this->trackback=trim($matches[0]);

		}  

		

	}



	function type() {

		if (empty($this->type)) {

			if ($this->blog > 0) {

				require_once(mnminclude.'blog.php');

				$blog = new Blog();

				$blog->id = $this->blog;

				if($blog->read()) {

					$this->type=$blog->type;

					return $this->type;

				}

			}

			return 'normal';

		}

		return $this->type;

	}



	function store() {

		global $db, $current_user;



		$this->store_basic();

		$link_url = $db->escape($this->url);

		$link_url_title = $db->escape($this->url_title);

		$link_title = $db->escape($this->title);

		$link_tags = $db->escape($this->tags);

		$link_content = $db->escape($this->content);

		$link_size = $db->escape($this->size);

		// $link_embedhtml = $db->escape($this->embedhtml);

		// $db->query("UPDATE links set link_url='$link_url', link_url_title='$link_url_title', link_title='$link_title', link_content='$link_content', link_tags='$link_tags', link_size='$link_size', link_embedhtml='$link_embedhtml' WHERE link_id=$this->id");

		$db->query("UPDATE links set link_url='$link_url', link_url_title='$link_url_title', link_title='$link_title', link_content='$link_content', link_tags='$link_tags', link_size='$link_size' WHERE link_id=$this->id LIMIT 1");

	}



	function store_basic() {

		global $db, $current_user;



		if(!$this->date) $this->date=time();

		$link_author = $this->author;

		$link_blog = $this->blog;

		$link_status = $this->status;

		$link_votes = $this->votes;

		$link_karma = $this->karma;

		$link_randkey = $this->randkey;

		$link_category = $this->category;

		$link_date = $this->date;

		$link_published_date = $this->published_date;

		$link_embedhtml = $db->escape($this->embedhtml);

		$link_viaurl = $db->escape ($this->viaurl);

		$link_uri = $db->escape($this->uri);



		if($this->id==0) {

			$db->query("INSERT INTO links (link_author, link_blog, link_status, link_randkey, link_category, link_date, link_published_date, link_votes, link_karma, link_embedhtml, link_viaurl, link_uri) VALUES ('$link_author', '$link_blog', '$link_status', '$link_randkey', '$link_category', FROM_UNIXTIME($link_date), FROM_UNIXTIME($link_published_date), '$link_votes', '$link_karma', '$link_embedhtml', '$link_viaurl', '$link_uri')");

			$this->id = $db->insert_id;

		} else {

		// update

			$db->query("UPDATE links set link_author=$link_author, link_blog=$link_blog, link_status='$link_status', link_randkey=$link_randkey, link_category=$link_category, link_modified=NULL, link_date=FROM_UNIXTIME($link_date), link_published_date=FROM_UNIXTIME($link_published_date), link_votes=$link_votes, link_karma=$link_karma, link_uri='$link_uri' WHERE link_id=$this->id");

			// , link_embedhtml='$link_embedhtml', link_viaurl='$link_viaurl'

		}

	}

	

	function read() {

		global $db, $current_user;

		

		$this->fullread = false;

		$this->read = false;		

		$id = $this->id;

		

		if(($link = $db->get_row("SELECT links.*, users.user_login FROM links, users WHERE link_id = $id AND user_id=link_author"))) {

			$this->author=$link->link_author;

			$this->username=$link->user_login;

			$this->blog=$link->link_blog;

			$this->status=$link->link_status;

			$this->votes=$link->link_votes;

			$this->karma=$link->link_karma;

			$this->randkey=$link->link_randkey;

			$this->category=$link->link_category;

			$this->url= $link->link_url;

			$this->url_title=$link->link_url_title;

			$this->title=$link->link_title;

			$this->tags=$link->link_tags;

			$this->content=$link->link_content;

			$date=$link->link_date;

			$this->date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			$date=$link->link_published_date;

			$this->published_date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			$date=$link->link_modified;

			$this->modified=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			$this->fullread = $this->read = true;

			$this->size = $link->link_size;

			$this->embedhtml = $link->link_embedhtml;

			$this->viaurl = $link->link_viaurl;

			$this->uri = $link->link_uri;

			return true;

		}

				

		return false;

	}



	function read_basic() {

		global $db, $current_user;

		$this->username = false;

		$this->fullread = false;

		$this->read = false;

		$id = $this->id;

		if(($link = $db->get_row("SELECT link_author, link_blog, link_status, link_randkey, link_category, link_date, link_votes, link_karma, link_published_date, link_embedhtml, link_viaurl, link_uri FROM links WHERE link_id = $id"))) {

			$this->author=$link->link_author;

			$this->blog=$link->link_blog;

			$this->votes=$link->link_votes;

			$this->karma=$link->link_karma;

			$this->status=$link->link_status;

			$this->randkey=$link->link_randkey;

			$this->category=$link->link_category;

			$date=$link->link_date;

			$this->date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			$date=$link->link_published_date;

			$this->published_date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			$this->embedhtml = $link->embedhtml;

			$this->viaurl = $link->viaurl;

			$this->uri = $link->uri;

			$this->read = true;

			return true;

		}

		return false;

	}



	function duplicates($url) {

		global $db;

		$link_url=$db->escape($url);

		$n = $db->get_var("SELECT count(*) FROM links WHERE link_url = '$link_url' AND (link_status != 'discard' OR link_votes>0)");

		return $n;

	}



	function print_summary($type='full') {

		global $current_user, $current_user, $globals;



		if(!$this->read) return;

		$this->voted = $this->vote_exists($current_user->user_id);



		$linktarget = '';

		$url = htmlspecialchars($this->url);

		$url_short = htmlspecialchars(txt_shorter($this->url));

		$title_short = htmlspecialchars(wordwrap($this->title, 36, " ", 1));

		

		if ($type == 'story_mini') {

			$linktarget = ' target=_top ';

		}



		echo '<div class="news-summary" id="news-'.$this->id.'">';

		echo '<div class="news-body">';

		if ($type != 'preview' && !empty($this->title) && !empty($this->content)) {

			$this->print_shake_box();

		}

		

		echo '<h3 id="title'.$this->id.'">';

	

		if(empty($globals['link_id'])) 

			if ( $this->embedhtml=='' )

				echo '<a href="/story_frame.php?id='.$this->id.'">'. $title_short. '</a>';

			else

				echo '<a href="' . $this->get_permalink() .'">' . $title_short . '</a>';

				// echo '<a href="/story.php?id='.$this->id.'">'. $title_short. '</a>';

		else 

			echo $title_short;

		echo '</h3>';



		echo '<p class="news-submitted">';



		// ToDo: Better 'ago' formatting for very old/very new posts		

		echo 'sent by <a href="user.php?login='.$this->username().'&amp;view=history"'.$linktarget.'><strong>'.$this->username().'</strong></a> '.txt_time_diff($this->date).' ago';

		if($this->status == 'published')

			echo ', published '. txt_time_diff($this->published_date).' ago';

		echo "</p>\n";

		

		// Show the content with the (from: url) text.



		if($type=='full' || $type=='preview' || $type='story') {			

			echo '<div class="news-body-text">';

			if ( $this->viaurl ) {

				$viatext = $this->viaurl;

				// if ($this->blog > 0) {

				//	$blog = new Blog();

				//	$blog->id = $this->blog;

				//	if($blog->read()) {

				//		$viatext = $blog->url;

				//	}

				// }

				echo '<em>(from <a href="'. $this->viaurl; echo '" rel="external nofollow">' . $viatext .'</a>)</em><br/>';

			}



			echo ' ' . text_to_html(htmlspecialchars($this->content)) . '</div>';

		}

		

		echo '<div class="news-details">';

		

		// Dump tags

		

		if (!empty($this->tags)) {

			// echo '<span class="tool"><a href="cloud.php?range=1" title="'. 'cloud' .'"'.$linktarget.'>tags</a>:&nbsp;';

			// <a href="index.php?search='.$tags_url.'&amp;tag=true"'.$linktarget.'>'.$tags_words."</a></span>";

			$tags_url = urlencode($this->tags);

			$tags_array = explode(",", $this->tags);

			$tags_counter = 0;

			

			echo '<div class="tool comments"><a href="index.php?search='.$tags_url.'&amp;tag=true"'.$linktarget.'>Tags:</a>';

                        foreach ($tags_array as $tag_item) {

                                $tag_item=trim($tag_item);

                                $tag_url = urlencode($tag_item);

                                if ($tags_counter > 0) echo ',';

                                echo ' <a href="index.php?search=tag:'.$tag_url.'"><img src="/img/common/tag_green.png" alt="tag: "/>'.$tag_item.'</a>';

                               $tags_counter++;

			}

			echo '</div>';	

		}

		

		echo '<br style="clear:both" />';

		

		// Dump embedded video object

		

		// if ( $type == 'story' ) { <-- sigue sin tener sentido ???

		if ($this->embedhtml!='' ) {

		   echo '<div class="news-details">';

		   echo html_entity_decode($this->embedhtml,ENT_COMPAT) . "\n";

		   echo '</div><br style="clear:both"/>';

		}

		

		echo '<div class="news-details">'; // hack. **TODO**



		$ncomments = $this->comments();



		if(empty($globals['link_id']))

			echo '<span class="tool comments"><a href="./story.php?id='.$this->id.'#comments" class="tool comments" '. $linktarget .' >Comment<img src="/img/common/comment.png" alt="comment icon" /> ('.$ncomments.')</a></span>';

		else

			echo '<span class="tool comments"><a href="#comments" class="tool comments" '. $linktarget .' >Comment<img src="/img/common/comment.png" alt="comment icon" /> ('.$ncomments.')</a></span>';

			

		// Categories

		

		echo '<span class="tool"><a href="./index.php?category='.$this->category.'" title="'. 'category' .'" '. $linktarget .' ><img src="/img/common/folder.png">&nbsp;Category: '.$this->category_name().'</a></span>';

		

		if ( $this->embedhtml != '') {

		   echo '<span class="tool"><a href="/embed.php?v='. $this->id .'" target="_embed"><img src="/img/common/wand.png" alt="Share this video: Add it to your blog/myspace/site">Share</a></span>';

		   // For future javascript FX

		   // echo '<span id="embed' .$this->id .'><br/>Link: <input name="permalink'. $this->id .'" readonly="true" size="30" value="'. 'http://'. $_SERVER['SERVER_NAME'] .'/story.php?v='.$this->id .'" onfocus="selectInputText(this)">';   

		   // $embedinput = $this->embedhtml;

		   // if ( !ereg ('/^&([lL][tT]|[aA][mM][pP])/', $embedinput) ) {

		   //   $embedinput = htmlspecialchars ( html_entity_decode ($embedinput ,ENT_COMPAT) );

		   // }

		   // echo '<br/>Embed: <input name="embedhtml'. $this->id .'" readonly="true" size="30" value="'. $embedinput .'" onfocus="selectInputText(this);"></span>';

		   // echo '</div>';

		}



		if(!empty($globals['link_id']))

			echo '<span  class="tool"><a href="'.get_trackback($this->id).'" class="simple tight" title="'. 'trackback' .'" '. $linktarget .' >trackback</a></span>';



		// Allow to modify it

		if ($type != 'preview' && $this->is_editable()) {

			echo ' <span  class="tool"><a href="editlink.php?id='.$this->id.'"'.$linktarget.'>'. 'edit' .'</strong></a></span> ';

		}



		if($this->status!='published' && $type != 'preview' /*&& $this->author != $current_user->user_id*/) {

			$this->print_problem_form();

		}

		

		echo '</div>'; // hack. (the div after the embedhtml stuff).



		echo '</div>'."\n";

		echo '</div></div>'."\n";



	}









	function print_mini_summary() {

		global $current_user, $current_user, $globals;



		if(!$this->read) return;

		$this->voted = $this->vote_exists($current_user->user_id);



		$url = htmlspecialchars($this->url);

		$url_short = htmlspecialchars(txt_shorter($this->url));

		$title_short = htmlspecialchars(wordwrap($this->title, 36, " ", 1));



		// echo '<div class="news-summary" id="news-'.$this->id.'">';

		// echo '<div class="news-body">';

		// $this->print_shake_box();

		echo '<h3 id="title'.$this->id.'">'. $title_short ."</h3>\n";

		echo 'submitted by' . ' <a target="_top" href="user.php?login='.$this->username().'&amp;view=history"><strong>'.$this->username().'</strong></a> '.txt_time_diff($this->date) .' ago';

		if($this->status == 'published')

			echo ', '  .txt_time_diff($this->published_date) .' ago';

		echo "</p>\n";

		echo '<div class="news-body-text">'.text_to_html(htmlspecialchars($this->content)).'</div>';

		echo '<div class="news-details">';

		$ncomments = $this->comments();



		if(empty($globals['link_id']))

			echo '<a href="./story.php?id='.$this->id.'" class="tool comments">'.$ncomments.' '. 'comments' . '</a>';

		else 

			echo '<span class="tool comments">'.$ncomments.' '. 'comments' .'</span>';



		// if (!empty($this->tags)) {

		//	$tags_words = str_replace(",", ", ", $this->tags);

		//	$tags_url = urlencode($this->tags);

		//	echo '<span class="tool"><a href="cloud.php" title="'. 'cloud' .'">'. 'tags' .'</a>: <a href="index.php?search='.$tags_url.'&amp;tag=true">'.$tags_words."</a></span>";

		// }

		// echo '<span class="tool"><a href="./index.php?category='.$this->category.'" title="'. 'category' .'">'.$this->category_name().'</a></span>';



		// if(!empty($globals['link_id']))

		//	echo '<span  class="tool"><a href="'.get_trackback($this->id).'" class="simple tight" title="'. 'trackback' .'">trackback</a></span>';



		// IGP // Allow to modify it

		// if ($this->is_editable()) {

		// 	echo ' <span  class="tool"><a target="_top" href="editlink.php?id='.$this->id.'">'. 'edit' .'</strong></a></span> ';

		// }



		echo '</div>'."\n";

		// echo '</div></div>'."\n";



	}



	function print_shake_box () {

		global $current_user, $anonnymous_vote, $site_key, $globals;

		

		switch ($this->status) {

			case 'queued': // another color box for not-published

				$altdiv01 = ' mnm-queued"';

				break;

			case 'discard': // another color box for discarded

				$altdiv01 = ' mnm-discarded"';

				break;

			case 'published': // default (without div) for published

			default:

				$altdiv01 = '"';

				break;

		}

		echo '<ul class="news-shakeit'.$altdiv01.'>';

		echo '<li class="mnm-count" id="main'.$this->id.'">';

		echo '<a id="mnms-'.$this->id.'">'.$this->votes.' '. 'mOOves' .'</a></li>';

		echo '<li class="menealo" id="mnmlink-'.$this->id.'">';

		//if( ($anonnymous_vote || $current_user->user_id > 0 ) && $this->votes($current_user->user_id) == 0) {

		if( !$this->voted) {

			echo '<a href="javascript:menealo('."$current_user->user_id, $this->id, $this->id, "."'".md5($site_key.$current_user->user_id.$this->id.$this->randkey.$globals['user_ip'])."'".')" title="'. 'Like It? mOOve It!' .'">'. 'mOOve It' .'</a></li>';

		} else {

			if ($this->voted > 0) $mess = 'Yeah!';

			else $mess = ':-(';

			echo '<span>'.$mess.'</span>';

		}

		echo '</ul>'."\n";

	}



	function print_problem_form() {

		global $current_user, $db, $anon_karma, $anonnymous_vote, $globals, $site_key;

		require_once(mnminclude.'votes.php');



		$vote = new Vote;

		$vote->link=$this->id;

		$vote->type='links';

		$vote->user=$current_user->user_id;

		if(/*!$current_user->user_id > 0 || */ (!$anonnymous_vote && $current_user->user_id == 0 ) || $this->voted) {

			// don't show it for now

			return;

			$status='disabled="disabled"';

		}

		$pvalue = -2;

		//echo '<span class="tool-right">';

		echo '<form class="tool" action="" id="problem-'.$this->id.'">';

		echo '<select '.$status.' name="ratings"  onchange="';

		echo 'report_problem(this.form,'."$current_user->user_id, $this->id, "."'".md5($site_key.$current_user->user_id.$this->randkey.$globals['user_ip'])."'".')';

		echo '">';

		echo '<option value="0" selected="selected">problem?</option>';

		echo '<option value="'.$pvalue.'">'. 'irrelevant' .'</option>'; $pvalue--;

		echo '<option value="'.$pvalue.'">'. 'self-promotion' .'</option>'; $pvalue--;

		echo '<option value="'.$pvalue.'">'. 'spam' .'</option>'; $pvalue--;

		echo '<option value="'.$pvalue.'">'. 'duplicated' .'</option>'; $pvalue--;

		echo '<option value="'.$pvalue.'">'. 'provocation' .'</option>'; $pvalue--;

		echo '<option value="'.$pvalue.'">'. 'trash' .'</option>'; $pvalue--;

		echo '</select>';

//		echo '<input type="hidden" name="return" value="" disabled />';

		echo '</form>';

	}



	function vote_exists($user) {

		require_once(mnminclude.'votes.php');

		$vote = new Vote;

		$vote->user=$user;

		$vote->link=$this->id;

		return $vote->exists();	

	}

	

	function votes($user) {

		require_once(mnminclude.'votes.php');



		$vote = new Vote;

		$vote->user=$user;

		$vote->link=$this->id;

		return $vote->count();

	}



	function insert_vote($user=0) {

		global $anon_karma;

		require_once(mnminclude.'votes.php');



		$vote = new Vote;

		$vote->user=$user;

		$vote->link=$this->id;

		if ($vote->exists()) return false;

		$vote->value=$anon_karma;

		if($user>0) {

			require_once(mnminclude.'user.php');

			$dbuser = new User($user);

			if($dbuser->id>0) {

				$vote->value = $dbuser->karma;

			}

		}

		if($vote->insert()) {

			$vote->user=-1;

			$this->votes=$vote->count();

			$this->store_basic();

			return true;

		}

		return false;

	}



	function category_name() {

		global $db, $dblang;

		return $db->get_var("SELECT category_name FROM categories WHERE category_lang='$dblang' AND category_id=$this->category");

	}



	function publish() {

		if(!$this->read) $this->read_basic();

		$this->published_date = time();

		$this->status = 'published';

		$this->store_basic();

	}



	function username() {

		global $db;

		if (!$this->fullread) {

			$this->username = $db->get_var("SELECT user_login FROM users WHERE user_id = $this->author");

		}

		return $this->username;

	}



	function comments() {

		global $db;

		return $db->get_var("SELECT count(*) FROM comments WHERE comment_link_id = $this->id");

	}



	function is_editable() {

		global $current_user, $db;



		if($current_user->user_id > 0 && (

			($this->author == $current_user->user_id && $this->status != 'published'  && time() - $this->date < 3600) ||

			$current_user->user_level != 'normal'))

			return true;

		return false;

	}

	

	function get_uri() {

		global $db, $globals;

		$seq = 0;

		require_once(mnminclude.'uri.php');

		$new_uri = "$this->id-" . $base_uri = get_uri($this->title);

		// mnm // while ($db->get_var("select count(*) from links where link_uri='$new_uri' and link_id != $this->id") && $seq < 20) {

		// mnm // 	$seq++;

		// mnm // 	$new_uri = $base_uri . "-$seq";

		// mnm // }

		// In case we tried 20 times, we just add the id of the article

		// mnm // if ($seq >= 20) {

		// mnm // 	$new_uri = $base_uri . "-$this->id";

		// mnm // }

		$this->uri = $new_uri;

	}

	



	function get_relative_permalink() {

		global $globals;

		if (!empty($this->uri)) {

			return $globals['base_url'] . $this->uri ;

		} else {

			# return $globals['base_url'] . 'story.php?id=' . $this->id;
			#return $globals['base_url'] . 'videos/' . $this->id . '-' . clean_text(preg_replace('/[\s|,|;|:|\/]+/', '-', $this->title), 40);
			return $globals['base_url'] . 'videos/' . $this->id . '-' . clean_text(preg_replace('#[\s\-\/\,\;]+#', '-', $this->title), 40);

		}
	}



	function get_permalink() {

		return 'http://'.get_server_name().$this->get_relative_permalink();

	}



	

	function get_trackback() {

		global $globals;

		return "http://".get_server_name().$globals['base_url'].'trackback.php?id='.$this->id;

	}



}



// EOF //

?>
