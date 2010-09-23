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
 * modcp.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu ();
list ($username, $password) = get_data ();
if (!login ($username, $password)) 
	die ("<br /><br /><br /><div class=\"error_msg\" align=\"center\">\nACCESS DENIED\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a></div>");
	
if (!(level($username) == 'moderator'))
	die ("<br /><br /><br /><div class=\"error_msg\" align=\"center\">\nACCESS DENIED\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a></div>");
	
csrf_attemp($_SERVER['HTTP_REFERER']);

$usr = $username;	// solo xke mi serve un nume diverso per fare dei confronti
?>
<div class = 'main_admin'>
<?php
@$mode = $_GET ['mode'];
switch ($mode) {	
	case 1;
		@$username = clear ($_POST ['username']);
		if ($username) {
			$query = "SELECT id,level FROM ".PREFIX."users WHERE username = '{$username}'";
			$row = mysql_fetch_row (mysql_query ($query));
			if (!$row [0])
				die ("<div class=\"error_msg\" align=\"center\">L'Username Specificato non esiste!<br /><br /> <a href='javascript:history.back()'>Torna in Dietro</a></div>");
			if($row[0] == nick2uid($usr))
				die ("<div class=\"error_msg\" align=\"center\">Ti banni da solo?!<br /><br /> <a href='javascript:history.back()'>Torna in Dietro</a></div>");
			if(nick2uid($username) == 'banned')
				die ("<div class=\"error_msg\" align=\"center\">Questo utente è già stato BANNATO!!!<br /><br /> <a href='javascript:history.back()'>Torna in Dietro</a></div>");
			if($row[1] == 'moderator')
				mysql_query("UPDATE ".PREFIX."users SET level= 'user' WHERE username = '{$username}'") or die(mysql_error());
			else
				mysql_query("UPDATE ".PREFIX."users SET level= 'moderator' WHERE username = '{$username}'") or die(mysql_error());
			print '<script>window.location="modcp.php?mode=1";</script>';
		}else{
			echo "
			<form action = 'modcp.php?mode=1' method = 'POST'>\n
				<br /><b>Banna\Sbanna Utente:</b>\n<br /><br />\n
				Inserire l'username: <input name = 'username' maxleght='20'><br />\n
				<input type = 'submit' value = 'Esegui'>\n
			</form>\n";
		}
		echo "<b>Banned(s):</b><p>\n";
			
		$query = "SELECT id, username FROM ".PREFIX."users WHERE level = 'moderator'";
		$res   = mysql_query ($query);
		while ($row = mysql_fetch_row ($res))
			print "<a href = 'profile.php?id={$row [0]}'>{$row [1]}</a><br />\n";
	break;
	
	default;
	?><br /></br>
		<b>Mod Control Panel:</b><p>
		<a href = 'modcp.php?mode=1'>Ban User</a><br />		
		<?php
	break;
}

?>
</div>
</div>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<?php footer (); ?>
</body>
</html>
