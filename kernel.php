<?php
/**************************************************************************
 * 		        		0xBB ~ Bullettin Board						      *
 **************************************************************************
 * This program is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or   
 * (at your option) any later version.                                  
 *                                                                      
 * This program is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of       
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        
 * GNU General Public License for more details.                         
 *                                                                      
 * You should have received a copy of the GNU General Public License    
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * =========================================================================*
 * Software:					0xBB
 * Software version:			2.0
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =========================================================================*
 * kernel.php                                                        
 ***************************************************************************/
ob_start();

include "config.php";
include "impaginazione.class.php";

define("VERSION","2.0");//Versione della Board

list ($username, $password) = get_data (); //aquisisco i dati di autenticazione

if(file_exists("./install.php")) {
	header("Location: install.php");
	die();
}

check_ban_ip($_SERVER['REMOTE_ADDR'], $username); //controllo se l'IP è bannato
		
function show_header() {

	list ($username, $password) = get_data ();
	
	$config =  mysql_fetch_array(mysql_query("SELECT site_name, description FROM ". __PREFIX__ ."settings"));
	
	if(!empty($username))
    	$theme  = mysql_fetch_row(mysql_query("SELECT theme FROM ". __PREFIX__ ."users WHERE id = ". mysql_real_escape_string(nick2uid($username))));
	
	// gestione di merda dei titoli
	if(!empty($_GET['id'])) {
		if(preg_match("/viewforum/i", $_SERVER['PHP_SELF']))
			$title_final = mysql_fetch_row(mysql_query('SELECT title FROM '.__PREFIX__.'forum WHERE id = '.(int) $_GET['id']));
		else
			$title_final = mysql_fetch_row(mysql_query('SELECT title FROM '.__PREFIX__.'topic WHERE id = '.(int) $_GET['id']));
	}		
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
if(preg_match("/(viewtopic|viewforum)/i", $_SERVER['PHP_SELF']))
	print "\n<title>".$title_final[0] .' &bull; '. $config['site_name']."</title>";
else
	print "\n<title>".$config['site_name']."</title>";
?>
<meta http-equiv = "Content-Type" CONTENT = "text/html; charset=UTF-8" />
<META NAME = "GENERATOR"          CONTENT = "VIM ~ The Linux Free Editor" />
<META NAME = "AUTHOR"             CONTENT = "KinG-InFeT" />
<META NAME = "COPYLEFT"           CONTENT = "Copyleft © By 0xBB - http://0xproject.netsons.org/#0xBB" />
<?php
if(login ($username, $password) == TRUE && (file_exists("themes/". $theme[0]) == TRUE))
	print "<link rel = \"stylesheet\" type = \"text/css\" href = \"themes/".$theme[0]."\">\n";
else
	print "\n<!-- default style -->\n<link rel = \"stylesheet\" type = \"text/css\" href = \"themes/default.css\">\n";
?>
</head>
<body>
<div id="header">
	<h1><a href="index.php"><?php print $config['site_name']; ?></a></h1>
	<p><i><?php print $config['description']; ?></i></p>
	<?php visual_maintenance_status(); ?>
</div>

<?php
}//end show_header

function csrf_attemp($ref) {
	$regola = "/".SERVER_NAME."/i";
	
	if(preg_match($regola, $ref) == FALSE)
		die("CSRF/XSRF Attemp!");
}

function _err($msg) {

	die( "<br /><br /><br /><div class=\"error_msg\" align=\"center\">". $msg ."<br /><br /><a href='javascript:history.back()'>Torna Indietro</a></div>");
	
	return NULL;
}

function check_version() {
	$update = NULL;

	if ($fsock = @fsockopen('www.0xproject.netsons.org', 80, $errno, $errstr, 10)) {
		@fputs($fsock, "GET /versions/0xBB.txt HTTP/1.1\r\n");
		@fputs($fsock, "HOST: www.0xproject.netsons.org\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");

		$get_info = FALSE;

		while (!@feof($fsock)) {
			if ($get_info)
				$update .= @fread($fsock, 1024);
			else
				if (@fgets($fsock, 1024) == "\r\n")
					$get_info = TRUE;
		}

		@fclose($fsock);

		$update1  = str_replace(".", "", $update);
		$version1 = str_replace(".", "", VERSION);
	
		if ($version1 >= $update1)
			$version_info = "<p style=\"color:green\">Non ci sono aggiornamenti per il sistema.</p><br />";
		else
			$version_info = "\n<p style=\"color:red\">Ci sono aggiornamenti per il sistema.<br />\nAggiorna all'ultima versione: ". $update."\n"
						  . "<br /><br />Link Download: <a href=\"http://0xproject.netsons.org/#0xBB\">Scarica l'ultima versione</a><br />\n";
	}else{
		if ($errstr)
			$version_info = '<p style="color:red">' . sprintf("Impossibile aprire la connessione a 0xProject Server, ha riferito il sequente errore:<br />%s", $errstr) . '</p>';
		else
			$version_info = '<p>Impossibile utilizzare la funzione socket.</p>';
	}

	print "<br /><br /><big><big>".$version_info."</big></big>";
}	

function check_block_register() {
	$row =  mysql_fetch_row(mysql_query("SELECT block_register FROM ". __PREFIX__ ."settings"));
	
	if($row[0] == 1)
		return FALSE;
	else
		return TRUE;
}

function check_maintenance($mode) {    // questa funzione è stata fatta da schifo forse la migliorerò
	list ($username, $password) = get_data ();
	
	$row =  mysql_fetch_row(mysql_query("SELECT maintenance FROM ". __PREFIX__ ."settings"));
	
	switch($mode) {
		case 1:
			if(($row[0] == 1) && (!login ($username, $password))) {
				print '<script>window.location="login.php";</script>';
			}elseif(($row[0] == 1) && (login ($username, $password))) {
				if((level($username) == 'admin')     || 
				   (level($username) == 'mod')
				  ) {
					continue;
				}
			}
		break;
		
		case 2:
			if($row[0] == 1)
				return $row[0];
		break;
	}
}

function visual_maintenance_status() {

	list ($username, $password) = get_data ();
	
	$row =  mysql_fetch_row(mysql_query("SELECT maintenance FROM ". __PREFIX__ ."settings"));
	
	if(($row[0] == 1) && (login ($username, $password) == TRUE)) {
		if((level($username) == 'admin') || (level($username) == 'mod')) {
			print "<p align=\"right\" style=\"float: right;\"><font color=\"red\" size=\"3\"><b>Forum in modalit&agrave; Manutenzione!</b></font></p>";
		}
	}
}

function check_ban_ip($ip,$username) {
	$row = mysql_fetch_row(mysql_query("SELECT * FROM ". __PREFIX__ ."ban_ip WHERE user_id = '".nick2uid($username)."'"));
	
	if($row[3] == 1) {
		_err("IP: \n".$row[2]."\n<br />Stato: <b>BANNATO!</b>");
	}
}	

function check_t_id($id_topic) {
	$sql = "SELECT t_id FROM ". __PREFIX__ ."topic WHERE id = '{$id_topic}'";
	$row = mysql_fetch_row(mysql_query($sql));
	
	return $row[0];
}

function delete_topic($username,$id) {

	if(empty($id))
		die(header('Location: index.php'));
		
	if((level($username) == 'admin') || (level($username) == 'mod')) 
	{
		$query = "SELECT f_id FROM ". __PREFIX__ ."topic WHERE id = '". $id ."'";
		$f_id  = mysql_fetch_row (mysql_query ($query));
		$t_id  = check_t_id($id);
		$sql   = "DELETE FROM ". __PREFIX__ ."topic WHERE t_id = '{$t_id}'";
		mysql_query($sql) or _err(mysql_error());
		header("Location: viewforum.php?id=".$f_id[0]);
	}else{
		print "<script>alert(\"Operazione consentita solo ad Amministratori e Moderatori\");</script>";
	}
}

function insert_topic($reply, $id) {
	
	$date   = (@date("d-m-y"));
	$ora    = (@date("G:i"));
	
	if(empty($reply))
		die (header('Location: viewtopic.php?id='. $id));
	else
		$reply = clear($reply);
			
	$query = "SELECT f_id, t_id, title FROM ". __PREFIX__ ."topic WHERE id = '" . $id . "'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	//Fix SQL Injection thanks hacku
	$title = clear ($row[2]);
	$f_id  = clear ($row[0]);
	$t_id  = clear ($row[1]);
	
	list ($username, $password) = get_data ();
	
	$query = "INSERT INTO ". __PREFIX__ ."topic (
				f_id, t_id, author, title, data, replyof, date, ora
			) VALUES (
				'{$f_id}', '{$t_id}', '". $username ."', 'Re: {$title}', '{$reply}', '" . $id . "', '{$date}', '{$ora}'
			)";
										   
	mysql_query ($query) or die (mysql_error());
	
	//aggiorno il last con time()
	$query = "UPDATE ". __PREFIX__ ."topic SET last = '".time()."' WHERE id = '" . $id . "'";
	
	mysql_query ($query) or _err(mysql_error());
	
	die(header('Location: viewtopic.php?id='. $id));

}

function move_topic($t_id, $to_forum) {

	$t_id = (int) $t_id;     // id del topic corrente da spostare
	$f_id = (int) $to_forum; // id del forum nella quale spostare il topic
	
	if(empty($t_id) || empty($f_id))
		die(header('Location: viewtopic.php?id='.$t_id));
	
	
	$query = "UPDATE ". __PREFIX__ ."topic SET f_id = ". $f_id ." WHERE t_id = " . $t_id;
	
	mysql_query ($query) or _err(mysql_error());
	
	print "<script>alert(\"Topic Spostato\"); window.location=\"viewforum.php?id=". $f_id ."\";</script>";
	exit;

}

function check_graphic_block_topic($id) {
	$check   = check_block_topic($id);
	$graphic = NULL;
	
	if($check == 1)
		$graphic = "<i>(Topic Chiuso)</i>";
		
	return $graphic;
}

function check_block_topic($id) {
	$query = mysql_query ("SELECT block FROM ". __PREFIX__ ."topic WHERE id = ". $id ."");

	$row = mysql_fetch_row ($query);
	
    return $row[0];	    
}

function check_graphic_important_topic($id) {
	$check   = check_important_topic($id);
	$graphic = NULL;
	
	if($check == 1)
		$graphic = "<i>[Importante]</i>";
		
	return $graphic;
}

function check_important_topic($id) {
	$query = mysql_query ("SELECT important FROM ". __PREFIX__ ."topic WHERE id = ". $id ."");

	$row = mysql_fetch_row ($query);
	
    return $row[0];	    
}

function set_topic($set_topic, $id) {

    if(empty($id))
        return FALSE;
        
    switch($set_topic) {

        case 'important':
            mysql_query("UPDATE ".__PREFIX__."topic SET important = 1, announcement = 0 WHERE id = ".(int) $id);        
        break;
        
        case 'announcement':
            mysql_query("UPDATE ".__PREFIX__."topic SET announcement = 1, important = 0 WHERE id = ".(int) $id);        
        break;
        
        case NULL:
            mysql_query("UPDATE ".__PREFIX__."topic SET important = 0, announcement = 0 WHERE id = ".(int) $id);
        break;
        
        default:
            return FALSE;
        break;
   }
   
   return TRUE;
}

function check_graphic_announcement_topic($id) {
	$check   = check_announcement_topic($id);
	$graphic = NULL;
	
	if($check == 1)
		$graphic = "<i>[Annuncio]</i>";
		
	return $graphic;
}

function check_announcement_topic($id) {
	$query = mysql_query ("SELECT announcement FROM ". __PREFIX__ ."topic WHERE id = ". $id ."");

	$row = mysql_fetch_row ($query);
	
    return $row[0];	    
}

function check_graphic_access_forum($id) {
	$check   = check_access_forum($id);
	$graphic = NULL;
	
	if($check == 'admin')
		$graphic = "<i><font color='darkred'>(Protetto ADMIN)</font></i>";
	
	if($check == 'mod')
		$graphic = "<i><font color='green'>(Protetto MOD)</font></i>";
	
	if($check == 'vip')
		$graphic = "<i><font color='gold'>(Protetto VIP)</font></i>";
		
	return $graphic;
}

function check_access_forum($id) {
	$query = "SELECT user_access 
				FROM ". __PREFIX__ ."forum 
			   WHERE id = '". $id ."'";
			   
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if($row[0] == NULL || $row[0] == '')
    	return 'user';
	else
    	return $row[0];
}

function check_access_topic($t_id) {
    $query = mysql_query ("
              SELECT user_access 
                FROM ". __PREFIX__ ."forum 
               WHERE id = (SELECT f_id 
                             FROM ". __PREFIX__ ."topic 
                            WHERE id = ". $t_id .")");
			   
	$row   = mysql_fetch_row ($query);
	
	if($row[0] == NULL || $row[0] == '')
    	return 'user';
	else
    	return $row[0];
}

function manage_block_topic($username, $id) {
	if((level($username) == 'admin') || (level($username) == 'mod')) {
		$block = check_block_topic($id);
		
		if($block == 0)
			$query = "UPDATE ". __PREFIX__ ."topic SET block = '1' WHERE id = '". $id ."'";
		elseif($block == 1)
			$query = "UPDATE ". __PREFIX__ ."topic SET block = '0' WHERE id = '". $id ."'";
		
		mysql_query ($query) or _err(mysql_error());
	}else{
		print "<script>alert(\"Operazione consentita solo ad Amministratori e Moderatori\");</script>";
	}
}

function random_pass($lunghezza = 6) {
	$lettere = explode(" ",
		  "A B C D E F G H I J K L M N O P Q R S T U V W X Y Z "
		. "a b c d e f g h i j k l m n o p q r s t u v w x y z "
		. "0 1 2 3 4 5 6 7 8 9"
	);

	for($i = 0; $i < $lunghezza; $i++) {
		srand((double) microtime() * 8622342);
		$foo  = rand(0, 61);
		$pass = $pass. $lettere[$foo];
	}

	return $pass;
}

function check_email_register($email) {
	$query = "SELECT * FROM ". __PREFIX__ ."users WHERE email = '". $email ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if($row[0] > 1)
		return FALSE; //esiste l'username quindi email non utilizzabile
	else
		return TRUE;  //altrimenti tutto bene
}

function check_exist_user($username) {
	$query = "SELECT * FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if ($row [0])
		return TRUE;
	else
		return FALSE;
}
		

function karma($user_id) {
global $karma;

	$query = "SELECT vote FROM ". __PREFIX__ ."karma WHERE vote_user_id = '{$user_id}'";
	$res   = mysql_query ($query) or _err(mysql_error());
	
	while($row = mysql_fetch_row($res)) 
			$karma += $row[0];
			
		if($karma == NULL)
			$karma = 0;
			
		if($karma > 0)
			$karma = "<b><font color = 'green'>+{$karma}</font></b>";
		elseif($karma < 0)
			$karma = "<b><font color = 'red'>{$karma}</font></b>";
		else
			$karma = "<b><font color = 'grey'>{$karma}</font></b>";
		
return $karma;
}

function check_email($email) {
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function check_url($url) {
	if(preg_match("/^(https?:\/\/+[\w\-]+\.[\w\-]+)/i",$url))
		return TRUE;
	else
		return FALSE;
}

function check_null($check, $mode) {
	$value = '<i>NULL</i>';
	
	if(!($check))
		return $value;
		
	switch ($mode) {
		case 1;//for msn contact and email contact :P
			if($check == "")
				$value = '<i>NULL</i>';
			else
				$value = $check;
		break;
		
		case 2;//for web_site
			if($check == "")
				$value = '<i>NULL</i>';
			else
				$value = '<a href="'.$check.'">'.$check.'</a>';
		break;
	}

	return $value;
}

function check_level($level) {
		switch ($level) {
			case 'admin'://se è admin
				$category = "<font color=\"red\">Administrator</font>";
			break;
			
			case 'banned';//se è bannato
				$category = "<i>Banned</i>";
			break;
			
			case 'mod';//se è moderatore
				$category = "<font color=\"green\">Moderator</font>";
			break;
			
			case 'vip'://se è vip
				$category = "<font color=\"gold\">VIP</font>";
			break;
			
			case NULL;//se l'utente è cancellato
				$category = "Utente Cancellato";
			break;
			
			default://altrimenti utente normale
				$category = "User";
			break;
		}
	return $category;
}

function print_pagination($num_page, $page, $id) {
    if($num_page > 1) {
        for ($i = 1; $i <= $num_page - 1; $i++) {
            if ($i < $num_page)
                print " <a href=\"viewforum.php?id=". $id ."&page=". $i ."\">". $i ."</a>";
        }
    }else{
		print " 1";
    }
}

function patch_forum($id, $mode) {
	switch ($mode) {
		case 1;
			$query = "SELECT f_id FROM ". __PREFIX__ ."topic WHERE id = '". $id ."'";
			
			list ($f_id) = mysql_fetch_row (mysql_query ($query));
			
			$query = "SELECT title FROM ". __PREFIX__ ."forum WHERE id = '".$f_id."}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			print "-> <a href = 'viewforum.php?id=". $f_id ."'>". $row[0] ."</a> ";
		break;
		
		case 2;
			$query = "SELECT title FROM ". __PREFIX__ ."forum WHERE id = '". $id ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			print "-> <a href = 'viewforum.php?id=". $id ."'>". $row[0] ."</a> ";
		break;
	}
}
	
function patch_topic($id) {
	$query = "SELECT title FROM ". __PREFIX__ ."topic WHERE id = '". $id ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	print "-> <a href = 'viewtopic.php?id=". $id ."'>{$row[0]}</a>";
}

function check_num_topic($author) {
	$topics = '<i>NULL</i>';
	
	if(!($author))
		return $topics;
		
	$query  = "SELECT id FROM ". __PREFIX__ ."topic WHERE author = '{$author}'";
	$topics = 0;
	$res    = mysql_query ($query);
	
	while (mysql_fetch_row ($res))
		$topics++;
		
	return $topics;
}

function check_exist_forum() {
	$query = "SELECT * FROM ". __PREFIX__ ."forum";
	$res   = mysql_query ($query);
	$num_forums = mysql_num_rows($res);
	
	if($num_forums >= 1)
		return TRUE;
	else
		return FALSE;
}

function BBcode($text) {

	//$text = nl2br($text);
	$text = str_replace("\n","<br />",$text);

	//escape
	$text = str_replace("&egrave;","è",$text);
	$text = str_replace("&agrave;","à",$text);
	$text = str_replace("&quot;","\"",$text);
	$text = str_replace("&ugrave;","ù",$text);
	$text = str_replace("&Igrave;","ì",$text);
	$text = str_replace("&nbsp;"," ",$text);
	$text = str_replace("&euro;","€",$text);
	
	/* Color */
	$text = str_replace("[red]", "<font color=\"#ff0000\">", $text);
	$text = str_replace("[/red]", "</font><!-- red -->", $text);
	$text = str_replace("[green]", "<font color=\"#7FFF00\">", $text);
	$text = str_replace("[/green]", "</font><!-- green -->", $text);
	$text = str_replace("[yellow]", "<font color=\"#ffff00\">", $text);
	$text = str_replace("[/yellow]", "</font><!-- yellow -->", $text);
	
	/* Smile */
	$text = str_replace(":)", "<img alt=\":)\" src=\"img/01.jpg\">", $text);
	$text = str_replace(":D", "<img alt=\":D\" src=\"img/02.jpg\">", $text);
	$text = str_replace(";)", "<img alt=\";)\" src=\"img/03.jpg\" >", $text);
	$text = str_replace("^_^", "<img alt=\"^_^\" src=\"img/04.gif\">", $text);
	$text = str_replace(":(", "<img alt=\":(\" src=\"img/05.gif\">", $text);
	$text = str_replace("0mg", "<img alt=\"0mg\" src=\"img/0mg.gif\">", $text);

	/* BBcode */
	$text = str_replace("[img]", "<img src=\"", $text);
	$text = str_replace("[/img]", "\"><!-- immagine -->", $text);
	$text = str_replace("[b]", "<b>", $text);
	$text = str_replace("[/b]", "</b>", $text);
	$text = str_replace("[i]", "<i>", $text);
	$text = str_replace("[/i]", "</i>", $text);
	$text = str_replace("[u]", "<u>", $text);
	$text = str_replace("[/u]", "</u>", $text);
	$text = str_replace("[center]", "<center>", $text);
	$text = str_replace("[/center]", "</center>", $text);
	$text = str_replace("[quote]", "<div class=\"quote\">Quote:<hr /><pre>", $text);
	$text = str_replace("[/quote]", "</pre></div><br /><!-- quote -->", $text);
	$text = str_replace("[code]", "<div class=\"code\">Code:<hr /><pre>", $text);
	$text = str_replace("[/code]", "</pre></div><br /><!-- code -->", $text);

	//Link BBcode
	$search = array(
		"/\\[url\\](.*?)\\[\\/url\\]/is",
		"/\\[url\\=(.*?)\\](.*?)\\[\\/url\\]/is",
		"/\\[youtube\\](.*?)\\[\\/youtube\\]/is"
	);
 
    $replace = array(
		"<a target=\"_blank\" href=\"$1\">$1</a>",
		"<a target=\"_blank\" href=\"$1\">$2</a>",
		"<br /><iframe title=\"YouTube video player\" width=\"480\" height=\"390\" src=\"http://www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>"
	);
    
	$text = preg_replace ($search, $replace, $text);

	return $text;
}

function clear($string) {
	return mysql_real_escape_string (htmlspecialchars (stripslashes ($string)));
}

function get_data () {
	$data = @array (clear ($_COOKIE ['0xBB_user']), clear ($_COOKIE ['0xBB_pass']));
	return $data;
}

function login ($username, $password) {

	if ((!$username) || (!$password))
		return FALSE;
		
	$query = "SELECT password FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if ($row [0] == $password) 
		return TRUE;
	else
		return FALSE;
}

function show_menu () {

	list ($username, $password) = get_data ();
	
	if (login ($username, $password)) {
	
		$not_read = 0;
		
		$query = "SELECT level FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
		$row   = mysql_fetch_row (mysql_query ($query));
		
		$query = "SELECT id FROM ". __PREFIX__ ."pm WHERE to_usr = '". $username ."' AND new = 1";
		$res   = mysql_query ($query);
		
		while (mysql_fetch_row ($res))
			$not_read++;
		?>
		<div class="menu" id="menu" >
			<ul>
				<li><b>Benvenuto, <a href="profile.php?id=<?php print nick2uid($username); ?>"><?php print $username; ?></a>!</b></li>
				<li><a href = 'settings.php'>[Pannello utente]</a></li>
				<li><a href = 'users_list.php'>Lista Utenti</a></li>
		<?php
		if ($not_read)
			print "\t\t\t<li><b><a href = 'pm.php?mode=1'>{$not_read} new PM(s)</a></b></li>\n";
		else
			print "\t\t<li><a href = 'pm.php?mode=1'>No new PMs</a></li>\n";

		if ($row [0] == 'admin')
			print "\t\t\t\t<li><a href = 'admin.php'>[Amministrazione]</a></li>\n";

		if($row[0] == 'mod')
			print "\t<li><a href = 'modcp.php'>[-Mod Panel-]</a></li>\n";
		?>
				<li><a href = 'index.php?logout=1'>Logout</a></li>
			</ul>
		</div>
	<div class = 'main' id = 'main'>
	<?php
	}else{//Se non si è loggati allora Guest :P
	?>
		<div class = 'menu' id = 'menu'>
			<ul>
				<li><b>Benvenuto, Guest!</b></li>
				<li><a href = 'users_list.php'>Lista Utenti</a></li>
				<li><a href = 'login.php'>Login</a></li>
				<li><a href = 'register.php'>Register</a></li>
			</ul>
		</div>
		<div class = 'main' id = 'main'>
	<?php
	}
}

function nick2uid($nick) {
	$query = "SELECT id FROM ". __PREFIX__ ."users WHERE username = '". $nick ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if ($row [0])
		return $row [0];
	else
		return FALSE;
}

function user_id($id) {
	$query = "SELECT username FROM ". __PREFIX__ ."users WHERE id = '". $id ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if ($row [0])
		return $row [0];
	else
		return FALSE;
}

function level($nick) {
	$query = "SELECT level FROM ". __PREFIX__ ."users WHERE username = '". $nick ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	return $row[0];
}

function check_user($email) {
	$query = "SELECT username FROM ". __PREFIX__ ."users WHERE email = '". $email ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if($row < 1)
		return FALSE;//non esiste nessuno con quella email :S
	
	return $row[0];//ritorno l'username
}

function check_forum_id ($id) {

	if (!$id)
		return FALSE;
		
	$query = "SELECT title FROM ". __PREFIX__ ."forum WHERE id = '". $id ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if ($row [0])
		return TRUE;
	else
		return FALSE;
}

function check_topic_id ($id) {

	if (!$id)
		return FALSE;
		
	$query = "SELECT f_id FROM ". __PREFIX__ ."topic WHERE id = '". $id ."' AND replyof = '-1'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if (!$row [0])
		return FALSE;
		
	return check_forum_id ($row [0]);
}

function is_post ($id) {

	if (!$id)
		return FALSE;
		
	$query = "SELECT f_id FROM ". __PREFIX__ ."topic WHERE id = '". $id ."'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if (!$row [0])
		return FALSE;
		
	return check_forum_id ($row [0]);
}

function stats ($stat) {
	
	switch($stat) {
		case 1://numero topic 
			$sql       = mysql_query("SELECT id FROM ". __PREFIX__ ."topic WHERE replyof < 0");
			$num_topic = mysql_num_rows($sql);
			return $num_topic;
		break;
		
		case 2://numero utenti iscritti
			$sql       = mysql_query("SELECT id FROM ". __PREFIX__ ."users");
			$num_users = mysql_num_rows($sql);
			return $num_users;
		break;
		
		case 3: //numero messaggi
			$sql      = mysql_query("SELECT * FROM ". __PREFIX__ ."topic");
			$num_post = mysql_num_rows($sql);
			return $num_post;
		break;
		
		default:
			return FALSE;
		break;
	}
}

function footer() { 

	print "\n</div>"// chiudo la div main
	    . "\n<div class = \"footer\">"
		. "\n<div id=\"sinistra\" >"
		. "\nNumero Topic Totali: ".stats(1)
		. "\n<br /> "
		. "\nNumero Messaggi: ".stats(3)
		. "\n</div>"// end left
		
		. "\n<div id=\"destra\">"
		. "\nNumero Iscritti: ".stats(2)
		. "\n</div>"//end right
		. "\n<div id=\"powered\">"
		. "\nPowered by <b><a href = \"http://0xproject.netsons.org/#0xBB\">0xBB</a> v " . VERSION . "</b>"
		. "\n</div>"
		. "\n";
}
?>
