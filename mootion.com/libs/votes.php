<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

class Vote {
	var $type='links';
	var $user=-1;
	var $value=1;
	var $link;
	var $ip='';
	
	function Vote() {
		return;
	}

	function get_where($value='> 0') {
		global $globals;
		// Begin check user and ip
		$where = "vote_type='$this->type' AND vote_link_id=$this->link";
		if ($this->ip == '') {
			$this->ip=$globals['user_ip'];
		}
		if($this->user > 0) {
			$where .= " AND (vote_user_id=$this->user OR vote_ip='$this->ip')";
		} elseif ($this->user == 0 ) {
			$where .= " AND vote_ip='$this->ip'";
		}
		if (!empty($value)) $where .= " AND vote_value $value ";
		// End check user and ip
		return $where;
	}

	function exists() {
		global $db;
		$where = $this->get_where('');
		return $db->get_var("SELECT vote_value FROM votes WHERE $where LIMIT 1");
	}

	function count($value="> 0") {
		global $db;
		$where = $this->get_where($value);
		$count=$db->get_var("SELECT count(*) FROM votes WHERE $where");
		return $count;
	}

	function insert() {
		global $db, $globals;
		if(empty($this->ip)) {
			$this->ip=$globals['user_ip'];
		}
		$this->value=intval($this->value);
		$sql="INSERT INTO votes (vote_type, vote_user_id, vote_link_id, vote_value, vote_ip) VALUES ('$this->type', $this->user, $this->link, $this->value, '$this->ip')";
		return $db->query($sql);
	}
}

