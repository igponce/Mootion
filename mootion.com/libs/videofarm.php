<?
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Inigo Gonzalez <igponce at corp dot vlog dot es> for mOOtion.com
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

$video_types = array (
        'mov' => 'Quicktime Movie',
        'avi' => 'AVI Movie File',
        '3gp' => '3GPP standard, GSM Network, Video',
        '3g2' => '3GPP2 standard, CDMA2000 Network, Video',
        'wmv' => 'Windows Media Video',
        'm4v' => 'MPEG4 Video (usually playable on iPOD and PSP)',
        'rm'  => 'Real Media'
);

// VideoFarm Helper Functions
//
// is_videofarm ( URL )
// videofarm_process ( Link )
// videofarm_googlevideo ( $linkres )

function process_videofarm ( &$linkres ) {

        $videofarm = is_videofarm ( $linkres->url );
        $retval = false;

        switch ( $videofarm ) {

                case 'bliptv':
                        $retval = process_bliptv ( $linkres );
                        break;

                case 'googlevideo':
                        $retval = process_googlevideo ( $linkres );
                        break;

                case 'youtube':
                        $retval = process_youtube ( $linkres );
                        break;
                
                case 'yoqoo':
                        $retval = process_yoqoo ( $linkres );
                        break;
                
                case 'metacafe':
                        $retval = process_metacafe ( $linkres );
                        break;
                
                case 'vimeo':
                        $retval = process_vimeo ( $linkres );
                        break;
                
                case 'revver':
                        $retval = process_revver ( $linkres );
                        break;
                default:
                        echo '<!-- process_videofarm() - NOT from a videofarm (' . $videofarm .') -->';
                        break;
        }

        if ( $retval ) {
                $linkres->store();
        }

        return $retval;
}

// Detecta 'granjas de videos //

function is_videofarm ( $url ) {

        $known_videofarms = array (
                'blip.tv' => 'bliptv' ,
                'www.blip.tv' => 'bliptv' ,
                'youtube.com' => 'youtube',
                'www.youtube.com' => 'youtube',
                'video.google.com' => 'googlevideo',
                'www.metacafe.com' => 'metacafe',
                'metacafe.com' => 'metacafe',
                'www.yoqoo.com' => 'yoqoo',
                'www.yoqoo.com' => 'yoqoo',
                'vimeo.com' => 'vimeo',
                'www.vimeo.com' => 'vimeo',
                'revver.com' => 'revver',
                'one.revver.com' => 'revver',
                );

        $host = false ;
        $videofarm = false ;

        trim ($url);
        $url = strtolower ( $url );

        // regexp: [protocol://](exocert.com)/index.php

        //if (preg_match ('#[a-zA-Z]*:?\/?\/?([^\/\s+]+)#i', $url, $host) ) {
        // if (preg_match ('#http://([a-z0-9_-]\.)*([a-z0-9\-]+).[a-z0-9\-]+#i', $url, $host)) {
	if (preg_match ('#(http://)?([^\/]*)#i', $url, $host)) {
           $videofarm = $known_videofarms [ $host[2] ] or null ;
        }
        /* debug */ echo '<!-- videofarm.php: is_videofarm(): ' . $videofarm . '-->';
       return $videofarm;
}


/** process_bliptv ( )
 * Spider Blip.tv content.
 * ALL SPIDER'd content MAY FAIL WITHOUT NOTICE
 * ALLWAYS, repeat: ALLWAYS send users to the ORIGINAL SOURCE URL.
 */

function process_bliptv ( &$linkres ) {
    
    echo "\n<!-- process_bliptv: $linkres->url -->\n";

    $retval = false;
    
    # preg_match ('#http://([^.]\.)?blip.tv/posts/([0-9]+)#', $linkres->url, $match);
    # if ($match[0]) {
    
    if ( apicall_bliptv ($linkres, false) ) {
        $linkres->type = 'video';
        $linkres->encoding = 'web';
        $retval = true;
    }
    
    echo "\n<!-- process_bliptv: retval" . ( $retval == true ? "true" : "false") . " -->\n";
    
    return $retval;
}



// process_googlevideo ()

function process_googlevideo ( &$linkres ) {

        // Para procesar GoogleVideo.
        // Podemos tener varios tipos de links...
        // a falta de un API que nos haga la vida sencilla.
        // TITULO     : En el tag <title>__</title>
        // Link1: http://video.google.com/videoplay?docid=-7725195077118012606
        // Miniaturas: En el Tags IMG SRC=http://video.google.com/ThumbnailServer?.*$ (75x100px)

        /* debug */
        echo '<!-- videofarm.php : Process Google VIDEO -->';

        $retval = false;
        $linkres->get ( $linkres->url );

        if ( $linkres->valid ) {
                
                preg_match ( '/<title>(.+)\s+- Google Video<\/title>/i', $linkres->html, $match );
                $linkres->url_title = $match[1];
                $linkres->type = 'video';
                $linkres->encoding = 'web';
                // Aqui va el cÛdigo 'capturavÌdeos'. °° Puede cambiar en cualquier momento !!
                // est· en un <textarea name=embedhtml> </textarea>. Hay que 'desescapar' los caracteres.
                // SerÌa algo asÌ como /textarea[name=embedhtml][0]

                // preg_match ( '/<textarea.*>(&lt;embed.*embed&gt;)\s+<\/textarea>/is', $linkres->html, $match);
                preg_match ('/[?|&]docId=(-?[^&\'"]*)/i', $linkres->url, $match);
                $linkres->embedhtml = '<embed style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=' .
                                      $match[1] . '&hl=es"> </embed>';
                // El URL Correcto es http://video.google.com/videoplay?docid=.........
                // hay que quitar todos los argumentos que no sean docid=.
                // linkres->url = _____ algo ;
                $retval = true;
        }
        
        return $retval;
}

function process_youtube ( &$linkres ) {
        // TITULO    : En el tag <title>__</title>
        // URL TIPO  : http://youtube.com/watch?v=<<<ID>>>$
        // Miniaturas: http://static10.youtube.com/get_still.php?video_id=<<<ID>>> 60x80px
        // DEBERIAMOS USAR EL API DE YOUTUBE - Developer ID: nEC61ml52bw 

        /* debug echo '<!-- process youtube: ' . $linkres->url . '-->'; */

        $retval = false;
        $linkres->get ( $linkres->url );
        if ( $linkres->valid) {
	        print "<!-- Valid:  $linkres->valid - URL: $linkres->url -->";
                preg_match ( '/<title>[Yy]ou[Tt]ube\s+-?\s+(.*)<\/title>/i', $linkres->html, $match );
                $linkres->url_title = $match[1];
                $linkres->type = 'video';
                $linkres->encoding = 'web'; // De momento, asi. No decimos nada de transcoding.
                                            // Tampoco preparamos URLs a medida...
                // Capturar el 'embed'
                //preg_match ( '/(<|(&lt;))object width="\d+" height="\d+"(.*)\/object(>|(&gt;))/i', $linkres->html, $match );
		// Capturar el video a partir del recurso opengraph:
		preg_match ('#<meta property="og:video" content="http://www.youtube.com/v/([^?]+)?#i', $linkres->html, $match);

		$videoid = $match[1];
                $linkres->embedhtml = '<iframe title="YouTube video player" width="640" height="510" src="http://www.youtube.com/embed/' . $videoid . '" frameborder="0" allowfullscreen></iframe>';

		
		print "<!-- Capturado: "; print_r ($match) ; print " -->\n";
                
                $linkres->url = "http://www.youtube.com/v/" . $videoid ; 
                $retval = true;
        }
        return $retval;
}

function process_metacafe ( &$linkres ) {

        /* debug echo '<!-- process metacafe: '. $linkres->url . '-->'; */

        $retval = false ;
        $linkres->get ( $linkres->url );
        if ( $linkres->valid) {
                preg_match ('/<title>(.*) on Metacafe<\/title>/i', $linkres->html, $match);
                $linkres->url_title= $match[1];
                $linkres->type = 'video';
                $linkres->encoding='web';
                // Captura contenido		
                // old: preg_match ('/var\sembeddedPlayer\s+=\s+\"(.*)\";/i', $linkres->html, $match);
                // preg_match ('/\="(&lt;embed.*\/embed&gt;)"/i', $linkres->html, $match)
                preg_match ('(www.)metacafe.com/fplayer/([0-9]+/[^/"]+)/i',$linkres->html, $match);
                $linkres->embedhtml='<embed flashVars="altServerURL=http://www.metacafe.com&playerVars=videoTitle=Interupting Fart|showStats=yes|autoPlay=no|blogName=Mootion.com|blogURL=http://mootion.com" src="http://www.metacafe.com/fplayer/610392/interupting_fart.swf" width="400" height="345" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
                $retval = true;		
        }
        return $retval;
}

function process_yoqoo (&$linkres) {
        echo '<!-- process metacafe: '. $linkres->url . '-->'; 
        $retval = false ;
        $linkres->get ( $linkres->url );
        if ( $linkres->valid) {
                preg_match ('/<title>.* --(.*)<\/title>/i', $linkres->html, $match);
                $linkres->url_title= $match[1];
                $linkres->type = 'video';
                $linkres->encoding='web';
                preg_match ('/\'(\<object width=.*\/object>)\'/i',$linkres->html, $match);
                $linkres->embedhtml = $match[1];
                $retval = true;
        }
        return $retval;
}

// process_vimeo ()
function process_vimeo ( &$linkres ) {
        // Para procesar VIMEO.com
        // TITULO     : En el tag <title>VIMEO /\s+(.*)</title>
        // Link1: (http://vimeo.com/clip:105919)/context/tag:stopmotion
        /* debug */ echo '<!-- videofarm.php : Process VIMEO -->';
        $retval = false;
        $linkres->get ( $linkres->url );
        if ( $linkres->valid ) {
                preg_match ('/<title>.* \/\s+(.*)<\/title>/i', $linkres->html, $match);
                $linkres->url_title = $match[1];
                $linkres->type = 'video';
                $linkres->encoding = 'web';

                // Para capturar videos: Buscamos el numero de vÌdeo de VIMEO
                // y lo incrustamos en el player_embed_html_code.
                // Ojo: si Vimeo empieza a usar no digitos, cambiar el[0-9]* por [^/*]

                preg_match ('/\/clip:([0-9]*).*$/i', $linkres->url, $match);
                
                $linkres->embedhtml =	'<embed src="http://www.vimeo.com/moogaloop.swf?clip_id=' . $match[1] . '" ' .
                                        ' quality="best" scale="exactfit" width="400" height="300" type="application/x-shockwave-flash"></embed>';
                $linkres->viaurl = 'http://www.vimeo.com/clip=105919';
                $retval = true;
        }

        return $retval;
}


// ToDO: process_selfcasttv :-)

/** get_video_filetype ()
 * Args: Linkres reference (must be valid)
 * Return: String with human-readable filetype.
 */

function get_video_filetype ( &$linkres ) {

        $video_types = array (
                'mov' => 'Quicktime Movie',
                'avi' => 'AVI Movie File',
                '3gp' => '3GPP standard, GSM Network, Video',
                '3g2' => '3GPP2 standard, CDMA2000 Network, Video',
                'wmv' => 'Windows Media Video',
                'm4v' => 'MPEG4 Video (Apple iPod / Sony PSP)',
                'rm'  => 'Real Media'
        );
        
        preg_match ('/http:\/\/.+\.(.+)$/', $linkres->url, $extension );
        return $video_types[$extension[1]];
}



// getvideofromblog ( &$linkres)

// De momento *sÛlo* detectamos vÌdeos de YouTube...
// ToDo: metacafe, vimeo, revver, blip.tv, etc...

function getvideofromblog ( &$linkres ) {

        // Prerequisites: $linkres->url is not a video.
        // If its content-type IS text, we may find videofarm <embed> statements
        $gotvideo = false;
        if ( $linkres->type != 'video' ) {
                $linkres->get ( $linkres->url );
        
                // Embedded video finder (CPU Intensive!!)
                if (! $gotvideo ) {
                        $gotvideo = getembedded_youtube ( $linkres );
                }
                
                if (! $gotvideo ) {
                        $gotvideo = getembedded_googlevideo ( $linkres );
                }
                
                if (! $gotvideo ) {
                        $gotvideo = getembedded_metacafe ( $linkres );
                }

                if (! $gotvideo ) {
                        $gotvideo = getembedded_blipttv ( $linkres );
                }
                
                if (! $gotvideo ) {
                        $gotvideo = getembedded_myspace ( $linkres );
                }

                if (! $gotvideo ) {
                        $gotvideo = getembedded_collegehumor ( $linkres );
                }
                
                if (! $gotvideo ) {
                        $gotvideo = getembedded_breakdotcom ($linkres);
                }

                if ( ! $gotvideo ){
                        $gotvideo = getembedded_vpodtv ( $linkres );
                }	

//		if ( ! $gotvideo ){
//			$gotvideo = getembedded_visuarios ( $linkres );
//		}	

                if (! $gotvideo ) { // Fallback for DEBUG		
                        // echo "<h1>getvideofromblog()</h1><br/>";
                        // debug // solo en caso de emergencia //
                        // echo '<tt>' . htmlentities ($linkres->html) . '</tt><br>' ;
                        // debug //
                        // echo "<hr/>";
                        // debug //
                        // echo "MATCH: ". htmlentities  ($match[0]) ." </br>MATCH1: " . htmlentities($match[1]) ."</br>";
                        // debug // echo "<hr/>";
                }		
        }
        return $gotvideo;
}		


function getembedded_youtube ( &$linkres ) {

        $videoid = '';
        $gotvideo = false;
        
        preg_match ('#http://(www.)?youtube.com/v/([^/"\'&]*)#', $linkres->html, $match);

        if ( $match[2] ) {
                $videoid = $match[2];
                $gotvideo = true;
        } else {
                // try linked YouTube
                preg_match ('#http://(www.)?youtube.com/watch\?v=([^&/\"]+)#', $linkres->html, $match);
                if ( $match[2] ) {
                        $videoid = $match[2];
                        $gotvideo = true;
                }
        }
        
        if ( $gotvideo ) {		
                $linkres->embedhtml =
                '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/' . $videoid .'"></param>'
                . '<param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' .$videoid. '" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
                // obsoleto: quitar // $linkres->embed = $match[0];
                $linkres->viaurl = $linkres->url;
                $linkres->url = 'http://www.youtube.com/watch?v='.$videoid;
                $linkres->type = 'web';
        }

        return $gotvideo;

}

function getembedded_metacafe ( &$linkres ) {

echo "\n<!--debug embedded_metacafe -->\n";

        $videoid = '';
        $gotvideo = false;
        
        preg_match ('#(www.)metacafe.com/fplayer/([0-9]+/[^/"]+.swf)#i', $linkres->html, $match);
        if ( $match[2] ) {
                $videoid = $match[2];
                $gotvideo = true;
        } 
        
        if ( $gotvideo ) {		
                $linkres->embedhtml =
                '<embed flashVars="altServerURL=http://www.metacafe.com&playerVars=showStats=yes|autoPlay=no|blogName=Mootion.com|blogURL=http://mootion.com" src="http://www.metacafe.com/fplayer/' .$videoid .'.swf" width="400" height="345" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
                $linkres->viaurl=$linkres->url;
                $linkres->url='http://www.metacafe.com/watch/' . $videoid . '/';
                $linkres->type='web';
                $retval = true;
        }
        return $retval;
}


// getembedded_vpodtv

function getembedded_vpodtv ( &$linkres ) {

        // VPOD.TV Embedding
        // Strategy:
        // The Embedding URL is always something like: http://portal.vpod.tv/USER/VIDEO_ID(/?)
        //    Where: user are simply characters.
        //    Video_id: is numeric.
        
        $gotvideo = false;

        preg_match ('|http://(portal.)?vpod.tv/([^ /]+/[0-9]+)|', $linkres->html, $match);
        if ( $match[0] ) {
                $n  = count($match);
                $videoid = $match[$n-1]; // The last one is always the videoid
                $linkres->embedhtml =
                '<object width="320" height="240"><param value="http://vpod.tv/' . $videoid
                . '/flash/nVideoPlayer" name="movie" /><param value="true" name="allowfullscreen" /><embed width="320" height="240" allowfullscreen="true" type="application/x-shockwave-flash"src="http://vpod.tv/' . $videoid
                . '/flash/nVideoPlayer"></embed></object>';
                $gotvideo = true;
                $linkres->viaurl = $linkres->url;
                $linkres->url = 'http://portal.vpod.tv/' . $videoid;
                $linkres->type = 'web';
        }

        return $gotvideo;
}

function getembedded_breakdotcom  (&$linkres ) {

        $gotvideo = false;
        preg_match ('#http://(www.)?embed.break.com/([^/"\'&]+)#', $linkres->html, $match);

        if ( $match[2] ) {
                $videoid = $match[2];
                $linkres->embedhtml = '<object width="425" height="350"><param name="movie" value="http://embed.break.com/' . $videoid . '"></param>' .
                    '<embed src="http://embed.break.com/' . $videoid . '" type="application/x-shockwave-flash" width="425" height="350"></embed></object>';
                $linkres->viaurl = $linkres->url;
                $linkres->type = 'web';
                $linkres->url = 'http://my.break.com/media/view.aspx?ContentID=' . $videoid;
                $gotvideo = true;
        }

        return $gotvideo;
}

function getembedded_collegehumor (&$linkres) {
   $gotvideo = false;
   preg_match ( '#http://(www.)?collegehumor.com/moogaloop/moogaloop.swf\?clip_id=([^/"\'&]+)#', $linkres->embedhtml, $match);
   
   if ( $match[2] ) {
       $videoid = $match[2];
       $linkres->embedhtml = '<embed src="http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=' . $videoid .'" quality="best" width="400" height="300" type="application/x-shockwave-flash"></embed>';
       $linkres->viaurl = $linkres->url;
       $linkres->url = 'http://www.collegehumor.com/video:' .$videoid . 'ls:9316';
   }   
   return $gotvideo;
}

function getembedded_googlevideo ( &$linkres ) {

   $gotvideo = false;
   $videoid = '';
   
   echo "\n<!-- getembedded_googlevideo -->\n";
  
   // Video embebido
   preg_match ( '#http://(www.)?video.google.[a-z]+/[a-zA-Z0-9]+.swf\?docId=(-?[0-9]+)#', $linkres->html, $match);
   if ($match[2]) {
        $videoid = $match[2];
   } else {
        // Capturar enlace
        preg_match ('#http://(www.)?video.google.[a-z]+/videoplay\?docid=(-?[0-9]+)#', $linkres->html, $match);
        if ( $match[2] ) {
                $videoid = $match[2];
        }
   }
   
   if ( $videoid ) {
        $gotvideo = true;	
        $linkres->embedhtml = '<embed style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=' .$videoid. '&hl=en" flashvars=""> </embed>';
        $linkres->viaurl = $linkres->url;
        $linkres->url = 'http://video.google.com/videoplay?docid=-' . $videoid;
   }


   return $gotvideo;
}

// Visuarios.com
// Videos para compartir conocimientos.

function getembedded_visuarios ( &$linkres ) {
        
   $gotvideo = false;
   $videoid='';
   
   preg_match ('', $linkres->html, $match);

   if ( $match[2] ) {	
        $videoid = $match[2];
        $linkres->embedhtml = '';
        $likres->viaulr = $linkres->url;
        $gotvideo = true;
   }
        
   return $gotvideo;
}

// Myspace.com
// Otro de los grandes - este no distingue en la URL el vídeo.
// De momento no vamaos a construir una regexp a propósitop para entender tags html.

function getembedded_myspace ( &$linkres ) {

   $gotvideo = false;
   $videoid = '';
   

   preg_match ('#http://(www.)?vids.myspace.com/index.cfm\?fuseaction=vids.individual&videoID=([0-9]+)#', $linkres->html, $match);
   if ($match[2]) {
        $videoid = $match[2];
        $gotvideo = true;
   }
   
   if ( $gotvideo ) {
        $linkres->embedhtml = '<embed src="http://lads.myspace.com/videos/vplayer.swf" flashvars="m=' .$videoid. '&type=video" type="application/x-shockwave-flash" width="430" height="346"></embed>';
        $linkres->viaurl = $linkres->url;
        $linkres->url = 'http://vids.myspace.com/index.cfm?fuseaction=vids.individual&videoID=' . $videoid;
   }
   
   return $gotvideo;
}


// Blip.tv
// Cuidado con este - aquí es mejor consultar el API para ver si podemos sacar la información.
// Usamos el 'mySpace' player. El player de Flash viene con Javascript y es *complicado*.
//
// Regex codigo embebido: (<script[^>]+http://blip.tv/syndication/write_player[^>]+posts_id=([0-9]+)[^>].+/script>)|(<embed[^>]+src="http://blip.tv/scripts/flash/blipplayer.swf[^"']+(http://blip.tv/file/get/[^"']+.flv))
// Consige el posts_id=num (hay que consultar el API), o la url http del fichero .swf que hay
// que incorporar al embed code de myspace.

function getembedded_blipttv ( &$linkres ) {

        $gotvideo = false;

        preg_match ('#(<script[^>]+http://blip.tv/syndication/write_player[^>]+posts_id=([0-9]+)[^>].+/script>)|(<embed[^>]+src="http://blip.tv/scripts/flash/blipplayer.swf[^\'"]+(http://blip.tv/file/get/[^\'"]+.flv))#', $linkres->html, $match);

        if ( $match[0] ) {
                
                $blipflv = '';
                
                if ( is_numeric ($match[2][0])  ) { // tenemos un numero de post
                        
                    // Llamamos a CURL para conseguir el ID del post y sacar la URL flash.
                    $posts_id = $match[2];                                        
                    $gotvideo = apicall_bliptv ($linkres, "http://blip.tv/posts/$posts_id/");                                                                              
                        
                } else {
                    // Tenemos una URL al .flv
                    $linkres->url = $match[2];
                    $blipflv = $match[2];
                }
                                
                
        }
        
        
        return $gotvideo;
}
		

//
// Llamadas a las APIs de las granjas de videos
//

function apicall_bliptv (&$linkres, $posts_url) {

    echo "\n<!-- apicall_bliptv: $linkres->url, $posts_url -->\n";    
    $retval = false;
    
    if ( $posts_url ) {
        $url = $posts_url;
    } else {
        $url = $linkres->url;
    }
    
    echo "\n<!-- apicall_bliptv: URL: $url -->\n";    
    $url .= '?skin=json&version=2'; // para la API de BLIP.
    
    $timeout = 5; // set to zero for no timeout
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $json = curl_exec($ch);
    
    if ( curl_errno($ch) == 0)
        $retval = true;

    curl_close($ch);
    
    if ( $retval == true ) { // tenemos video    
    
        $linkres->type = 'web';
        
        // Analizamos el JSON
        
        // Title
        preg_match ("#'title'\s*:\s*'([^']+)#", $json, $match);
        $linkres->url_title = $match[1];
        
        // Embed Code
        preg_match ("#'media'\s*:\s*[^']*'url'\s*:\s*'([^'?]+)#",$json, $match);
        $blipflv = $match[1];
        $linkres->embedhtml =
            '<embed wmode="transparent" src="http://blip.tv/scripts/flash/blipplayer.swf?autoStart=false&' .
            'file=' . $blipflv . '%3Fsource%3D3" quality="high" width="450" height="253" name="movie" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>';
            
        // Permalink
        $linkres->viaurl = $linkres->url;        
        preg_match ("#'url'\s*:\s*'([^']+)#", $json, $match);
        $linkres->url = $match[1];
        
        // Category
        $linkres->category = 5; // de la BBDD = Videoblog
    }
    
    return $retval;

}
		
		
?>

