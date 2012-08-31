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
 * install.php
 ***************************************************************************/
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

<?php
if (!( file_exists('./config.php')))
	die("[ERROR]  File config.php inesistente!");
	
if (!(is_writable('./config.php')))
	die("[ERROR] File config.php non &egrave; scrivibile!");
	
if(!(phpversion() >= '5.2.0')) {
	die('<h2 align="center">In questo server √® installata una versione di PHP inferiore alla 5.2.0, quindi 0xBB non potr√† essere installato!<br />
		Si contatti l\'amministratore del server per aggiornare la versione di PHP installata sul server almeno alla 5.2.0</h2>');
}

if(@$_GET['action'] == 'install') {

	if (isset($_POST['descrizione']) 
		&& isset($_POST['user_admin']) 
		&& isset($_POST['pass_admin'])
		&& isset($_POST['host'])
		&& isset($_POST['user'])
		&& isset($_POST['name'])
		&& isset($_POST['__PREFIX__'])
		&& isset($_POST['email_admin'])
		&& isset($_POST['site_name'])) {
		
	//Dati Connessione MySQL
	$db_host = trim($_POST['host']);
	$db_user = trim($_POST['user']);
	$db_pass = trim($_POST['pass']);
	$db_name = trim($_POST['name']);

	/* controllo connessione avvenuta :P */
	$db = mysql_connect ($db_host , $db_user ,$db_pass);

	if($db == FALSE)
		die("<script>alert(\"Errore!\nControllare i dati per la connessione al database MySQL\"); window.location=\"".$_SERVER['PHP_SELF']."\";</script>");

	$select_db = mysql_select_db ($db_name,$db);

	if($select_db == FALSE)
		die("<script>alert(\"Errore!\nControllare i dati per la connessione al database MySQL\"); window.location=\"".$_SERVER['PHP_SELF']."\";</script>");
		
	//*******************************
		
	//Vari Dati
	$site_name   = mysql_real_escape_string(trim(htmlspecialchars($_POST['site_name'])));
	$descrizione = mysql_real_escape_string(trim(htmlspecialchars($_POST['descrizione'])));
	$__PREFIX__      = trim($_POST['__PREFIX__']);

	//Dati amministrazione
	$user_admin     = trim(mysql_real_escape_string($_POST['user_admin']));
	$pass_admin     = md5($_POST['pass_admin']);
	$email_admin    = trim(mysql_real_escape_string($_POST['email_admin']));

	$sql_settings = "CREATE TABLE ". $__PREFIX__ ."settings (
		site_name TEXT NOT NULL,
		description TEXT NOT NULL,
		block_register INT NOT NULL,
		maintenance INT NOT NULL
	);";

	mysql_query($sql_settings) or die ("SQL Error:".mysql_error());
		print "Table '". $__PREFIX__ ."settings' creata con successo<br />\n";
		
		mysql_query("INSERT INTO ". $__PREFIX__ ."settings (site_name, description, block_register, maintenance) VALUES ('{$site_name}', '{$descrizione}', '0', '0');") or _err(mysql_error());
		
	$sql_users = "CREATE TABLE ". $__PREFIX__ ."users (
		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		username VARCHAR (20) NOT NULL,
		password VARCHAR (32) NOT NULL,
		level TEXT NOT NULL,
		email TEXT NOT NULL,
		web_site TEXT,
		msn TEXT,
		firma TEXT,
		theme TEXT
		
	);";

	mysql_query($sql_users) or die ("SQL Error:".mysql_error());
		print "Table '". $__PREFIX__ ."users' creata con successo<br />\n";

	$sql_forum = "CREATE TABLE ". $__PREFIX__ ."forum (
		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		title VARCHAR (20) NOT NULL,
		description VARCHAR (100) NOT NULL,
		user_access VARCHAR(20),
		position INT
	);";
			
	mysql_query($sql_forum) or die ("SQL Error:".mysql_error());
		print "Table '". $__PREFIX__ ."forum' creata con successo<br />\n";
		
	$sql_topic = "CREATE TABLE ". $__PREFIX__ ."topic (
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
		block INT NOT NULL,
		important INT DEFAULT 0,
		announcement INT DEFAULT 0
	);";
			
	mysql_query($sql_topic) or die ("SQL Error:".mysql_error());
		print "Table '". $__PREFIX__ ."topic' creata con successo<br />\n";
		
	$sql_pm = "CREATE TABLE ". $__PREFIX__ ."pm (
		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		from_usr VARCHAR (20) NOT NULL,
		to_usr VARCHAR (20) NOT NULL,
		title VARCHAR (60) NOT NULL,
		data TEXT NOT NULL,
		new INT NOT NULL
	);";
			
	mysql_query($sql_pm) or die ("SQL Error:".mysql_error());
		print "Table '". $__PREFIX__ ."pm' creata con successo<br />\n";
		
		$sql_karma = "CREATE TABLE ". $__PREFIX__ ."karma (
		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		vote_user_id VARCHAR (255) NOT NULL,
		vote INT NOT NULL
	);";
			
	mysql_query($sql_karma) or die ("SQL Error:".mysql_error());
		print "Table '". $__PREFIX__ ."karma' creata con successo<br />\n";
		
		$sql_ban_ip = "CREATE TABLE ". $__PREFIX__ ."ban_ip (
		id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
		user_id VARCHAR (255) NOT NULL,
		ip VARCHAR (255) NOT NULL,
		banned INT NOT NULL
	);";
			
	mysql_query($sql_ban_ip) or die ("SQL Error:".mysql_error());
		print "Table '". $__PREFIX__ ."ban_ip' creata con successo<br />\n";	
		
		/*inserisco i dati dell'admin in ban ip */
	mysql_query("INSERT INTO ". $__PREFIX__ ."ban_ip (user_id, ip, banned) VALUES ('1', '".$_SERVER['REMOTE_ADDR']."', '0');") or _err(mysql_error());
		/* fine inserimento */
		
	$sql_admin_user = "INSERT INTO ". $__PREFIX__ ."users (
		id, username, password, level, email, web_site, msn, firma, theme
		) VALUES (
		'1', '{$user_admin}', '{$pass_admin}', 'admin', '{$email_admin}', 'NULL', 'NULL', 'Administrator Site', 'default.css');";
			
	mysql_query($sql_admin_user) or die ("SQL Error:".mysql_error());
		print "User '<i>". $user_admin ."</i>' Added with success<br />\n";
		
	$sql_first_forum = "INSERT INTO ". $__PREFIX__ ."forum (
		title, description, user_access, position
		) VALUES (
		'Benvenuto in 0xBB', 'Questo &egrave; il primo forum di 0xBB', 'user', 1);";
			
	mysql_query($sql_first_forum) or die ("SQL Error:".mysql_error());

	$sql_first_topic = "INSERT INTO ". $__PREFIX__ ."topic (
			f_id, author, title, data, replyof, last, ora, date, important, announcement
		) VALUES (
			1, '{$user_admin}', 'Benvenuto', 'Comunico che la board 0xBB &egrave; stata installata con successo!<br /><br />
			Per segnalazioni di Bug alla board o semplicemente supporto cliccate <a href=\"http://0xproject.netsons.org/bug_tracker\">QUI</a>', -1, 0, '".date("G:i")."', '".date("d-m-y")."', 0, 1
		);";
			
	mysql_query($sql_first_topic) or die ("SQL Error:".mysql_error());

	$sql_karma = "INSERT INTO ". $__PREFIX__ ."karma (`vote_user_id`, `vote`) VALUES ('1', '0');";

	mysql_query($sql_karma) or _err(mysql_error());

	mysql_close($db);		

	// creazione contenuto file config.php
	$config = '<?php
	/**************************************************************************
	 * 					0xBB ~ Bullettin Board						          *
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
	 * Software version:			Config File v2.x
	 * Author:						KinG-InFeT
	 * Copyleft:					GNU General Public License              
	 * =======================================================================*
	 * config.php                                                        
	 **************************************************************************/

	define("__PREFIX__", "'.$__PREFIX__.'");						//prefisso tabelle
	define("SERVER_NAME", "'.$_SERVER['SERVER_NAME'].'");	//url base del sito per l\'anti csrf/xsrf

	//Dati per la connessione al Database MySQL
	$db_host = "'.$db_host.'";
	$db_user = "'.$db_user.'";
	$db_pass = "'.$db_pass.'";
	$db_name = "'.$db_name.'";

	mysql_connect ($db_host, $db_user, $db_pass) or die (mysql_error());
	mysql_select_db ($db_name) or die (mysql_error());

	?>';
		
			// Scriviamo sul config.php i dati che ci occorrono
			$open = fopen('config.php', 'w'); // se il file gi‡† esiste verr‡† totalmente sovrascritto
			fwrite ($open, $config);//Scrivo sul file config.php
			fclose ($open); // chiudo il file
			print "config.php creato con successo<br />\n";

		if(@unlink("./install.php")) { //cancello il file
			print "File install.php cancellato<br />"
				. "\n<br /><font color=green>Installazione avvenuta con successo!</font>\n"
				. "\nTornare alla <a href=\"index.php\">Home-Page</a>"; 
		}else	// se non ci riesco esplicito che deve essere cancellato manualmente
			print '<font color=red>Errore! File install.php ancora esistente, settare i permetti a 777 (chmod) sul file install.php<br />'."\n".'
					Ed eliminarlo successivamente!<br />Torna alla <a href="index.php">Home-Page</a></font>';
	}else
		print "<span style=\"color:red; font-weight:bold;\">Errore! Riempire Tutti i Campi</span>\n";
}else{
?>
<center><h1>Installazione 0xBB</h1></center>
<table border="0" cellpadding="2" cellspacing="2">
  <tbody>
    <tr><td>
		<form action="?action=install" method="POST">
			<tr><td><b>Info Generali</b></td></tr>
			<tr><td>*Titolo Forum:</td><td><input type="text" name="site_name" /></td></tr>
			<tr><td>*Descrizione:</td><td><input type="text" name="descrizione" /></td></tr>
			<tr><td><br /></td></tr>
			<tr><td><b>Dati per la connessione al MySQL</b></td></td></tr></tr>
			<tr><td>*Hostname:</td><td><input type="text" name="host" value="localhost" /></td></tr>
			<tr><td>*Username:</td><td><input type="text" name="user" value="root" /></td></tr>
			<tr><td>Password:</td><td><input type="password" name="pass" /></td></tr>
			<tr><td>*DB Name:</td><td><input type="text" name="name" value="0xBB" /></td></tr>
			<tr><td>*Prefisso Tabelle:</td><td><input type="text" name="__PREFIX__" value="0xBB_" /></td></tr>
			<tr><td><br /></td></tr>
			<tr><td><b>Dati di amministrazione:</b></td></td></tr></tr>
			<tr><td>*Username:</td><td><input type="text" name="user_admin" /></td></tr>
			<tr><td>*Password:</td><td><input type="password" name="pass_admin" /></td></tr>
			<tr><td>*E-Mail:</td><td><input type="text" name="email_admin" /></td></tr>
			<tr><td><input type="submit" value="Installa!" name="install" /></td></tr>
		</form>
		<tr><td><b>*</b>: Campi obbligatori</td></tr>
      </td>
    </tr>
  </tbody>
</table>
<?php
}
?>
<center><pre>Powered By <a href="http://0xproject.netsons.org/#0xBB">0xBB<a/></pre>
</center>
</body>
</html>
