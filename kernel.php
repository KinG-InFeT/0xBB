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
 * Software version:			1.0 ~ RC3
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =========================================================================*
 * kernel.php                                                        
 ***************************************************************************/
ob_start();
include "config.php";
include "impaginazione.class.php";

define("VERSION","1.0 - RC3");//Versione della Board

list ($username, $password) = get_data ();//aquisisco i dati di autenticazione

if(file_exists("./install.php")) {
	header("Location: install.php");
	die();
}

check_ban_ip($_SERVER['REMOTE_ADDR'], $username);//controllo se l'IP è bannato oppure no :P
		
function show_header() {
?>
<html>
<head>
<title><?php echo SITE_NAME; ?></title>
<meta http-equiv = "Content-Type" CONTENT = "text/html; charset=UTF-8" />
<META NAME = "GENERATOR"          CONTENT = "VIM ~ The Linux Free Editor" />
<META NAME = "AUTHOR"             CONTENT = "KinG-InFeT" />
<META NAME = "COPYLEFT"           CONTENT = "Copyleft © By 0xBB" />

<link rel  = "stylesheet" type = "text/css" href = "style.php" />

<script type = 'text/javascript'>

var i = 0
var j = 0
var texteNE, affiche
var texte = '<b>/<?php echo SITE_NAME; ?>/<blink>_</blink></b><br /><i><?php echo DESCRIZIONE; ?></i>'
var ie = (document.all);
var ne = (document.layers); 
function title(){
	texteNE='';
	effetto_macchina();
}
function effetto_macchina(){
	texteNE=texteNE+texte.charAt(i)
	affiche='<font size=3 color=grey >'+texteNE+'</font>'
	
	if (texte.charAt(i) == "<") {
		j=1
	}
	if (texte.charAt(i) == ">") {
		j=0
	}
	if (j == 0) {
		if (document.getElementById) {
			document.getElementById("title").innerHTML = affiche;
		}
	}
	if (i < texte.length-1){
		i++
		setTimeout("effetto_macchina()",81)
	}
	else
		return
}
function info_BBcode() {
	window.open("info_BBcode.html", "BBcode ~ 0xBB", 'resizable=yes, scrollbars=yes,width=670,height=435')
}
</script>

</head>
<body onload="title()">
<p id="title" style="text-align: center;"></p>

<?php
}//end show_header

function csrf_attemp($ref) {
	$regola = "/".SERVER_NAME."/i";
	if(!preg_match($regola, $ref))
		die("CSRF/XSRF Hacking Attemp!");
}

function check_version() {
	$link = 'http://www.0xproject.hellospace.net/versions/0xBB.txt';
	$version_ufficial = file_get_contents($link);
	$version = VERSION;
	
	if ($version != $version_ufficial) {
		if (preg_match ("/admin.php/i", $_SERVER['SCRIPT_NAME'])) {
			echo "<script language=\"JavaScript\">if(confirm('Uscita la versione " . $version_ufficial . ". Vuoi venire reindirizzato alla pagina di download?.'))
				{
				 location.href = 'http://www.0xproject.hellospace.net/#0xBB';
			}
			</script>";
		}
	}
}	

function check_block_register() {
	$row =  mysql_fetch_row(mysql_query("SELECT block_register FROM ".PREFIX."settings"));
	if($row[0] == 1) {
		return FALSE;
	}else{
		return TRUE;
	}
}

function check_maintenance($mode) {    // questa funzione è stata fatta da schifo forse la migliorerò xd xd 
	list ($username, $password) = get_data ();
	$row =  mysql_fetch_row(mysql_query("SELECT maintenance FROM ".PREFIX."settings"));
	switch($mode) {
		case 1:
			if(($row[0] == 1) && (!login ($username, $password))) {
				print '<script>window.location="login.php";</script>';
			}elseif(($row[0] == 1) && (login ($username, $password))) {
				if((level($username) == 'admin') && (level($username) == 'moderator')) {
					//niente va tutto bene :P LOL ASD xD
				}
			}
		break;
		
		case 2:
			if($row[0] == 1) { return $row[0]; }
		break;
	}
}

function check_ban_ip($ip,$username) {
	$row = mysql_fetch_row(mysql_query("SELECT * FROM ".PREFIX."ban_ip WHERE user_id = '".nick2uid($username)."'"));
	if($row[3] == 1) {
		print "<script>alert(\"Il tuo IP è stato Bannato da questo Forum\");</script>";
		echo "<p align=\"center\">IP: <br />\n".$row[2]."\n<br />Stato: <b>BANNATO!</b></p>";
		die();
	}
}	

function check_t_id($id_topic) {
	$sql = "SELECT t_id FROM ".PREFIX."topic WHERE id = '{$id_topic}'";
	$row = mysql_fetch_row(mysql_query($sql));
	
	return $row[0];
}

function delete_topic($username,$id) {
	if((level($username) == 'admin') || (level($username) == 'moderator')) 
	{
		$query = "SELECT f_id FROM ".PREFIX."topic WHERE id = '{$id}'";
		$f_id  = mysql_fetch_row (mysql_query ($query));
		$t_id  = check_t_id($id);
		$sql   = "DELETE FROM ".PREFIX."topic WHERE t_id = '{$t_id}'";
		mysql_query($sql) or die(mysql_error());
		header("Location: viewforum.php?id=".$f_id[0]);
	}else{
		echo "<script>alert(\"Operazione consentita solo ad Amministratori e Moderatori\");</script>";
	}
}

function check_graphic_block_topic($id) {
	$check   = check_block_topic($id);
	$graphic = NULL;
	if($check == 1)
		$graphic = "<i>(Topic Chiuso)</i>";
		
	return $graphic;
}

function check_block_topic($id) {
	$query = "SELECT block FROM ".PREFIX."topic WHERE id = '{$id}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	return $row[0];
}

function manage_block_topic($username,$id) {
	if((level($username) == 'admin') || (level($username) == 'moderator')) 
	{
		$block = check_block_topic($id);
		if($block == 0)
			$query = "UPDATE ".PREFIX."topic SET block = '1' WHERE id = '{$id}'";
		elseif($block == 1)
			$query = "UPDATE ".PREFIX."topic SET block = '0' WHERE id = '{$id}'";
		
		mysql_query ($query) or die(mysql_error());
	}else{
		echo "<script>alert(\"Operazione consentita solo ad Amministratori e Moderatori\");</script>";
	}
}

function random_pass($lunghezza = 6) {
	$lettere = explode(" ",
		"A B C D E F G H I J K L M N O P Q R S T U V W X Y Z "
		."a b c d e f g h i j k l m n o p q r s t u v w x y z "
		."0 1 2 3 4 5 6 7 8 9");

	for($i = 0;$i < $lunghezza; $i++) {
		srand((double)microtime()*8622342);
		$foo  = rand(0, 61);
		$pass = $pass.$lettere[$foo];
	}

	return $pass;
}

function check_email_register($email) {
	$query = "SELECT * FROM ".PREFIX."users WHERE email = '{$email}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if($row[0] > 1)
		return FALSE;//esiste l'username quindi email non utilizzabile
	else
		return TRUE; //altrimenti tutto bene :D
}

function check_exist_user($username) {
	$query = "SELECT * FROM ".PREFIX."users WHERE username = '{$username}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	
	if ($row [0])
		return TRUE;
	else
		return FALSE;
}
		

function karma($user_id) {
global $karma;

	$query = "SELECT vote FROM ".PREFIX."karma WHERE vote_user_id = '{$user_id}'";
	$res   = mysql_query ($query) or die(mysql_error());
	
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
	//viva PHP5
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function check_url($url) {
	if(preg_match("/^(https?:\/\/+[\w\-]+\.[\w\-]+)/i",$url))
		return TRUE;
	else
		return FALSE;
}

function check_null($check,$mode) {
	$value = '<i>NULL</i>';
	if(!($check))
		return $value;
	switch ($mode) 
	{
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
		switch ($level) 
		{
			case 'admin'://se è admin
				$category = "<font color=\"red\">Administrator</font>";
			break;
			
			case 'banned';//se è bannato
				$category = "<i>Banned</i>";
			break;
			
			case 'moderator';//se è moderatore
				$category = "<font color=\"green\">Moderator</font>";
			break;
			
			case NULL;//se l'utente è cancellato ;)
				$category = "Utente Cancellato";
			break;
			
			default://altrimenti normale utente :P
				$category = "User";
			break;
		}
	return $category;
}

function clear_br($post) {
	$post = str_replace("<br />", "\n", $post);//&nbsp;
	return $post;
}

function pagination($num_pages,$page,$id) {
	for ($i = 1; $i <= $num_pages; $i++) {  

		if ($i == $page) 
			echo "-> ".$i; 
		else
			echo " <a href=\"viewforum.php?id=".$id."&page=".$i."\">$i</a>"; 
	}
}

function patch_forum($id,$mode) {
	switch ($mode) 
	{
		case 1;
			$query = "SELECT f_id FROM ".PREFIX."topic WHERE id = '{$id}'";
			list ($f_id) = mysql_fetch_row (mysql_query ($query));
			$query = "SELECT title FROM ".PREFIX."forum WHERE id = '{$f_id}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			print "-> <a href = 'viewforum.php?id=".$f_id."'>".$row[0]."</a> ";
		break;
		
		case 2;
			$query = "SELECT title FROM ".PREFIX."forum WHERE id = '{$id}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			print "-> <a href = 'viewforum.php?id=".$id."'>".$row[0]."</a> ";
		break;
	}
}
	
function patch_topic($id) {
	$query = "SELECT title FROM ".PREFIX."topic WHERE id = '{$id}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	print "-> <a href = 'viewtopic.php?id={$id}'>{$row[0]}</a>";
}

function check_num_topic($author) {
	$topics = '<i>NULL</i>';
	if(!($author))
		return $topics;
	$query  = "SELECT id FROM ".PREFIX."topic WHERE author = '{$author}'";
	$topics = 0;
	$res    = mysql_query ($query);
	while (mysql_fetch_row ($res))
		$topics++;
	return $topics;
}

function check_exist_forum() {
	$query = "SELECT * FROM ".PREFIX."forum";
	$res   = mysql_query ($query);
	$num_forums = mysql_num_rows($res);
	
	if($num_forums >= 1)
		return TRUE;
	else
		return FALSE;
}

function br($text) {
	return str_replace("\n", "<br />", $text);
}

function BBcode($testo) {
	/* Text Format */
	$testo = str_replace("[b]", "<b>", $testo);
	$testo = str_replace("[/b]", "</b>", $testo);
	$testo = str_replace("[s]", "<i>", $testo);
	$testo = str_replace("[/s]", "</i>", $testo);
	$testo = str_replace("[u]", "<u>", $testo);
	$testo = str_replace("[/u]", "</u>", $testo);
	
	/* Color */
	$testo = str_replace("[red]", "<font color=\"#ff0000\">", $testo);
	$testo = str_replace("[/red]", "</font><!-- red -->", $testo);
	$testo = str_replace("[green]", "<font color=\"#7FFF00\">", $testo);
	$testo = str_replace("[/green]", "</font><!-- green -->", $testo);
	$testo = str_replace("[yellow]", "<font color=\"#ffff00\">", $testo);
	$testo = str_replace("[/yellow]", "</font><!-- yellow -->", $testo);
	
	/* Smile */
	$testo = str_replace(":)", "<img alt=\"Sorriso\" src=\"smile/01.jpg\">", $testo);
	$testo = str_replace(":D", "<img alt=\"Felice\" src=\"smile/02.jpg\">", $testo);
	$testo = str_replace(";)", "<img alt=\"Occhiolino\" src=\"smile/03.jpg\" >", $testo);
	$testo = str_replace("^_^", "<img alt=\"Faccina Felice\" src=\"smile/04.gif\">", $testo);
	$testo = str_replace("0mg", "<img alt=\"0mg\" src=\"smile/0mg.gif\">", $testo);
	$testo = str_replace("omg", "<img alt=\"0mg\" src=\"smile/0mg.gif\">", $testo);
	$testo = str_replace(":(", "<img alt=\"Triste\" src=\"smile/06.gif\">", $testo);
	
	/* Other */
	$testo = str_replace("[quote]", "<div class=\"quote\">Quote:<br />", $testo);
	$testo = str_replace("[/quote]", "</div><!-- quote -->", $testo);
	$testo = str_replace("[code]", "<div class=\"code\">Code:<br />", $testo);
	$testo = str_replace("[/code]", "</div><!-- code -->", $testo);
	
	$search  = array("/\\[url\\](.*?)\\[\\/url\\]/is", "/\\[url\\=(.*?)\\](.*?)\\[\\/url\\]/is");
    $replace = array("<a target=\"_blank\" href=\"$1\">$1</a>", "<a target=\"_blank\" href=\"$1\">$2</a>");
 
    $testo = preg_replace ($search, $replace, $testo);
	//$testo = eregi_replace("(https?|ftp|http)://([^<>[:space:]]+)","<a target=\"_blank\" href=\"\\1://\\2\">\\1://\\2</a>",$testo); // Si trasformano tutti gli indirizzi di siti Web in link
	$testo = @eregi_replace("([a-z0-9\._-]+)(@[a-z0-9\.-_]+)(\.{1}[a-z]{2,6})","<a href=\"mailto:\\1\\2\\3\">\\1\\2\\3</a>",$testo); // Si trasformano tutti gli indirizzi e-mail in link
	return $testo;
}

function BBcode_revers($testo) {
	/* Text Format */
	$testo = str_replace("<b>", "[b]", $testo);
	$testo = str_replace("</b>", "[/b]", $testo);
	$testo = str_replace("<i>", "[s]", $testo);
	$testo = str_replace("</i>", "[/s]", $testo);
	$testo = str_replace("<u>", "[u]", $testo);
	$testo = str_replace("</u>", "[/u]", $testo);
	
	/* Color */
	$testo = str_replace("<font color=\"#ff0000\">","[red]", $testo);
	$testo = str_replace("</font><!-- red -->","[/red]", $testo);
	$testo = str_replace("<font color=\"#7FFF00\">","[green]", $testo);
	$testo = str_replace("</font><!-- green -->","[/green]", $testo);
	$testo = str_replace("<font color=\"#ffff00\">","[yellow]", $testo);
	$testo = str_replace("</font><!-- yellow -->","[/yellow]", $testo);
	
	/* Smile */
	$testo = str_replace("<img alt=\"Sorriso\" src=\"smile/01.jpg\">", ":)", $testo);
	$testo = str_replace("<img alt=\"Felice\" src=\"smile/02.jpg\">", ":D", $testo);
	$testo = str_replace("<img alt=\"Occhiolino\" src=\"smile/03.jpg\" >", ";)", $testo);
	$testo = str_replace("<img alt=\"Faccina Felice\" src=\"smile/04.gif\">", "^_^", $testo);
	$testo = str_replace("<img alt=\"0mg\" src=\"smile/0mg.gif\">", "0mg", $testo);
	$testo = str_replace("<img alt=\"0mg\" src=\"smile/0mg.gif\">", "omg", $testo);
	$testo = str_replace("<img alt=\"Triste\" src=\"smile/06.gif\">", ":(", $testo);
	
	/* Other */
	$testo = str_replace("<div class=\"quote\">Quote:<br />", "[quote]", $testo);
	$testo = str_replace("</div><!-- quote -->", "[/quote]", $testo);
	$testo = str_replace("<div class=\"code\">Code:<br />", "[code]", $testo);
	$testo = str_replace("</div><!-- code -->", "[/code]", $testo);
	return $testo;
}

function clear($string) {
	return mysql_real_escape_string (htmlspecialchars (stripslashes ($string)));
}

function get_data () {
	$data = @array (clear ($_COOKIE ['_user']), clear ($_COOKIE ['_pass']));
	return $data;
}

function login ($username, $password) {
	if ((!$username) || (!$password))
		return FALSE;
	$query = "SELECT password FROM ".PREFIX."users WHERE username = '{$username}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if ($row [0] == $password) 
		return TRUE;// tt  ok ;)
	else
		return FALSE;//altrimenti no xd
}

function show_menu () {

	list ($username, $password) = get_data ();
	if (login ($username, $password)) {
	
		$not_read = 0;
		
		$query = "SELECT level FROM ".PREFIX."users WHERE username = '{$username}'";
		$row   = mysql_fetch_row (mysql_query ($query));
		$query = "SELECT id FROM ".PREFIX."pm WHERE to_usr = '{$username}' AND new = 1";
		$res   = mysql_query ($query);
		while (mysql_fetch_row ($res))
			$not_read++;
		?>
		<div class = 'menu' id = 'menu'>
			<table>
				<tr>
					<td>Benvenuto, <a href="profile.php?id=<?php echo nick2uid($username); ?>"><?php echo $username; ?></a>!</td>
					<td><a href = 'settings.php'>[-UCP-]</a></td>
					<td><a href = 'users_list.php'>Lista Utenti</a></td>
		<?php
		if ($not_read)
			print "\t\t\t\t<td><b><a href = 'pm.php?mode=1'>{$not_read} new PM(s)</a></b></td>\n";
		else
			print "\t\t\t<td><a href = 'pm.php?mode=1'>No new PMs</a></td>\n";

		if ($row [0] == 'admin')
			print "\t\t\t\t\t<td><a href = 'admin.php'>[-Admin Panel-]</a></td>\n";

		if($row[0] == 'moderator')
			print "\t\t<td><a href = 'modcp.php'>[-Mod Panel-]</a></td>\n";
		?>
							<td><a href = 'stats.php'>Stats</a></td>					
			                <td><a href = 'index.php?logout=1'>Logout</a></td>
				</tr>
			</table>
		</div>
	<div class = 'main' id = 'main'>
	<?php
	}else{//Se non si è loggati allora Guest :P
	?>
		<div class = 'menu' id = 'menu'>
			<table>
				<tr>
					<td>Benvenuto, Guest!</td>
					<td><a href = 'users_list.php'>Lista Utenti</a></td>
					<td><a href = 'login.php'>Login</a></td>
					<td><a href = 'register.php'>Register</a></td>
					<td><a href = 'stats.php'>Stats</a></td>					
				</tr>
			</table>
		</div>
		<div class = 'main' id = 'main'>
	<?php
	}
}

function nick2uid($nick) {
	$query = "SELECT id FROM ".PREFIX."users WHERE username = '{$nick}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if ($row [0])
		return $row [0];
	else
		return FALSE;
}

function user_id($id) {
	$query = "SELECT username FROM ".PREFIX."users WHERE id = '{$id}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if ($row [0])
		return $row [0];
	else
		return FALSE;
}

function level($nick) {
	$query = "SELECT level FROM ".PREFIX."users WHERE username = '{$nick}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	return $row[0];
}

function check_user($email) {
	$query = "SELECT username FROM ".PREFIX."users WHERE email = '{$email}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if($row < 1)
		return FALSE;//non esiste nessuno con quella email :S
	
	return $row[0];//ritorno l'username
}

function check_forum_id ($id) {
	if (!$id)
		return FALSE;
	$query = "SELECT title FROM ".PREFIX."forum WHERE id = '{$id}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if ($row [0])
		return TRUE;
	else
		return FALSE;
}

function check_topic_id ($id) {
	if (!$id)
		return FALSE;
	$query = "SELECT f_id FROM ".PREFIX."topic WHERE id = '{$id}' AND replyof = '-1'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if (!$row [0])
		return FALSE;
	return check_forum_id ($row [0]);
}

function is_post ($id) {
	if (!$id)
		return FALSE;
	$query = "SELECT f_id FROM ".PREFIX."topic WHERE id = '{$id}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if (!$row [0])
		return FALSE;
	return check_forum_id ($row [0]);
}

function visite ($mode) {
	
	$db     = 'counter.txt';
	$a      = file($db);
	$visite = ( int ) $a[ 0 ] + 1;
	$open   = fopen($db,'w');
	fwrite($open, $visite);
	fclose($open);
	
	if($mode == 2)
		echo $visite;
	else
		return TRUE;
}

function stats ($stat) {

	switch($stat) {
		case 1;//numero topic 
			$sql       = mysql_query("SELECT * FROM ".PREFIX."topic WHERE replyof < 0");
			$num_topic = mysql_num_rows($sql);
			echo $num_topic;
		break;
		
		case 2;//numero utenti iscritti
			$sql       = mysql_query("SELECT * FROM ".PREFIX."users");
			$num_users = mysql_num_rows($sql);
			echo $num_users;
		break;
		
		case 4: //numero post
			$sql      = mysql_query("SELECT * FROM ".PREFIX."topic WHERE replyof > 0");
			$num_post = mysql_num_rows($sql);
			echo $num_post;
		break;
	}
		return FALSE;
}

function footer ($top) { 
	if($top > 0) { 
		$top += 13;
	}else{
		$top += 90;
	}
	
	visite(NULL);
	
	print '<div class = "footer" id = "footer" style="top: '.$top.'%"><br />'
		. '<hr /><p align="center">Powered by <b><a href = "http://0xproject.hellospace.net/#0xBB">0xBB</a> v '.VERSION.'</b></p>'
		. '</div>';
}
?>
