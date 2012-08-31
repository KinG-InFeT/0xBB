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
 * karma.php
 ***************************************************************************/
include "kernel.php";

list ($username, $password) = get_data ();

if (!login ($username, $password))
		die ('<script>window.location="login.php";</script>');
	
$referer = "viewtopic.php?id=".$_POST['topic_id'];
$user_id = (int) $_POST['user_id'];
$vote    = ($_POST['vote'] > 0) ? "+1" : "-1";

if(user_id($user_id) == $username) {
	print "<script>window.location=\"". $referer ."\";</script>";
}else{

	if(isset($user_id) && isset($vote)) {
		$row = mysql_fetch_row (mysql_query ("SELECT vote FROM ". __PREFIX__ ."karma WHERE vote_user_id = '". $user_id ."'"));
		
		if($vote > 0)
			$vote_final = $row[0] + 1;
		else
			$vote_final = $row[0] - 1;
			
		$sql = "UPDATE `". __PREFIX__ ."karma` SET vote = '". $vote_final ."' WHERE vote_user_id = '". $user_id ."'";
		
		mysql_query($sql) or _err(mysql_error());
		
		print "<script>window.location=\"". $referer ."\";</script>";
	}else{
		print "<script>window.location=\"index.php\";</script>";
	}
}
?>
