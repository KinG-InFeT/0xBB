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
 * Software version:			1.0 ~ RC2
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =========================================================================*
 * install.php
 ***************************************************************************/
 
$date = (date("d-m-y"));
$ora  = (date("G:i"));

if (!(is_writable('./config.php')))
	die("config.php non si ha i permessi per scriverlo");

if (!( file_exists('./config.php')))
	die("File config.php inesistente!");

if(@$_GET['action'] == 'install') {

if (isset($_POST['descrizione']) 
	&& isset($_POST['user_admin']) 
	&& isset($_POST['pass_admin'])
	&& isset($_POST['host'])
	&& isset($_POST['user'])
	&& isset($_POST['name'])
	&& isset($_POST['prefix'])
	&& isset($_POST['email_admin'])
	&& isset($_POST['site_name'])) {
	
//Vari Dati
$site_name   = trim(htmlspecialchars($_POST['site_name']));
$descrizione = trim(htmlspecialchars($_POST['descrizione']));
$prefix      = trim($_POST['prefix']);

//Dati amministrazione
$user_admin     = trim(mysql_real_escape_string($_POST['user_admin']));
$pass_admin     = md5($_POST['pass_admin']);
$email_admin    = trim(mysql_real_escape_string($_POST['email_admin']));
$msn_admin      = trim(mysql_real_escape_string($_POST['msn']));
$web_site_admin = trim(mysql_real_escape_string($_POST['web_site']));

//Dati Connessione MySQL
$db_host = $_POST['host'];
$db_user = $_POST['user'];
$db_pass = $_POST['pass'];
$db_name = $_POST['name'];

$db = mysql_connect ($db_host , $db_user ,$db_pass);
/* controllo connessione avvenuta :P */
if($db == FALSE) {
	die("<script>alert(\"Errore!\nControllare i dati per la connessione al database MySQL\"); window.location=\"".$_SERVER['PHP_SELF']."\";</script>");
}else{
	$select_db = mysql_select_db ($db_name,$db);
	if($select_db == FALSE)
		die("<script>alert(\"Errore!\nControllare i dati per la connessione al database MySQL\"); window.location=\"".$_SERVER['PHP_SELF']."\";</script>");
}
	//*******************************
$sql_settings= "CREATE TABLE ".$prefix."settings (
	block_register INT NOT NULL,
	maintenance INT NOT NULL
) ENGINE = MYISAM;";

mysql_query($sql_settings) or die ("SQL Error:".mysql_error());
	echo "Table '".$prefix."settings' created with success<br />\n";
	
	mysql_query("INSERT INTO ".$prefix."settings (block_register, maintenance) VALUES ('0', '0');") or die(mysql_error());
	
$sql_users= "CREATE TABLE ".$prefix."users (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	username VARCHAR (20) NOT NULL,
	password VARCHAR (32) NOT NULL,
	level TEXT NOT NULL,
	text VARCHAR (7) NOT NULL,
	background VARCHAR (7) NOT NULL,
	email TEXT NOT NULL,
	web_site TEXT NOT NULL,
	msn TEXT NOT NULL
) ENGINE = MYISAM;";

mysql_query($sql_users) or die ("SQL Error:".mysql_error());
	echo "Table '".$prefix."users' created with success<br />\n";

$sql_forum = "CREATE TABLE ".$prefix."forum (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR (20) NOT NULL,
	description VARCHAR (100) NOT NULL
) ENGINE = MYISAM;";
		
mysql_query($sql_forum) or die ("SQL Error:".mysql_error());
	echo "Table '".$prefix."forum' created with success<br />\n";
	
$sql_topic = "CREATE TABLE ".$prefix."topic (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	f_id INT NOT NULL,
	t_id INT NOT NULL,
	author VARCHAR (20) NOT NULL,
	title VARCHAR (60) NOT NULL,
	data TEXT NOT NULL,
	replyof INT NOT NULL,
	last INT (10) NOT NULL,
	ora TEXT NOT NULL,
	date TEXT NOT NULL,
	block INT NOT NULL
) ENGINE = MYISAM;";
		
mysql_query($sql_topic) or die ("SQL Error:".mysql_error());
	echo "Table '".$prefix."topic' created with success<br />\n";
	
$sql_pm = "CREATE TABLE ".$prefix."pm (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	from_usr VARCHAR (20) NOT NULL,
	to_usr VARCHAR (20) NOT NULL,
	title VARCHAR (60) NOT NULL,
	data TEXT NOT NULL,
	new INT NOT NULL
) ENGINE = MYISAM;";
		
mysql_query($sql_pm) or die ("SQL Error:".mysql_error());
	echo "Table '".$prefix."pm' created with success<br />\n";
	
	$sql_karma = "CREATE TABLE ".$prefix."karma (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	vote_user_id VARCHAR (255) NOT NULL,
	vote INT NOT NULL
) ENGINE = MYISAM;";
		
mysql_query($sql_karma) or die ("SQL Error:".mysql_error());
	echo "Table '".$prefix."karma' created with success<br />\n";
	
	$sql_ban_ip = "CREATE TABLE ".$prefix."ban_ip (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id VARCHAR (255) NOT NULL,
	ip VARCHAR (255) NOT NULL,
	banned INT NOT NULL
) ENGINE = MYISAM;";
		
mysql_query($sql_ban_ip) or die ("SQL Error:".mysql_error());
	echo "Table '".$prefix."ban_ip' created with success<br />\n";	
	
	/*inserisco i dati dell'admin in ban ip */
mysql_query("INSERT INTO ".$prefix."ban_ip (user_id, ip, banned) VALUES ('1', '".$_SERVER['REMOTE_ADDR']."', '0');") or die(mysql_error());
	/* fine inserimento */
	
$sql_admin_user = "INSERT INTO ".$prefix."users (
	id, username, password, level, text, background, email, web_site, msn
	) VALUES (
	'1', '{$user_admin}', '{$pass_admin}', 'admin', '#FFFFFF', '#000000', '{$email_admin}', '{$web_site_admin}', '{$msn_admin}');";
		
mysql_query($sql_admin_user) or die ("SQL Error:".mysql_error());
	echo "User '<i>$user_admin</i>' Added with success<br />\n";
	
$sql_first_forum= "INSERT INTO ".$prefix."forum (
	title, description
	) VALUES (
	'Benvenuto in 0xBB', 'Questo è il primo forum di 0xBB');";
		
mysql_query($sql_first_forum) or die ("SQL Error:".mysql_error());

$sql_first_topic= "INSERT INTO ".$prefix."topic (
	f_id, author, title, data, replyof, last, ora, date
	) VALUES (
	1, '{$user_admin}', 'Primo Topic', 'Comunico che la board 0xBB è stata installata con successo!<br /><br />Per segnalazioni di Bug alla board o semplicemente supporto cliccate <a href=\"http://0xproject.hellospace.net/forum\">QUI</a>', -1, 0, '{$ora}', '{$date}');";
		
mysql_query($sql_first_topic) or die ("SQL Error:".mysql_error());

$sql_karma = "INSERT INTO ".$prefix."karma (`vote_user_id`, `vote`) VALUES ('1', '0');";

mysql_query($sql_karma) or die(mysql_error());

mysql_close($db);		

// creazione contenuto file config.php
$config='<?php
/**************************************************************************
 * 					0xBB ~ Bullettin Board						         *
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
 * =======================================================================*
 * Software:					0xBB
 * Software version:			Config File v1.x
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =======================================================================*
 * config.php                                                        
 ***************************************************************************/

define("SITE_NAME","'.$site_name.'");//Nome del forum
define("DESCRIZIONE","'.$descrizione.'");//Descrizione del forum
define("PREFIX","'.$prefix.'");//prefisso tabelle
define("SERVER_NAME","'.$_SERVER['SERVER_NAME'].'");//url base del sito per l\'anti csrf/xsrf

//Dati per la connessione al Database MySQL
$db_host = "'.$db_host.'";
$db_user = "'.$db_user.'";
$db_pass = "'.$db_pass.'";
$db_name = "'.$db_name.'";

mysql_connect ($db_host, $db_user, $db_pass) or die (mysql_error());
mysql_select_db ($db_name) or die (mysql_error());

?>';
	
		// Scriviamo sul config.php i dati che ci occorrono
		$open = fopen('config.php', 'w'); // se il file già esiste verrà totalmente sovrascritto
		fwrite ($open, $config);//Scrivo sul file config.php
		fclose ($open); // chiudo il file
		echo "config.php creato con successo<br />\n";

	if(@unlink($_SERVER['PHP_SELF'])) { //cancello il file
		print 'File install.php cancellato<br />'."\n".'Tornare alla <a href="index.php">Home-Page</a>';
		echo "\n<br /><font color=green>Installazione avvenuta con successo!</font>\n"; //stampo l'avvenuto successo di installazione
	}else
		print '<font color=red>Errore! File install.php ancora esistente, settare i permetti a 777 (chmod) sul file install.php<br />'."\n".'
				Oppure eliminarlo manualmente!<br />Torna alla <a href="index.php">Home-Page</a></font>';
}else
	echo "<span style=\"color:red; font-weight:bold;\">Errore! Riempire Tutti i Campi</span>\n";// errore! i campi non sono stati inseriti
}else{
?>
<html>
<head>
<title>Installazione 0xBB </title>
<style type="text/css">
body {
	font-family: monospace;
}
</style>
</head>
<body bgcolor="#000000" text="white" >
<center><h1>Installazione 0xBB</h1></center>
<table border="0" cellpadding="2" cellspacing="2">
  <tbody>
    <tr><td>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=install" method="POST">
			<tr><td><b>Info Generali</b></td></tr>
			<tr><td>*Titolo Forum:</td><td><input type="text" name="site_name" /></td></tr>
			<tr><td>*Descrizione:</td><td><input type="text" name="descrizione" /></td></tr>
			<tr><td><br /></td></tr>
			<tr><td><b>Dati per la connessione al MySQL</b></td></td></tr></tr>
			<tr><td>*Hostname:</td><td><input type="text" name="host" value="localhost" /></td></tr>
			<tr><td>*Username:</td><td><input type="text" name="user" value="root" /></td></tr>
			<tr><td>Password:</td><td><input type="password" name="pass" /></td></tr>
			<tr><td>*DB Name:</td><td><input type="text" name="name" value="0xBB" /></td></tr>
			<tr><td>*Prefisso Tabelle:</td><td><input type="text" name="prefix" value="0xBB_" /></td></tr>
			<tr><td><br /></td></tr>
			<tr><td><b>Dati di amministrazione:</b></td></td></tr></tr>
			<tr><td>*Username:</td><td><input type="text" name="user_admin" /></td></tr>
			<tr><td>*Password:</td><td><input type="password" name="pass_admin" /></td></tr>
			<tr><td>*E-Mail:</td><td><input type="text" name="email_admin" /></td></tr>
			<tr><td>Contatto MsN:</td><td><input type="text" name="msn" /></td></tr>
			<tr><td>Web Site:</td><td><input type="text" name="web_site" /></td></tr>
			<tr><td><input type="submit" value="Installa!" name="install" /></td></tr>
		</form>
		<tr><td><b>*</b>: Campi obbligatori</td></tr>
      </td>
    </tr>
  </tbody>
</table>
<?php
}
echo "\n<center><pre>PoWeReD By <a href=\"http://0xproject.hellospace.net/#0xBB\">0xBB<a/></pre>\n</center>\n</body>\n</html>";
?>

