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

defined('_JEXEC') or die('Restricted access');

class MstRecipients
{		

	public function updateRecipientInLists($userId, $isJUser){	
		$lists = $this->getListsUserIsMemberOf($userId, $isJUser);
		$this->recipientsUpdatedInLists($lists);
	}
	
	public function getListsUserIsMemberOf($userId, $isJUser){
		$model = MstFactory::getModel('user');
		$memberInfo = $model->getMemberInfo($userId, $isJUser);
		$usersLists = array();
		$lists = $memberInfo['lists'];
		for($i=0; $i<count($lists); $i++){
			$list = &$lists[$i];
			if(in_array($list->id, $usersLists) == false){
				$usersLists[] = $list->id; 
			}
		}
		$lists = $memberInfo['listGroups'];
		for($i=0; $i<count($lists); $i++){
			$list = &$lists[$i];
			if(in_array($list->id, $usersLists) == false){
				$usersLists[] = $list->id; 
			}
		}
		array_unique($usersLists);
		return $usersLists;
	}
	
	public function recipientsUpdatedInLists($listIds){	
		for($i=0;$i<count($listIds);$i++){
			$this->recipientsUpdated($listIds[$i]);
		}	
	}
	public function recipientsUpdated($listId){		
		$log = & MstFactory::getLogger();
		if($listId){
			if($listId != '' && $listId > 0){
				$cacheUtils = & MstFactory::getCacheUtils();		
				$cacheUtils->newRecipientState($listId);
				return;
			}
		}
		$log->error('Cannot update Cache State for Recipients, listId not set');
		$log->error('listId: ' . $listId);
	}
	
	public function getRecipients($listId){
		$log = & MstFactory::getLogger();	
		$cache = & MstFactory::getCache();
		$cacheUtils = & MstFactory::getCacheUtils();
		$version = $cacheUtils->getRecipientState($listId);	
		$countOnly = 0;
		$res =  $cache->call( array( $this, 'recipients' ), $listId, $version);
		return $res;	
	}
		
	public function getTotalRecipientsCount($listId){
		$recips = $this->getRecipients($listId);
		return count($recips);
	}
		
	function recipients($listId, $cacheVersion){
		$log = & MstFactory::getLogger();
		
		$toSelect = ' SELECT name, email';
				
		$query = ' SELECT name, email FROM ('
				. $toSelect 
				. ' FROM #__mailster_users'
				. ' WHERE id in ('
					. ' SELECT user_id'
					. ' FROM #__mailster_list_members'
					. ' WHERE list_id = \'' . $listId . '\''
					. ' AND is_joomla_user=\'0\' )'
				. ' UNION'
				. $toSelect
				. ' FROM #__users'
				. ' WHERE id in ('
					. ' SELECT user_id'
					. ' FROM #__mailster_list_members'
					. ' WHERE list_id = \'' . $listId . '\''
					. ' AND is_joomla_user=\'1\' )'
				. ' UNION'
				. $toSelect
				. ' FROM #__mailster_users'
				. ' WHERE id in ('
					. ' SELECT user_id'
					. ' FROM #__mailster_group_users'
					. ' WHERE group_id in ('
						. ' SELECT group_id'
						. ' FROM #__mailster_list_groups'
						. ' WHERE list_id = \'' . $listId . '\' )'
					. ' AND is_joomla_user=\'0\' )'
				. ' UNION'
				. $toSelect
				. ' FROM #__users'
				. ' WHERE id in ('
					. ' SELECT user_id'
					. ' FROM #__mailster_group_users'
					. ' WHERE group_id in ('
						. ' SELECT group_id'
						. ' FROM #__mailster_list_groups'
						. ' WHERE list_id = \'' . $listId . '\' )'
					. ' AND is_joomla_user=\'1\' )'					
			 	. ' ORDER BY name, email';			 	
		$query .= ') AS recips';		
		
		$db = & JFactory::getDBO();
		$db->setQuery( $query );		
		$result = $db->loadObjectList();
		return $result;	
	}
	
	
	function isRecipient($listId, $email){	
		$email = strtolower(trim($email));	
		$recipients = $this->getRecipients($listId);
		$recipCount = count($recipients);
		for($j = 0; $j < $recipCount; $j++) {
			$recipient = &$recipients[$j];
			$recipMail = strtolower(trim($recipient->email));
			if($recipMail === $email){
				return true;
			}
		}
		return false;		
	}	
	
	
	
}
?>
