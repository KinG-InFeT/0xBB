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
 * impaginazione.class.php                                                        
 ***************************************************************************/
 
class Pagination  {

	function paginazione ($numHits, $limit, $page) {
		$numHits  = (int) $numHits; 
		$limit    = (int) $limit; 
		$page     = (int) $page; 
		$numPages = @ceil($numHits / $limit);
		
		if(($page > $numPages) && ($numPages > 0))
			$page = $numPages;
		
		if($page < 1)
			$page = 1;
		
		$offset = ( $page - 1 ) * $limit; 
		
		$ret = array(); 
		
		$ret['offset'] 	 = $offset; 
		$ret['limit'] 	 = $limit;
		$ret['numPages'] = $numPages; 
		$ret['page']	 = $page; 
		
		return $ret; 
    }
}
?>
