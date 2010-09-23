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
 * Software version:			1.0 ~ RC1
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =========================================================================*
 * settings.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu ();
list ($username, $password) = get_data ();

if (!login ($username, $password))
	die ("<div class=\"error_msg\">Devi essere registrato per accedere qui.<br /><br /><a href=\"index.php\">Torna alla Index</a></div>");

if(level($username) == 'banned')
	die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b><br /><b><u>".$username."</u></b> è stato BANNATO dal forum!.\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");

csrf_attemp($_SERVER['HTTP_REFERER']);

@$mode = $_GET ['mode'];
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<div class = 'settings'>
<?php
switch ($mode) {
	case 1:
		@$old_pass  = $_POST ['old_pass'];
		@$new_pass1 = $_POST ['new_pass1'];
		@$new_pass2 = $_POST ['new_pass2'];
		if (($old_pass) && ($new_pass1) && ($new_pass1 == $new_pass2)) {
			$old_pass  = md5 ($old_pass);
			$new_pass1 = md5 ($new_pass1);
			$query = "SELECT password FROM ".PREFIX."users WHERE username = '{$username}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if ($row [0] == $old_pass) {
				$query = "UPDATE ".PREFIX."users SET password = '{$new_pass1}' WHERE username = '{$username}'";
				mysql_query ($query);
				setcookie ("_pass", $new_pass1);
				echo "<script>alert(\"Password changed successfully.\"); window.location=\"settings.php\";</script>";
			}
			else 
				echo "La vecchia password è ERRATA!";
		}else {
			?>
			<p><b>Change password:</b><p>
			<form method = 'POST' action = 'settings.php?mode=1'>
				<table>
					<tr><td>Old password:</td><td><input name = 'old_pass' type = 'password'></td></tr>
					<tr><td>New password:</td><td><input name = 'new_pass1' type = 'password'></td></tr>
					<tr><td>New password (again):</td><td><input name = 'new_pass2' type = 'password'></td></tr>
					<tr><td><input type = 'submit' value = 'Change'></td></tr>
				</table>
			</form>
			<?php
		}
		break;
	case 2:
		@$text       = clear($_POST ['text']);
		@$background = clear($_POST ['background']);
		if (($text) && ($background)) {
			if ((!preg_match("|^[0-9A-Fa-f]{6}$|", $text)) || (!preg_match("|^[0-9A-Fa-f]{6}$|", $background)))
				echo "Il codice Hex non è valido!";
			else {
				$text = "#".$text;
				$background = "#".$background;
				$query = "UPDATE ".PREFIX."users SET text = '{$text}', background = '{$background}' WHERE username = '{$username}'";
				mysql_query ($query) or die(mysql_error());
				echo "<script>alert(\"Colors changed successfully.\");window.location=\"settings.php\"; </script>";
			}
		}
		?>
		<script>
		function change_text () {
		        if (document.change_color.text.value.length == 6)
		                document.getElementById ("text_try").style.background = "#"+document.change_color.text.value;
		}		
		function change_background () {
		        if (document.change_color.background.value.length == 6)
		                document.getElementById ("background_try").style.background = "#"+document.change_color.background.value;
		}
		</script>
		<p><b>Change colors:</b><p>
		<form name = 'change_color' method = 'POST' action = 'settings.php?mode=2'>
			<table>
				<tr><td>Text color (HEX format):</td>
				<td><input onChange = 'change_text();' maxlength = 6 name = 'text'></td>
				<td>Anteprima: <input id = 'text_try' readonly = 'readonly'></td></tr>
				<tr><td>Background color (HEX format):</td>
				<td><input onChange = 'change_background();' maxlength = 6 name = 'background'></td>
				<td>Anteprima: <input id = 'background_try' readonly = 'readonly'></td></tr>
				<tr><td><input type = 'submit' value = 'Change'></td></tr>
			</table>
		</form>
		<?php
		break;
	case 3;
		@$old_email  = $_POST ['old_email'];
		@$new_email1 = $_POST ['new_email1'];
		@$new_email2 = $_POST ['new_email2'];
		if (($old_email) && ($new_email1) && ($new_email1 == $new_email2)) {
			if(!(check_email($new_email1)))
				die("<br /><br /><center><b>Errore!</b>L' E-Mail inserita non è valida<br /><br /><a href='javascript: history.back ();'>Torna Indietro</a></center>");
			$query = "SELECT email FROM ".PREFIX."users WHERE username = '{$username}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if ($row [0] == $old_email) {
				$query = "UPDATE ".PREFIX."users SET email = '{$new_email1}' WHERE username = '{$username}'";
				mysql_query ($query) or die(mysql_error());
				echo "<script>alert(\"E-Mail changed successfully.\"); window.location=\"settings.php\";</script>";
			}else 
				echo "ERRORE! La vecchia E-Mail è errata!";
		}else{
			?>
			<p><b>Change E-Mail:</b><p>
			<form method = 'POST' action = 'settings.php?mode=3'>
				<table>
					<tr><td>Old E-Mail</td><td><input name = 'old_email' type = 'text'></td></tr>
					<tr><td>New E-Mail:</td><td><input name = 'new_email1' type = 'text'></td></tr>
					<tr><td>New E-Mail (again):</td><td><input name = 'new_email2' type = 'text'></td></tr>
					<tr><td><input type = 'submit' value = 'Change'></td></tr>
				</table>
			</form>
			<?php
		}
	break;
	
	case 4;
		@$new_web_site = $_POST ['new_web_site'];
		if ($new_web_site) {
				if(check_url($new_web_site) == FALSE)
					die('<div class="error_msg" align="center">Il Sito Web inserito non è valido<br />
						<a href=\'javascript:history.back()\'>Torna In Dietro</a></div>') ;
			$query = "UPDATE ".PREFIX."users SET web_site = '{$new_web_site}' WHERE username = '{$username}'";
			mysql_query ($query) or die(mysql_error());
			echo "<script>alert(\"Web Site Change Successfully.\"); window.location=\"settings.php\";</script>";
		}else{
			?>
			<p><b>Change Web Site:</b><p>
			<form method = 'POST' action = 'settings.php?mode=4'>
				<table>
					<tr><td>New Web Site: </td><td><input name = 'new_web_site' type = 'text' value="http://www."></td></tr>
					<tr><td><input type = 'submit' value = 'Change'></td></tr>
				</table>
			</form>
			<form method = 'POST' action = 'settings.php?mode=clear'>
				<input type='hidden' value='clear_web_site' name='clear' />
				<input type="submit" value="Svuota Campo" />
			</form>
			<?php			
		}
	break;
	
	case 5;
		@$new_msn = $_POST ['new_msn'];
		if ($new_msn) {
			if(!(check_email($new_msn)))
				die("<br /><br /><center><b>Errore!</b>\t Il contatto MsN inserito non è valido<br /><br /><a href='javascript: history.back ();'>Torna Indietro</a></center>");
			$query = "UPDATE ".PREFIX."users SET msn = '{$new_msn}' WHERE username = '{$username}'";
			mysql_query ($query) or die(mysql_error());
			echo "<script>alert(\"MsN Contact Change Successfully.\"); window.location=\"settings.php\";</script>";
		}else{
			?>
			<p><b>Change MsN Contact:</b><p>
			<form method = 'POST' action = 'settings.php?mode=5'>
				<table>
					<tr><td>New MsN Contact: </td><td><input name = 'new_msn' type = 'text'></td></tr>
					<tr><td><input type = 'submit' value = 'Change'></td></tr>
				</table>
			</form>
			<form method = 'POST' action = 'settings.php?mode=clear'>
				<input type='hidden' value='clear_msn' name='clear' />
				<input type="submit" value="Svuota Campo" />
			</form>
			<?php
		}
	break;
	
	case 'clear';
	
	switch(@$_POST['clear']) {
		case 'clear_web_site';
			mysql_query("UPDATE ".PREFIX."users SET web_site = '' WHERE username = '{$username}'");
			die('<div class="success_msg" align="center">Campo Svuotato!<br /><br /><a href="settings.php">Torna al Pannello</a></div>');
		break;
		
		case 'clear_msn';
			mysql_query("UPDATE ".PREFIX."users SET msn = '' WHERE username = '{$username}'");
			die('<div class="success_msg" align="center">Campo Svuotato!<br /><br /><a href="settings.php">Torna al Pannello</a></div>');
		break;
	}
	
	break;	
	
	default:
		?>
		<p><b>Control Pannel for User: <?php echo $username; ?></b><p>
		<a href = 'settings.php?mode=1'>-> Change password</a><br />
		<a href = 'settings.php?mode=3'>-> Change E-Mail</a><br />
		<a href = 'settings.php?mode=4'>-> Change My Site Web</a><br />
		<a href = 'settings.php?mode=5'>-> Change MsN Contact</a><br />		
		<a href = 'settings.php?mode=2'>-> Edit Theme Colors</a>
		<?php
		break;
}
?>
</div>
</div>
<?php
$top = NULL;
footer ($top);
?>
</body>
</html>
