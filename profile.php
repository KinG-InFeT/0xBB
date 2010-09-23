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
 * profile.php                                                        
 ***************************************************************************/
include "kernel.php";
show_header();
show_menu ();
?>
<div class = 'path' id = 'path'>
	<table>
		<tr><td><b><a href = 'index.php'>Indice Forum</a></b></td></tr>
	</table>
</div>		
	<div class = 'profile'>
<?php
$id = (int) $_GET ['id'];
if ($id) {
	$query = "SELECT * FROM ".PREFIX."users WHERE id = '{$id}'";
	$row   = mysql_fetch_row (mysql_query ($query));
	if ($row [0]) {
		?>
			<h2>Profilo Utente -> <b><?php echo $row[1]; ?></b></h2>
			<br /><br />
			<table>
				<tr><td><b>Invia </b></td><td><a href="pm.php?mode=3&to=<?php echo $row [1]; ?>">PM</a></td></tr>
				<tr><td><b>E-Mail: </b></td><td><?php echo check_null($row [6],1); ?></td></tr>
				<tr><td><b>Web Site: </b></td><td><?php echo check_null($row[7],2); ?></td></tr>
				<tr><td><b>MsN: </b></td><td><?php echo check_null($row[8],1); ?></td></tr>		
				<tr><td><b>Categoria: </b></td><td><?php echo check_level($row[3]); ?></td></tr>
				<tr><td><b>Numero Post: </b></td><td><?php echo check_num_topic($row[1]); ?></td></tr>
			</table>
<?php
	}
}else{
	print "\n<br /><br /><br />\n<center>Specificare un ID</center>";
}
?>
		</div>
</div>
</div>
<?php
$top = NULL;
footer ($top);
?>
</body>
</html>
