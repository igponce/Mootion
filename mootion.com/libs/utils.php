<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

require_once (mnminclude.'check_behind_proxy.php');
require (mnminclude.'localization-utils_en.php');

$globals['user_ip'] = check_ip_behind_proxy();

function user_exists($username) {
	global $db;
	$res=$db->get_var("SELECT count(*) FROM users WHERE user_login='$username'");
	if ($res>0) return true;
	return false;
}

function email_exists($email) {
	global $db;
	$res=$db->get_var("SELECT count(*) FROM users WHERE user_email='$email'");
	if ($res>0) return $res;
	return false;
}

function check_email($email) {
	return preg_match('/^[a-z0-9_\-\.]+@[a-z0-9_\-\.]+\.[a-z]{2,4}$/', $email);
}
/////

function xss_clean($data)
{
	// Fix &entity\n;
	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

	do {
        // Remove really unwanted tags
	$old_data = $data;
	$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	} while ($old_data !== $data);
			

	// we are done...
	return $data;
}

////

function txt_time_diff($from, $now=0){
	// ugly but works (from localization_LANG.php)
	global $msg_days, $msg_hours, $msg_minutes, $msg_secondsago,
	       $msg_day,  $msg_hour,  $msg_minute; 
	
	$txt = '';
	if($now==0) $now = time();
	$diff=$now-$from;
	$days=intval($diff/86400);
	$diff=$diff%86400;
	$hours=intval($diff/3600);
	$diff=$diff%3600;
	$minutes=intval($diff/60);

	if($days>1) $txt  .= " $days ".$msg_days;
	else if ($days==1) $txt  .= " $days ". $msg_day;

	if($hours>1) $txt .= " $hours ". $msg_hours;
	else if ($hours==1) $txt  .= " $hours ". $msg_hour;

	if($minutes>1) $txt .= " $minutes ". $msg_minutes;
	else if ($minutes==1) $txt  .= " $minutes ". $msg_minute;

	if($txt=='') $txt = ' '. $msg_fewseconds . ' ';
	return $txt;
}

function txt_shorter($string, $len=80) {
	if (strlen($string) > $len)
		$string = substr($string, 0, $len-3) . "...";
	return $string;
}

function save_text_to_html($string) {
	$string = strip_tags(trim($string));
	$string= htmlspecialchars($string);
	$string= text_to_html($string);
	$string = preg_replace("/[\r\n]{2,}/", "<br /><br />\n", $string);
	return $string;
}

function text_to_html($string) {
	return preg_replace('/([hf][tps]{2,4}:\/\/[^ \t\n\r\]]+[^ .\t,\n\r\(\)"\'\]])/', '<a href="$1" rel="nofollow">$1</a>', $string);
}

function check_integer($which) {
	if (intval($_REQUEST[$which])>0) {
		return intval($_REQUEST[$which]);
	} else {
		return false;
	}
}

function get_current_page() {
	if(($var=check_integer('page'))) {
		return $var;
	} else {
		return 1;
	}
    // return $_GET['page']>0 ? $_GET['page'] : 1;
}

function get_search_clause($option='') {
	if($option == 'boolean') {
		$mode = 'IN BOOLEAN MODE';
	}
	if(!empty($_REQUEST['search'])) {
		# Evitar CSS 
		# OLD, with CSS $words = $_REQUEST['search'];
		$words = filter_var ( xss_clean ( $_REQUEST['search'] ) , FILTER_SANITIZE_SPECIAL_CHARS );

		if ($_REQUEST['tag'] == 'true') {
			$where .= " AND MATCH (link_tags) AGAINST ('$words' $mode) ";
		} else {
			$where = " AND MATCH (link_url, link_url_title, link_title, link_content, link_tags) AGAINST ('$words' $mode) ";
		}
		if (!empty($_REQUEST['from'])) {
			$where .=  " AND link_date > from_unixtime(".$_REQUEST['from'].") ";
		}
		return $where;
	} else {
		return false;
	}
}

function get_date($epoch) {
    return date("Y-m-d", $epoch);
}

function get_date_time($epoch) {
	    return date("Y-m-d H:i", $epoch);
}

function get_server_name() {
	global $server_name;
	if(empty($server_name)) 
		return $_SERVER['SERVER_NAME'];
	else
		return $server_name;
}

function get_permalink($id) {
	return "http://".get_server_name()."/story.php?v=$id";
}

function get_trackback($id) {
	return "http://".get_server_name()."/trackback.php?id=$id";
}

function utf8_substr($str,$start)
{
	preg_match_all("/./su", $str, $ar);
 
	if(func_num_args() >= 3) {
		$end = func_get_arg(2);
		return join("",array_slice($ar[0],$start,$end));
	} else {
		return join("",array_slice($ar[0],$start));
	}
}

// Used to get the text content for stories and comments
function clean_text($string, $wrap=0, $replace_nl=true, $maxlength=0) {
	$string = stripslashes(trim($string));
	$string = html_entity_decode($string, ENT_COMPAT, 'UTF-8');
	// Replace two "-" by a single longer one, to avoid problems with xhtml comments
	$string = preg_replace('/--/', '–', $string);
	if ($wrap>0) $string = wordwrap($string, $wrap, " ", 1);
	if ($replace_nl) $string = preg_replace('/[\n\t\r]+/s', ' ', $string);
	if ($maxlength > 0) $string = mb_substr($string, 0, $maxlength);
	return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
}

?>
