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
<br />
<div class = 'profile'>
<?php
$id = (int) $_GET ['id'];

if ($id) {
	$query = "SELECT * FROM ". __PREFIX__ ."users WHERE id = '". $id ."'";
	$row   = mysql_fetch_array(mysql_query ($query));
	
	if ($row [0]) {
		$mail = (login ($username, $password) == FALSE) ? '<i>Login richiesto!</i>' : check_null($row [4], 1);
		?>
			<h2>Profilo Utente: <i><?php print $row[1]; ?></i></h2>
			<br />
			<div class="info_profile">
    			<b>Invia </b><a href="pm.php?mode=3&to=<?php print $row [1]; ?>">PM</a><br />
	    		<b>E-Mail: </b><?php print $mail; ?><br />
	    		<b>Sito Web: </b><?php print check_null($row[5], 2); ?><br />
        		<b>MsN: </b><?php print check_null($row[6], 1); ?><br />		
	    		<b>Categoria: </b> <?php print check_level($row[3]); ?><br />
	    		<b>Numero Post: </b><?php print check_num_topic($row[1]); ?><br />
			</div>
<?php
	}else
    	die(header('Location: index.php'));	
}else
	die(header('Location: index.php'));
?>
	</div>
</div>
<?php
footer();
?>
</body>
</html>
