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
 * login.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
list ($username, $password) = get_data ();
if (login ($username, $password))
	die ('<br /><br /><br /><div class="error_msg" align="center">Sei già Loggato<br /><br /><a href="index.php">Torna alla Index</a></div>');

show_menu ();
$error_msg = array();//inizializzo l'array di errori

if(@$_GET['login'] == 1) {
	$username = clear ($_POST ['username']);
	$password = $_POST ['password'];
	
	if(empty($username) && empty($password))
		$error_msg[] = "<font color=red><p><i>Inserire i dati per il Login!</i><p></font>";
		
	elseif(login ($username, md5($password)) == FALSE)
			$error_msg[] = "<font color=red><p><i>Dati inseriti Errati!</i><p></font>";
			
		elseif((check_maintenance(2) == 1) && (level($username) == 'user'))
			$error_msg[] = "<font color=red><p><i>Login Impossibile (Forum in Modalità Manutenzione)</i><p></font>";
			
		elseif (login ($username, md5 ($password)) == TRUE) {
			setcookie ("_user", $username);
			setcookie ("_pass", md5 ($password));
			
			//aggiorno l'IP dell'utente nel ban_ip
			mysql_query("UPDATE ".PREFIX."ban_ip SET ip = '".$_SERVER['REMOTE_ADDR']."' WHERE user_id = '".nick2uid($username)."'") or die(mysql_error());
			header ("Location: index.php");
			die ();
		}else{
			$error_msg[] = "<font color=red><p><i>Errore di Login! Riprova</i><p></font>";
		}
}
if($error_msg) {
	echo '<div class="error_msg">
		  <h3 align="center">ERRORI DI SISTEMA</h2><br />
	          <br />';
 	foreach($error_msg as $error_message)
		print $error_message."<br />\n";
		
	echo "<br />\n<center><a href='javascript:history.back()'>Torna Indietro</a>\n</center>\n</div>\n";
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
<form method = 'POST' action="login.php?login=1">
	<table align = "center">
		<tr><td>Username:</td><td><input name = 'username' type = 'text'></td></tr>
		<tr><td>Password:</td><td><input name = 'password' type = 'password'></td></tr>
		<tr><td><p><input value = 'Login' type = 'submit'></p></tr></td>
	</table>
</form>
<p>Se non sei ancora registrato <a href = 'register.php'>Registrati</a><br /></p>
<p>Hai dimenticato la password? <a href = 'sendpassword.php'>Recuperala</a></p>
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
