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
 * admin.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu ();
list ($username, $password) = get_data ();
if (!login ($username, $password)) 
	die ("<br /><br /><br /><div class=\"error_msg\" align=\"center\">\nACCESS DENIED\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a></div>");

if (!(level($username) == 'admin'))
	die ("<br /><br /><br /><div class=\"error_msg\" align=\"center\">\nACCESS DENIED\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a></div>");

csrf_attemp($_SERVER['HTTP_REFERER']);

$usr = $username;	// solo xke mi serve un nome diverso per fare dei confronti
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<div class = 'main_admin'>
<?php
@$mode = $_GET ['mode'];
switch ($mode) 
{
	case 1:
		$action = $_GET ['action'];
		switch ($action) 
		{
			case 1:
				$query = "SELECT * FROM ".PREFIX."forum";
				$res   = mysql_query ($query) or die(mysql_error());
				?>
				<br /><p align="center"><b>Gestione Forum:</b><p><hr /><br />
				<h4><a href = 'admin.php?mode=1&action=4'>[-Aggiungi Nuovo Forum-]</a></h4>
				<br /><br />
				<?php
				while ($row = mysql_fetch_row ($res)) {
					?>
					<p><b>Titolo: </b><?php echo $row [1]; ?><br />
					<i><?php echo $row [2]; ?></i><br />
					<a href = 'admin.php?mode=1&action=2&id=<?php echo $row [0]; ?>'>Elimina</a> 
					<a href = 'admin.php?mode=1&action=3&id=<?php echo $row [0]; ?>'>Edita</a><p>
					<hr />
					<?php
				}
				break;
				
			case 2:
				$id = (int) $_GET ['id'];
				if (!(check_forum_id($id)))
					die ("<div class=\"error_msg\" align=\"center\">L'ID specificato non è Valido!.</div>");
				$query = "DELETE FROM ".PREFIX."forum WHERE id = '{$id}'";
				mysql_query ($query);
				$query = "DELETE FROM ".PREFIX."topic WHERE f_id = '{$id}'";
				mysql_query ($query) or die(mysql_error());
				header ("Location: admin.php?mode=1");
				echo "Forum deleted successfully.";
				break;
				
			case 3:
				$id = (int) $_GET ['id'];
				if (!(check_forum_id($id)))
					die ("<div class=\"error_msg\" align=\"center\">L'ID Specificato non è valido!</div>");
					
				@$title = clear ($_POST ['title']);
				@$descr = clear ($_POST ['descr']);
				
				if (($title) && ($descr)) {
					$query = "UPDATE ".PREFIX."forum SET title = '{$title}', description = '{$descr}' WHERE id = '{$id}'";
					mysql_query ($query) or die(mysql_error());
					header ("Location: admin.php?mode=1");
					echo "Forum updated successfully.";
				}else {
					$query = "SELECT * FROM ".PREFIX."forum WHERE id = '{$id}'";
					$row = mysql_fetch_row (mysql_query ($query));
					?>
					<br><b>Edit Forum:</b><p>
					<form method = 'POST' action = 'admin.php?mode=1&action=3&id=<?php echo $id; ?>'>
						<br />
						<table>
							<tr><td>Titolo:</td><td><input name = 'title' value = '<?php echo $row [1]; ?>'></td></tr>
						</table>
					<br />
						Descrizione:<br />
						<textarea class = 'forum_text' name = 'descr'><?php echo $row [2]; ?></textarea><br />
						<input type = 'submit' value = 'Edit'>
					</form>
					<?php
				}
				break;
				
			case 4:
				@$title = clear ($_POST ['title']);
				@$descr = clear ($_POST ['descr']);
				
				if (($title) && ($descr)) {
					$query = "INSERT INTO ".PREFIX."forum (title, description) VALUES ('{$title}', '{$descr}')";
					mysql_query ($query) or die(mysql_error());
					header ("Location: admin.php?mode=1");
					echo "New forum added successfully.";
				}else{
					?>
					<br><b>Aggiungi un nuovo Forum:</b><p>
					<form action = 'admin.php?mode=1&action=4' method = 'POST'>
						<table>
							<tr><td>Titolo: </td>
							<td><input name = 'title'></td></tr>
						</table>
						Descrizione:<br />
						<textarea class = 'forum_text' name = 'descr'></textarea><br>
						<input type = 'submit' value = 'Create'>
					</form>
					<?php
				}
				break;
				
			default:
				header ("Location: admin.php?mode=1&action=1");
				echo "<a href = 'admin.php?mode=1&action=1'>Go</a>";
			break;
		}
		break;
		
	case 2:
		@$username = clear ($_POST ['username']);
		if ($username) {
			$query = "SELECT id,level FROM ".PREFIX."users WHERE username = '{$username}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if (!$row [0])
				die ("<div class=\"error_msg\" align=\"center\">L'Username Specificato non esiste!</div>");
			if($row[0] == nick2uid($usr))
				die ("<div class=\"error_msg\" align=\"center\">Già sei ADMIN?!<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");				
			if ($row [1] == 'admin') 
				$query = "UPDATE ".PREFIX."users SET level = 'user' WHERE id = '{$row [0]}'";
			else
				$query = "UPDATE ".PREFIX."users SET level = 'admin' WHERE id = '{$row [0]}'";
			mysql_query ($query) or die(mysql_error());
			header ("Location: admin.php?mode=2");
			echo "Admin permissions edited successfully.";
		}
		else {
			?>
			<form action = 'admin.php?mode=2' method = 'POST'>
				<br><b>Gestione dei permessi per Admin:</b>
				<p>Aggiungi/Rimuovi un admin(Basta scrivere l'username): <input name = 'username'><br>
				<input type = 'submit' value = 'Edit'>
			</form>
			<br /><br />
		<?php
		}
		print "<b>Admin(s):</b><p>\n";
		$query = "SELECT id, username FROM ".PREFIX."users WHERE level = 'admin'";
		$res   = mysql_query ($query) or die(mysql_error());
		while ($row = mysql_fetch_row ($res)) {			
			print "<a href = 'profile.php?id=".$row [0]."'>".$row [1]."</a><br>";
		}
		break;
		
	case 3;
		@$username = clear ($_POST ['username']);
		if ($username) {
			$query = "SELECT id FROM ".PREFIX."users WHERE username = '{$username}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [0])
				die ("<div class=\"error_msg\" align=\"center\">L'Username Specificato non esiste!<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");
			if($row[0] == nick2uid($usr))
				die ("<div class=\"error_msg\" align=\"center\">Cancelli il tuo account?!<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");				
				
			mysql_query("DELETE FROM ".PREFIX."users WHERE username = '{$username}'") or die ("SQL Error:".mysql_error());
			mysql_query("DELETE FROM ".PREFIX."karma WHERE vote_user_id = '".nick2uid($username)."'") or die(mysql_error());
			mysql_query("DELETE FROM ".PREFIX."ban_ip WHERE user_id = '".nick2uid($username)."'") or die(mysql_error());
			print '<script>alert("Utente cancellato con successo!"); window.location="admin.php?mode=3";</script>';
		}else{
			echo "
			<form action = 'admin.php?mode=3' method = 'POST'>
				<br><b>Cancella Utente:</b>
				<p>Inserire l'username: <input name = 'username' maxleght='20'><br>
				<input type = 'submit' value = 'Cancella'>
			</form>";
		}
		
	break;
	
	case 4;
		@$username = clear ($_POST ['username']);
		if ($username) {
			$query = "SELECT id,level FROM ".PREFIX."users WHERE username = '{$username}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if (!$row [0])
				die ("<div class=\"error_msg\" align=\"center\">L'Username Specificato non esiste!<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");
			if($row[0] == nick2uid($usr))
				die ("<div class=\"error_msg\" align=\"center\">Banni il tuo stesso account?!<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");				
			if(nick2uid($username) == 'banned')
				die ("<div class=\"error_msg\" align=\"center\">Questo utente è già stato BANNATO!!!<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");
			if($row[1] == 'banned')
				mysql_query("UPDATE ".PREFIX."users SET level= 'user' WHERE username = '{$username}'") or die(mysql_error());
			else
				mysql_query("UPDATE ".PREFIX."users SET level= 'banned' WHERE username = '{$username}'") or die(mysql_error());
				
			print '<script>window.location="admin.php?mode=4";</script>';
		}else{
			echo "
			<form action = 'admin.php?mode=4' method = 'POST'>\n
				<br /><b>Banna\Sbanna Utente:</b>\n<br /><br />\n
				Inserire l'username: <input name = 'username' maxleght='20'><br />\n
				<input type = 'submit' value = 'Esegui'>\n
			</form>\n";
		}
		echo "<b>Banned(s):</b><p>\n";
			
		$query = "SELECT id, username FROM ".PREFIX."users WHERE level = 'banned'";
		$res   = mysql_query ($query);
		while ($row = mysql_fetch_row ($res))
			print "<a href = 'profile.php?id={$row [0]}'>{$row [1]}</a><br />\n";
	break;
	
		case 5:
		@$username = clear ($_POST ['username']);
		if ($username) {
			$query = "SELECT id,level FROM ".PREFIX."users WHERE username = '{$username}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if (!$row [0])
				die ("<div class=\"error_msg\" align=\"center\">L'Username Specificato non esiste!</div>");
			if($row[0] == nick2uid($usr))
				die ("<div class=\"error_msg\" align=\"center\">Non puoi diventare Mod se ora sei ADMIN!!!<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");				
			if ($row [1] == 'moderator') 
				$query = "UPDATE ".PREFIX."users SET level = 'user' WHERE id = '{$row [0]}'";
			else
				$query = "UPDATE ".PREFIX."users SET level = 'moderator' WHERE id = '{$row [0]}'";
				
			mysql_query ($query) or die(mysql_error());
			header ("Location: admin.php?mode=5");
			echo "Cambiato con successo";
		}
		else {
			?>
			<form action = 'admin.php?mode=5' method = 'POST'>
				<br><b>Gestione dei permessi per Mod:</b>
				<p>Aggiungi/Rimuovi un Mod (Basta scrivere l'username): <input name = 'username'><br>
				<input type = 'submit' value = 'Edit'>
			</form>
			<br /><br />
		<?php
		}
		?>
		<b>Moderator(s):</b><p>
		<?php
		$query = "SELECT id, username FROM ".PREFIX."users WHERE level = 'moderator'";
		$res   = mysql_query ($query);
		while ($row = mysql_fetch_row ($res)) {
			?>
			<a href = 'profile.php?id=<?php echo $row [0]; ?>'><?php echo $row [1]; ?></a><br>
			<?php
		}
		break;
		
		case 6:
		
		@$ip = clear ($_REQUEST['ip']);
		if (($ip) && (@$_GET['ban'] == 2)) {
			$query = "SELECT * FROM ".PREFIX."ban_ip WHERE ip = '{$ip}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if (!$row [2]) {
				//ip inesistente quindi lo aggiungo
				$ban_ip = "INSERT INTO ".PREFIX."ban_ip (
													user_id, ip, banned
													) VALUES (
													'', '".$ip."', '1')";
				mysql_query($ban_ip) or die(mysql_error());
			}else{
				die("<div class=\"error_msg\" align=\"center\">Questo IP esiste nel Database!</div>");
			}
			header ("Location: admin.php?mode=6");
		}
		else {
		?>
			<form action = 'admin.php?mode=6&ban=2' method = 'POST'>
				<br /><b>Gestione Ban per IP:</b>
				<p>Banna IP Esterno: <input name = 'ip'><br />
				<input type = 'submit' value = 'Banna'></p>
			</form>
		<br /><br />
		<?php
		}
		if(@$_GET['ban'] == 1) {
			$query = "SELECT * FROM ".PREFIX."ban_ip WHERE ip = '{$ip}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if($row[1] == nick2uid($usr))
				die ("<div class=\"error_msg\" align=\"center\">Errore! Questo IP da bannare è identico al tuo, ti banni?<br /><br /> <a href='javascript:history.back()'>Torna Indietro</a></div>");				
			if ($row [3] == 0) 
				$query = "UPDATE ".PREFIX."ban_ip SET banned = '1' WHERE id = '{$row [0]}'";
			else
				$query = "UPDATE ".PREFIX."ban_ip SET banned = '0' WHERE id = '{$row [0]}'";
				
			mysql_query ($query) or die(mysql_error());
			header ("Location: admin.php?mode=6");
		}
		
		if((@$_GET['elimina'] == 1) && (!empty($_GET['id'])))
		{
			$id = (int) $_GET['id'];
			mysql_query("DELETE FROM ".PREFIX."ban_ip WHERE id = '{$id}'") or die(mysql_error());
			print '<script>alert("IP Cancellato con successo!); window.location="admin.php?mode=6";</script>';
		}
		?>
		<table border="1">
		<tr><td>Username</td><td>IP</td><td>Bannato?</td><td></td></tr>
		<?php
		$query = "SELECT * FROM ".PREFIX."ban_ip";
		$res   = mysql_query ($query);
		while ($row = mysql_fetch_row ($res)) 
		{
			if(($row[3] == 0) || ($row[3] == NULL))
				$banned = "NO";
			else
				$banned = "SI";
				
			if($banned == 'NO')
				$banna = "<a href=\"admin.php?mode=6&ip=".$row[2]."&ban=1\"><i>Banna</i></a> ~ <a href=\"admin.php?mode=6&id=".$row[0]."&elimina=1\"><i>Elimina</i></a>";
			else
				$banna = "<a href=\"admin.php?mode=6&ip=".$row[2]."&ban=1\"><i>Leva Ban</i></a> ~ <a href=\"admin.php?mode=6&id=".$row[0]."&elimina=1\"><i>Elimina</i></a>";
			
			if(!($row[1] == NULL))
				$usr = "<a href = 'profile.php?id=".$row [1]."'>".user_id($row [1])."</a>";
			else
				$usr = "<i>IP Esterno</i>";
			?>
			<tr>
				<td><?php echo $usr; ?></td>
				<td><?php echo $row[2]; ?></td><td><?php echo $banned; ?></td>
				<td><?php echo $banna; ?></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
		break;
		
		case 7: ?>
			<form action = 'admin.php?mode=7' method = 'POST'>
				<br /><b>Modifica Modalità "Manutenzione"</b><br /><br />
				<select name="operazione">
					<option value ="disattiva">Disattiva</option>
					<option value ="attiva">Attiva</option>
				</select>
				<input type = 'submit' value = 'Modifica'></p>
			</form>
		<?php
		if(!empty($_POST['operazione'])) {
			if($_POST['operazione'] == 'attiva') {
				mysql_query("UPDATE `".PREFIX."settings` SET maintenance = '1'")or die(mysql_error());
				print '<script>alert("Modalità Manutenzione ATTIVATA!"); window.location="admin.php";</script>';
			}elseif($_POST['operazione'] == 'disattiva') {
				mysql_query("UPDATE `".PREFIX."settings` SET maintenance = '0'")or die(mysql_error());
				print '<script>alert("Modalità Manutenzione DISATTIVATA!"); window.location="admin.php";</script>';
			}
		}			
		break;
		
		case 8: ?>
			<form action = 'admin.php?mode=8' method = 'POST'>
				<br /><b>Attiva/Disattiva Registrazioni</b><br /><br />
				<select name="operazione">
					<option value ="disattiva">Disattiva</option>
					<option value ="attiva">Attiva</option>
				</select>
				<input type = 'submit' value = 'Modifica'></p>
			</form>
		<?php
		if(!empty($_POST['operazione'])) 
		{		
			if($_POST['operazione'] == 'attiva') 
			{
				mysql_query("UPDATE `".PREFIX."settings` SET block_register = '0'")or die(mysql_error());
				print '<script>alert("Iscrizioni/Registrazioni ATTIVATE!"); window.location="admin.php";</script>';
				
			}elseif($_POST['operazione'] == 'disattiva') 
				{
				mysql_query("UPDATE `".PREFIX."settings` SET block_register = '1'")or die(mysql_error());
				print '<script>alert("Iscrizioni/Registrazioni DISATTIVATE!"); window.location="admin.php";</script>';
			}
		}			
		break;
		
		case 9: ?>
			<form action="dump.php" method="POST">
			  <br>Opzioni per il Dump:
			  <table border="0">
			    <tr>
			      <td>Drop table statement: </td>
			      <td><input type="checkbox" name="sql_drop_table" <?php if(isset($_REQUEST['action']) && ! isset($_REQUEST['sql_drop_table'])) ; else echo 'checked' ?> /></td>
			    </tr>
			    <tr>
			      <td>Create table statement: </td>
			      <td><input type="checkbox" name="sql_create_table" <?php if(isset($_REQUEST['action']) && ! isset($_REQUEST['sql_create_table'])) ; else echo 'checked' ?> /></td>
			    </tr>
			    <tr>
			      <td>Table data: </td>
			      <td><input type="checkbox" name="sql_table_data"  <?php if(isset($_REQUEST['action']) && ! isset($_REQUEST['sql_table_data'])) ; else echo 'checked' ?>/></td>
			    </tr>
			  </table>
			<input type="submit" name="esegui_backup"  value="Backup"><br />
			</form>
		<?php
		break;
		
		case 10:
			@$oggetto   = clear ($_POST['oggetto']);
			@$messaggio = clear ($_POST['messaggio']);
			
			if(($oggetto) && ($messaggio)) {
				$query = "SELECT email ".PREFIX."users";
				$res   = mysql_query($query) or die(mysql_error());
				
				$emails = 0;
				
				while($email = mysql_fetch_row($res)) {
					$headers = "From: ".SITE_NAME;
					mail($email[0], $oggetto, $messaggio, $headers);
					$emails++;
				}
				print "<script>alert(\"Mandate ".$emails."\"); window.location=\"admin.php\";</script>";
			}else{
				print "<form method='POST' action='admin.php?mode=10'>\n
					<table>\n
						<tr>\n
							<td>Oggetto</td>\n
						</tr>\n
						<tr>\n
        					<td><input name='oggetto' type='text' /></td>\n
						</tr>\n
						<tr>\n
							<td>Messaggio</td>\n
						</tr>\n
						<tr>\n
							<td>\n
				              <textarea rows='12' cols='50' name='text'/>\n\n\n\n
--------------------------------------------
News-Letter dal sito ".SITE_NAME."\n
http://www.".$_SERVER['SERVER_NAME']."</textarea>\n
							</td>\n
						</tr>\n
						<tr>\n
							<td><input type='submit' value='Invia'/></td>\n
						</tr>\n
					</table>\n
						</form>\n";
  	}
		break;
	
	case '11':
		check_version();
		echo "<p align='center'>Nessun Aggiornamento Disponibile\n<br /><br /><a href=\"admin.php\">Torna Indietro</a></p>\n";
	break;
		
	default;
	?><br /></br>
		<b>Admin Control Panel:</b><p>
		-> <a href = 'admin.php?mode=1'>Gestione Forums</a><br />
		-> <a href = 'admin.php?mode=2'>Aggiungi/Rimuovi Amministratori</a><br />
		-> <a href = 'admin.php?mode=5'>Aggiungi/Rimuovi Moderatori</a><br />		
		-> <a href = 'admin.php?mode=3'>Cancella Utente</a><br />
		-> <a href = 'admin.php?mode=4'>Banna Utente</a><br />	
		-> <a href = 'admin.php?mode=6'>Banna IP</a><br />
		<br /><br />
		<p>Strumenti di Sistema</p>
		-> <a href = 'admin.php?mode=7'>Modalità "Manutenzione"</a><br />
		-> <a href = 'admin.php?mode=8'>Attiva/Disattiva Registrazioni</a><br />
		-> <a href = 'admin.php?mode=9'>Effettua un backup del Database</a><br />
		-> <a href = 'admin.php?mode=10'>Manda una NewsLetter a tutti gli utenti registrati</a><br />
		<br />
		-> <a href = 'admin.php?mode=11'>Controlla Versione 0xBB</a><br />		
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
