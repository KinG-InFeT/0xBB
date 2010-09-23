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
 * Software version:			1.0 ~ RC2
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
	die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b>Devi essere registrato per creare nuovi Topic.\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");
	
if(level($username) == 'banned')
	die ("<br /><br /><br />\n<div class=\"error_msg\" align=\"center\">\n<b>Errore!</b><br /><b><u>".$username."</u></b> è stato BANNATO dal forum!.\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a>\n</div>");

$id     = (int) $_GET ['id'];
$t_id   = rand (10000,99999);
@$title = clear ($_POST ['title']);
@$text  = BBcode (clear ($_POST ['text']));
$date   = (@date ("d-m-y"));
$ora    = (@date ("G:i"));

if (($title) && ($text)) {
	$query = "INSERT INTO ".PREFIX."topic (f_id, t_id, author, title, data, replyof, last, date, ora) VALUES ('{$id}', '{$t_id}', '{$username}', '{$title}', '{$text}', -1, '".time()."', '{$date}', '{$ora}')";
	mysql_query ($query) or die(mysql_error());
	print "<br /><br /><br /><div class=\"success_msg\" align=\"center\">Topic Postato con Successo!<br /><br /><br /><a href=\"viewforum.php?id={$id}\">Vai al Topic</a></div>";
	header( "refresh:3; url=viewforum.php?id={$id}" );
	exit;
}else{
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<div class = 'new_topic'>
		BBcode: <a href="javascript:info_BBcode();">INFO</a> 
		<button name="b" onclick="document.getElementById('text').value+='[b] [/b]'"><b>[b]</b></button>
		<button name="corsivo" onclick="document.getElementById('text').value+='[s] [/s]'"><i>[s]</i></button>
		<button name="sottolineato" onclick="document.getElementById('text').value+='[u] [/u]'"><u>[u]</u></button>
		<button name="code" onclick="document.getElementById('text').value+='[code] [/code]'">[code]</button>
		<button name="quote" onclick="document.getElementById('text').value+='[quote] [/quote]'">[quote]</button>
		<form action = 'post.php?id=<?php echo $id; ?>' method = 'POST'>
			<p>Title: <input name = 'title' type = 'text' style = "width: 50%"><p>
			Inserire il Testo:<br />
			<textarea id = 'text' name = 'text' class = 'topic_data'></textarea><br>
			<input type = 'submit' value = 'Post'>
		</form>
</div>
</div>
<?php
}
$top = NULL;
footer ($top); 
?>
</body>
</html>
