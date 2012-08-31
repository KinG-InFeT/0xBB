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
 * viewforum.php                                                        
 ***************************************************************************/
 
include "kernel.php";

show_header();
show_menu ();

list ($username, $password) = get_data ();

if (!check_forum_id ($_GET ['id']) || !($_GET ['id']))
	_err("ID non Specificato!");
	
$id = (int) $_GET ['id'];

// controllo se il forum è protetto
if(check_access_forum($id) != NULL && check_access_forum($id) != 'user') {
	if(login($username, $password) == TRUE) {
		if(level($username) != 'admin' && level($username) != 'mod')
			if(level($username) != check_access_forum($id))
			    _err('Non hai i permessi per visualizzare questo topic!');
	}else{
		if((check_access_forum($id) != NULL) && (check_access_forum($id) != 'user'))
			header('Location: index.php');
	}
}

if (isset($_GET['page']) && is_numeric($_GET['page']) && ((int) $_GET['page']) > 0 ) 
	$page = (int) $_GET['page']; 
else
	$page = 1;

$query   = "SELECT * 
			  FROM ". __PREFIX__ ."topic 
			 WHERE f_id = '". $id ."' 
			   AND replyof < 0 
			 ORDER by last DESC";

$res     = mysql_query ($query);
$limit   = 10; //quanti topic per pagina?
$Pager	 = new Pagination();
$pager 	 = $Pager->paginazione($res, $limit, $page); 
$offset  = $pager['offset'];
$limit 	 = $pager['limit']; 
?>
<br />
<div class = 'path' id = 'path'>
	<table>
		<tr>
			<td><b><a href = 'index.php'>Indice Forum</a></b></td>
			<td><?php patch_forum($id, 2); ?></td>
		</tr>
		<tr>
			<td>Pagine: <?php print print_pagination($pager['numPages'], $page, $id); ?></td>
		</tr>
	</table>
</div>
<br />
<?php
// se loggato visualizzo il pulsante per la creazione di un nuovo topic
if(login($username, $password) == TRUE) {
?>
<!-- New Topic -->
<table align="center">
	<tr>
		<td>
			<form method="GET" action="post.php" />
				<input type="hidden" value="<?php print $id; ?>" name="id"/>
				<input type="submit" value="Nuovo Topic" />
			</form>
		</td>
	</tr>
</table>
<!-- end new topic -->
<?php
}
?>
<table class="main">
<tr class="forums" >
	<td class="forums"><b>Nome Topic</b></td>
	<td class="forums"><b>Autore</b></td>
	<td class="forums"><b>Risposte</b></td>
</tr>
<tr>
	<td colspan="3"><hr style="margin:1px;height:1px;border:none;background-color:white;"></td>
</tr>
<?php
// print topic announcement and important
$query_2 = "SELECT * 
			  FROM ". __PREFIX__ ."topic 
			 WHERE f_id = '". $id ."' 
			   AND replyof < 0
			   AND (important = 1
			    	OR announcement = 1) 
			 ORDER by last DESC 
			 LIMIT ".$limit." 
			OFFSET ".$offset;
			
$result  = mysql_query($query_2) or _err(mysql_error());

while ($row = mysql_fetch_row ($result)) {

	$query = "SELECT id FROM ". __PREFIX__ ."topic WHERE replyof = '". $row[0] ."'";
	$res2  = mysql_query ($query);
	
	$replies = 0;
	
	while(mysql_fetch_row($res2))
		$replies++;
?>
	<tr>
		<td class="forums"><a href = 'viewtopic.php?id=<?php print $row [0]."'>".$row [4]."</a> ".check_graphic_block_topic($row[0]) . check_graphic_important_topic($row[0]) . check_graphic_announcement_topic($row[0]); ?></td>
		<td class="forums"><a href = 'profile.php?id=<?php print nick2uid ($row [3])."'>".$row [3]; ?></a></td>
		<td class="forums"><?php print $replies; ?></td>
	</tr>
	<tr>
	<td colspan="3"><hr style="margin:1px;height:1px;border:none;background-color:white;"></td>
</tr>
<?php
	
}// end while write topics announcement and important

$query_2 = "SELECT * 
			  FROM ". __PREFIX__ ."topic 
			 WHERE f_id = '". $id ."' 
			   AND replyof < 0
			   AND ((important = 0 OR important = NULL) 
			   AND (announcement = 0 OR announcement = NULL)) 
			 ORDER by last DESC 
			 LIMIT ".$limit." 
			OFFSET ".$offset;
			
$result  = mysql_query($query_2) or _err(mysql_error());

while ($row = mysql_fetch_row ($result)) {

	$query = "SELECT id FROM ". __PREFIX__ ."topic WHERE replyof = '". $row[0] ."'";
	$res2  = mysql_query ($query);
	
	$replies = 0;
	
	while(mysql_fetch_row($res2))
		$replies++;
?>
	<tr>
		<td class="forums"><a href = 'viewtopic.php?id=<?php print $row [0]."'>".$row [4]."</a> ".check_graphic_block_topic($row[0]); ?></td>
		<td class="forums"><a href = 'profile.php?id=<?php print nick2uid ($row [3])."'>".$row [3]; ?></a></td>
		<td class="forums"><?php print $replies; ?></td>
	</tr>
	<tr>
	<td colspan="3"><hr style="margin:1px;height:1px;border:none;background-color:white;"></td>
</tr>
<?php
	
}// end while write topics

print "</table>\n";

footer(); 
?>
</body>
</html>
