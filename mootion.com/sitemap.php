<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('config.php');
/*
include(mnminclude.'html1.php');
*/
include(mnminclude.'link.php');
include(mnminclude.'localization_en.php'); // may need it's own localization file ?
	
if(!empty($_REQUEST['rows'])) {
	$rows = $_REQUEST['rows'];
	if ($rows > 100) $rows = 30; //avoid abuses
} else $rows = 30;

/* cabecera del sitemap */

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<url>
   <loc>http://mootion.com</loc>
   <changefreq>daily</changefreq>
   <priority>1</priority>
</url>

<url>
   <loc>http://mootion.com/shakeit.php</loc>
   <changefreq>daily</changefreq>
   <priority>1</priority>
</url>

';


	$sql = "SELECT link_id from links order by link_id desc;";
	$link = new Link;
	$links = $db->get_col($sql);
	if ($links) {
	foreach($links as $link_id) {
		$link->id=$link_id;
		$link->read();
		
		$content = text_to_html($link->content);

		echo "	<url>\n";
		echo "		<loc>". $link->get_permalink() ."</loc>\n";
		echo "		<priority>0.2</priority>\n";
		echo "		<changefreq>weekly</changefreq>\n";
		echo "  </url>\n";
	   }
	}
		
echo '</urlset>';
?>
