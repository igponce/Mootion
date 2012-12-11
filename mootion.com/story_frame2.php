<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp mootion com> for mOOtion

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');


$globals['ads'] = true;

if(is_numeric($_REQUEST['id'])) {

	$link = new Link;
	$link->id=$_REQUEST['id'];
	$link->read();

	// Set globals
	$globals['link_id']=$link->id;
	$globals['category_id']=$link->category;
	$globals['category_name']=$link->category_name();
	
	// Preparamos el frame en el que mostraremos la
	// entrada en mootion.com y la URL destino.

	global $current_user, $dblang, $globals;

	header("Content-type: text/html; charset=utf-8");
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$dblang.'" lang="'.$dblang.'">' . "\n";
	echo '<head>' . "\n";
	echo "<title>mOOtion / ". $link->title ."</title>\n";
	echo '<meta name="generator" content="mOOtion" />' . "\n";
	echo '<meta name="keywords" content="'.$globals['tags'].'" />' . "\n";
	echo '<link rel="icon" href="/favicon.ico" type="image/x-icon" />' . "\n";

	header("Content-type: text/html; charset=utf-8");
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$dblang.'" lang="'.$dblang.'">' . "\n";
	echo '<head>' . "\n";
	echo "<title>mOOtion / ". $title ."</title>\n";
	echo '<meta name="generator" content="mOOtion" />' . "\n";
	echo '<meta name="keywords" content="'.$globals['tags'].'" />' . "\n";
	echo '<link rel="icon" href="/favicon.ico" type="image/x-icon" />' . "\n";


        echo '<div id="hoverdiv">HOVER';
        $link->print_shake_box;
        echo '</div>';
	
	echo '<frameset rows="*" frameborder="no" border="0" framespacing="0" cols="*">' . "\n";
	// echo '<frame name="arriba" scroll="NO" noresize src="story_mini.php?id='. $id .'">' ." \n";
	echo '<frame name="abajo" src="'. $link->url .'">' . "\n";
	echo '<noframes><body></body></noframes>' . "\n";
	echo '</frameset>';
        // Include tracking (analytics / statcounter code here)
	@include('ads/googleanalytics.inc');
        echo '</body></html>';

}

// else redirect ( http://mootion.com )

?>
