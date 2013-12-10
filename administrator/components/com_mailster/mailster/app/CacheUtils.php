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
	
	class MstCacheUtils
	{
		
		public static function getRecipientState($listId){	
			$db = & JFactory::getDBO();
			$query = 'SELECT cstate FROM #__mailster_lists WHERE id =\'' . $listId . '\'';
			$db->setQuery( $query );
			$state = $db->loadResult();
			return $state;
		}		
		
		public static function newRecipientState($listId){
			$oldState = self::getRecipientState($listId);			
			$newState = $oldState + 1;
			self::saveRecipientState($listId, $newState);
		}
				
		public static function saveRecipientState($listId, $state){
			$log = & MstFactory::getLogger();
			$db = & JFactory::getDBO();
			$query = 'UPDATE #__mailster_lists SET cstate=\'' . $state . '\' WHERE id =\'' . $listId . '\'';
			$db->setQuery( $query );
			$result = $db->query(); // update cache version/state
			if(!$result){
				$log->error('Updating of cache version for list ' . $listId . ' failed: ' . $db->getErrorNum() . ', Message: ' . $db->getErrorMsg());
			}
		}
	}

?>
