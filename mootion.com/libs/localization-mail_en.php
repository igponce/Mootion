<?
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// Inigo Gonzalez <igponce at corp dot mootion dot com>
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// From: mail.php

$email_subject		= 'Password Recovery / Check from ';
$email_fragment1 	= 'You can log into mOOtion without a password for two hours with this URL:';
$email_fragment2 	= 'Once logged in, you can change your password from your mOOtion profile';
$email_fragment3 	= 'password change requested from the IP Address ';
$email_signature 	= "-- \n The mOOtion.com Crew  ( http://mootion.com )";
$email_headers          = 'Content-Type: text/plain; charset="utf-8"'."\n"
                        . 'X-Mailer: mOOtion.com/PHP/' . phpversion(). "\n"
                        . 'From: mOOtion accounts <noreply@'.get_server_name().">\n";
?>
