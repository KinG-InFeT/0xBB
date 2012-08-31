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
 * pm.php                                                        
 ***************************************************************************/
include "kernel.php";

show_header();
show_menu ();
?>
<div class = 'path' id = 'path'>
	<table>
	<?php if(@$_GET['mode'] == 1) {?>
		<tr><td><b><a href = 'index.php'>Home Forum</a></b></td></tr>
	<?php }else{ ?>
		<tr><td><b><a href = 'pm.php'>Home PM</a></b></td></tr>
	<?php }?>
	</table>
</div>
<br />
<div class = 'pm'>
<?php
list ($username, $password) = get_data ();

if (!login ($username, $password))
	_err("<b>Errore!</b>Devi essere registrato per accedere!");
	
if(level($username) == 'banned')
	_err("<b>Errore!</b><br /><b><u>". $username ."</u></b> è stato BANNATO dal forum!");
	
switch (@$_GET ['mode']) {
	case 1:
		?>
		<a href = 'pm.php?mode=3'>[ Nuovo PM ]</a>
		<br /><br />
		<table style="width: 70%;">
			<tr><td align="left" class="users">Oggetto:</td><td align="left" class="users">Da:</td></tr>
		<?php
		$query = "SELECT * FROM ". __PREFIX__ ."pm WHERE to_usr = '". $username ."' ORDER BY ID DESC";
		$res   = mysql_query ($query) or die (mysql_error ());
		
		while ($row = mysql_fetch_row ($res)) {
			if ($row [5])
				print "<tr><td align=\"left\" class=\"users\"><b><a href = 'pm.php?mode=2&id=".$row [0]."'>".$row [3]."</a></b></td><td class=\"users\">".$row [1]."</td></tr>";
			else
				print "<tr><td align=\"left\" class=\"users\"><a href = 'pm.php?mode=2&id=".$row [0]."'>".$row [3]."</a></td><td class=\"users\">".$row [1]."</td></tr>";
		}
		?>
        </table>
		<?php
	break;
	
	case 2:
		$id = (int) $_GET ['id'];
		
		if (!$id)
			_err("ID non Specificato");
			
		$query = "SELECT * FROM ". __PREFIX__ ."pm WHERE id = '". $id ."'";
		$row   = mysql_fetch_row (mysql_query ($query));
		
		if ((!$row [0]) || ($row [2] != $username))
			_err("<b>Errore!</b> Non sei autorizzato a leggere questo PM!");
			
		if ($row [5]) {
			$query = "UPDATE ". __PREFIX__ ."pm SET new = 0 WHERE id = '". $id ."'";
			mysql_query ($query);
		}
		?>
		<table>
			<tr><td>Oggetto:</td><td><?php print $row [3]; ?></td></tr>
			<tr><td>Da:</td><td><?php print $row [1]; ?></td></tr>
		</table>
		<p>Messaggio </p>
		<br />
		<div class="message_pm">
		<?php
		print BBcode($row[4])
			. "\n<br />"
			. "\n<hr />"
			. "\n<p><a href = 'pm.php?mode=3&id=". $row [0] ."'>[ Rispondi ]</a>"
			. "\n<a href = 'pm.php?mode=4&id=". $row[0] ."'>[ Cancella ]</a>"
			. "</p>"
			. "\n</div>";
	break;
	
	case 3:
	
		@$to    = clear ($_REQUEST['to']);
		@$title = clear ($_POST ['title']);
		@$data  = clear ($_POST ['data']);
		
		if (($title) && ($to) && ($data)) {
			$query = "SELECT id FROM ". __PREFIX__ ."users WHERE username = '". $to ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (!$row [0])
				_err("<b>Errore!</b>l'Username specificato non è valido!");
				
			$query = "INSERT INTO ". __PREFIX__ ."pm (from_usr, to_usr, title, data, new
						) VALUES (
					'". $username ."', '{$to}', '{$title}', '{$data}', 1)";
					
			mysql_query ($query) or _err(mysql_error());
			
			header ("Location: pm.php");
		}else{
			@$id   = (int) $_GET ['id'];
			$query = "SELECT from_usr, to_usr, title, data FROM ". __PREFIX__ ."pm WHERE id = '". $id ."'";
			$row   = mysql_fetch_row (mysql_query ($query));
			
			if (($row [0]) && ($row [1] == $username)) {
			
				$to = $row [0];
				
				if (!preg_match ("|^Re\:|", $row [2]))
					$title = "Re: ". $row [2];
				else
					$title = $row [2];
				
				$message = "[quote]\n". $row[3] ."\n[/quote]\n\n";
				
				
			}
			?>
			<br />
			<button name="grassetto" onclick="document.getElementById('reply').value+='[b] [/b]'"><b>[b]</b></button>
			<button name="corsivo" onclick="document.getElementById('reply').value+='[i] [/i]'"><i>[i]</i></button>
			<button name="sottolineato" onclick="document.getElementById('reply').value+='[u] [/u]'"><u>[u]</u></button>
			<button name="code" onclick="document.getElementById('reply').value+='[code] [/code]'">[code]</button>
			<button name="quote" onclick="document.getElementById('reply').value+='[quote] [/quote]'">[quote]</button>
			<button name="scrivi_rosso" onclick="document.getElementById('reply').value+='[red] [/red]'"><u>[red]</u></button>
			<button name="scrivi_verde" onclick="document.getElementById('reply').value+='[green] [/green]'">[green]</button>
			<button name="scrivi_giallo" onclick="document.getElementById('reply').value+='[yellow] [/yellow]'">[yellow]</button>
			<button name="link" onclick="document.getElementById('reply').value+='[url=http://site.com/] /name_site/ [/url]'"><u>[url]</u></button>
			<button name="youtube" onclick="document.getElementById('reply').value+='[youtube] /youtube_id/ [/youtube]'">[youtube]</button>
			<br />
			<br />
			<img src="img/02.jpg" alt="allegro" onclick="document.getElementById('reply').value+=' :D '">
			<img src="img/01.jpg" alt="sorriso" onclick="document.getElementById('reply').value+=' :) '">
			<img src="img/05.gif" alt="triste" onclick="document.getElementById('reply').value+=' :( '">
			<img src="img/0mg.gif" alt="omg" onclick="document.getElementById('reply').value+=' 0mg '">
			<img src="img/04.gif" alt="felice" onclick="document.getElementById('reply').value+=' ^_^ '">
			<img src="img/01.jpg" alt="ok" onclick="document.getElementById('reply').value+=' ;) '">
			<br />
			<br />
			<form action = 'pm.php?mode=3' method = 'POST'>
				<table>
					<tr><td>Invia A:</td><td><input name = 'to' value = '<?php print $to; ?>'></td></tr>
					<tr><td>Oggetto:</td><td><input name = 'title' value = '<?php print $title; ?>'></td></tr>
				</table>
				<p>Messaggio</p>
				<textarea id = "reply" class = 'pm_text' name = 'data'><?php print $message; ?></textarea>
				<br />
				<input type = 'submit' value = 'Invia'>
			</form>
			<?php
		}
	break;
	
	case 4:
		$id    = (int) $_GET ['id'];
		
		$query = "SELECT to_usr FROM ". __PREFIX__ ."pm WHERE id = '". $id ."'";
		$row   = mysql_fetch_row (mysql_query ($query));
		
		if ($row [0] == $username) {
			$query = "DELETE FROM ". __PREFIX__ ."pm WHERE id = '". $id. "'";
			mysql_query ($query) or die("SQL Error:".mysql_error());
			header ("Location: pm.php");
		}else 
			_err("<b>Errore!</b>Non sei autorizzato a leggere questo PM!");
	break;
	
	default:
		header ("Location: pm.php?mode=1");
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
