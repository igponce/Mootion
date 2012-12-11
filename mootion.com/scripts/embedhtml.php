<?
include('../config.php');
include(mnminclude.'link.php');
include(mnminclude.'videofarm.php');

header("Content-Type: text/html");

$now = time();

echo "Inicio: ".get_date_time($now)."<br/>\n";
echo "Buscando Links con htmlembed=null o htmlembed=''<br/>\n\n";


/*
 * Paso 1: Informar del estado de la BBDD
 */

$num_links = $db->get_var ("select count(*) from links;");
$num_embed = $db->get_var ("select count(*) from links where link_embedhtml not like '&lt;%' ;");

echo "<hr/>";
echo "Articulos Totales: $num_links <br/> \n";
echo "Embedhtml!=null  : $num_embed <br/> \n";
echo "<hr/>\n";

echo "<h2>Procesando los 5 primeros articulos con embedhtml vacio</h2>";

$links = $links = $db->get_results("SELECT link_id from links where link_embedhtml not like '&lt%';");
$rows = $db->num_rows;

foreach($links as $dblink) {
	$link = new Link;
	$link->id=$dblink->link_id;
	$link->read();
        
        process_videofarm ($link);
        
        echo "<h1>$link->title ($link->id)</h1>";
        echo html_entity_decode ($link->embedhtml, ENT_COMPAT);
        echo "<hr/>";
        
        $link->store();
	
	echo "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n<hr/>\n\n\n\n\n\n\n\n\n\n\n\n";
	sleep (2);
	
	/*
	* Solo comprobaciones
	* --------------------
	* 
	* $link2 = new Link;
	*
	* $link2->id = $link->id;
	* $link2->read();
	*
	* echo '<br/>Probando Link2:</br>';
	* echo html_entity_decode ($link2->embedhtml, ENT_COMPAT);
	* echo "<br/>Fin Prueba<br/><hr/>\n";
	* 
	*/
	
}

?>

