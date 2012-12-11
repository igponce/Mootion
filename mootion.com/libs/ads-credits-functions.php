<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".


/*****
// banners and credits funcions: FUNCTIONS TO ADAPT TO YOUR CONTRACTED ADS AND CREDITS
*****/



function do_banner_top () { // top banner
	global $globals, $dblang;
//
// WARNING!
//
// IMPORTANT! adapt this section to your contracted banners!!
//
	echo '<div class="banner-01">' . "\n";
	echo '<div class="banner-01-c">'."\n";
	if($globals['external_ads'] && $globals['ads'])
		@include('ads/adrotator-top.inc');
	else
		@include('ads/meneame-01.inc');
		//echo '<br /><br /><strong>'. 'recuerda: ' .'</strong>'. 'encontrar&aacute;s ayuda en la secci&oacute;n' .' "<a href="./faq-'.$dblang.'.php">'. 'acerca de men&eacute;ame' .'</a>".'."\n";
	echo '</div>' . "\n";
	echo '</div>' . "\n";
}



function do_banner_right_a() { // side banner A
	global $globals;
//
// WARNING!
//
// IMPORTANT! adapt this section to your contracted banners!!
//
	if($globals['external_ads'] && $globals['ads']) {
		// echo '<li>' . "\n";
		// echo '<div class="banner-02">' . "<b>Advertisement</b><br/>\n";
		echo '<div class="banner-02">' . "<br/>\n";
		echo '<div class="banner-02-c">'."\n";
		@include('ads/hombrelobo.inc');
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		// echo '</li>' . "\n";
		echo "<!--ben_tmp-functions:do_banner_right_a-->\n";
	}
}


function do_credits() {

	global $dblang;
	
   ?>
	<br style="clear: both;">
	<div id="credits-strip">
	   <span class="credits-strip-text">
   <?php
	
	if ( ! preg_match ('/mootion.com$/',get_server_name() ) ) {
	   echo 'Powered by <a href="http://mootion.com" alt="mOOtion"><img src="./img/common/mootion-16px.png" alt="Mootion" border=0></a>' .
	        '&nbsp|&nbsp;Must Provide Source Code. See <a href="<a href="http://blog.mootion.com/?page_id=24#nosource">this page</a>. For details.';
	} else {
	   // IMPORTANT: legal note only for our servers, CHANGE IT!!
	   echo '<a href="http://mootion.com"><img src="./img/common/mootion-16px.png" alt="Mootion" border=0></a>&nbsp;|&nbsp;'.
	        '<a href="http://blog.mootion.com/?page_id=23">Legal</a>&nbsp;|&nbsp;<a href="http://blog.mootion.com/?page_id=24#nocode">Source code</a>';
	}
	
	echo '
	   </span> <!-- span credits-strip-text --> 
	   <span class="credits-strip-buttons">
	   </span>
	</div>
	<!--ben-tmp-functions:do_credits-->
	';
	// do_credits() ends here

}
?>
