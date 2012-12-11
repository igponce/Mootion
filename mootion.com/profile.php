<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
//              http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>
// I still don't know *if* showing user's votes is confidential data or not...
// May disappear on mOOtion - iteration2.

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'user.php');
include(mnminclude.'localization_en.php');


// User recovering her password
if (!empty($_GET['login']) && !empty($_GET['t']) && !empty($_GET['k'])) {
	$time = intval($_GET['t']);
	$key = $_GET['k'];

	$user=new User();
	$user->username=$_GET['login'];
	if($user->read()) {
		$now = time();
		$key2 = md5($user->id.$user->pass.$time.$site_key.get_server_name());
		//echo "$now, $time; $key == $key2\n";
		if ($time > $now - 7200 && $time < $now && $key == $key2) {
			$db->query("update users set user_validated_date = now() where user_id = $user->id and user_validated_date is null");
			$current_user->Authenticate($user->username, $user->pass);
		}
	}
}
//// End recovery

if ($current_user->user_id > 0 && $current_user->authenticated) {
		$login=$current_user->user_login;
} else {
	header("Location: ./login.php");
	die;
}

$user=new User();
$user->username = $login;
if(!$user->read()) {
	echo "error 2";
	die;
}

do_header( $header_edit_profile. ': ' . $login);
do_navbar('<a href="/topusers.php">' . $msg_users . '</a> &#187; <a href="/user.php">' . $user->username .'</a> &#187; ' . $msg_edit);

show_profile();

do_footer();


function show_profile() {
	global $user;
	// ugly
	global $msg_change_your_profile, $msg_user_realname, $msg_user_email, $msg_user_webpage,
	       $msg_user_passwarn, $msg_user_pass, $msg_user_passrepeat, $msg_user_updateprofile;


	save_profile();
	echo '<div id="genericform-contents"><div id="genericform"><fieldset><legend>';
	echo '<span class="sign">'. $msg_change_your_profile .'</span></legend>';

	echo '<form action="profile.php" method="post" id="thisform">';
	echo '<input type="hidden" name="process" value="1">';
	echo '<input type="hidden" name="user_id" value="'.$user->id.'">';

	echo '<p class="l-top"><label for="name" accesskey="1">'. $msg_user_realname.':</label><br/>';
	echo '<input type="text" name="names" id="names" tabindex="1" value="'.$user->names.'">';
	echo '</p>';

	echo '<p class="l-mid"><label for="name" accesskey="1">'. $msg_user_email .':</label><br/>';
	echo '<input type="text" name="email" id="email" tabindex="2" value="'.$user->email.'">';
	echo '</p>';

	echo '<p class="l-mid"><label for="name" accesskey="1">'. $msg_user_webpage .':</label><br/>';
	echo '<input type="text" name="url" id="url" tabindex="3" value="'.$user->url.'">';
	echo '</p>';

	
	echo '<p>'. $msg_user_passwarn .'</p>';

	echo '<p class="l-mid"><label for="password">' . $msg_user_pass . ':</label><br />' . "\n";
	echo '<input type="password" id="password" name="password" size="25" tabindex="4"/></p>' . "\n";

	echo '<p class="l-mid"><label for="verify">' . $msg_user_passrepeat . ': </label><br />' . "\n";
	echo '<input type="password" id="verify" name="password2" size="25" tabindex="5"/></p>' . "\n";


	
	echo '<p class="l-bottom"><input type="submit" name="save_profile" value="'. $msg_user_updateprofile .'" class="genericsubmit"></p>';
	echo "</form></fieldset></div></div>\n";
	
}

function save_profile() {
	global $user, $current_user, $globals;
	// ugly (localization)
	global $err_invalid_email, $err_user_passdontmatch, $msg_user_passchanged, $msg_user_updated;
	$errors = 0; // benjami: control added (2005-12-22)
	
	if(!isset($_POST['save_profile']) || !isset($_POST['process']) || $_POST['user_id'] != $current_user->user_id ) return;
	if(!check_email(trim($_POST['email']))) {
		echo '<p class="form-error">'. $err_invalid_email .'</p>';
		$errors = 1;
	} else {
		$user->email=trim($_POST['email']);
	}
	$user->url=trim($_POST['url']);
	$user->names=trim($_POST['names']);
	if(!empty($_POST['password']) || !empty($_POST['password2'])) {
		if($_POST['password'] !== $_POST['password2']) {
			echo '<p class="form-error">'. $err_user_passdontmatch .'</p>';
			$errors = 1;
		} else {
			$user->pass=trim($_POST['password']);
			echo '<p>'. $msg_user_passchanged .'</p>';
		}
	}
	if (!$errors) { // benjami: "if" added (2005-12-22)
		if (empty($user->ip)) {
			$user->ip=$globals['user_ip'];
		}
		$user->store();
		$user->read();
		$current_user->Authenticate($user->username, $user->pass);
		echo '<p class="form-act">'. $msg_user_updated .'</p>';
	}
}

?>
