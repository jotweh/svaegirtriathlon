<?php

	/**
	 * @package Joomla
	 * @subpackage Mailster
	 * @copyright (C) 2010 Holger Brandt IT Solutions
	 * @license GNU/GPL, see license.txt
	 * Mailster is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License 2
	 * as published by the Free Software Foundation.
	 * 
	 * Mailster is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * You should have received a copy of the GNU General Public License
	 * along with Mailster; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
	 * or see http://www.gnu.org/licenses/.
	 */

	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	class MstAuthorization
	{
		
		public static function userGroupAuthorizedInherited($gid){
			
			$user =& JFactory::getUser();
	
			$option = 'com_gcontact';
			$parent = false;
			$entity = 'users';					
			$ugid = $user->gid;		
			$gidOk = array();
					
			if(($gid == 29) && ($ugid == 0)){
				return true;
			}
		
			if($gid == 29){
				if($ugid == 29){
					return true;
				}
				$parent = true;
			}
			if($gid == 18 || ($parent == true)){
				if($ugid == 18){
					return true;
				}
				$parent = true;
			}
			if($gid == 19 || ($parent == true)){
				if($ugid == 19){
					return true;
				}
				$parent = true;
			}
			if($gid == 20 || ($parent == true)){
				if($ugid == 20){
					return true;
				}
				$parent = true;
			}
			if($gid == 21 || ($parent == true)){
				if($ugid == 21){
					return true;
				}
				$parent = true;
			}
			if($gid == 30 || ($parent == true)){
				if($ugid == 30){
					return true;
				}
				$parent = true;
			}
			if($gid == 23 || ($parent == true)){
				if($ugid == 23){
					return true;
				}
				$parent = true;
			}
			if($gid == 24 || ($parent == true)){
				if($ugid == 24){
					return true;
				}
				$parent = true;
			}	
			if($gid == 25 || ($parent == true)){
				if($ugid == 25){
					return true;
				}
				$parent = true;
			}		
			
			return false;
		}
		
		public static function printUnauthorizedMsg(){
			echo '<span class="mailster_unauthorized" style="color:red;">'.JText::_( 'COM_MAILSTER_NOT_AUTHORIZED').'</span>';
		}
		
	}

?>
