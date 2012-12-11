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
include(mnminclude.'localization_en.php');

do_header($hdr_login);
do_navbar($hrd_login);

if($_GET["op"] === 'logout') {
	$current_user->Logout($_REQUEST['return']);
}


echo '<div id="genericform-contents">'."\n";
echo '<div id="genericform">'."\n";

if($_GET["op"] === 'recover' || !empty($_POST['recover'])) {
	do_recover();
} else {
	do_login();
}

echo '</div>'."\n";
echo '</div>'."\n";

do_footer();


function do_login() {
	global $current_user;
	global $hdr_login, $msg_forgottenpassword, $msg_login, $err_invalidauth,
	       $msg_user, $msg_passwd, $msg_rememberme, $msg_register_newuser;

	echo '<div class="recoverpass" align="center"><h4><a href="register.php">'.$msg_register_newuser.'</a> | <a href="login.php?op=recover">'. $msg_forgottenpassword .'</a></h4></div>';
	
	echo '<form action="login.php" id="thisform" method="post">'."\n";

	echo '<fieldset>'."\n";
	echo '<legend><span class="sign">'. $msg_login .'</span></legend>'."\n";

	if($_POST["processlogin"] == 1) {
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$persistent = $_POST['persistent'];
		if($current_user->Authenticate($username, $password, $persistent) == false) {
			recover_error( $err_invalidauth );
		} else {
			if(strlen($_REQUEST['return']) > 1) {
				header('Location: '. urldecode ($_REQUEST['return']));
			} else {
				header('Location: ./');
			}
			die;
		}
	}

	echo '<p class="l-top"><label for="name">'. $msg_user .':</label><br />'."\n";
	echo '<input type="text" name="username" size="25" tabindex="1" id="name" value="'.$username.'" /></p>'."\n";
	echo '<p class="l-mid"><label for="password">'. $msg_passwd .':</label><br />'."\n";
	echo '<input type="password" name="password" id="password" size="25" tabindex="2"/></p>'."\n";
	echo '<p class="l-mid"><label for="remember">'. $msg_rememberme .': </label><input type="checkbox" name="persistent" id="remember" tabindex="3"/></p>'."\n";
	echo '<p class="l-bot"><input type="submit" value="login" class="genericsubmit" tabindex="4" />'."\n";
	echo '<input type="hidden" name="processlogin" value="1"/></p>'."\n";
	echo '<input type="hidden" name="return" value="'. $_REQUEST['return'] .'"/>'."\n";
	// echo '<h2>Return to:</h2><p class="1-bot">'.$_REQUEST['return']."</p>\n";
	echo '</fieldset>'."\n";
	echo '</form>'."\n";
}

function do_recover() {
	global $site_key, $globals;
	global $msg_passwordrecovery, $err_invalidcaptcha, $err_inexistentuser,
	       $err_accountdisabled, $msg_inexistentuser, $msg_willreceiveemail,
	       $msg_user, $msg_receiveemail;
	
	require_once(mnminclude.'ts.php');

	echo '<fieldset>'."\n";
	echo '<legend><span class="sign">'. $msg_passwordrecovery .'</span></legend>'."\n";

	if(!empty($_POST['recover'])) {
		if (!ts_is_human()) {
			recover_error( $err_invalidcaptcha );
		} else {
			require_once(mnminclude.'user.php');
			$user=new User();
			$user->username=$_POST['username'];
			if(!$user->read()) {
				recover_error( $err_inexistentuser );
				return false;
			}
			if($user->pass == 'SPAMMER') {
				recover_error( $err_accountdisabled );
				return false;
			}
			require_once(mnminclude.'mail.php');
			$sent = send_recover_mail($user);
		}
	}
	if (!$sent) {
		echo '<form action="/login.php" id="thisform-recover" method="post">'."\n";
		echo '<label for="name">'. $msg_user .':</label><br />'."\n";
		echo '<input type="text" name="username" size="25" tabindex="1" id="name" value="'.$username.'" />'."\n";
		echo '<p class="nobold">'. $msg_willreceiveemail .'</p>';
		echo '<input type="hidden" name="recover" value="1"/>'."\n";
		echo '<input type="hidden" name="return" value="'.$_REQUEST['return'].'"/>'."\n";
		ts_print_form();
		echo '<br /><input type="submit" value="'. $msg_receiveemail .'" class="genericsubmit" />'."\n";
		echo '</form>'."\n";
	}
	echo '</fieldset>'."\n";
}

function recover_error($message) {
	echo '<div class="form-error">';
	echo "<p>$message</p>";
	echo "</div>\n";
}

?>
