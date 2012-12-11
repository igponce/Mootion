<?PHP
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".



class UserAuth {
	var $user_id  = 0;
	var $user_login = '';
	var $md5_pass = '';
	var $authenticated = FALSE;
	var $user_level='';


	function UserAuth() {
		global $db;

		if(isset($_COOKIE['mnm_user']) && isset($_COOKIE['mnm_key']) && $_COOKIE['mnm_user'] !== '') {
			// Si ya está autentificado de antes, rellenamos la estructura.
			$userInfo=explode(":", base64_decode($_REQUEST['mnm_key']));
			// Windows Cough: if(crypt($userInfo[0], substr(posix_getuid(),0,2))===$userInfo[1]
			if(crypt($userInfo[0], substr('root',0,2))===$userInfo[1] 
				&& $_COOKIE['mnm_user'] === $userInfo[0]) {
				$dbusername = $db->escape($_COOKIE['mnm_user']);
				$dbuser=$db->get_row("SELECT user_id, user_pass, user_level, user_validated_date FROM users WHERE user_login = '$dbusername'");
				if ($dbuser->user_pass == 'SPAMMER' || empty($dbuser->user_validated_date)) return;
				if($dbuser->user_id > 0 && md5($dbuser->user_pass)==$userInfo[2]) {
					$this->user_id = $dbuser->user_id;
					$this->user_login  = $userInfo[0];
					$this->md5_pass = $userInfo[2];
					$this->user_level = $dbuser->user_level;
					$this->authenticated = TRUE;
				}
			}
		}
	}


	function SetIDCookie($what, $remember) {
		switch ($what) {
			case 0:	// Borra cookie, logout
				setcookie ("mnm_user", "", time()-3600); // Expiramos el cookie
				setcookie ("mnm_key", "", time()-3600); // Expiramos el cookie
				break;
			case 1: //Usuario logeado, actualiza el cookie
				// Atencion, cambiar aqu�cuando se cambie el password de base de datos a MD5
				$strCookie=base64_encode(join(':',
					array(
						$this->user_login,
						// Windows cough:
						// crypt($this->user_login, substr(posix_getuid(),0,2)),
						crypt($this->user_login, substr('root',0,2)),
						$this->md5_pass)
					)
				);
				if($remember) $time = time() + 3600000; // Lo dejamos v�idos por 1000 horas
				else $time = 0;
				setcookie("mnm_user", $this->user_login, $time);
				setcookie("mnm_key", $strCookie, $time);
				break;
		}
	}

	function Authenticate($username, $pass, $remember=false) {
		global $db;
		$dbusername=$db->escape($username);
		$user=$db->get_row("SELECT user_id, user_pass, user_level, user_validated_date FROM users WHERE user_login = '$dbusername'");
		if ($user->user_pass == 'SPAMMER' || empty($user->user_validated_date)) return false;
		if ($user->user_id > 0 && $user->user_pass === $pass) {
			$this->user_login = $username;
			$this->user_id = $user->user_id;
			$this->authenticated = TRUE;
			$this->md5_pass = md5($user->user_pass);
			$this->user_level = $user->user_level;
			$this->SetIDCookie(1, $remember);
			return true;
		}
		return false;
	}

	function Logout($url='./') {
		$this->user_login = "";
		$this->authenticated = FALSE;
		$this->SetIDCookie (0);

		//header("Pragma: no-cache");
		header("Cache-Control: no-cache, must-revalidate");
		header("Location: $url");
		header("Expires: " . gmdate("r", time()-3600));
		header("ETag: \"logingout" . time(). "\"");
		die;
	}

}

$current_user = new UserAuth();
?>
