<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

class User {
	var $read = false;
	var $id = 0;
	var $username = '';
	var $level = 'normal';
	var $modification = false;
	var $date = false;
	var $ip = '';
	var $pass = '';
	var $email = '';
	var $names = '';
	var $lang = 1;
	var $karma = 10;
	var $url = '';
	// For stats
	var $total_votes = 0;
	var $published_votes = 0;
	var $total_links = 0;
	var $published_links = 0;
	var $positive_votes_received = 0;
	var $negative_votes_received = 0;
	

	function User($id=0) {
		if ($id>0) {
			$this->id = $id;
			$this->read();
		}
	}

	function store($full_save = true) {
		global $db, $current_user, $globals;

		if(!$this->date) $this->date=time();
	/*
		if($full_save && empty($this->ip)) {
			$this->ip=$globals['user_ip'];
		}
		*/
		$user_login = $db->escape($this->username);
		$user_level = $this->level;
		$user_karma = $this->karma;
		$user_date = $this->date;
		$user_ip = $this->ip;
		$user_pass = $db->escape($this->pass);
		$user_lang = $this->lang;
		$user_email = $db->escape($this->email);
		$user_names = $db->escape($this->names);
		$user_url = $db->escape(htmlentities($this->url));
		if($this->id===0) {
			$db->query("INSERT INTO users (user_login, user_level, user_karma, user_date, user_ip, user_pass, user_lang, user_email, user_names,  user_url) VALUES ($user_login, '$user_level', $user_karma, FROM_UNIXTIME($user_date), '$user_ip', '$user_pass', $user_lang, '$user_email', '$usr_names',  '$user_url'");
			$this->id = $db->insert_id;
		} else {
			// Username is never updated
			if ($full_save) $modification = ', user_modification = now() ' ;
			$db->query("UPDATE users set user_level='$user_level', user_karma=$user_karma, user_date=FROM_UNIXTIME($user_date), user_ip='$user_ip', user_pass='$user_pass', user_lang=$user_lang, user_email='$user_email', user_names='$user_names', user_url='$user_url' $modification  WHERE user_id=$this->id");
		}
	}
	
	function read() {
		global $db, $current_user;
		$id = $this->id;
		if($this->id>0) $where = "user_id = $id";
		else if(!empty($this->username)) $where = "user_login='".$db->escape($this->username)."'";

		if(!empty($where) && ($user = $db->get_row("SELECT * FROM users WHERE $where"))) {
			$this->id =$user->user_id;
			$this->username = $user->user_login;
			$this->level = $user->user_level;
			$date=$user->user_date;
			$this->date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");
			$this->ip = $user->user_ip;
			$date=$user->user_modification;
			$this->modification=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");
			$this->pass = $user->user_pass;
			$this->email = $user->user_email;
			$this->names = $user->user_names;
			$this->lang = $user->user_lang;
			$this->karma = $user->user_karma;
			$this->url = $user->user_url;
			$this->read = true;
			return true;
		}
		$this->read = false;
		return false;
	}

	function all_stats() {
		global $db;

		if(!$this->read) $this->read();

		$this->total_votes = $db->get_var("SELECT count(*) FROM votes WHERE vote_user_id = $this->id");
		$this->published_votes = $db->get_var("SELECT count(*) FROM votes,links WHERE vote_user_id = $this->id AND link_id = vote_link_id AND link_status = 'published' AND vote_date < link_published_date");
		$this->total_links = $db->get_var("SELECT count(*) FROM links WHERE link_author = $this->id AND link_status != 'discard'");
		$this->published_links = $db->get_var("SELECT count(*) FROM links WHERE link_author = $this->id AND link_status = 'published'");
		//$this->positive_votes_received = $db->get_var("SELECT count(*) FROM links, votes WHERE link_author = $this->id and vote_link_id = link_id and vote_value > 0");
		//$this->negative_votes_received = $db->get_var("SELECT count(*) FROM links, votes WHERE link_author = $this->id and vote_link_id = link_id and vote_value < 0");
		$this->total_comments = $db->get_var("SELECT count(*) FROM comments WHERE comment_user_id = $this->id");

	}

}
