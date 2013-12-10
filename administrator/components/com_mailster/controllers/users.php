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

	jimport('joomla.application.component.controller');

	/**
	 * Mailster Component User Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerUsers extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{
			parent::__construct();
		}
		
		function newUsers()
		{
			$mailUtils = &MstFactory::getMailUtils();
			$addedCr = 0;
			$userCount = JRequest::getInt('userCount');
			for($i=0; $i < $userCount; $i++)
			{
				$name 	= JRequest::getString('name-' . ($i+1));
				$email 	= JRequest::getString('email-' . ($i+1));
				if($name != '' || $email != ''){
					if($email != '')
					{
						if($mailUtils->isValidEmail($email)){
							$model = $this->getModel('user');
							$user = new stdClass();
							$user->id						= 0;
							$user->name						= $name;
							$user->email					= $email;
							$returnid = $model->store($user);
							if($returnid > 0){
								$addedCr++;
							}
						}else{
							JError::raiseWarning( 100,  JText::sprintf( 'COM_MAILSTER_INVALID_EMAIL_ADDRESS_X', $email) );
						}
					}else{
						JError::raiseWarning( 100, JText::_( 'COM_MAILSTER_NO_EMAIL_PROVIDED_USER_CAN_NOT_BE_SAVED' ) );
					}
				}
			}
			$msg = JText::sprintf( 'COM_MAILSTER_SAVED_X_USERS', $addedCr);
			$this->setRedirect( 'index.php?option=com_mailster&view=users', $msg );			
		}
				
		/**
		 * logic for cancel an action
		 *
		 * @access public
		 * @return void
		 */
		function cancel()
		{
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			
			$group = & JTable::getInstance('mailster_users', '');
			$group->bind(JRequest::get('post'));
			$this->setRedirect( 'index.php?option=com_mailster&view=users' );
		}

		/**
		 * logic to create the new user screen
		 *
		 * @access public
		 * @return void
		 */
		function add( )
		{
			$this->setRedirect( 'index.php?option=com_mailster&view=user' );
		}

		/**
		 * logic to create the edit user screen
		 *
		 * @access public
		 * @return void
		 */
		function edit( )
		{
			JRequest::setVar( 'view', 'user' );
			JRequest::setVar( 'hidemainmenu', 1 );

			$model 	= $this->getModel('user');
			$task 	= JRequest::getVar('task');

			$user	=& JFactory::getUser();
		
			parent::display();
		}
		
		
		function apply()
		{
			$this->save();
		}

		/**
		 * logic to save a user
		 *
		 * @access public
		 * @return void
		 */
		function save()
		{
			
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			$log = &MstFactory::getLogger();
			$cbUtils = &MstFactory::getCBUtils();
			$mstRecipients = & MstFactory::getRecipients(); 
			$subscrUtils = &MstFactory::getSubscribeUtils();
			
			$task		= JRequest::getVar('task');
			$userId 	= JRequest::getInt( 'id', 0);
			$isJUser 	= JRequest::getInt( 'isjuser', 0);

			$post = JRequest::get( 'post' );
			$post['datdescription'] = JRequest::getVar( 'datdescription', '', 'post','string', JREQUEST_ALLOWRAW );
			$post['datdescription']	= str_replace( '<br>', '<br />', $post['datdescription'] );
			$model = $this->getModel('user');
			
			if($isJUser == 0){ // only save user data for Mailster user
				$log->debug('Save Mailster user data for user ' . $userId);	
				if ($returnid = $model->store($post)) {		
					$userId = $returnid;		
					$msg	= JText::_( 'COM_MAILSTER_USER_SAVED');
					$cache = &JFactory::getCache('com_mailster');
					$cache->clean();
					$mstRecipients->updateRecipientInLists($userId, $isJUser);	// update cache states (to correct name/email)
				} else {
					$userId = JRequest::getInt('id');
					$msg 	= '';
				}
			}
			
			
			$log->debug('Save list/group membership data for user ' . $userId);	
			
			$groupMemberInfo = $model->getGroupMemberInfo($userId, $isJUser);
			$listMemberInfo = $model->getListMemberInfo($userId, $isJUser);
			
			for($i=0; $i<count( $listMemberInfo ); $i++){
				$lInfo = &$listMemberInfo[$i];
				$listId = $lInfo->id;
				$isMemberBefore = $lInfo->is_list_member;
				$isMemberAfter = JRequest::getInt( 'is_list_member'.$listId, -1);
				if($isMemberAfter >= 0 && $isMemberBefore != $isMemberAfter){
					$log->debug('Changed list member for list ' . $listId);	
					if($isMemberAfter == 1){
						$log->debug('SUBSCRIBE for list ' . $listId);
						$subscrUtils->subscribeUserId($userId, $isJUser, $listId); // subscribe (incl. cache state update)
					}else{
						$log->debug('UNSUBSCRIBE for list ' . $listId);
						$subscrUtils->unsubscribeUserId($userId, $isJUser, $listId); // unsubscribe (incl. cache state update)
					}
				}					
			}
			
			$groupUsersModel = MstFactory::getModel('groupusers');
			for($i=0; $i<count( $groupMemberInfo ); $i++){
				$gInfo = &$groupMemberInfo[$i];
				$groupId = $gInfo->id;
				
				$isMemberBefore = $gInfo->is_group_member;
				$isMemberAfter = JRequest::getInt( 'is_group_member'.$groupId, -1);
				if($isMemberAfter >= 0 && $isMemberBefore != $isMemberAfter){
											
					$log->debug('Changed group member for group ' . $listId);	
					$groupUser = new stdClass();
					$groupUser->user_id			= $userId;
					$groupUser->group_id		= $groupId;					
					$groupUser->is_joomla_user	= $isJUser;		
					
					if($isMemberAfter == 1){
						$log->debug('ADD to group ' . $groupId);					
						$success = $groupUsersModel->store($groupUser);
						$cbUtils->addGroupInCBUser($userId, $groupId);
					}else{
						$log->debug('REMOVE from group ' . $groupId);
						$success = $groupUsersModel->delete($groupId, array($userId), array($isJUser));
						$cbUtils->removeGroupFromCBUser($userId, $groupId);
					}
					
															
					$log->debug('Operation successful: '.($success?'yes':'no'));
					
					$listsToUpdRecips = $groupUsersModel->getListsWithGroup($groupId);
					for($k=0; $k < count($listsToUpdRecips); $k++){			
						$mstRecipients->recipientsUpdated($listsToUpdRecips[$k]->id); // update cache state
					}	
				}					
			}
			
			switch ($task)
			{
				case 'apply' :
					$link = 'index.php?option=com_mailster&view=user&task=edit&id='.$userId.'&isjuser='.$isJUser;
					break;
			
				default :
					$link = 'index.php?option=com_mailster&view=users';
				break;
			}
			$this->setRedirect( $link, $msg );
		}

		/**
		 * logic to remove users
		 *
		 * @access public
		 * @return void
		 */
		function removeUsers()
		{
			$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

			$total = count( $cid );

			if (!is_array( $cid ) || count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}

			$model = $this->getModel('users');
			if(!$model->delete($cid)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}

			$msg = $total.' '.JText::_( 'COM_MAILSTER_USER_DELETED');

			$cache = &JFactory::getCache('com_mailster');
			$cache->clean();

			$this->setRedirect( 'index.php?option=com_mailster&view=users', $msg );
		}
	}
?>
