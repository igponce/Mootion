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

    header ("Share this video");
    echo '<html><head><title>Share this video</title></head>';
    echo '<body>';
    echo '<h1>Share this video</h1>';
    echo '<form name="sendvideo">';
    echo '<textarea id="fromaddress">';
    
    if ( $current_user->authenticated ) {
        // Authenticated user? -> use that email.
        echo $current_user->email;
    }
    
    echo '</textarea>';
    echo '<textarea id="toaddress">';
    
    if ($_REQUEST['phase']==2) {
        echo $toaddress;
    }

} else {
    header ('Location: /');
}

?>