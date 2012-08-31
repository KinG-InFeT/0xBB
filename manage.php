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
 * menage.php                                                        
 ***************************************************************************/
include "kernel.php";

show_header();
show_menu();

list ($username, $password) = get_data ();

$id   = (int) $_GET ['id'];
$t_id = (int) $_GET['t_id'];

if (!is_post ($id))
	_err("ID Inesistente");
	
$query = "SELECT * FROM ". __PREFIX__ ."topic WHERE id = '". $id ."'";
$data  = mysql_fetch_row (mysql_query ($query));

if (!login ($username, $password))
	_err("<b>Errore!</b>Non sei Loggato!");

if ((!level($username)) && ($username != $data [2]))
	_err("Non sei autorizzato a modificare questo topic!");

// cancello singolo messaggio
if (@$_GET ['delete']) {
	if(@$_GET['confirm'] == 1) {
		$query = "DELETE FROM ". __PREFIX__ ."topic WHERE id = '". $id ."' OR replyof = '". $id ."'";
		mysql_query ($query) or _err(mysql_error());
		print "<br /><br /><br /><div class=\"success_msg\" align=\"center\">Topic Cancellato con successo<br /><br /><a href='viewtopic.php?id={$t_id}'>Torna al Topic</a></div>";
		header( "refresh:3; url=viewtopic.php?id={$t_id}" );
		exit;
	}
	
	print "<script>"
		. "\n\tif(confirm('Sei sicuro di voler eliminare il topic?') == true) {"
		. "\n\t\tlocation.href = 'manage.php?delete=1&id=". clear($_GET['id']) ."&t_id=" . clear($_GET['t_id']) . "&confirm=1'"
		. "\n\t}else{"
		. "\n\t\tlocation.href = 'viewtopic.php?id=". clear($_GET['t_id']) ."'"
		. "\n\t}"
		. "\n</script>";
}

if (!empty($_POST['text']) && !empty($_POST['title'])) {
	@$text  = clear ($_POST['text']);
	@$title = clear ($_POST['title']);

	$query = "UPDATE ". __PREFIX__ ."topic SET title = '". $title ."', data = '". $text ."' WHERE id = '". $id ."'";
	mysql_query ($query) or _err(mysql_error());
	print "<br /><br /><br /><div class=\"success_msg\" align=\"center\">Topic Editato con successo<br /><br /><a href='viewtopic.php?id=". $t_id ."'>Torna al Topic</a></div>";
	
	if($_id == NULL || $t_id == 0)
		header( "refresh:3; url=index.php");
	else
		header( "refresh:3; url=viewtopic.php?id=". $t_id);
		
	exit;
}
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'viewtopic.php?id=<?php print $t_id; ?>'>Torna al Topic</a></b></td></tr>
	</table>
</div>
<div class = 'edit'>
	<h2 align="center">Editor Messaggio</h2><br />
		<button name="grassetto" onclick="document.getElementById('text').value+='[b] [/b]'"><b>[b]</b></button>
		<button name="corsivo" onclick="document.getElementById('text').value+='[i] [/i]'"><i>[i]</i></button>
		<button name="sottolineato" onclick="document.getElementById('text').value+='[u] [/u]'"><u>[u]</u></button>
		<button name="code" onclick="document.getElementById('text').value+='[code] [/code]'">[code]</button>
		<button name="quote" onclick="document.getElementById('text').value+='[quote] [/quote]'">[quote]</button>
		<button name="scrivi_rosso" onclick="document.getElementById('text').value+='[red] [/red]'"><u>[red]</u></button>
		<button name="scrivi_verde" onclick="document.getElementById('text').value+='[green] [/green]'">[green]</button>
		<button name="scrivi_giallo" onclick="document.getElementById('text').value+='[yellow] [/yellow]'">[yellow]</button>
		<button name="link" onclick="document.getElementById('text').value+='[url=http://site.com/] /name_site/ [/url]'"><u>[url]</u></button>
		<button name="youtube" onclick="document.getElementById('text').value+='[youtube] /youtube_id/ [/youtube]'">[youtube]</button>
		<br />
		<br />
		<img src="img/02.jpg" alt="allegro" onclick="document.getElementById('text').value+=' :D '">
		<img src="img/01.jpg" alt="sorriso" onclick="document.getElementById('text').value+=' :) '">
		<img src="img/05.gif" alt="triste" onclick="document.getElementById('text').value+=' :( '">
		<img src="img/0mg.gif" alt="omg" onclick="document.getElementById('text').value+=' 0mg '">
		<img src="img/04.gif" alt="felice" onclick="document.getElementById('text').value+=' ^_^ '">
		<img src="img/01.jpg" alt="ok" onclick="document.getElementById('text').value+=' ;) '">
		<br />
		<br />
	<form action = 'manage.php?id=<?php print $id; ?>&t_id=<?php print $t_id; ?>' method = 'POST'>
		Titolo: <input name = 'title' value = '<?php print $data [4]; ?>' style = "width: 50%">
		<p>Messaggio</p>
		<textarea id = "text" name = 'text' class = 'topic_data'><?php print $data[5]. "\n\n\n\n\n Editato il ".date("d-m-y")." alle ".date("G:i"); ?></textarea>
		<br />
		<input type = 'submit' value = 'Edit'>
		<p>
	</form>
	</div>
</div>
<?php
footer();
?>
</body>
</html>
