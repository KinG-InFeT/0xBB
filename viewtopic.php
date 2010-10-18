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
 * viewtopic.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu ();
$id = (int) $_GET ['id'];

if (!check_topic_id ($id))
	die ('<br /><br /><br /><div class="error_msg" align="center">ID Inesistente!<br /><br /><a href="index.php">Torna alla Index</a></div>');

?>
<div class = 'path' id = 'path'>
	<table>
		<tr>
			<td><b><a href = 'index.php'>Indice Forum</a></b></td>
			<td><?php patch_forum($id,1); ?></td>
			<td><?php patch_topic($id); ?></td>
		</tr>
	</table>
</div>
<table class="table_main">
<?php
@$reply = BBcode (clear ($_POST ['reply']));
$date   = (@date("d-m-y"));
$ora    = (@date("G:i"));

list ($username, $password) = get_data ();

if(@$_POST['block'] == 1) {
	manage_block_topic($username,$id);
}

if(@$_POST['delete_topic'] == 1) {
	delete_topic($username,$id);
}
	
if ($reply) {
	if (!login ($username, $password))
		die ('<br /><br /><br /><div class="error_msg" align="center"><b>Errore!</b>Bisogna essere Loggati per postare nel forum.</div>');
		
	$query = "SELECT f_id, t_id, title FROM ".PREFIX."topic WHERE id = '{$id}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	//Fix SQL Injection :P
	$title = clear ($row[2]);
	$f_id  = clear ($row[0]);
	$t_id  = clear ($row[1]);
	//Fine Fix
	$query = "INSERT INTO ".PREFIX."topic (
											f_id, t_id, author, title, data, replyof, date, ora
										   ) VALUES (
											'{$f_id}', '{$t_id}', '{$username}', 'Re: {$title}', '{$reply}', '{$id}', '{$date}', '{$ora}'
										   )";
	mysql_query ($query) or die (mysql_error());
	//aggiorno il last con time()
	$query = "UPDATE ".PREFIX."topic SET last = '".time()."' WHERE id = '{$id}'";
	mysql_query ($query) or die(mysql_error());
}

$t_id  = check_t_id($id);
$query = "SELECT  id, f_id, t_id, author, title, data, replyof, last, ora, date FROM ".PREFIX."topic WHERE id = '{$id}' OR replyof = '{$id}'";
$res   = mysql_query ($query);
$top   = 20;
while ($row = mysql_fetch_row ($res)) {

	$query_2  = "SELECT email,web_site,msn,level,id FROM ".PREFIX."users WHERE username = '{$row[3]}'";
	$row_info = mysql_fetch_row (mysql_query ($query_2));
?>
		<tr>
		<td>
		<table class="table_info_user">
		<tr><td class="userinfoentry">-><font size="2"><b><?php echo $row[3]; ?></b></font></td><td class="userinfoentry" style="text-align: right"><?php print check_level($row_info[3]); ?></td></tr>
		<tr><td class="userinfoentry">Post: </td><td class="userinfoentry" style="text-align: right"><?php print check_num_topic($row[3]); ?></td></tr>
		<tr><td class="userinfoentry">E-Mail: </td><td class="userinfoentry" style="text-align: right"> <?php echo check_null($row_info[0],1); ?></td></tr>
		<tr><td class="userinfoentry">MsN:</td><td class="userinfoentry" style="text-align: right"><?php echo check_null($row_info[2],1); ?></td></tr>
		<tr><td class="userinfoentry">Sito Web:</td><td class="userinfoentry" style="text-align: right"><?php echo check_null($row_info[1],2); ?></td></tr>
		<tr><td class="userinfoentry">karma:</td><td class="userinfoentry" style="text-align: right"><?php echo karma(nick2uid($row[3])); ?></td></td>
		<tr><td class="userinfoentry">
		<form method="POST" action="karma.php" />
			<input type = 'hidden' name = 'topic_id' value = '<?php echo $id; ?>' >
			<input type = 'hidden' name = 'user_id' value = '<?php echo nick2uid($row[3]); ?>'>
			<input class='karma_più' type = 'submit' value = '+1' name = 'vote' > <input class='karma_meno' type = 'submit' value = '-1' name = 'vote'>
		</form></td>
		<td class="userinfoentry" style="text-align: right"><a href="pm.php?mode=3&to=<?php echo $row [3]; ?>">PM</a>  <a href = 'profile.php?id=<?php echo nick2uid ($row [3]) . "'>Profile</a>"; ?></td>
		</table>
		</td>
		<td class="topiccontent">
		<hr />
		<li class="posttime">

		<b>Title: </b><?php echo $row [4] . "<br />\n<font size=1> @<i>Scritto il ".$row[9]." alle ore ".$row[8]."</i></font>\n"; ?><br>
		<?php
		if (((login ($username, $password))) && (($row [2] == $username) || (level($username) == 'admin')) || (level($username) == 'moderator'))
		{
			echo "<a href = 'manage.php?id={$row[0]}&t_id={$id}'>[Edita]</a> <a href='manage.php?id={$row[0]}&t_id={$id}&delete=1'>[Elimina]</a>";
		}
		?>
		</li><p>
		<?php
			//parte del messaggio		
			//print wordwrap (br($row [5]), 200 , "<br />") . "</p>\n</td>\n</tr>";
			print br($row [5]) . "</p>\n</td>\n</tr>";
			$top += 38;
		}//end while 
	
	print "</table>";
	
	if((level($username) == 'admin') || (level($username) == 'moderator')) 
	{
		if((check_block_topic($id) == 0) || (check_block_topic($id) == NULL))
		{
			$block_topic = "\n<form method=\"POST\" action=\"viewtopic.php?id={$id}\" />\n<input type=\"hidden\" value=\"1\" name=\"block\" />\n<input type=\"submit\" value=\"Chiudi Topic\" />\n</form>\n";
		}else{
			$block_topic = "\n<form method=\"POST\" action=\"viewtopic.php?id={$id}\" />\n<input type=\"hidden\" value=\"1\" name=\"block\" />\n<input type=\"submit\" value=\"Ri-Apri Topic\" />\n</form>\n";
		}
		
		$delete_topic = "<form method=\"POST\" action=\"viewtopic.php?id={$id}\" />\n<input type=\"hidden\" value=\"1\" name=\"delete_topic\" />\n<input type=\"submit\" value=\"Rimuovi Topic\" />\n</form>\n";
	}

print "\n<div class = 'reply' style = 'top: ".$top."%'>\n";

if (login ($username, $password)) 
{
	if((check_block_topic($id) == 0) || (check_block_topic($id) == NULL)) 
	{
		if(level($username) != 'banned')
		{
		print "<table><tr><td>".@$block_topic."</td><td>".@$delete_topic."</td></tr></table>";
	?> 
		BBcode: <a href="javascript:info_BBcode();">INFO</a> 
		<button name="b" onclick="document.getElementById('reply').value+='[b] [/b]'"><b>[b]</b></button>
		<button name="corsivo" onclick="document.getElementById('reply').value+='[s] [/s]'"><i>[s]</i></button>
		<button name="sottolineato" onclick="document.getElementById('reply').value+='[u] [/u]'"><u>[u]</u></button>
		<button name="code" onclick="document.getElementById('reply').value+='[code] [/code]'">[code]</button>
		<button name="quote" onclick="document.getElementById('reply').value+='[quote] [/quote]'">[quote]</button>
		<br />
	<form method = 'POST' action = 'viewtopic.php?id=<?php echo $id; ?>&t_id=<?php echo $t_id; ?>'>
		Inserire il Testo:<br />
		<textarea id = 'reply' name = 'reply' class = 'reply_text'></textarea><br>
		<input type = 'submit' value = 'Invia Reply'>
	</form>
<?php 
		}else{
			print "\n<p align='center'><fonr color='red'>Sei Stato bannato dal Forum, per tanto non puoi rispondere ai topic.</font></p>";	
		}	
	}else{
		print "\n<p align='center'><fonr color='red'>Il topic è Stato Bloccato/Chiuso!</font><br /><br /> {$block_topic}</p>\n";	
	}
	
}else{
	print "\n<p align='center'>Devi essere loggato per poter rispondere ai Topic.</p>";
}
?>
</div>
<?php
$top += 50;
footer ($top); 
?>
</body>
</html>
