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
 * index.php                                                        
 ***************************************************************************/
include "kernel.php";

check_maintenance(1);

show_header();

if (@$_GET ['logout']) {
	setcookie ("0xBB_user", "", -1);
	setcookie ("0xBB_pass", "", -1);
	header ("Location: index.php");
}

show_menu (); 

if(check_exist_forum() == FALSE) {
	print '<br /><br /><br /><h2 align="center">Nessun Forum Creato, Entrare nell\'amministrazione e crearne uno!</h2>';
	footer();
	
	print "</body>"
		. "\n</html>";
		
	exit;
}
?>

<table class="main" >
<tr class="forums" >
	<td class="forums"><b>Nome Forum</b></td>
	<td class="forums"><b>Ultima Discussione</b></td>
	<td class="forums"><b>Discussioni</b></td>
	<td class="forums"><b>Messaggi</b></td>
</tr>
<tr>
	<td colspan="4"><hr style="margin:1px;height:1px;border:none;background-color:white;"></td>
</tr>
<?php
$query = "SELECT * 
			FROM ". __PREFIX__ ."forum 
		ORDER BY position";
		
$res   = mysql_query ($query);

while ($row = mysql_fetch_row ($res)) {
	
	//numero topic aperti
	$topics = 0;
	
	$query  = "SELECT id 
				 FROM ". __PREFIX__ ."topic 
				WHERE f_id = '". $row[0] ."' 
			      AND replyof < 0";
				  
	$res2   = mysql_query ($query);
	
	while ($row2 = mysql_fetch_row ($res2))
		$topics++;
		
	//numero post per ogni forum/topic
	$posts  = 0;	
	$query2 = "SELECT id 
				 FROM ". __PREFIX__ ."topic 
				WHERE f_id = '". $row[0] ."'";
				
	$res3   = mysql_query ($query2);
	
	while ($row3 = mysql_fetch_row ($res3))
		$posts++;
		
	//last topic
	$query      = "SELECT id, title 
					 FROM ". __PREFIX__ ."topic 
					WHERE f_id = '".$row[0]."' 
					  AND replyof < 0 
					ORDER BY last DESC";
					
	$last_topic = mysql_fetch_row (mysql_query ($query));
	
	//controllo se esistono messaggi oppure no
	if($last_topic[0] == NULL)
		$last_topic = "Nessun Messaggio.";
	else
		$last_topic = "@ <a href=\"viewtopic.php?id=".$last_topic[0]."\">".$last_topic[1]."</a>";
	?>
	<tr class="forums">
		<td class="forums"><a href = 'viewforum.php?id=<?php print $row [0]."'>./ ".$row [1]."</a>  ". check_graphic_access_forum($row[0]) ."<br /><font color=\"grey\"><i>".$row [2]."</i></font>"; ?></td>
		<td class="forums"><?php print $last_topic; ?></td>
		<td class="forums"><?php print $topics; ?></td>
		<td class="forums"><?php print $posts; ?></td>
	</tr>
	<tr>
	<td colspan="4"><hr style="margin:1px;height:1px;border:none;background-color:white;"></td>
</tr>
	<?php
}
?>
	</table>
</div>
<?php
footer();
?>
</body>
</html>
