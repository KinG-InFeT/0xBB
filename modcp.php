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
 * modcp.php                                                        
 ***************************************************************************/
include "kernel.php";

show_header();
show_menu ();

list ($username, $password) = get_data ();

if (!login ($username, $password)) 
	die (header('Location: index.php'));
	
if (!(level($username) == 'mod'))
	die (header('Location: index.php'));
	
csrf_attemp($_SERVER['HTTP_REFERER']);

$usr = $username;
?>
<div class = 'path' id = 'path'>
	<table>
	<?php if(empty($_GET['mode'])) {?>
		<tr><td><b><a href = 'index.php'>Home Forum</a></b></td></tr>
	<?php }else{ ?>
		<tr><td><b><a href = 'modcp.php'>Home MCP</a></b></td></tr>
	<?php }?>
	</table>
</div>
<div class = 'main_admin'>
<?php
switch (@$_GET ['mode']) {	
	case 1;
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
				
			print '<script>window.location="modcp.php?mode=1";</script>';
		}else{
			print "
			<form action = 'modcp.php?mode=1' method = 'POST'>\n
				<br /><b>Banna\Sbanna Utente:</b>\n<br /><br />\n
				Inserire l'username: <input name = 'username' maxleght='20'><br />\n
				<input type = 'submit' value = 'Banna'>\n
			</form>\n";
		}
		print "<br /><b>Utenti Bannati:</b><p>\n";
			
		$query = "SELECT id, username FROM ". __PREFIX__ ."users WHERE level = 'banned'";
		$res   = mysql_query ($query);
		
		while ($row = mysql_fetch_row ($res))
			print "<a href = 'profile.php?id=". $row[0] ."'>". $row[1] ."</a><br />\n";
	break;
	
	case 2:
		@$ip = clear ($_REQUEST['ip']);
		
		if (($ip) && (@$_GET['ban'] == 2)) {
		
			$query = "SELECT * FROM ". __PREFIX__ ."ban_ip WHERE ip = '{$ip}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [2]) {
				//ip inesistente quindi lo aggiungo
				$ban_ip = "INSERT INTO ". __PREFIX__ ."ban_ip (
													user_id, ip, banned
													) VALUES (
													'', '".$ip."', '1')";
													
				mysql_query($ban_ip) or _err(mysql_error());
			}else{
				_err("Errore! Questo IP esiste nel Database!");
			}
			header ("Location: modcp.php?mode=2");
		}
		else {
		?>
			<form action = 'modcp.php?mode=2&ban=2' method = 'POST'>
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
				_err("Errore! Questo IP da bannare è identico al tuo, ti banni?");
				
			if ($row [3] == 0) 
				$query = "UPDATE ". __PREFIX__ ."ban_ip SET banned = '1' WHERE id = '". $row [0]. "'";
			else
				$query = "UPDATE ". __PREFIX__ ."ban_ip SET banned = '0' WHERE id = '". $row [0] ."'";
				
			mysql_query ($query) or _err(mysql_error());
			header ("Location: modcp.php?mode=2");
		}
		
		if((@$_GET['elimina'] == 1) && (!empty($_GET['id']))) {
		
			$id = (int) $_GET['id'];
			mysql_query("DELETE FROM ". __PREFIX__ ."ban_ip WHERE id = '". $id ."'") or _err(mysql_error());
			print '<script>alert("IP Cancellato!); window.location="modcp.php?mode=2";</script>';
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
				$banna = "<a href=\"modcp.php?mode=2&ip=".$row[2]."&ban=1\"><i>Banna</i></a> ~ <a href=\"modcp.php?mode=2&id=".$row[0]."&elimina=1\"><i>Elimina</i></a>";
			else
				$banna = "<a href=\"modcp.php?mode=2&ip=".$row[2]."&ban=1\"><i>Leva Ban</i></a> ~ <a href=\"modcp.php?mode=2&id=".$row[0]."&elimina=1\"><i>Elimina</i></a>";
			
			if(!($row[1] == NULL))
				$usr = "<a href = 'profile.php?id=".$row [1]."'>".user_id($row [1])."</a>";
			else
				$usr = "<i>IP Esterno</i>";
			?>
			<tr>
				<td><?php print $usr; ?></td>
				<td><?php print $row[2]; ?></td><td><?php print $banned; ?></td>
				<td><?php print $banna; ?></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	break;
	
	default;
	?><br /></br>
		<b>Pannello di Controllo Moderatore:</b><p>
		-> <a href = 'modcp.php?mode=1'>Banna Utente</a><br />	
		-> <a href = 'modcp.php?mode=2'>Banna IP</a><br />
		<?php
	break;
}

?>
</div>
</div>
<?php footer (); ?>
</body>
</html>
