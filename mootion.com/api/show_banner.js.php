<?
header('Content-Type: text/javascript; charset=UTF-8');
header('Cache-Control: max-age=3600');
?>
// var mnm_date = new Date();
var mnm_banner_url = 'http://<?echo $_SERVER['SERVER_NAME']; ?>/api/show_banner.php?width='+mnm_banner_width+'&height='+mnm_banner_height+'&format='+mnm_banner_format+'&color_border='+mnm_banner_color_border+'&color_bg='+mnm_banner_color_bg+'&color_link='+mnm_banner_color_link+'&color_text='+mnm_banner_color_text+'&font_pt='+mnm_banner_font_pt;
//+'&time='+mnm_date.getTime();

function write_banner_frame() {
	var div = document.getElementById("mnm_banner");
	div.innerHTML='<iframe width="'+mnm_banner_width+'" height="'+mnm_banner_height+'" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" vspace="0" hspace="0" allowtransparency="true" src="'+mnm_banner_url+'"></iframe>';
}

document.write('<div id="mnm_banner" style="width: '+mnm_banner_width+'px; height: '+mnm_banner_height+'px; border: none; padding: 0; margin: 0; background: transparent ; "><script type="text/javascript">setTimeout("write_banner_frame()", 100)</script></div>');
