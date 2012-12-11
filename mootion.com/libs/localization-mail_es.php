<?
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// Inigo Gonzalez <igponce at corp dot mootion dot com> 
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// From: mail.php

	$subject = 'Recuperación o verificación de la contraseña de ' . get_server_name();
	$message = $to .': para poder acceder sin la clave, conéctate a la siguiente dirección en menos de dos horas:' . "\n\n$url\n\n";
	$message .= 'Pasado este tiempo puedes volver a solicitar acceso en: ' . "\nhttp://".get_server_name()."/login.php?op=recover\n\n";
	$message .= 'Una vez en tu perfil, puedes cambiar la clave de acceso.' . "\n" . "\n";
	$message .= "\n\n". 'Este mensaje ha sido enviado a solicitud de la dirección: ' . $globals['user_ip'] . "\n\n";
	$message .= "-- \n  " . 'el equipo de menéame';
	$message = wordwrap($message, 70);

?>
