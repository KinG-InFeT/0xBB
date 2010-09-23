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
 * viewforum.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu ();

if (!check_forum_id ($_GET ['id']) || !($_GET ['id']))
	die ("<br /><br /><br /><center>ID non Specificato<br /><br /><a href=\"index.php\">Torna alla Index</a></center>");	

$id = (int) $_GET ['id'];

if (isset($_GET['page']) && is_numeric($_GET['page']) && ((int)$_GET['page']) > 0 ) 
	$page = (int) $_GET['page']; 
else
	$page = 1;

$query   = "SELECT * FROM ".PREFIX."topic WHERE f_id = '{$id}' AND replyof < 0 ORDER by last DESC";
$res     = mysql_query ($query);
$Pager	 = new Pagination();
$pager 	 = $Pager->paginazione($res, $limit, $page); 
$offset  = $pager['offset'];
$limit 	 = $pager['limit']; 
$page 	 = $pager['page'];
$query_2 = "SELECT * FROM ".PREFIX."topic WHERE f_id = '{$id}' AND replyof < 0 ORDER by last DESC LIMIT ".$limit." OFFSET ".$offset;
$result  = mysql_query($query_2) or die(mysql_error());
$top     = 11;
?>
<br />
<div class = 'path' id = 'path'>
	<table>
		<tr>
			<td><b><a href = 'index.php'>Indice Forum</a></b></td>
			<td><td><?php patch_forum($id,2); ?></td></td></tr>
			<tr><td>Pagine: <?php echo pagination($pager['numPages'],$page,$id); ?></td></tr>
		</tr>
	</table>
	<table>
		<tr>

</tr>
</table>
</div>
<br />
<!-- New Topic -->
<table align="center"><tr><td>
<form method="GET" action="post.php" />
	<input type="hidden" value="<?php echo $id; ?>" name="id"/>
	<input type="submit" value="Nuovo Topic" />
</form></td></tr></table>
<!-- end new topic -->
<br />
<table class="main" align="center" width = 75%>
<tr class="forums" >
	<td class="forums"><b><p align="center">-Nome Topic-</p></b></td>
	<td class="forums"><b><p align="center">-Autore-</p></b></td>
	<td class="forums"><b><p align="center">-Risposte-</p></b></td>
</tr>
<?php
while ($row = mysql_fetch_row ($result)) {

	$query = "SELECT id FROM ".PREFIX."topic WHERE replyof = '{$row [0]}'";
	$res2 = mysql_query ($query);
	$replies = 0;
	while (mysql_fetch_row ($res2))
		$replies++;
?>
	<tr class="forums" >
		<td class="forums"><a href = 'viewtopic.php?id=<?php echo $row [0]."'>".$row [4]."</a> ".check_graphic_block_topic($row[0])."</td>\n"; ?></td>
		<td class="forums"><a href = 'profile.php?id=<?php echo nick2uid ($row [3])."'>".$row [3]."</td>\n"; ?></td>
		<td class="forums"><?php echo $replies; ?></td>
	</tr>
<?php
	$top += 6;
}// end while write topics

print "</table>\n";

$top = NULL;
footer ($top); 
?>
</body>
</html>
