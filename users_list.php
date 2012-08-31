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
 * users_list.php                                                        
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
	<p align="center"><br /><b>Lista Membri</b>
	<table align="center">
		<tr><td width="200" class="users"><p align="center"><b>Username</b></p></td>
		<td width="200" class="users"><p align="center">E-Mail</p></td>
		<td width="200" class="users"><p align="center">Gruppo</p></td></tr>
<?php
	$query = "SELECT id, username, email, level 
				FROM ". __PREFIX__ ."users";
	$res   = mysql_query ($query);
	
	while ($row = mysql_fetch_row ($res)) {
	
		$mail = (login ($username, $password) == FALSE) ? '<i>Login richiesto!</i>' : "<a href=\"mailto:".$row[2]."\">".$row[2]."</a>";
		
		print "\n<tr>"
		    . "\n<td width=\"200\" class=\"users\"><b><a href = 'profile.php?id=".$row [0]."'>".$row [1]."</a></b><br /></td>"
		    . "\n<td width=\"200\" class=\"users\">".$mail."</td>"
		    . "\n<td width=\"200\" class=\"users\">".check_level($row[3]). "</td>"
		    . "\n</tr>";
	}
?>
	</table>
<p>
</div>
<?php
footer();
?>
</body>
</html>
