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
 * sendpassword.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
list ($username, $password) = get_data ();
if (login ($username, $password))
	die ('<br /><br /><br /><div class="error_msg" align="center">Sei già Loggato<br /><br /><a href="index.php">Torna alla Index</a></div>');

show_menu ();
$error_msg = array();//inizializzo l'array di errori

if(@$_GET['sendpassword'] == 1) {
	$email = clear ($_POST['email']);
	
	if(empty($email))
		$error_msg[] = "<font color=red><p><i>Inserire E-Mail per il recupero Password</i><p></font>";
	elseif(check_email ($email) == FALSE)
			$error_msg[] = "<font color=red><p><i>Email inserita non valida!</i><p></font>";
		elseif (check_user ($email)) {
			$new_password = random_pass();
			
			mysql_query("UPDATE ".PREFIX."users SET password = '".md5($new_password)."' WHERE email = '".$email."'") or die(mysql_error());
			
			$oggetto   = "Recupera password: ".SITE_NAME.".";
			$messaggio = "Hai utilizzato il modulo per la reimpostazione della password su ".SITE_NAME."\n"
						. "Ecco quindi la tua nuova password:\n\n"
						. "Password: ".$new_password."\n\n"
						."Lo Staff ~ ".SITE_NAME.".";
						
			@mail($email, $oggetto, $messaggio,"From: ".$email);
			
			print "<p align=\"center\">Email Inviata con la nuova password a: ".$email." :D</p>";
			header( "refresh:5; url=login.php" );
			exit;
		}else{
			$error_msg[] = "<font color=red><p><i>Email Inesistente nel Forum!</i><p></font>";
		}
}
if($error_msg) {
	echo '<div class="error_msg">
		  <h3 align="center">ERRORI DI SISTEMA</h2><br />
	          <br />';
 	foreach($error_msg as $error_message)
		print "$error_message <br />\n";
	echo "<br />\n<center><a href='javascript:history.back()'>Torna In Dietro</a>\n</center>\n</div>\n";
}else{

	if(check_maintenance(2) == 1) {
		echo "<h3 align='center'><p><font color='#FF0000'>Il Forum è in modalità Manutenzione.</font></p></h3>";
	}
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<div class = 'login'>
<br /><br />
<form method = 'POST' action="sendpassword.php?sendpassword=1">
	<table align = "center">
		<tr><td>Email:</td><td><input name = 'email' type = 'text'></td></tr>
		<tr><td><p><input value = "Recupera Password" type = 'submit'></p></tr></td>
	</table>
</form>
</div>
<?php
} //close else
?>
</div>
<?php
$top = NULL;
footer ($top);
?>
</body>
</html>
