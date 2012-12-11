<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Inigo Gonzalez <igponce at corp dot mootion dot com>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// PARA REHACER COMPLETAMENTE !!!

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');

if ( is_null ($_REQUEST['v'])) {
   die;
} else {
   $vid = $_REQUEST['v'];
}

if (is_numeric ($vid) ) {
	$link = new Link;
	$link->id=$vid; // $_REQUEST['id'];
	$link->read();
        
        if ($link->embedhtml != '') {
	    do_header ($link->title, 'embed');


	    // <a href="http://'.get_server_name().'/"><img src="img/es/logo01.png" border=0></a><br/>

	    echo '<div id=contents>';
            echo '<h1>Share this video</h1>';

	    // $link->print_summary();

	    echo '<h4><p>Copy the text from the box below and paste it into the html on your site.</p></h4>';

	    // embed box
	    echo '<div class = "news-summary"><textarea name="bodytext"  rows="5" cols="60"  id="bodytext" wrap=soft onfocus="selectInputText(this)" onclick="selectInputText(this)">';

	    $embedcode = $link->embedhtml;
	    $previewcode = $link->embedhtml;

	    if ( !ereg ('/^&([lL][tT]|[aA][mM][pP])/', $embedcode) ) {
		// Sometimes needed (ej: Google Video).
	        $embedcode = htmlspecialchars ( html_entity_decode ($embedcode ,ENT_COMPAT) );
	    }

	    $embedcode .= '<br/><a href="' . $link->get_permalink() .'">Add to your site</a><br/>';
	    $previewcode .= '<br/><a href="#">Add to your site</a><br/>';

	    $viahtml = '';

	    // Calcular el link viral del sitio que nos enlaza
	    if ( $_SERVER['HTTP_REFERER'] != '') {
		$viasite = '';
	        $viaurl = $_SERVER['HTTP_REFERER'];
		if ( strpos ($viaurl, 'mootion.com') == false ) {
		// no se enlaza desde mOOtion
	    	if ( preg_match ( '/http:\/\/([a-zA-Z0-9\.\-]*)\//', $viaurl,  $match) ) {
		    $viasite = $match[1];
		   $viahtml = '<a href="'.$viaurl.'">'.$viasite.'</a>&nbsp;';
		}
	    }
	}

    $vialabel = ( $viahtml=='' ? '' : 'Via: ');
    $embedcode .= $vialabel . $viahtml . '<a href="' . $link->get_permalink() . '">' . $link->title .'</a>&nbsp;</br/>';
    $previewcode .= $vialabel . $viahtml . '<a href="' . $link->get_permalink() . '">' . $link->title .'</a>&nbsp;</br/>';

echo $embedcode;
echo '</textarea>';

echo '<div id="preview"><h1>Preview</h1>';
echo $previewcode;
echo '</div>';

echo '<hr/><a href="#" onclick="self.close();return false;">Close Window</a></div>';

echo '</div>'; // div id=contents

	// do_sidebar();
	// do_footer();
	
        } else {
            do_header ('');
        }
} else {
    echo 'Location: /';
}
?>
