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
 * post.php                                                        
 ***************************************************************************/
include "kernel.php";

show_header();
show_menu ();

list ($username, $password) = get_data ();

if (!login ($username, $password))
	_err("<b>Errore!</b>Devi essere registrato per creare nuovi Topic!");
	
if(level($username) == 'banned')
	_err("<b>Errore!</b><br /><b><u>".$username."</u></b> è stato BANNATO dal forum!");

$id     = (int) $_GET ['id'];
$t_id   = time() + rand (10000,99999);
@$title = clear  ($_POST ['title']);
@$text  = clear ($_POST ['text']);
$date   = (@date ("d-m-y"));
$ora    = (@date ("G:i"));

if (($title) && ($text)) {

	// ho settato il topic in maniera particolare?
	if((level($username) == 'admin') || (level($username) == 'mod')) {
		if(@$_POST['set_topic'] == 'announcement') {
			$set_announcement = 1;
			$set_important    = 0;
		}elseif(@$_POST['set_topic'] == 'important'){
			$set_announcement = 0;
			$set_important    = 1;
		}else{
			$set_announcement = 0;
			$set_important    = 0;
		}
	}else{
		$set_announcement = 0;
		$set_important    = 0;
		_err('Non hai i permessi per settare un topic livello '. htmlspecialchars(@$_POST['set_topic']));
	}
		
	$query = "INSERT INTO ". __PREFIX__ ."topic (f_id, t_id, author, title, data, replyof, last, date, ora, important, announcement
				) VALUES (
			'". $id ."', '". $t_id ."}', '". $username ."', '{$title}', '{$text}', -1, '".time()."', '{$date}', '{$ora}', ".$set_important.", ".$set_announcement.")";
	
	mysql_query ($query) or _err(mysql_error());
		
	print "<br /><br /><br /><div class=\"success_msg\" align=\"center\">Topic Postato con Successo!<br /><br /><br /><a href=\"viewforum.php?id=". $id ."\">Vai al Topic</a></div>";
	header( "refresh:3; url=viewforum.php?id=". $id);
	exit;
}else{
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<br />
<div class = 'new_topic'>
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
		<form action = 'post.php?id=<?php print $id; ?>' method = 'POST'>
			<p>Titolo: <input name = 'title' type = 'text' style = "width: 50%"><p>
			Messaggio:<br />
			<textarea id='text' name = 'text' class="topic_data"></textarea>
			<br />
		<?php
		if((level($username) == 'admin') || (level($username) == 'mod')) {
		?>
			<br />
			<input type="radio" name="set_topic" value="" checked> Topic Normale
			<input type="radio" name="set_topic" value="announcement"> Setta il topic in forma Annuncio
			<input type="radio" name="set_topic" value="important"> Setta il topic in forma Importante
			<br />
			<br />
		<?php 
		}
		?>
			<input type = 'submit' value = 'Invia Post'>
		</form>
</div>
</div>
<?php
}
footer (); 
?>
</body>
</html>
