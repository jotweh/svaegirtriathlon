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
		
class MstSubscribeUtils
{
	
	public function getUnsubscribeURL($mail){
		if(!empty($mail->id) && ($mail->id > 0)){
			jimport( 'joomla.environment.uri' );
			$uri = JURI::root() . 'index.php';
			$jUri =& JURI::getInstance( $uri );
			$hKey = $mail->hashkey;
			$salt = rand();
			$saltedKey = $hKey . $salt;
			$query = 'option=com_mailster&view=unsubscribe&m=' . ($mail->id) . '&h=' . sha1($saltedKey) . '&s=' . $salt;
			$jUri->setQuery($query);
			return $jUri->toString();
		}
		return false;
	}

	public function subscribeUser($name, $email, $listId)
	{
		$name = trim($name);
		$email = trim($email);
		$success = false;
		$log = & MstFactory::getLogger();
		$mstApp = & MstFactory::getApplication();
		$mstRecipients = & MstFactory::getRecipients();
		$cr = $mstRecipients->getTotalRecipientsCount($listId);
		if($cr < $mstApp->getRecC('com_mailster'))
		{
			$isJoomlaUser = '0';
			$db = & JFactory::getDBO();
			$query = ' SELECT *'
					. ' FROM #__users'
					. ' WHERE email =\'' . $db->getEscaped( $email ) . '\'';
			$db->setQuery( $query );
			$users = $db->loadObjectList();
			if(count($users) > 0)
			{
				$userId = $users[0]->id;
				$isJoomlaUser = '1';
			}else{
				$query = ' SELECT *'
						. ' FROM #__mailster_users'
						. ' WHERE email =\'' . $db->getEscaped( $email ) . '\'';
				$db->setQuery( $query );
				$users = $db->loadObjectList();
				if(count($users) > 0)
				{
					$userId = $users[0]->id;
					$isJoomlaUser = '0';
				}else{
					$query = ' INSERT INTO'
						. ' #__mailster_users'
						. ' (id, name, email)'
						. ' VALUES ('
						. ' NULL, \'' . $db->getEscaped( $name ) . '\', \'' . $db->getEscaped( $email ) . '\')';
					$db->setQuery( $query );
					$db->query();
					$userId = $db->insertid();
					$isJoomlaUser = '0';
				}
			}
			$success = $this->subscribeUserId($userId, $isJoomlaUser, $listId);
		}
		return $success;
	}
	
	
	public function subscribeUserId($userId, $isJoomlaUser, $listId)
	{
		$success = false;
		$db = & JFactory::getDBO();
		$query = ' SELECT *'
					. ' FROM #__mailster_list_members'
					. ' WHERE list_id =\'' . $db->getEscaped( $listId ) . '\''
					. ' AND user_id =\'' . $db->getEscaped( $userId ) . '\''
					. ' AND is_joomla_user =\'' . $isJoomlaUser . '\'';
		$db->setQuery( $query );
		$members = $db->loadObjectList();
		if(count($members) > 0)
		{
			// no need to insert, the user is already in the list
		}else{
			$query = ' INSERT INTO'
				. ' #__mailster_list_members'
				. ' (list_id, user_id, is_joomla_user)'
				. ' VALUES ('
				. ' \'' . $db->getEscaped( $listId ) . '\', \'' . $db->getEscaped( $userId ) . '\', \'' . $isJoomlaUser . '\')';
			$db->setQuery( $query );			
			$db->query();
		}
		$mstRecipients = & MstFactory::getRecipients();				
		$mstRecipients->recipientsUpdated($listId);  // update cache state
		$success = true;
		return $success;
	}
	
	public function unsubscribeUser($email, $listId)
	{
		$email = trim($email);
		$success = false;
		for($i=0; $i < 2; $i++)
		{
			$userFound = false;
			$isJoomlaUser = '0';
			if($i==0){
				$db = & JFactory::getDBO();
				$query = ' SELECT *'
						. ' FROM #__users'
						. ' WHERE email =\'' . $db->getEscaped( $email ) . '\'';
				$db->setQuery( $query );
				$users = $db->loadObjectList();
				if(count($users) > 0)
				{
					$userId = $users[0]->id;
					$isJoomlaUser = '1';
					$userFound = true;
				}
			}
			if($i==1){
				$query = ' SELECT *'
						. ' FROM #__mailster_users'
						. ' WHERE email =\'' . $db->getEscaped( $email ) . '\'';
				$db->setQuery( $query );
				$users = $db->loadObjectList();
				if(count($users) > 0)
				{
					$userId = $users[0]->id;
					$isJoomlaUser = '0';
					$userFound = true;
				}
			}
			if($userFound == true)
			{
				$success = $this->unsubscribeUserId($userId, $isJoomlaUser, $listId);
			}	
		}
		return $success;
	}	
	
	public function unsubscribeUserId($userId, $isJoomlaUser, $listId)
	{
		$success = false;
		$db = & JFactory::getDBO();
		$query = ' DELETE '
				. ' FROM #__mailster_group_users'
				. ' WHERE user_id =\'' . $userId . '\''
				. ' AND is_joomla_user =\'' . $isJoomlaUser . '\''
				. ' AND group_id IN ('
				. ' 	SELECT group_id'
				. ' 	FROM #__mailster_list_groups'
				. '		WHERE list_id = \'' . $listId . '\')';
		$db->setQuery( $query );
		$result = $db->query();
		$affRows = $db->getAffectedRows();	
		if($affRows > 0){
			$success = true;
		}
		$query = ' DELETE '
				. ' FROM #__mailster_list_members'
				. ' WHERE user_id =\'' . $userId . '\''
				. ' AND is_joomla_user =\'' . $isJoomlaUser . '\''
				. ' AND list_id = \'' . $listId . '\'';
		$db->setQuery( $query );
		$result = $db->query();
		$affRows = $db->getAffectedRows();
		if($affRows > 0){
			$success = true;
		}
		$mstRecipients = & MstFactory::getRecipients();				
		$mstRecipients->recipientsUpdated($listId);  // update cache state
		return $success;
	}

	public function isUserLoggedIn()
	{
		$user =& JFactory::getUser();
		if($user->id){
			return true;
		}
		else{
			return false;
		}
	} 
	
	public function getMailingLists2RegisterAt($onlyPublicRegistration)
	{		
		$publicRegistration = $onlyPublicRegistration ? '1' : '0'; 
		$db = & JFactory::getDBO();
		$query = ' SELECT *'
				. ' FROM #__mailster_lists'
				. ' WHERE allow_registration =\'1\'';
		if($onlyPublicRegistration == true){	
			$query = $query	. ' AND public_registration =\'' . $db->getEscaped( $publicRegistration ) . '\'';
		}
		$db->setQuery( $query );
		$lists = $db->loadObjectList();
		return $lists;
	}	
	
}

?>
