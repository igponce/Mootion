<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
//              http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'ts.php');
include(mnminclude.'localization_en.php');

do_header("Register", "post");
do_navbar("Register");

echo '<div id="genericform-contents">'."\n";
echo '<div id="genericform">'."\n";

if(isset($_POST["process"])) {
	switch ($_POST["process"]) {
		case 1:
			do_register1();
			break;
		case 2:
			do_register2();
			break;
	}
} else {
	do_register0();
}

echo '</div>' . "\n";
echo '</div>' . "\n";
do_footer();
exit;

function do_register0() {
	//echo '<div id="formz">' . "\n";
	global	$msg_forgottenpassword, $msg_signup, $msg_user, $msg_signup, $msg_verify,
		$msg_emailhlptxt, $btn_verifydata, $msg_password, $msg_passwordretype,
		$msg_passhlptxt, $msg_passwordrecovery, $msg_signmeup;

	echo '<div class="recoverpass" align="center"><h4><a href="login.php?op=recover">'. $msg_forgottenpassword .'</a></h4></div>';

	echo '<form action="/register.php" method="post" id="thisform">' . "\n";
	echo '<fieldset>' . "\n";
	echo '<legend><span class="sign">' . $msg_signup . '</span></legend>' . "\n";
	echo '<p class="l-top"><label for="name">' . $msg_user . ':</label><br />' . "\n";

	echo '<input type="text" name="username" id="name" value="" onkeyup="enablebutton(this.form.checkbutton1, this.form.submit, this)" size="25" tabindex="1"/>' . "\n";
	echo '<span id="checkit"><input type="button" name="checkbutton1" id="checkbutton1" disabled="disabled" value="'. $btn_verifydata .'" onclick="checkfield(\'username\', this.form, this.form.username)"/></span>' . "\n";
	echo '<br/><span id="usernamecheckitvalue"></span></p>' . "\n";

	echo '<p class="l-mid"><label for="email">email:</label><br />' . "\n";
	echo $msg_emailhlptxt .' <br />';
	echo '<input type="text" id="email" name="email" value=""  onkeyup="enablebutton(this.form.checkbutton2, this.form.submit, this)" size="25" tabindex="2"/>' . "\n";
		echo '<input type="button" name="checkbutton2" id="checkbutton3" disabled="disabled" value="'. $btn_verifydata .'" onclick="checkfield(\'email\', this.form, this.form.email)"/>' . "\n";
	echo '<br/><span id="emailcheckitvalue"></span></p>' . "\n";

	echo '<p class="l-mid"><label for="password">' . $msg_password . ':</label><br />' . "\n";
	echo $msg_passhlptxt .' <br />';
	echo '<input type="password" id="password" name="password" size="25" tabindex="3"/></p>' . "\n";

	echo '<p class="l-mid"><label for="verify">' . $msg_passwordretype . ': </label><br />' . "\n";
	echo '<input type="password" id="verify" name="password2" size="25" tabindex="4"/></p>' . "\n";


	echo '<p class="l-bot"><input type="submit" disabled="true" name="submit" value="'. $msg_signmeup .'" class="log2" tabindex="6" /></p>' . "\n";
	echo '<input type="hidden" name="process" value="1"/>' . "\n";

	echo '</fieldset>' . "\n";
	echo '</form>' . "\n";
	//echo '</div>' . "\n";
}

function do_register1() {
	global $db, $globals;
	global $err_usernametooshort, $err_usernameinvalichars, $err_usernameexists,
	       $err_emailinvalid, $err_emailduplicated, $err_passtooshort,
	       $err_passmismatch, $btn_goback, $btn_continue, $sign_validation,
	       $err_registerbot;

	$error = false;

	echo "\n\n<!-- error: " . $error . " -->\n\n";
	if(!isset($_POST["username"]) || strlen($_POST["username"]) < 3) {
		register_error($err_usernametooshort . "<!-- shortusername -->");
		$error=true;
	}
		echo "\n\n<!-- error: " . $error . " -->\n\n";
	if(!preg_match('/^[a-zA-Z0-9_\-\.@]+$/', $_POST["username"])) {
		register_error($err_usernameinvalichars. "<!-- username invalid chars -->");
		$error=true;
	}
		echo "\n\n<!-- error: " . $error . " -->\n\n";
	if(user_exists(trim($_POST["username"])) ) {
		register_error($err_usernameexists . "<!-- username exists -->");
		$error=true;
	}
		echo "\n\n<!-- error: " . $error . " -->\n\n";
	if(!check_email(trim($_POST["email"]))) {
		register_error($err_emailinvalid . "<!-- Wrong email -->");
		$error=true;
	}
		echo "\n\n<!-- error: " . $error . " -->\n\n";
	if(email_exists(trim($_POST["email"])) ) {
		register_error($err_emailduplicated . "<!-- Duplicated email-->");
		$error=true;
	}
		echo "\n\n<!-- error: " . $error . " -->\n\n";

	if(strlen($_POST["password"]) < 5 ) {
		register_error($err_passtooshort . "<!-- Short Passwd -->");
		$error=true;
	}
		echo "\n\n<!-- error: " . $error . " -->\n\n";
	if($_POST["password"] !== $_POST["password2"] ) {
		register_error($err_passmismatch . "<!-- Pass and confirm don't match -->");
		$error=true;
	}
		echo "\n\n<!-- error: " . $error . " -->\n\n";
	
	$user_ip = $globals['user_ip'];
	$from = time() - 86400*2;
	$last_register = $db->get_var("select count(*) from users where user_date > from_unixtime($from) and user_ip = '$user_ip'");
	if($last_register > 0) {
		register_error ($err_registerbot);
		$error=true;
	}
	
	echo "\n\n<!-- error: " . $error . " -->\n\n";

	if ($error) return; // {
//		echo '<div class="genericformtxt"><label><a href="javascript:history.go(-1)">'. $btn_goback .'</a></label></div>';
//		return;
//	}
	
	echo '<br style="clear:both" />';
// 	echo '<div id="contents-wide">' . "\n";
	//echo '<div id="capform">' . "\n";

	echo '<form action="/register.php" method="post" id="thisform">' . "\n";
	echo '<fieldset><legend><span class="sign">'.$sign_validation.'</span></legend>'."\n";
	ts_print_form();
	/*
	echo  "introduzca el código que ve en la imagen:" ."<br/><br/>\n";
	echo '<table><tr><td>';
	$ts_random=rand();
	echo '<input type="hidden" name="ts_random" value="'.$ts_random.'" />';
	echo '<img src="/images/code.php?ts_random='.$ts_random.'" class="ch2" /></td>';
	echo '<tr><td><input type="text" size="20" name="ts_code" /></td></tr></table><br/>'."\n";
	*/
	echo '<input type="submit" name="submit" value="'. $btn_continue .'" />';
	echo '<input type="hidden" name="process" value="2" />';
	echo '<input type="hidden" name="email" value="'.$_POST["email"].'" />';
	echo '<input type="hidden" name="username" value="'.$_POST["username"].'" />';
	echo '<input type="hidden" name="password" value="'.$_POST["password"].'" />';
	echo '</fieldset></form>'."\n";
	//echo '</div>';
// 	echo '</div>'."\n";
}

function do_register2() {
	global $db, $current_user, $globals;
	global $err_invalidcode, $err_databaseinsert, $msg_signup, $err_user_already_exists;
	
	if ( !ts_is_human()) {
		register_error( $err_invalidcode );
		return;
	}
	$username=trim($_POST['username']);
	$password=trim($_POST['password']);
	$email=trim($_POST['email']);
	$user_ip = $globals['user_ip'];
	if (!user_exists($username)) {
		if ($db->query("INSERT INTO users (user_login, user_email, user_pass, user_date, user_ip) VALUES ('$username', '$email', '$password', now(), '$user_ip')")) {
			//register_error( "Usuario creado" .'.<a href="login.php">'. 'Login'.'</a>');
/***
			if($current_user->Authenticate($username, $password, false) == false) {
				register_error("Error insertando usuario en la base de datos");
			} else {
****/
			echo '<fieldset>'."\n";
			echo '<legend><span class="sign">'.$msg_signup.'</span></legend>'."\n";
			require_once(mnminclude.'user.php');
			$user=new User();
			$user->username=$username;
			if(!$user->read()) {
				register_error($err_databaseinsert);
			} else {
				require_once(mnminclude.'mail.php');
				// $sent = send_recover_mail($user);
				$sent = send_newuser_mail ($user);
			}
			//header('Location: ./user.php?login='.$username);
			echo '</fieldset>'."\n";
		} else {
			register_error($err_databaseinsert);
		}
	} else {
		register_error($err_user_already_exists);
	}
}

function register_error($message) {
	echo '<div class="form-error">';
	//echo '<h3>'."Noticia".'</h3>';
	echo "<p>$message</p>";
	echo "</div>\n";
}

?>
