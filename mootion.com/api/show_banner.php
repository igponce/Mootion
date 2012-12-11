<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include('../config.php');

header('Content-Type: text/html; charset=UTF-8');
header('Pragma: no-cache');
header('Cache-Control: max-age=10, must-revalidate');

$maxlen = 70;

$width = $_GET['width'];
$height = $_GET['height'];
$format = $_GET['format'];
$color_border = $_GET['color_border'];
$color_bg = $_GET['color_bg'];
$color_link = $_GET['color_link'];
$color_text = $_GET['color_text'];
$font_pt = $_GET['font_pt'];

echo '<html><body>';


$from = time() - 3600;
$res = $db->get_row("select link_id, link_title, count(*) as votes from links, votes where vote_date > FROM_UNIXTIME($from) and vote_value > 0 and link_id = vote_link_id group by link_id order by votes desc limit 1");
if ($res) {
	$votes_hour = $res->votes;
	$title['most'] = cut($res->link_title) . ' <span style="font-size: 90%;">['.$votes_hour."&nbsp;". 'votes/hour'."]</span>";
	$url['most'] = "http://".get_server_name()."/story.php?id=$res->link_id";
}

$res = $db->get_row("select link_id, link_title, link_votes from links where link_status = 'published' order by link_published_date desc limit 1");
if ($res) {
	$title['published'] = cut($res->link_title) . ' <span style="font-size: 90%;">['.$res->link_votes."&nbsp;". 'votes'."]</span>";
	$url['published'] = "http://".get_server_name()."/story.php?id=$res->link_id";
}

$res = $db->get_row("select link_id, link_title, link_votes from links where link_status = 'queued' order by link_date desc limit 1");
if ($res) {
	$title['sent'] = cut($res->link_title) . ' <span style="font-size: 90%;">['.$res->link_votes."&nbsp;". 'votes' ."]</span>";
	$url['sent'] = "http://".get_server_name()."/story.php?id=$res->link_id";
}

$res = $db->get_row("select link_id, link_title, link_votes from links, votes where vote_type='links' and link_id = vote_link_id  and vote_value > 0 order by vote_date desc limit 1");
if ($res) {
	$title['voted'] = cut($res->link_title) . ' <span style="font-size: 90%;">['.$res->link_votes."&nbsp;". 'votos' ."]</span>";
	$url['voted'] = "http://".get_server_name()."/story.php?id=$res->link_id";
}

switch ($format) {
	case 'vertical':
		$div1 = '<div style="padding: 1px 1px 1px 1px; height: 23%; width: 100%; ">';
		$div2 = '<div style="padding: 1px 1px 1px 1px; height: 23%; width: 100%; border-top: 1px solid #'.$color_border.';">';
		$div3 = '<div style="padding: 1px 1px 1px 1px; height: 23%; width: 100%; border-top: 1px solid #'.$color_border.';">';
		$div4 = '<div style="padding: 1px 1px 1px 1px; height: 23%; width: 100%; border-top: 1px solid #'.$color_border.';">';
		$signature = 'mOOtion';
		break;
	case 'horizontal':
	default:
		$div1 = '<div style="position: absolute; left: 2px; top: 2px; width: 24%;">';
		$div2 = '<div style="position: absolute; left: 25%; top: 2px; width: 24%;">';
		$div3 = '<div style="position: absolute; left: 50%; top: 2px; width: 24%;">';
		$div4 = '<div style="position: absolute; left: 75%; top: 2px; width: 24%;">';
		$signature = 'a public disservice spam by mOOoootion';
}

?>
<div style="padding: 0 0 0 0 ; font-size: <? echo $font_pt;?>pt; line-height: 1.1em; color : #<? echo $color_text;?>; background: #<?echo $color_bg ?>; border: 1px solid #<?echo $color_border?>; width: <? echo $width-2; ?>px; height: <? echo $height-2; ?>px; ">

<?echo $div1;?>
<a href="<? echo $url['published']?>" style="color: #<? echo $color_link;?>" target="_parent"><? echo 'Last published'; ?></a><br />
<? echo $title['published'] ?>
</div>
<?echo $div2;?>
<a href="<? echo $url['sent']?>" style="color: #<? echo $color_link;?>" target="_parent"><? echo 'Last Sent'; ?> </a><br />
<? echo $title['sent'] ?>
</div>
<?echo $div3;?>
<a href="<? echo $url['most']?>" style="color: #<? echo $color_link;?>" target="_parent"><? echo 'mOOving'; ?></a><br />
<? echo $title['most'] ?>
</div>
<?echo $div4;?>
<a href="<? echo $url['voted']?>" style="color: #<? echo $color_link;?>" target="_parent"><? echo 'Last mOOved'; ?></a><br />
<? echo $title['voted'] ?>
</div>

<div style="position: absolute; left: 0; bottom: 0px; font-size: 8pt; background: #<? echo $color_border;?>; color: #<?echo $color_bg ?>; height: 10pt; width: 100%; text-align: right;">
<a href="http://<?echo get_server_name();?>" style="color : #<?echo $color_bg ?>; text-decoration: none" target="_parent" ><?echo $signature;?></a>&nbsp;
<div>
</div>

<?
echo '</body></html>';

function cut($string) {
	global $maxlen;

	if (strlen($string) > $maxlen) {
		$string = utf8_substr($string, 0, $maxlen) . "...";
	}
	return $string;
}
?>
