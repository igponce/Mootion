<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// Portions Copyright (C) 2006 Inigo Gonzalez <igponce at corp dot mootion dot com>

	include('config.php');
	include(mnminclude.'html1.php');
	include(mnminclude.'link.php');
	include(mnminclude.'localization_en.php');

	$offset=(get_current_page()-1)*$page_size;
	$globals['ads'] = true;

	$search = get_search_clause();
	// Search all if it's a search
	if($search) 
		$from_where = "FROM links WHERE link_status!='discard' ";
	else
		$from_where = "FROM links WHERE link_status='published' ";
	if(($cat=check_integer('category'))) {
		$from_where .= " AND link_category=$cat ";
	}

	if($search) {
		do_header($msg_searchfor . '"'.htmlspecialchars($_REQUEST['search']).'"');
		do_navbar($msg_search);
		echo '<div id="contents">'; // benjami: repetit, no m'agrada, arreglar depres
		echo '<h2>'.$msg_searchresults. ' "'.htmlspecialchars($_REQUEST['search']).'" </h2>';
		$from_where .= $search;
		$order_by = '';
	} else {
		do_header($msg_lastpublished);
		do_navbar('');
		echo '<div id="contents">'; // benjami: repetit, no m'agrada, arreglar despres
		echo '<h2>'. $msg_lastvideos .'</h2>';
		$order_by = " ORDER BY link_published_date DESC ";
	}

	// echo '<div id="contents">'; benjami: contents anava aqui
	$link = new Link;
	$rows = $db->get_var("SELECT count(*) $from_where $order_by");
	$links = $db->get_col("SELECT link_id $from_where $order_by LIMIT $offset,$page_size");
        $nlinks = 1;
	if ($links) {
		foreach($links as $link_id) {
			$link->id=$link_id;
			$link->read();
			$link->print_summary();
			
                        if (  $nlinks == 1 || $nlinks == 6 ) {
                                echo '<div class="news-summary" id="robapaginas"><div class="news-body">';
                                echo '<h4>Publi</h4>';
                                include ('ads/google-robapaginas.inc');
                                echo '</div></div>';
                        }
                        $nlinks++;		}
	}

	do_pages($rows, $page_size);
	echo '</div> <!--index.php-->';
	do_sidebar();
	do_footer();
?>
