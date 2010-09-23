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
 * pm.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu ();
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<br />
<div class = 'pm'>
<?php
list ($username, $password) = get_data ();

if (!login ($username, $password))
	die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b>Devi essere registrato per accedere qui.\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");
	
if(level($username) == 'banned')
	die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b><br /><b><u>".$username."</u></b> è stato BANNATO dal forum!.\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");
	
$mode = $_GET ['mode'];
switch ($mode) {
	case 1:
		?>
		<table width = 80%>
			<tr><td>Title:</td><td>From:</td></tr>
		<?php
		$query = "SELECT * FROM ".PREFIX."pm WHERE to_usr = '{$username}' ORDER BY ID DESC";
		$res   = mysql_query ($query) or die (mysql_error ());
		while ($row = mysql_fetch_row ($res)) {
			if ($row [5])
				echo "<tr><td><b><a href = 'pm.php?mode=2&id=".$row [0]."'>".$row [3]."</a></b></td><td>".$row [1]."</td></tr>";
			else
				echo "<tr><td><a href = 'pm.php?mode=2&id=".$row [0]."'>".$row [3]."</a></td><td>".$row [1]."</td></tr>";
		}
		?>
</table><br /><br />
		<a href = 'pm.php?mode=3'>[ New PM ]</a>
		<?php
		break;
	case 2:
		$id = (int) $_GET ['id'];
		if (!$id)
			die ("ID non specificato!");
		$query = "SELECT * FROM ".PREFIX."pm WHERE id = '{$id}'";
		$row   = mysql_fetch_row (mysql_query ($query));
		if ((!$row [0]) || ($row [2] != $username))
			die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b>Non sei autorizzato a leggere questo PM!\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");
		if ($row [5]) {
			$query = "UPDATE ".PREFIX."pm SET new = 0 WHERE id = '{$id}'";
			mysql_query ($query);
		}
		?>
		<table>
			<tr><td>Titolo:</td><td><?php echo $row [3]; ?></td></tr>
			<tr><td>From:</td><td><?php echo $row [1]; ?></td></tr>
		</table>
		Messaggio: <br /><br />
		<?php
		print $row[4];
		echo "<br />\n<hr />\n<p><a href = 'pm.php?mode=3&id={$row [0]}'>[ Rispondi ]</a>\n";
		echo "<a href = 'pm.php?mode=4&id={$row [0]}'>[ Cancella ]</a></p>";
		break;
	case 3:
	
		@$to    = clear ($_REQUEST['to']);
		@$title = clear ($_POST ['title']);
		@$data  = clear ($_POST ['data']);
		
		if (($title) && ($to) && ($data)) {
			$query = "SELECT id FROM ".PREFIX."users WHERE username = '{$to}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if (!$row [0])
				die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b>l'Username specificato non è valido!\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");
			$query = "INSERT INTO ".PREFIX."pm (from_usr, to_usr, title, data, new) VALUES ('{$username}', '{$to}', '{$title}', '{$data}', 1)";
			mysql_query ($query) or die("SQL Error:".mysql_error());
			header ("Location: pm.php");
		}else{
			@$id   = (int) $_GET ['id'];
			$query = "SELECT from_usr, to_usr, title FROM ".PREFIX."pm WHERE id = '{$id}'";
			$row   = mysql_fetch_row (mysql_query ($query));
			if (($row [0]) && ($row [1] == $username)) {
				$to = $row [0];
				if (!preg_match ("|^Re\:|", $row [2]))
					$title = "Re: {$row [2]}";
				else
					$title = $row [2];
			}
			?>
			<form action = 'pm.php?mode=3' method = 'POST'>
				<table>
					<tr><td>To:</td><td><input name = 'to' value = '<?php echo $to; ?>'></td></tr>
					<tr><td>Title:</td><td><input name = 'title' value = '<?php echo $title; ?>'></td></tr>
				</table>
				<textarea class = 'pm_text' name = 'data'></textarea><br>
				<input type = 'submit' value = 'Send'>
			</form>
			<?php
		}
		break;
	case 4:
		$id    = (int) $_GET ['id'];
		$query = "SELECT to_usr FROM ".PREFIX."pm WHERE id = '{$id}'";
		$row   = mysql_fetch_row (mysql_query ($query));
		if ($row [0] == $username) {
			$query = "DELETE FROM ".PREFIX."pm WHERE id = '{$id}'";
			mysql_query ($query) or die("SQL Error:".mysql_error());
			header ("Location: pm.php");
		}
		else 
			die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b>Non sei autorizzato a leggere questo PM!\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");
		break;
	default:
		header ("Location: pm.php?mode=1");
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
