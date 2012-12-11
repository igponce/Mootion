<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>

	include('config.php');
	include(mnminclude.'localization_en.php');

	header('Content-Type: text/plain; charset=UTF-8');
	$type=$_REQUEST['type'];
	$name=$_GET["name"];
	#echo "$type, $name...";
	switch ($type) {
		case 'username':
			if (strlen($name)<3) {
				echo $err_name_too_short;
				return;
			}
			if (!preg_match('/^[a-z0-9_\-\.@]+$/i', $name)) {
				echo $err_invalid_character;
				return;
			}
			if(user_exists($name)) {
				echo $err_user_already_exists;
				return;
			}
			echo "OK";
			break;
		case 'email':
			if (!check_email($name)) {
				echo $err_invalid_emailaddr;
				return;
			}
			if(email_exists($name)) {
				echo $err_duplicate_emailaddr;
				return;
			}
			echo "OK";
			break;
			default:
				echo "KO $type";
	}
?>
