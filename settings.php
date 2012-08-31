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
 * settings.php                                                        
 ***************************************************************************/
include "kernel.php";

show_header();
show_menu ();

list ($username, $password) = get_data ();

if (!login ($username, $password))
	die (header('Location: login.php'));

if(level($username) == 'banned')
	_err("<b>[ERROR] </b><br /><b><u>". $username ."</u></b> &egrave; stato BANNATO dal forum!");

csrf_attemp($_SERVER['HTTP_REFERER']);
?>
<div class = 'path' id = 'path'>
	<table>
	<?php if(empty($_GET['mode'])) {?>
		<tr><td><b><a href = 'index.php'>Home Forum</a></b></td></tr>
	<?php }else{ ?>
		<tr><td><b><a href = 'settings.php'>Home CP</a></b></td></tr>
	<?php }?>
	</table>
</div>
<div class = 'settings'>
<?php
switch (@$_GET ['mode']) {
	case 1:
		@$old_pass  = $_POST ['old_pass'];
		@$new_pass1 = $_POST ['new_pass1'];
		@$new_pass2 = $_POST ['new_pass2'];
		
		if (($old_pass) && ($new_pass1) && ($new_pass1 == $new_pass2)) {
			$old_pass  = md5 ($old_pass);
			$new_pass1 = md5 ($new_pass1);
			
			$query = "SELECT password 
						FROM ". __PREFIX__ ."users 
					   WHERE username = '". $username ."'";
					   
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if ($row [0] == $old_pass) {
			
				$query = "UPDATE ". __PREFIX__ ."users 
							 SET password = '{$new_pass1}' 
						   WHERE username = '". $username ."'";
						   
				mysql_query ($query);
				
				setcookie ("0xBB_pass", $new_pass1);
				
				print "<script>alert(\"Password cambiata con successo.\"); window.location=\"settings.php\";</script>";
			}else 
				print "<div class=\"error_msg\">La vecchia password &egrave; ERRATA!<br /><a href='javascript: history.back();'>Torna Indietro</a></div>";
		}else{
			?>
			<p><b>Cambia Password:</b><p>
			<form method = 'POST' action = 'settings.php?mode=1'>
				<table>
					<tr><td>Vecchia password:</td><td><input name = 'old_pass' type = 'password'></td></tr>
					<tr><td>Nuova password:</td><td><input name = 'new_pass1' type = 'password'></td></tr>
					<tr><td>Nuova password (again):</td><td><input name = 'new_pass2' type = 'password'></td></tr>
					<tr><td><input type = 'submit' value = 'Cambia'></td></tr>
				</table>
			</form>
			<?php
		}
		break;
	case 2:
		$themes = scandir("themes/");

		if (!empty($_GET['select'])) {
			if (in_array ($_GET['select'], $themes)) {
			
				mysql_query("UPDATE ". __PREFIX__ ."users 
								SET theme = '".clear($_GET['select'])."';");

				print "<script>alert(\"Tema Aggiornato\"); window.location.href = 'settings.php?mode=2';</script>";
			}else {
				die ("<script>alert(\"Errore! Il tema non &egrave; stato trovato!\"); window.location.href = 'settings.php?mode=2';</script>");
			}
		}else{
			print "<p><b>Cambia Tema:</b><p>\n<br /><br />";
			
			foreach ($themes as $theme)
				if ($theme != "." && $theme != "..")
					print "\n". $theme ." <a href = 'settings.php?mode=2&select=".$theme."'>Seleziona</a><br />";
		}
		
		break;
	case 3:
		@$old_email  = $_POST ['old_email'];
		@$new_email1 = $_POST ['new_email1'];
		@$new_email2 = $_POST ['new_email2'];
		
		if (($old_email) && ($new_email1) && ($new_email1 == $new_email2)) {
		
			if(!($old_email != $new_email1))
				_err("<b>Errore!</b>L' E-Mail inserita deve essere diversa da quella nuova!");
		
			if(!(check_email($new_email1)))
				_err("<b>Errore!</b>L' E-Mail inserita non &egrave; valida!");
				
			if(check_email_register($new_email1) == FALSE)
				_err("<b>Errore!</b>L' E-Mail inserita &egrave; gi&agrave; utilizzata da un'altro utente!");
				
			$query = "SELECT email FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if ($row [0] == $old_email) {
				$query = "UPDATE ". __PREFIX__ ."users SET email = '". $new_email1 ."' WHERE username = '". $username ."'";
				mysql_query ($query) or _err(mysql_error());
				
				print "<script>alert(\"E-Mail aggiornata con successo.\"); window.location=\"settings.php\";</script>";
			}else 
				_err("ERRORE! La vecchia E-Mail &egrave; errata!");
		}else{
			?>
			<p><b>Cambia E-Mail:</b><p>
			<form method = 'POST' action = 'settings.php?mode=3'>
				<table>
					<tr><td>Vecchia E-Mail</td><td><input name = 'old_email' type = 'text'></td></tr>
					<tr><td>Nuova E-Mail:</td><td><input name = 'new_email1' type = 'text'></td></tr>
					<tr><td>Nuova E-Mail (again):</td><td><input name = 'new_email2' type = 'text'></td></tr>
					<tr><td><input type = 'submit' value = 'Cambia'></td></tr>
				</table>
			</form>
			<?php
		}
	break;
	
	case 4:
		
		if(@$_GET['send'] == 1) {
				
			if ($_POST ['new_web_site']) {
				if(check_url($_POST ['new_web_site']) == FALSE)
					die('<div class="error_msg">Il Sito Web inserito non &egrave; valido<br />
						<a href=\'javascript:history.back()\'>Torna Indietro</a></div>');
						
				$query = "UPDATE ". __PREFIX__ ."users SET web_site = '".$_POST['new_web_site']."' WHERE username = '". $username ."'";
				mysql_query ($query) or _err(mysql_error());
				
				print "<script>alert(\"Sito web aggiornato con successo!.\"); window.location=\"settings.php\";</script>";
			}else{
				mysql_query("UPDATE ". __PREFIX__ ."users SET web_site = '' WHERE username = '". $username ."'");
				print "<script>alert(\"Sito web eliminato con successo!.\"); window.location=\"settings.php\";</script>";
			}
		}else{
			$web_site = mysql_fetch_row(mysql_query("SELECT web_site FROM ". __PREFIX__ ."users WHERE username = '". $username ."'"));
			?>
			<p><b>Aggiorna Sito Web:</b><p>
			<form method = 'POST' action = 'settings.php?mode=4&send=1'>
				<table>
					<tr><td>* Nuovo Sito Web: </td><td> <input name = 'new_web_site' type = 'text' value="<?php print $web_site[0]; ?>" size="50"></td></tr>
					<tr><td><input type = 'submit' value = 'Aggiorna'></td></tr>
				</table>
				<br />
				* Svuotare il campo per eliminare il tuo Sito web
			</form>
			<?php
		}
	break;
	
	case 5:
		if(@$_GET['send'] == 1) {
				
			if (!empty($_POST ['new_msn'])) {
				if(check_email($_POST ['new_msn']) == FALSE)
					die('<div class="error_msg">Il contatto MsN inserito non &egrave; valido<br />
						<a href=\'javascript:history.back()\'>Torna Indietro</a></div>');
						
				$query = "UPDATE ". __PREFIX__ ."users SET web_site = '".$_POST['new_web_site']."' WHERE username = '". $username ."'";
				mysql_query ($query) or _err(mysql_error());
				
				print "<script>alert(\"Contatto MsN aggiornato con successo!.\"); window.location=\"settings.php\";</script>";
			}else{
				mysql_query("UPDATE ". __PREFIX__ ."users SET msn = '' WHERE username = '". $username ."'");
				print "<script>alert(\"Contatto MsN eliminato con successo!.\"); window.location=\"settings.php\";</script>";
			}
		}else{
				$msn = mysql_fetch_row(mysql_query("SELECT msn FROM ". __PREFIX__ ."users WHERE username = '". $username ."'"));
			?>
			<p><b>Aggiorna Contatto MsN:</b><p>
			<form method = 'POST' action = 'settings.php?mode=5&send=1'>
				<table>
					<tr><td>* Nuovo contatto MsN: </td><td><input name = 'new_msn' type = 'text' value="<?php print $msn[0]; ?>" size="50"></td></tr>
					<tr><td><input type = 'submit' value = 'Aggiorna'></td></tr>
				</table>
				<br />
				* Svuotare il campo per eliminare il tuo contatto MsN
			</form>
			<?php			
		}
	break;

	case 6:
		@$new_firma = clear($_POST ['new_firma']);
		
		if ($new_firma) {
		
			$query = "UPDATE ". __PREFIX__ ."users SET firma = '{$new_firma}' WHERE username = '". $username ."'";
			
			mysql_query ($query) or _err(mysql_error());
			
			print "<script>alert(\"Firma Aggiornata!\"); window.location=\"settings.php\";</script>";
		}else{
			$firma =  mysql_fetch_row(mysql_query("SELECT firma FROM ". __PREFIX__ ."users WHERE username = '". $username ."'"));
			?>
			<p><b>Cambia Firma:</b><p>
			<form method = 'POST' action = 'settings.php?mode=6'>
				Firma<br />
				* NO HTML! Solo BBcode!
				<br />
				<textarea name = 'new_firma' cols="90" rows="13"><?php print htmlspecialchars($firma[0]); ?></textarea>
				<br />
				<input type = 'submit' value = 'Aggiorna'>
			</form>
			<?php
		}
	break;	
	
	default:
		?>
		<p><b>Pannello di controllo Utente</b><p>
		<a href = 'settings.php?mode=1'>-> Cambia Password</a><br />
		<a href = 'settings.php?mode=3'>-> Cambia E-Mail</a><br />
		<a href = 'settings.php?mode=4'>-> Cambia Sito Web</a><br />
		<a href = 'settings.php?mode=5'>-> Cambia Contatto MsN</a><br />		
		<a href = 'settings.php?mode=2'>-> Cambia Tema</a><br />
		<a href = 'settings.php?mode=6'>-> Cambia Firma</a>
		<?php
		break;
}
?>
</div>
</div>
<?php
footer();
?>
</body>
</html>
