<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// Inigo Gonzalez <igponce at gmail dot com>
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

    include('../config.php');
    include(mnminclude.'html1.php');
    include(mnminclude.'link.php');
    include(mnminclude.'trackback.php');
    include(mnminclude.'localization_en.php');
        
// Trackback Test...

if ( $_REQUEST ['id'] ) {
    
    // buscamos en la Base de datos.
    
    $link = new Link;
    $id = $_REQUEST['id'];
    $link->id = $id;
    echo 'ID: '.$link->id .'<br/>';

    $link->read();
    // $link->print_summary();

    echo '<h1>' . $link->title . '</h1>';
    echo '<b>Link_url:</b>'. $link->url . '<br/>';
    echo '<b>Link_viaurl:</b>'. $link->viaurl . '<br/>';
    echo '<hr>';
    
    echo '<h2>Trackback</h2>';
    echo '';

    // do_trackbacks();
    
	echo '<h2>trackbacks</h2>';
	$trackbacks = $db->get_col("SELECT trackback_id FROM trackbacks WHERE trackback_link_id=$id AND trackback_type='in' ORDER BY trackback_date DESC");
	if ($trackbacks) {
		echo '<ul>';
		require_once(mnminclude.'trackback.php');
		$trackback = new Trackback;
		foreach($trackbacks as $trackback_id) {
			$trackback->id=$trackback_id;
			$trackback->read();
			echo '<li><a href="'.$trackback->url.'" title="'.htmlspecialchars($trackback->content).'">'.$trackback->title.'</a></li>';
		}
		echo "</ul>\n";
	}
	else {
		echo '<ul>';
		echo '<li>'. $msg_notrackbacks .'</li></ul>';
	}
        
        if ( $_REQUEST['doit'] == '1' ) {
            
            // Hay que hacer trackback.
            echo '<tt>Trackback Requested....</tt><br/>';
            // $link->get($link->viaurl); // fetch the web page.
            // echo 'Trackback URL: ' . $link->trackback . '<br/>';
            // echo 'HTML:<br/>' . $link->html;
            
            // Mandar Trackback.
            
		$trackres = new Trackback;
		$trackres->url='http://exocert.dreamhosters.com/wp/wp-trackback.php?p=10';
		$trackres->link=$link->id;
		$trackres->title=$link->title;
		$trackres->author=$link->author;
		$trackres->content=$link->content;
		$res = $trackres->send();
                
                $trackres->dump();
                
                echo 'Trackback Result: <tt>:' . $res .'</tt><br/>';
                echo 'Server Output:<br><tt>' . htmlspecialchars ( $trackres->html ) . '</tt>';
        }
    


} else {
    // Errorcillo
    echo '<h1>Usage: tracktest.php?id=______</h1>';
}

?>
