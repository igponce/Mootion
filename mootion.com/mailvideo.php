<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// Iñigo González <igponce at corp dot mootion dot com>
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'mail.php');
// include(mnminclude.'localization_en.php');

// Send an email with this video...

if (is_numeric($_REQUEST["id"])) {
    
    $phase = $_REQUESET["phase"];
    
    switch ( $phase ) {
        
        case 2 :
            mail_sent ();
        case 1 :
            if ( valid_mailform () )
                send_email ();
        break;
    
        default:
            /* initial phase: everything (almost) empty */
        break;
        
    }
    
    if ( $current_user->authenticated ) {
        // Authenticated user? -> use that email.
        $fromemail = $current_user->email;
        $mailtosender = false;
        $required_fromaddr = '';
    } else {
        $required_fromaddr = 'required';
        $mailtosender = true;
    }

    echo '<html><head><title>Share this video</title></head>';
    echo '<body>';
    echo '<h1>Share this video</h1>';
    
    echo '<form name="sendvideo" action="post">';
    
    echo '<label class="'.$required_fromaddr.'mailaddr">Your e-mail</h2>';
    echo '<input type="text" name="fromaddress" lenght="64"/>';
    
    echo '<label class="requiredmailaddr">e-mail address:</h2>';
    echo '<input type="text" name="dstaddress" lenght="64"/>';
    
    echo '<label class="requiredtext">message</h2>';
    echo '<input type="text" name="messagetext" lenght="4096">'.$message.'</textarea>';
    
    echo '<input type="button" action="submit">send</input>&nbsp;';
    echo '<input type="button" actuin="clear">cancel</input>';
    
    echo '</form>';
    echo '</html></body>';

} else {
    header ('Location: /');
}

?>