<?

// The source code packaged with this file is Free Software, Copyright (C) 2005 by

// Ricardo Galli <gallir at uib dot es>.

// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.

// You can get copies of the licenses here:

// 		http://www.affero.org/oagpl.html

// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".





class Trackback {

	var $id = 0;

	var $author = 0;

	var $link = 0;

	var $type = 'out';

	var $status = 'pendent';

	var $date = false;

	var $modified = false;

	var $url  = '';

	var $title = '';

	var $content = '';

	var $read = false;

	var $html = false;



	function dump() {

		echo "Trackback := {\n id: $id,\n author: $this->author,\n link: $this->link,\n status: $this->status,\n modified: $this->modified,\n url: $this->url,\n title: $this->title,\n content: $this->content }\n"; 

	}

	function store() {

		global $db, $current_user;



		if(!$this->date) $this->date=time();

		$trackback_date=$this->date;

		$trackback_author = $this->author;

		$trackback_link = $this->link;

		$trackback_type = $this->type;

		$trackback_status = $this->status;

		$trackback_url = $db->escape(trim($this->url));

		$trackback_title = $db->escape(trim($this->title));

		$trackback_content = $db->escape(trim($this->content));

		if($this->id===0) {

			$db->query("INSERT INTO trackbacks (trackback_user_id, trackback_link_id, trackback_type, trackback_date, trackback_status, trackback_url, trackback_title, trackback_content) VALUES ($trackback_author, $trackback_link, '$trackback_type', FROM_UNIXTIME($trackback_date), '$trackback_status', '$trackback_url', '$trackback_title', '$trackback_content')");

			$this->id = $db->insert_id;

		} else {

			$db->query("UPDATE trackbacks set trackback_user_id=$trackback_author, trackback_link_id=$trackback_link, trackback_type='$trackback_type', trackback_date=FROM_UNIXTIME($trackback_date), trackback_status='$trackback_status', trackback_url='$trackback_url', trackback_title='$trackback_title', trackback_content='$trackback_content' WHERE trackback_id=$this->id");

		}

	}

	

	function read() {

		global $db, $current_user;



		if($this->id == 0 && !empty($this->url) && $this->link > 0) 

			$cond = "trackback_type = '$this->type' AND trackback_link_id = $this->link AND trackback_url = '$this->url'";



		else $cond = "trackback_id = $this->id";

	

		if(($link = $db->get_row("SELECT * FROM trackbacks WHERE $cond"))) {

			$this->id=$link->trackback_id;

			$this->author=$link->trackback_user_id;

			$this->link=$link->trackback_link_id;

			$this->type=$link->trackback_type;

			$this->status=$link->trackback_status;

			$this->url=$link->trackback_url;

			$this->title=$link->trackback_title;

			$this->content=$link->trackback_content;

			$date=$link->trackback_date;

			$this->date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			$date=$link->trackback_modified;

			$this->modified=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			$this->read = true;

			return true;

		}

		$this->read = false;

		return false;

	}



// Send a Trackback

	

	function send () {

		

	   if ( empty ($this->url) ) {

		echo "\n<!-- Trackback::send - Cannot send trackback url(".$this->url.") -->\n";

	   } else {

		// $this->send_meneameoriginal() ;

		$this->send_withcurl();

	   }

	}



	function send_meneameoriginal() {



		if (empty($this->url))

			return;

	

		$title = urlencode($this->title);

		$excerpt = urlencode($this->content);

		$blog_name = urlencode('Mootion.com');

		$tb_url = $this->url;

		$url = urlencode(get_permalink($this->link));

		

		$query_string = "charset=UTF-8&title=$title&url=$url&blog_name=$blog_name&excerpt=$excerpt";

		$trackback_url = parse_url($this->url);

		$http_request  = 'POST ' . $trackback_url['path'] . ($trackback_url['query'] ? '?'.$trackback_url['query'] : '') . " HTTP/1.0\r\n";

		$http_request .= 'Host: '.$trackback_url['host']."\r\n";

		$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'."\r\n";

		$http_request .= 'Content-Length: '.strlen($query_string)."\r\n";

		$http_request .= "User-Agent: MNM (compatible; mOOtion 1.0; http://mOOtion.com) ";

		$http_request .= "\r\n\r\n";

		$http_request .= $query_string;

		if ( '' == $trackback_url['port'] )

			$trackback_url['port'] = 80;

		$fs = @fsockopen($trackback_url['host'], $trackback_url['port'], $errno, $errstr, 5);

			if($fs && ($res=@fputs($fs, $http_request)) ) {

			/*********** DEBUG **********

		$debug_file = '/tmp/trackback.log';

		$fp = fopen($debug_file, 'a'); 

		fwrite($fp, "\n*****\nRequest:\n\n$http_request\n\nResponse:\n\n");

		while(!@feof($fs)) {

			fwrite($fp, @fgets($fs, 4096));

		}

		fwrite($fp, "\n\n");

		fclose($fp);

			/*********** DEBUG ************/

			@fclose($fs);

				$this->status='ok';

				$this->store();

				return true;	

			}

			$this->status='error';	

			$this->store();

		return $false;

	} // Trackback->send_meneameoriginal()





	// Trackback - send with curl library.

	

	function send_withcurl () {



		$title = urlencode($this->title);

		$excerpt = urlencode($this->content);

		$blog_name = urlencode('Mootion.com');

		$tb_url = $this->url;

		$url = urlencode(get_permalink($this->link));

		

		$trackback_url = parse_url($this->url);



		$query_string = "charset=UTF-8&title=$title&url=$url&blog_name=$blog_name&excerpt=$excerpt" ;





		$ch = curl_init();

		curl_setopt ($ch, CURLOPT_URL, $tb_url );

		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt ($ch, CURLOPT_HEADER, 1);

		curl_setopt ($ch, CURLOPT_VERBOSE,1);

		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5); // 5s = una eternidad

		curl_setopt ($ch, CURLOPT_POST, true);

		curl_setopt ($ch, CURLOPT_POSTFIELDS, $query_string );

		curl_setopt ($ch, CURLOPT_USERAGENT, 'Mootion (mootion-trackback; 1.0 ; http://blog.mootion.com/?p=28)');

		$this->html = curl_exec($ch);

		curl_close($ch);

		

		/** DEBUG **/

		$debug_file = '/home/igponce/APPTMP/trackback.log';

		$fp = fopen($debug_file, 'a'); 

		fwrite($fp, "\n*****\nRequest:\n\n$query_string\n\nResponse:\n\n $this->html");

		fwrite($fp, "\n\n");

		fclose($fp);

				



	} // Trackback->send_withcurl()



} // class Trackback



?>

