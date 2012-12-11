<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

class Comment {
	var $id = 0;
	var $randkey = 0;
	var $author = 0;
	var $link = 0;
	var $date = false;
	var $karma = 0;
	var $content = '';
	var $read = false;

	function store() {
		global $db, $current_user;

		if(!$this->date) $this->date=time();
		$comment_author = $this->author;
		$comment_link = $this->link;
		$comment_karma = $this->karma;
		$comment_date = $this->date;
		$comment_randkey = $this->randkey;
		$comment_content = $db->escape($this->content);
		if($this->id===0) {
			$db->query("INSERT INTO comments (comment_user_id, comment_link_id, comment_karma, comment_date, comment_randkey, comment_content) VALUES ($comment_author, $comment_link, $comment_karma, FROM_UNIXTIME($comment_date), $comment_randkey, '$comment_content')");
			$this->id = $db->insert_id;
		} else {
			$db->query("UPDATE comments set comment_user_id=$comment_author, comment_link_id=$comment_link, comment_karma=$comment_karma, comment_date=FROM_UNIXTIME($comment_date), comment_randkey=$comment_randkey, comment_content='$comment_content' WHERE comment_id=$this->id");
		}
	}
	
	function read() {
		global $db, $current_user;
		$this->username = false;
		$id = $this->id;
		if(($link = $db->get_row("SELECT * FROM comments WHERE comment_id = $id"))) {
			$this->author=$link->comment_user_id;
			$this->randkey=$link->comment_randkey;
			$this->link=$link->comment_link_id;
			$this->karma=$link->comment_karma;
			$this->content=$link->comment_content;
			$date=$link->comment_date;
			$this->date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");
			$this->read = true;
			return true;
		}
		$this->read = false;
		return false;
	}

	function print_summary($link) {
		global $current_user;
		static $comment_counter = 0;

		if(!$this->read) return;
		$comment_counter++;
		echo '<li><div class="comment-body" id="wholecomment'.$this->id.'"><strong>#'.$comment_counter.'</strong>&nbsp;&nbsp;&nbsp;'.save_text_to_html($this->content).'</div>';
		echo '<div class="comment-info">';
		echo 'written by'. ' <a href="./user.php?login='.$this->username().'">'.$this->username().'</a> '.txt_time_diff($this->date) . 'ago' ;
	
		/** TODO
		'&nbsp;'<strong>score:<span id="scorediv'.$this->id.'" style="display:inline">--</span></strong>';
		echo '<form style="display:inline;" action="/setrating/'.$this->id.'" method="post">';
		echo '<select name="ratings" onchange="updaterating(this.form,'.$this->id.',\'wholecomment'.$this->id.'\', 0)">';
		echo '<option value="3">+3 Excellent</option>';
		echo '<option value="2">+2 Insightful</option>';
		echo '<option value="1">+1 Useful</option>';
		echo '<option value="0" selected="selected">Rate Comment</option>';
		echo '<option value="-1">-1 Off Topic</option>';
		echo '<option value="-2">-2 Flame</option>';
		echo '<option value="-3">-3 SPAM</option>';
		echo '</select>';
		echo '<input type="hidden" name="return" value="story.php?id=aaaa"/>';
		echo '</form>
		**/
		
		echo '</div></li>'."\n";

	}

	function username() {
		global $db;
//TODO
		$this->username = $db->get_var("SELECT user_login FROM users WHERE user_id = $this->author");
		return $this->username;
	}
}
