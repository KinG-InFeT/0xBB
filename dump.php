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
 * dump.php
 ***************************************************************************/
set_time_limit(0);
include "kernel.php";
list ($username, $password) = get_data ();

if (!login ($username, $password)) 
	die ("<br /><br /><br /><div class=\"error_msg\" align=\"center\">\nACCESS DENIED\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a></div>");

if (!(level($username) == 'admin'))
	die ("<br /><br /><br /><div class=\"error_msg\" align=\"center\">\nACCESS DENIED\n<br /><br />\n<a href=\"index.php\">Torna alla Index</a></div>");


if(isset($_REQUEST['esegui_backup'])) {

	$mysql_host     = $db_host;
	$mysql_database = $db_name;
	$mysql_username = $db_user;
	$mysql_password = $db_pass;
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="'.$mysql_host."_".$mysql_database."_".date('YmdHis').'.sql"');
	_mysqldump($mysql_database);
}

function _mysqldump($mysql_database)
{
	$sql    = "show tables;";
	$result = mysql_query($sql);
	if( $result)
	{
		while( $row = mysql_fetch_row($result))
		{
			_mysqldump_table_structure($row[0]);

			if( isset($_REQUEST['sql_table_data']))
			{
				_mysqldump_table_data($row[0]);
			}
		}
	}
	else
	{
		echo "/* no tables in $mysql_database */\n";
	}
	mysql_free_result($result);
}

function _mysqldump_table_structure($table)
{
	echo "/* Table structure for table `$table` */\n";
	if( isset($_REQUEST['sql_drop_table']))
	{
		echo "DROP TABLE IF EXISTS `$table`;\n\n";
	}
	if( isset($_REQUEST['sql_create_table']))
	{

		$sql="show create table `$table`; ";
		$result=mysql_query($sql);
		if( $result)
		{
			if($row = mysql_fetch_assoc($result))
			{
				echo $row['Create Table'].";\n\n";
			}
		}
		mysql_free_result($result);
	}
}

function _mysqldump_table_data($table)
{

	$sql    = "select * from `$table`;";
	$result = mysql_query($sql);
	if( $result)
	{
		$num_rows   = mysql_num_rows($result);
		$num_fields = mysql_num_fields($result);

		if( $num_rows > 0)
		{
			echo "/* dumping data for table `$table` */\n";

			$field_type = array();
			$i = 0;
			while( $i < $num_fields)
			{
				$meta = mysql_fetch_field($result, $i);
				array_push($field_type, $meta->type);
				$i++;
			}

			echo "INSERT INTO `$table` VALUES\n";
			$index = 0;
			while( $row = mysql_fetch_row($result))
			{
				echo "(";
				for( $i = 0; $i < $num_fields; $i++)
				{
					if( is_null( $row[$i]) )
						echo "null";
					else
					{
						switch( $field_type[$i])
						{
							case 'int':
								echo $row[$i];
							break;
							
							case 'string':
							
							case 'blob' :
							
							default:
								echo "'".mysql_real_escape_string($row[$i])."'";

						}
					}
					if( $i < $num_fields - 1)
						echo ",";
				}
				
				echo ")";

				if( $index < $num_rows - 1)
					echo ",";
				else
					echo ";";
					
				echo "\n";

				$index++;
			}
		}
	}
	mysql_free_result($result);
	echo "\n";
}
?>
