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
 * stats.php                                                        
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
<h2 align="center">Statistiche del Forum</h2>
<br />
	<table cellpadding="10" cellspacing="1" align="center" border="1">
		<tr><td>Versione 0xBB: <b><?php print VERSION; ?></b></td></tr>	
		<tr><td><br /></td></tr>
		<tr><td>Numero Topic Totali: <?php stats(1); ?></td></tr>
		<tr><td>Numero Iscritti: <?php stats(2); ?></td></tr>
		<tr><td>Numero Messaggi: <?php stats(4); ?></td></tr>
		<tr><td>Visite Ricevute: <?php visite(2); ?></td></tr>
	</table>
</div>
<?php
$top = NULL;
footer ($top);
?>
</body>
</html>
