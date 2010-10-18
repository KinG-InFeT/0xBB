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
 * menage.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu();
list ($username, $password) = get_data ();

$id   = (int) $_GET ['id'];
$t_id = (int) $_GET['t_id'];

if (!is_post ($id))
	die ('<br /><br /><br /><div class="error_msg" align="center">ID Inesistente<br /><br /><a href="index.php">Torna alla Home</a></div>');
	
$query = "SELECT * FROM ".PREFIX."topic WHERE id = '{$id}'";
$data  = mysql_fetch_row (mysql_query ($query));

if (!login ($username, $password))
	die ("<div class=\"error_msg\" align=\"center\"><b>Errore!</b>Non sei Loggato!<br /><br /><a href=\"index.php\">Torna alla Index</a></div>");

if ((!level($username)) && ($username != $data [2]))
	die ('<br /><br /><br /><div class="error_msg" align="center">Non sei autorizzato a modificare questo topic.<br /><br /><a href="index.php">Torna alla Index</a></div>');

if (@$_GET ['delete']) {
	$query = "DELETE FROM ".PREFIX."topic WHERE id = '{$id}' OR replyof = '{$id}'";
	mysql_query ($query) or die(mysql_error());
	print "<br /><br /><br /><div class=\"success_msg\" align=\"center\">Topic Cancellato con successo<br /><br /><a href='viewtopic.php?id={$t_id}'>Torna al Topic</a></div>";
	header( "refresh:3; url=viewtopic.php?id={$t_id}" );
	exit;
}

@$text  = BBcode (clear ($_POST ['text']));
@$title = clear  ($_POST ['title']);

if (($text) && ($title)) {
	$query = "UPDATE ".PREFIX."topic SET title = '{$title}', data = '{$text}' WHERE id = '{$id}'";
	mysql_query ($query) or die(mysql_error());
	print "<br /><br /><br /><div class=\"success_msg\" align=\"center\">Topic Editato con successo<br /><br /><a href='viewtopic.php?id={$t_id}'>Torna al Topic</a></div>";
	header( "refresh:3; url=viewtopic.php?id={$t_id}" );
	exit;
}
@$text = clear_br (BBcode_revers ($data[5]));
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>
<div class = 'edit'>
<h2 align="center">Editor Topic</h2>
<form action = 'manage.php?id=<?php echo $id; ?>&t_id=<?php echo $t_id; ?>' method = 'POST'>
	Titolo: <input name = 'title' value = '<?php echo $data [4]; ?>' style = "width: 50%">
	<p>Topic:</p>
	<textarea name = 'text' class = 'topic_data'><?php echo $text; ?></textarea>
	<input type = 'submit' value = 'Edit'><p>
</form>
	</div>
</div>
<?php
$top = NULL;
footer ($top); 
?>
</body>
</html>
