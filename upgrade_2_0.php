<?php
ob_start();

if(!file_exists("config.php"))
	die("File config.php does not exist!");
else
	include("config.php");

?>
<html>
<head><title>Upgrade File</title></head>
<body>
<h1 align="center">Upgrade System of 0xBB</h1><br />		  
<?php

if(@$_GET['send'] == 1) {

	mysql_query("ALTER TABLE `". PREFIX ."settings` ADD site_name TEXT NOT NULL") or die(mysql_error());
	
	mysql_query("UPDATE ".PREFIX."settings SET site_name = '".SITE_NAME."'") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."settings` ADD description TEXT NOT NULL") or die(mysql_error());
	
	mysql_query("UPDATE ".PREFIX."settings SET description = '".DESCRIZIONE."'") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."users` DROP COLUMN background") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."users` DROP COLUMN text") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."users` ADD firma TEXT") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."users` ADD theme TEXT") or die(mysql_error());
	
	mysql_query("UPDATE `". PREFIX ."users` SET theme = 'default.css'") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."users`
					  CHANGE `web_site` `web_site` text COLLATE 'latin1_swedish_ci' NULL AFTER `email`,
					  CHANGE `msn` `msn` text COLLATE 'latin1_swedish_ci' NULL AFTER `web_site`,
					  COMMENT=''
					  REMOVE PARTITIONING;") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."forum` ADD user_access VARCHAR(20)") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."topic` ADD important INT") or die(mysql_error());
	
	mysql_query("UPDATE ".PREFIX."topic SET important = 0") or die(mysql_error());
		
	mysql_query("ALTER TABLE `". PREFIX ."topic` ADD announcement INT") or die(mysql_error());
	
	mysql_query("UPDATE ".PREFIX."topic SET announcement = 0") or die(mysql_error());
	
	mysql_query("ALTER TABLE `". PREFIX ."forum` ADD position INT") or die(mysql_error());
	
	// setto le posizioni di default dei forum
	$position = 0;
	$sql = mysql_query("SELECT id FROM ".PREFIX."forum");
	
	while ($row = mysql_fetch_array($sql)) {
		mysql_query("UPDATE ".PREFIX."forum SET position = ".$position." WHERE id = ".$row['id']) or die(mysql_error());
		$position++;
	}
	
	//creo il file config.php ;)
	$config = '<?php
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
 * Software version:			Config File v2.x
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =======================================================================*
 * config.php                                                        
 ***************************************************************************/

define("__PREFIX__", "'. PREFIX .'");					//prefisso tabelle
define("SERVER_NAME", "'. $_SERVER['SERVER_NAME'] .'");	//url base del sito per l\'anti CSRF/XSRF

//Dati per la connessione al Database MySQL
$db_host = "'.$db_host.'";
$db_user = "'.$db_user.'";
$db_pass = "'.$db_pass.'";
$db_name = "'.$db_name.'";

  mysql_connect ($db_host, $db_user, $db_pass) or die (mysql_error());
mysql_select_db ($db_name) or die (mysql_error());
?>';
	
		// Scriviamo sul config.php i dati che ci occorrono
		if(!($open = fopen( "config.php", "w" )))
			die("Errore durante l'apertura sul file config.php<br /> Prego di controllare i permessi sul file!");
			
		fwrite ($open, $config);//Scrivo sul file config.php
		
		fclose ($open); // chiudo il file
		print "<script>alert(\"Upgrade System with success\");</script>";
		header('Location: index.php');
}else{
	print "\n<br />"
		. "\nUpgrade v1.x -> v2.0"
		. "\n<br />"
		. "\n<form method=\"POST\" action=\"?send=1\" />"
		. "\n<input type=\"submit\" value=\"Upgrade\" />"
		. "\n</form>";
}
	
?>
</body>
</html>
