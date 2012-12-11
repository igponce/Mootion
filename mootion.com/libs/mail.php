<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
//      http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp mootion com> for mOOtion

function send_recover_mail ($user) {
	require_once(mnminclude.'user.php');
	// include (mnminclude.'localization-mail_en.php');
	
	global $site_key, $globals;
	global $msg_emailsent ;
	// , $email_fragment1, $email_fragment2, $email_fragment3,
	//       $email_signature, $email_subject, $email_headers;

$email_headers          = 'Content-Type: text/plain; charset="utf-8"'."\n"
                        . 'X-Mailer: mOOtion.com/PHP/' . phpversion(). "\n"
                        . 'From: mOOtion accounts <noreply@'.get_server_name().">\n";
$email_subject		= ' Password Recovery/Change';
$email_fragment1 	= "mOOtion.com - mooving pictures\n\n   Now you can log into mOOtion without a password\n   for the next two hours following this URL:";
$email_fragment2 	= '   Once logged in, you can change your password from your mOOtion profile.';
$email_fragment3 	= "PS:\n  Password change requested from IP Address ";
$email_signature 	= "   Thank you for using mOOtion\n--\n The mOOtion.com Crew  ( http://mootion.com )\n"; // " Personal Data Protection: http://mootion.com/faq.php#nobigbrother";
	
	$now = time();
	$key = md5($user->id.$user->pass.$now.$site_key.get_server_name());
	$url = 'http://'.get_server_name().'/profile.php?login='.$user->username.'&t='.$now.'&k='.$key;
	$to      = $user->email;

	$subject = get_server_name() . $email_subject;

	$message = $email_fragment1 .
	           "\n\n     $url\n\n" .
		   $email_fragment2 . "\n\n" .
		   $email_fragment3 . $globals['user_ip'] . "\n\n" .
		   $email_signature;
		   
	$message = wordwrap($message, 70);
	
	mail($to, $subject, $message, $email_headers);
	
	// debug only (embedded in page source)
	// echo "<!-- Debug:\nDebug HDR:" . $email_headers . "\nSUBJECT:\n" . $subject . "\nMESSAGE:\n" . $message . "\n\n-->\n";
	
	echo '<p><strong>' . $msg_emailsent . '</strong></p>';
	return true;
}

// Only for signup !!
function send_newuser_mail ($user) {
	
	require_once(mnminclude.'user.php');
	// include (mnminclude.'localization-mail_en.php');
	
	global $site_key, $globals;
	global $msg_newuser_emailsent ;
	// , $email_fragment1, $email_fragment2, $email_fragment3,
	//       $email_signature, $email_subject, $email_headers;

$email_headers          = 'Content-Type: text/plain; charset="utf-8"'."\n"
                        . 'X-Mailer: mOOtion.com/PHP/' . phpversion(). "\n"
                        . 'From: mOOtion accounts <noreply@'.get_server_name().">\n";
$email_subject		= ' Welcome to mOOtion.com !!!';
$email_fragment1 	= "                            mOOtion.com - mooving pictures\n\n   Welcome" .
			  "   mOOtion is a global community to share videos on the Internet\n".
			  "As a registered user, you can send us your videos and publish your comments.\n".
			  "Once you submit a video, other users can vote (mOOve) it;\n".
			  "if your video gets enough votes, it will get published (promoted) into mOOtion home page.\n".
			  "   For your convenience, we've sent you this special URL valid for two hours where you can update/change your mOOtion profile:\n";
$email_fragment2 	= '';
$email_fragment3 	= "ps:\n  User signup requested from the IP Address ";
$email_signature 	= "   Thank you for using mOOtion\n--\n The mOOtion.com Crew  ( http://mootion.com )\n"; // " Personal Data Protection: http://mootion.com/faq.php#nobigbrother";
	
	$now = time();
	$key = md5($user->id.$user->pass.$now.$site_key.get_server_name());
	$url = 'http://'.get_server_name().'/profile.php?login='.$user->username.'&t='.$now.'&k='.$key;
	$to      = $user->email;

	$subject = get_server_name() . $email_subject;

	$message = $email_fragment1 .
	           "\n\n     $url\n\n" .
		   $email_fragment2 . "\n\n" .
		   $email_fragment3 . $globals['user_ip'] . "\n\n" .
		   $email_signature;
		   
	$message = wordwrap($message, 70);
	
	mail($to, $subject, $message, $email_headers);
	
	// debug only (embedded in page source)
	// echo "<!-- Debug:\nDebug HDR:" . $email_headers . "\nSUBJECT:\n" . $subject . "\nMESSAGE:\n" . $message . "\n\n-->\n";
	
	echo '<p><strong>' . $msg_newuser_emailsent . '</strong></p>';
	return true;
}

function send_video_mail ($user, $id) {
	
	require_once(mnminclude.'user.php');
	require_once(mnminclude.'link.php');

	// Subject: [mOOtion] Video Name
	// 
	// Your Friend [fulanitow] has found this video you might find interesting:
        //
	//    VIDEO NAME:
	//    http://mootion.com/story.php?id=el_id
}

?>
