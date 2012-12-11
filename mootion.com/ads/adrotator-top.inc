<?php

$adlist = array ( 

   array ( 'Ad1 description' , 
   '<!-- Ad Code -->' ),

   array ( 'Ad2 description', 
   '<!-- Ad Code -->')

);

/* Anuncio */

$top_ad_num = rand(0,count($adlist)-1) ;
echo $adlist[ $top_ad_num ][1] ;

/* Tracker de anuncios */

?>
<script type='text/javascript'>
   var _gaq = _gaq || [];
   _gaq.push( [ "_setCustomVar" , 1 , "affiliateTop", "<?php echo $adlist[$top_ad_num][0] ?>"] );
</script>

