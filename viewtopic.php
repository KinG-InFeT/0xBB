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
 * viewtopic.php                                                        
 ***************************************************************************/
 
include "kernel.php";

show_header();
show_menu ();

list ($username, $password) = get_data ();

$id = (int) $_GET ['id'];

if (!check_topic_id ($id))
	_err('ID Inesistente!');

// controllo se il topic è protetto
if(check_access_topic($id) != NULL && check_access_topic($id) != 'user') {
	if(login($username, $password) == TRUE) {
		if(level($username) != 'admin' && level($username) != 'mod')
			if(level($username) != check_access_topic($id))
			    _err('Non hai i permessi per visualizzare questo topic!');
	}else{
		if((check_access_topic($id) != NULL) && (check_access_topic($id) != 'user'))
			header('Location: index.php');
	}
}
?>
<div class = 'path' id = 'path'>
	<ul>
		<li><b><a href = 'index.php'>Indice Forum</a></b></li>
		<li><?php patch_forum($id,1); ?></li>
		<li><?php patch_topic($id); ?></li>
	</ul>
</div>
<br />
<div class="table_main">
	<div id="container">
<?php

// blocco topic
if(@$_POST['block'] == 1) 
	manage_block_topic($username, $id);

// cancello topic
if(@$_POST['delete_topic'] == 1) 
	delete_topic($username, $id);
	
// aggiunta nuovo messaggio
if(@$_GET['send'] == 1) 
	insert_topic(@$_POST['reply'], $id);

// sposto topic
if(@$_GET['move_topic'] == 1)
	move_topic(@$_POST['move_t_id'], @$_POST['to_forum']);

// setta topic
if(@$_GET['set_topic'] == 1) 
	set_topic(@$_POST['set_topic'], $id);

$t_id  = check_t_id($id);

$query = "SELECT  id, f_id, t_id, author, title, data, replyof, last, ora, date 
		    FROM ". __PREFIX__ ."topic 
		   WHERE id = '" . $id . "' 
		      OR replyof = '" . $id . "' 
		   ORDER BY id, last DESC";
		   
$res   = mysql_query ($query);
	
while ($row = mysql_fetch_row ($res)) {
	
	$query_2  = "SELECT email, web_site, msn, level, id, firma 
				   FROM ". __PREFIX__ ."users 
				  WHERE username = '". $row[3] ."'";
				  
	$row_info = mysql_fetch_row (mysql_query ($query_2));
	
	$mail = (login ($username, $password) == FALSE) ? '<i>Login richiesto!</i>' : check_null($row_info[0], 1);
?>

<div id="content">
	<div id="userinfo">	
		<div><div style="float: left;"><b><?php print $row[3]; ?></b></div><div style="float: right;"><?php print check_level($row_info[3]); ?></div></div><hr />
		<div><div style="float: left;">Post:</div><div style="float:right;"><?php print check_num_topic($row[3]); ?></div></div><hr />
		<div><div style="float: left;">E-Mail:</div><div style="float: right;"><?php print $mail; ?></div></div><hr />
		<div><div style="float: left;">MsN:</div><div style="float: right;"><?php print check_null($row_info[2],1); ?></div></div><hr />
		<div><div style="float: left;">Sito Web:</div><div style="float: right;"><?php print check_null($row_info[1], 2); ?></div></div><hr />
		<div><div style="float: left;">karma:</div><div style="float: right;"><?php print karma(nick2uid($row[3])); ?></div></div><hr />
	<div>
	<div style="float: left;">
		<form method="POST" action="karma.php" />
			<input type = 'hidden' name = 'topic_id' value = '<?php print $id; ?>' >
			<input type = 'hidden' name = 'user_id' value = '<?php print nick2uid($row[3]); ?>'>
			<input class='karma_più' type = 'submit' value = '+1' name = 'vote' > <input class='karma_meno' type = 'submit' value = '-1' name = 'vote'>
		</form>
	</div>
		<div style="float: right;"><a href="pm.php?mode=3&to=<?php print $row [3]; ?>">PM</a>  <a href = 'profile.php?id=<?php print nick2uid ($row [3]); ?>'>Profile</a></div>
	</div>
	</div>
	<div id="topic">	

		<b>Titolo: </b><?php print $row [4] .check_graphic_block_topic($row[0]) . check_graphic_important_topic($row[0]) . check_graphic_announcement_topic($row[0])."<br />\n<font size=1> @<i>Scritto il ".$row[9]." alle ore ".$row[8]."</i></font>\n"; ?>
		<br />
		<?php
		if (
			(login ($username, $password)) && 
			(($row [2] == $username) || (level($username) == 'admin')) || 
			(level($username) == 'mod')
		) {
				print "\n<a href = 'manage.php?id=". $row[0]. "&t_id=" . $id . "'>[Edita]</a>"
					. "\n<a href='manage.php?id=". $row[0] ."&t_id=" . $id . "&delete=1'>[Elimina]</a>";
		}
		?>
		<br />
		<br />
		<?php
			
			//parte del messaggio		
			//print wordwrap (br($row [5]), 200 , "<br />");
			print BBcode($row [5]);		
			
			//chiusura della div di topic
			print "\n</div>";
		    
		    // se la firma cè la visualizzo...altrimenti no
		    if($row_info[5]) {
    			//stampo la firma dell'utente
	    		print "\n<div id=\"user_firma\">"
	    			. "\n<center><b>.::Firma::.</b></center><br />"
					. BBcode($row_info[5])
					. "</div>";
	        }
			
			//chiusura della div di content
			print "\n</div>";
			
		}//end while 
	
	if((level($username) == 'admin') || (level($username) == 'mod')) {
	
		// blocca/chiudi topic
		if((check_block_topic($id) == 0) || (check_block_topic($id) == NULL)) {
			$block_topic = "\n<form method=\"POST\" action=\"viewtopic.php?id=" . $id . "\" />"
						 . "\n<input type=\"hidden\" value=\"1\" name=\"block\" />"
						 . "\n<input type=\"submit\" value=\"Chiudi Topic\" />"
						 . "\n</form>\n";
		}else{
			$block_topic = "\n<form method=\"POST\" action=\"viewtopic.php?id=" . $id . "\" />"
						 . "\n<input type=\"hidden\" value=\"1\" name=\"block\" />"
						 . "\n<input type=\"submit\" value=\"Ri-Apri Topic\" />"
						 . "\n</form>\n";
		}
		
		// setta topic come annuncio o importante (deselezionato annulla il set)
		if(check_important_topic($id) == 0 && check_announcement_topic($id) == 0) {
		
			$set_topic  = "\n<form method=\"POST\" action=\"viewtopic.php?id=" . $id . "&set_topic=1\" />"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"null\" checked> Topic Normale"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"announcement\"> Setta il topic in forma Annuncio"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"important\" > Setta il topic in forma Importante"
						. "\n<input type=\"submit\" value=\"Setta topic\" />"
						. "\n</form>";
						 
		}elseif(check_important_topic($id) == 1 && check_announcement_topic($id) == 0) {
		
			$set_topic  = "\n<form method=\"POST\" action=\"viewtopic.php?id=" . $id . "&set_topic=1\" />"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"null\"> Topic Normale"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"announcement\"> Setta il topic in forma Annuncio"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"important\" checked> Setta il topic in forma Importante"
						. "\n<input type=\"submit\" value=\"Setta topic\" />"
						. "\n</form>";
												
		}elseif(check_important_topic($id) == 0 && check_announcement_topic($id) == 1) {
		
			$set_topic  = "\n<form method=\"POST\" action=\"viewtopic.php?id=" . $id . "&set_topic=1\" />"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"null\"> Topic Normale"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"announcement\" checked> Setta il topic in forma Annuncio"
						. "\n<input type=\"radio\" name=\"set_topic\" value=\"important\"> Setta il topic in forma Importante"
						. "\n<input type=\"submit\" value=\"Setta topic\" />"
						. "\n</form>";
		}
		
		// cancella topic
		$delete_topic = "<form method=\"POST\" action=\"viewtopic.php?id=" . $id . "\" />"
					  . "\n<input type=\"hidden\" value=\"1\" name=\"delete_topic\" />"
					  . "\n<input type=\"submit\" value=\"Rimuovi Topic\" />"
					  . "\n</form>\n";
		
		// sposta topic
		$move_topic = "<form method=\"POST\" action=\"viewtopic.php?id=" . $id . "&move_topic=1\" />"
					  . "\n<select name=\"to_forum\">";
					  
		$list_forum = mysql_query("SELECT id, title 
									 FROM ". __PREFIX__ ."forum 
								 ORDER BY position");
		
		while($row = mysql_fetch_array($list_forum))
		    $move_topic .= "\n<option value=\"".$row['id']."\">".$row['title']."</option>";
  
		$move_topic .= "\n</select>"
					  . "\n<br />"
					  . "\n<br />"
					  . "\n<input type=\"hidden\" value=\"".check_t_id($id)."\" name=\"move_t_id\" />"
					  . "\n<input type=\"submit\" value=\"Sposta Topic\" />"
					  . "\n</form>\n";
	}else{
		$block_topic  = '';
		$delete_topic = '';
		$move_topic   = '';
		$set_topic    = '';
	}

// inizio div reply
print "\n<div class = 'reply'>\n";

if (login ($username, $password)) 
{// controllo se l'utente è loggato
	if((check_block_topic($id) == 0) || (check_block_topic($id) == NULL)) 
	{// controllo se il topic è bloccato/chiuso
		if(level($username) != 'banned')
		{// controllo se l'utente è bannato
			print "<table>"
				. "\n<tr><td>"
				. @$block_topic  . "\n</td><td>"
				. @$delete_topic . "</td><td>"
				. @$move_topic   . "</td></tr>"
				. "\n</table>";
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
	<form method = 'POST' action = 'viewtopic.php?id=<?php print $id; ?>&t_id=<?php print $t_id; ?>&send=1'>
		Messaggio:<br />
		<textarea id = 'reply' name = 'reply' class = 'reply_text'></textarea><br /><br />
		<input type = 'submit' value = 'Invia Reply'>
	</form>
    <br />
    <br />	
	<?php
		print @$set_topic;
	?>

	</div>
	
<?php 
		
		}else{
			print "\n<p align='center'><fonr color='red'>Sei Stato bannato dal Forum, per tanto non puoi rispondere ai topic.</font></p>";	
		}	
	}else{
		print "\n<p align='center'><fonr color='red'>Il topic è Stato Bloccato/Chiuso!</font><br /><br /> ".$block_topic."</p>\n";	
	}
	
}else{
	print "\n<center><h2><i>Devi essere loggato per poter rispondere ai Topic.</i></h2></center>";
}
?>
	
		<!--chiusura della div di content-->
		</div>
	<!-- chiusura della div di container -->
	</div>
<!-- chiusura della div di table_main -->
</div>
<?php
footer();
?>
</body>
</html>
