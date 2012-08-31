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
 * admin.php                                                        
 ***************************************************************************/
 
include "kernel.php";

show_header();
show_menu ();

list ($username, $password) = get_data ();

if (!login ($username, $password)) 
	die (header('Location: index.php'));

if (!(level($username) == 'admin'))
	die (header('Location: index.php'));

csrf_attemp($_SERVER['HTTP_REFERER']);

$usr = $username;
?>
<div class = 'path' id = 'path'>
	<table>
	<?php if(empty($_GET['mode'])) {?>
		<tr><td><b><a href = 'index.php'>Home Forum</a></b></td></tr>
	<?php }else{ ?>
		<tr><td><b><a href = 'admin.php'>Home ACP</a></b></td></tr>
	<?php }?>
	</table>
</div>
<div class = 'main_admin'>
<?php
switch (@$_GET ['mode']) {
	case 1:
		switch (@$_GET ['action']) {
			case 1:
				?>
				<br /><p align="center"><b>Gestione Forum:</b><p><hr /><br />
				<h4><a href = 'admin.php?mode=1&action=4'>[-Aggiungi Nuovo Forum-]</a></h4>
				<br /><br />
				<?php
				$query = "SELECT * FROM ". __PREFIX__ ."forum";
				$res   = mysql_query ($query) or _err(mysql_error());
				
				while ($row = mysql_fetch_row ($res)) {
					?>
					<p><b>Titolo: </b><?php print $row [1]; ?><br />
					<i><?php print $row [2]; ?></i><br />
					<i>Access: <?php if(!empty($row[3])) print check_graphic_access_forum($row[0]); else print 'FULL ACCESS'; ?></i><br />
					<a href = 'admin.php?mode=1&action=2&id=<?php print $row [0]; ?>'>Elimina</a> 
					<a href = 'admin.php?mode=1&action=3&id=<?php print $row [0]; ?>'>Edita</a><p>
					<hr />
					<?php
				}
				break;
				
			case 2:
				$id = (int) $_GET ['id'];
				
				if (!(check_forum_id($id)))
					_err("Errore! L'ID specificato non è Valido!");
					
				$query = "DELETE FROM ". __PREFIX__ ."forum WHERE id = '". $id ."'";
				mysql_query ($query);
				
				$query = "DELETE FROM ". __PREFIX__ ."topic WHERE f_id = '". $id ."'";
				mysql_query ($query) or _err(mysql_error());
				
				header ("Location: admin.php?mode=1");
				print "Forum deleted successfully.";
			break;
				
			case 3:
				$id = (int) $_GET ['id'];
				
				if (!(check_forum_id($id)))
					_err("Errore! L'ID Specificato non è valido!");
					
				@$title  = clear ($_POST ['title']);
				@$descr  = clear ($_POST ['descr']);
				@$access = clear ($_POST ['access']);
				
				if (($title) && ($descr)) {
					$query = "UPDATE ". __PREFIX__ ."forum SET title = '{$title}', description = '{$descr}', user_access = '{$access}' WHERE id = '". $id ."'";
					mysql_query ($query) or _err(mysql_error());
					header ("Location: admin.php?mode=1");
				}else {
					$query = "SELECT * FROM ". __PREFIX__ ."forum WHERE id = '". $id ."'";
					$row   = mysql_fetch_row (mysql_query ($query));
					?>
					<br /><b>Modifica Forum:</b><p>
					<form method = 'POST' action = 'admin.php?mode=1&action=3&id=<?php print $id; ?>'>
						<br />
						<table>
							<tr><td>Titolo:</td><td><input name = 'title' value = '<?php print $row [1]; ?>'></td></tr>
						</table>
					<br />
						Descrizione:<br />
						<textarea class = 'forum_text' name = 'descr'><?php print $row [2]; ?></textarea><br />
						Accesso: <select name="access">
								  <?php print "<option value=\"".$row[3]."\">".$row[3]."</option>"; ?>
								  <option value="">Tutti</option>
								  <option value="admin">Admin</option>
								  <option value="mod">Mod</option>
								  <option value="vip">VIP</option>
								</select> 
						<br />
						<input type = 'submit' value = 'Modifica'>
					</form>
					<?php
				}
			break;
				
			case 4:
				@$title  = clear ($_POST ['title']);
				@$descr  = clear ($_POST ['descr']);
				@$access = clear ($_POST ['access']);
				
				if (($title) && ($descr)) {
					$query = mysql_query ("SELECT MAX(position) FROM ".__PREFIX__."forum") or _err(mysql_error());
					$result = mysql_fetch_row($query);
					
					$position = $result[0] + 1;
					
					$query = "INSERT INTO ". __PREFIX__ ."forum (title, description, user_access, position) VALUES ('{$title}', '{$descr}', '{$access}', ".$position.")";
					mysql_query ($query) or _err(mysql_error());
					header ("Location: admin.php?mode=1");
				}else{
					?>
					<br /><b>Aggiungi un nuovo Forum:</b><p>
					<form action = 'admin.php?mode=1&action=4' method = 'POST'>
						<table>
							<tr><td>Titolo: </td>
							<td><input name = 'title'></td></tr>
						</table>
						Descrizione:<br />
						<textarea class = 'forum_text' name = 'descr'></textarea><br />
						Accesso: <select name="access">
								  <option value="">Tutti</option>
 								  <option value="admin">Admin</option>
								  <option value="mod">Mod</option>
								  <option value="vip">VIP</option>
								</select> 
						<br />
						<input type = 'submit' value = 'Crea forum'>
					</form>
					<?php
				}
			break;
				
			default:
				header ("Location: admin.php?mode=1&action=1");
			break;
		}
	break;
		
	case 2:
		@$username = clear ($_POST ['username']);
		if ($username) {
			$query = "SELECT id,level FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [0])
				_err("Errore! L'Username Specificato non esiste!");
				
			if($row[0] == nick2uid($usr))
				_err("Errore!  L'utente selezionato &egrave; gi&agrave; amministratore!");
				
			if ($row [1] == 'admin') 
				$query = "UPDATE ". __PREFIX__ ."users SET level = 'user' WHERE id = '". $row[0] ."'";
			else
				$query = "UPDATE ". __PREFIX__ ."users SET level = 'admin' WHERE id = '". $row[0] ."'";
				
			mysql_query ($query) or _err(mysql_error());
			header ("Location: admin.php?mode=2");
		}else {
			?>
			<form action = 'admin.php?mode=2' method = 'POST'>
				<br /><b>Gestione dei permessi per Admin:</b>
				<p>Aggiungi/Rimuovi un admin(Basta scrivere l'username): <input name = 'username'><br />
				<input type = 'submit' value = 'Edit'>
			</form>
			<br /><br />
		<?php
		}
		print "<b>Amministratori:</b><p>\n";
		$query = "SELECT id, username FROM ". __PREFIX__ ."users WHERE level = 'admin'";
		$res   = mysql_query ($query) or _err(mysql_error());
		
		while ($row = mysql_fetch_row ($res))
			print "<a href = 'profile.php?id=".$row [0]."'>".$row [1]."</a><br />";
	break;
		
	case 3;
		@$username = clear ($_POST ['username']);
		
		if ($username) {
			$query = "SELECT id FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [0])
				_err("Errore!  L'Username Specificato non esiste!");
				
			if($row[0] == nick2uid($usr))
				_err("Errore!  L'accaunt selezionato è in utilizzo!");				
				
			mysql_query("DELETE FROM ". __PREFIX__ ."users WHERE username = '". $username ."'") or _err(mysql_error());
			mysql_query("DELETE FROM ". __PREFIX__ ."karma WHERE vote_user_id = '". nick2uid($username) ."'") or _err(mysql_error());
			mysql_query("DELETE FROM ". __PREFIX__ ."ban_ip WHERE user_id = '". nick2uid($username) ."'") or _err(mysql_error());
			
			print '<script>alert("Utente cancellato con successo!"); window.location="admin.php?mode=3";</script>';
		}else{
		?>
			<form action = 'admin.php?mode=3' method = 'POST'>
				<br /><b>Cancella Utente:</b>
				<p>Inserire l'username: <input name = 'username' maxleght='20'><br />
				<input type = 'submit' value = 'Cancella'>
			</form>
		<?php
		}
		
	break;
	
	case 4;
		@$username = clear ($_POST ['username']);
		
		if ($username) {
			$query = "SELECT id,level FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [0])
				_err("Errore! L'Username Specificato non esiste!");
				
			if($row[0] == nick2uid($usr))
				_err("Errore! Banni il tuo stesso account?!");				
				
			if($row[1] == 'banned')
				mysql_query("UPDATE ". __PREFIX__ ."users SET level= 'user' WHERE username = '". $username ."'") or _err(mysql_error());
			else
				mysql_query("UPDATE ". __PREFIX__ ."users SET level= 'banned' WHERE username = '". $username ."'") or _err(mysql_error());
				
			print '<script>window.location="admin.php?mode=4";</script>';
		}else{
		?>
			<form action = 'admin.php?mode=4' method = 'POST'>
				<br /><b>Banna\Sbanna Utente:</b><br /><br />
				Inserire l'username: <input name = 'username' value='' maxleght=20><br />
				<input type = 'submit' value = 'Banna'>
			</form>
		<?php
		}
		
		print "<br /><b>Utenti Bannati:</b><p>\n";
			
		$query = "SELECT id, username FROM ". __PREFIX__ ."users WHERE level = 'banned'";
		$res   = mysql_query ($query);
		
		while ($row = mysql_fetch_row ($res))
			if($row[0] != nick2uid($username))
				print "<a href = 'profile.php?id=". $row[0] ."'>". $row[1] ."</a><br />\n";
	break;
	
	case 5:
		@$username = clear ($_POST ['username']);
		
		if ($username) {
			$query = "SELECT id,level FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [0])
				_err("Errore! L'Username Specificato non esiste!</div>");
				
			if($row[0] == nick2uid($usr))
				_err("Errore! Non puoi diventare Mod se ora sei ADMIN!");				
				
			if ($row [1] == 'mod') 
				$query = "UPDATE ". __PREFIX__ ."users SET level = 'user' WHERE id = '". $row[0] ."'";
			else
				$query = "UPDATE ". __PREFIX__ ."users SET level = 'mod' WHERE id = '". $row[0] ."'";
				
			mysql_query ($query) or _err(mysql_error());
			header ("Location: admin.php?mode=5");
		}else {
			?>
			<form action = 'admin.php?mode=5' method = 'POST'>
				<br /><b>Gestione dei permessi per Mod:</b>
				<p>Aggiungi/Rimuovi un Mod (Basta scrivere l'username): <input name = 'username'><br />
				<input type = 'submit' value = 'Edit'>
			</form>
			<br /><br />
		<?php
		}
		?>
		<b>Moderatori:</b><p>
		<?php
		$query = "SELECT id, username FROM ". __PREFIX__ ."users WHERE level = 'mod'";
		$res   = mysql_query ($query);
		
		while ($row = mysql_fetch_row ($res))
			print "<a href = 'profile.php?id=".$row [0]."'>".$row [1]."</a><br />";
			
	break;
		
	case 6:
		@$ip = clear ($_REQUEST['ip']);
		
		if (($ip) && (@$_GET['ban'] == 2)) {
		
			$query = "SELECT * FROM ". __PREFIX__ ."ban_ip WHERE ip = '". $ip ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if($ip == $_SERVER['REMOTE_ADD'])
				_err("Errore! IP da bannare è identico al tuo, ti banni?");
				
			if (!$row [2]) {
				//ip inesistente quindi lo aggiungo
				$ban_ip = "INSERT INTO ". __PREFIX__ ."ban_ip ( user_id, ip, banned ) VALUES ('', '". $ip ."', '1')";
													
				mysql_query($ban_ip) or _err(mysql_error());
			}else{
				_err("Errore! Questo IP esiste nel Database!");
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
			$query = "SELECT * FROM ". __PREFIX__ ."ban_ip WHERE ip = '{$ip}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if($row[1] == nick2uid($usr))
				_err("Errore! IP da bannare è identico al tuo, ti banni?");
				
			if ($row [3] == 0) 
				$query = "UPDATE ". __PREFIX__ ."ban_ip SET banned = '1' WHERE id = '". $row[0] ."'";
			else
				$query = "UPDATE ". __PREFIX__ ."ban_ip SET banned = '0' WHERE id = '". $row[0] ."'";
				
			mysql_query ($query) or _err(mysql_error());
			header ("Location: admin.php?mode=6");
		}
		
		if((@$_GET['elimina'] == 1) && (!empty($_GET['id']))) {
		
			$id = (int) $_GET['id'];
			mysql_query("DELETE FROM ". __PREFIX__ ."ban_ip WHERE id = '". $id ."'") or _err(mysql_error());
			print '<script>alert("IP Cancellato!); window.location="admin.php?mode=6";</script>';
		}
		?>
		<table border="1">
		<tr><td>Username</td><td>IP</td><td>Bannato?</td><td></td></tr>
		<?php
		$query = "SELECT * FROM ". __PREFIX__ ."ban_ip";
		$res   = mysql_query ($query);
		
		while ($row = mysql_fetch_row ($res)) {
		
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
			
			if($row[1] != nick2uid($username)) {
			?>
				<tr>
					<td><?php print $usr; ?></td>
					<td><?php print $row[2]; ?></td><td><?php print $banned; ?></td>
					<td><?php print $banna; ?></td>
				</tr>
			<?php
			}
		}
		?>
		</table>
		<?php
	break;
		
	case 7: 
	?>
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
			
				mysql_query("UPDATE `". __PREFIX__ ."settings` SET maintenance = '1'")or _err(mysql_error());
				print '<script>alert("Modalità Manutenzione ATTIVATA!"); window.location="admin.php";</script>';
				
			}elseif($_POST['operazione'] == 'disattiva') {
			
				mysql_query("UPDATE `". __PREFIX__ ."settings` SET maintenance = '0'")or _err(mysql_error());
				print '<script>alert("Modalità Manutenzione DISATTIVATA!"); window.location="admin.php";</script>';
			}
		}			
	break;
		
	case 8: 
	?>
			<form action = 'admin.php?mode=8' method = 'POST'>
				<br /><b>Attiva/Disattiva Registrazioni</b><br /><br />
				<select name="operazione">
					<option value ="disattiva">Disattiva</option>
					<option value ="attiva">Attiva</option>
				</select>
				<input type = 'submit' value = 'Modifica'></p>
			</form>
		<?php
		if(!empty($_POST['operazione'])) {		
			if($_POST['operazione'] == 'attiva') {
				mysql_query("UPDATE `". __PREFIX__ ."settings` SET block_register = '0'")or _err(mysql_error());
				print '<script>alert("Iscrizioni/Registrazioni ATTIVATE!"); window.location="admin.php";</script>';
				
			}elseif($_POST['operazione'] == 'disattiva') {
				mysql_query("UPDATE `". __PREFIX__ ."settings` SET block_register = '1'")or _err(mysql_error());
				print '<script>alert("Iscrizioni/Registrazioni DISATTIVATE!"); window.location="admin.php";</script>';
			}
		}			
	break;
		
	case 9: ?>
		<form action="dump.php" method="POST">
		  <br />Opzioni per il Dump:
		  <table border="0">
		    <tr>
		      <td>Drop table statement: </td>
		      <td><input type="checkbox" name="sql_drop_table" <?php if(isset($_REQUEST['action']) && ! isset($_REQUEST['sql_drop_table'])) ; else print 'checked' ?> /></td>
		    </tr>
		    <tr>
		      <td>Create table statement: </td>
		      <td><input type="checkbox" name="sql_create_table" <?php if(isset($_REQUEST['action']) && ! isset($_REQUEST['sql_create_table'])) ; else print 'checked' ?> /></td>
		    </tr>
		    <tr>
		      <td>Table data: </td>
		      <td><input type="checkbox" name="sql_table_data"  <?php if(isset($_REQUEST['action']) && ! isset($_REQUEST['sql_table_data'])) ; else print 'checked' ?>/></td>
		    </tr>
		  </table>
		<input type="submit" name="esegui_backup"  value="Backup"><br />
		</form>
	<?php
	break;
		
	case 10:
		@$oggetto   = clear ($_POST['oggetto']);
		@$messaggio = clear ($_POST['messaggio']);
		
		$config =  mysql_fetch_array(mysql_query("SELECT site_name, description FROM ". __PREFIX__ ."settings"));
						
		if(($oggetto) && ($messaggio)) {
			$query = "SELECT email ". __PREFIX__ ."users";
			$res   = mysql_query($query) or _err(mysql_error());
			
			$emails = 0;
			
			while($email = mysql_fetch_row($res)) {				
				$headers = "From: ".$config['site_name'];
				mail($email[0], $oggetto, $messaggio, $headers);
				$emails++;
			}
			
			print "<script>alert(\"Inviate ".$emails."\"); window.location=\"admin.php\";</script>";
		}else{
			print "<form method='POST' action='admin.php?mode=10'>\n
				<table>\n
					<tr>\n
						<td>Oggetto</td>\n
					</tr>\n
					<tr>\n
       					<td><input name='oggetto' type='text' size='50'/></td>\n
					</tr>\n
					<tr>\n
						<td>Messaggio</td>\n
					</tr>\n
					<tr>\n
						<td>\n
			              <textarea rows='20' cols='100' name='text'/>\n\n\n\n
--------------------------------------------
News-Letter dal sito ".$config['site_name']."\n
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
	break;
	
	case '12':
				
		@$site_name   = clear ($_POST ['site_name']);
		@$description = clear ($_POST ['description']);
				
		if (($site_name) && ($description)) {
		
			$query = "UPDATE ". __PREFIX__ ."settings SET site_name = '".$site_name."', description = '".$description."'";
			mysql_query ($query) or _err(mysql_error());
			header ("Location: admin.php?mode=12");
		}else {
			$config =  mysql_fetch_array(mysql_query("SELECT site_name, description FROM ". __PREFIX__ ."settings"));
			?>
			<br /><b>Aggiorna Settaggi:</b><p>
			<form method = 'POST' action = 'admin.php?mode=12'>
				<br />
				<table>
					<tr><td>Titolo Board:</td><td><input name = 'site_name'   value = '<?php print $config['site_name']; ?>' size="50"></td></tr>
					<tr><td>Descrizione:</td><td><input name = 'description' value = '<?php print $config['description']; ?>' size="50"></td></tr>
				</table>
				<input type = 'submit' value = 'Aggiorna'>
			</form>
			<?php
		}
	
	break;
	
	case '13':
	
        if(@$_GET['reset'] == 1) {
            
            mysql_query("UPDATE ". __PREFIX__ ."users SET theme = 'default.css'") or _err(mysql_error());
            
            print "\n<script>alert(\"Reset Completato\"); window.location=\"admin.php\";</script>";
        
        }
	    print "<script>"
	        . "\n\tif(confirm('Sei sicuro di voler procedere al reset del tema per tutti gli utenti?.') == true) {"
	        . "\n\t\tlocation.href = 'admin.php?mode=13&reset=1'"
	        . "\n\t}else{"
	        . "\n\t\tlocation.href = 'admin.php'"
	        . "\n\t}"
	        . "\n</script>";
	break;
	
	case '14':
	
		@$username = clear ($_POST ['username']);
		
		if ($username) {
			$query = "SELECT id, level FROM ". __PREFIX__ ."users WHERE username = '". $username ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [0])
				_err("Errore! L'Username Specificato non esiste!");
				
			if($row[0] == nick2uid($usr))
				_err("Errore!  L'utente selezionato &egrave; gi&agrave; VIP!");
				
			if ($row [1] == 'vip') 
				$query = "UPDATE ". __PREFIX__ ."users SET level = 'user' WHERE id = '". $row[0] ."'";
			else
				$query = "UPDATE ". __PREFIX__ ."users SET level = 'vip' WHERE id = '". $row[0] ."'";
				
			mysql_query ($query) or _err(mysql_error());
			header ("Location: admin.php?mode=14");
		}else {
			?>
			<form action = 'admin.php?mode=14' method = 'POST'>
				<br /><b>Gestione dei permessi per Utenti VIP:</b>
				<p>Aggiungi/Rimuovi un' utente VIP(Basta scrivere l'username): <input name = 'username'><br />
				<input type = 'submit' value = 'Invia'>
			</form>
			<br /><br />
		<?php
		}
		print "<b>Utenti VIP:</b><p>\n";
		$query = "SELECT id, username FROM ". __PREFIX__ ."users WHERE level = 'vip'";
		$res   = mysql_query ($query) or _err(mysql_error());
		
		while ($row = mysql_fetch_row ($res))
			print "<a href = 'profile.php?id=".$row [0]."'>".$row [1]."</a><br />";
    break;
    
    case '15':
	
		@$position      = (int) $_GET ['position'];
    	@$real_position = (int) $_GET ['real_position'];
		@$f_id          = (int) $_GET ['f_id'];
		
		if(($position) && ($f_id) && ($real_position)) {
		    
		    // nuova posizione
		    $new_position = $real_position + $position;
		    
		    
  			$query = "UPDATE ". __PREFIX__ ."forum SET position = ".$real_position." WHERE position = ".$new_position;
		    $res   = mysql_query ($query) or _err(mysql_error());
		    
   			$query = "UPDATE ".__PREFIX__."forum SET position = ".$new_position." WHERE id = ".$f_id;
		    $res   = mysql_query ($query) or _err(mysql_error());
		    
			header ("Location: admin.php?mode=15");
		}else {
		    print "\n<h2 align=\"center\">Posizionamento dei Forum</h2><br /><br />"
                . "\n<table align=\"center\">"
                . "\n<tr>"
				. "\n\t<td class=\"users\"> Titolo</td>"
				. "\n\t<td class=\"users\">Descrizione</td>"
				. "\n\t<td class=\"users\">Sposta</td>"
				. "\n</tr>";
				
			$query = "SELECT * FROM ". __PREFIX__ ."forum ORDER BY position";
		    $res   = mysql_query ($query) or _err(mysql_error());
		
		    while ($row = mysql_fetch_array ($res)) {
			    print "\n<tr>"
					. "\n\t<td class=\"users\">".$row['title']."</td>"
					. "\n\t<td class=\"users\">".$row['description']."</td>"
					. "\n\t<td class=\"users\"><a href=\"admin.php?mode=15&position=-1&f_id=".$row['id']."&real_position=".$row['position']."\">[SU]</a> -"
					. "\n\t<a href=\"admin.php?mode=15&position=1&f_id=".$row['id']."&real_position=".$row['position']."\">[GIU]</a></td>"
					. "\n</tr>";
			}
			
    	    print "\n</table>";
		}
    break;
    
	default;
	?>
		<p><b>Pannello di Controllo Amministratore:</b></p>
		-> <a href = 'admin.php?mode=1'>Gestione Forums</a><br />
    	-> <a href = 'admin.php?mode=15'>Gestione Posizione Forums</a><br />
		-> <a href = 'admin.php?mode=2'>Aggiungi/Rimuovi Amministratori</a><br />
		-> <a href = 'admin.php?mode=5'>Aggiungi/Rimuovi Moderatori</a><br />	
		-> <a href = 'admin.php?mode=14'>Aggiungi/Rimuovi Utenti VIP</a><br />		
		-> <a href = 'admin.php?mode=3'>Cancella Utente</a><br />
		-> <a href = 'admin.php?mode=4'>Banna Utente</a><br />	
		-> <a href = 'admin.php?mode=6'>Banna IP</a><br />
		<br /><br />
		<p><b>Strumenti di Sistema</b></p>
		-> <a href = 'admin.php?mode=7'>Modalità "Manutenzione"</a><br />
		-> <a href = 'admin.php?mode=8'>Attiva/Disattiva Registrazioni</a><br />
		-> <a href = 'admin.php?mode=9'>Effettua un backup del Database</a><br />
		-> <a href = 'admin.php?mode=10'>Manda una NewsLetter a tutti gli utenti registrati</a><br />
		<br />
		<p><b>Settaggi</b></p>
		-> <a href = 'admin.php?mode=12'>Gestione Settaggi Board</a><br />		
		<br />
		<p><b>TOOLS</b></p>
		-> <a href = 'admin.php?mode=11'>Controlla Versione 0xBB</a><br />	
		-> <a href = 'admin.php?mode=13'>Resetta Tema per tutti gli utenti</a><br />
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
