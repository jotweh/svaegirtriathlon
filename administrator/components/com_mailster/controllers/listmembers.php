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
	 * Mailster Component List Members Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerListMembers extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{

			parent::__construct();			
			
		}
		
		function listMembers()
		{
			$list_id = JRequest::getInt('listID');
			$model = $this->getModel ( 'listmembers' );
			$view  = $this->getView  ( 'listmembers', 'html'  );
			$view->setModel( $model, true );  // true is for the default model;			
			$listGroupsModel = &$this->getModel ( 'listgroups' );			
			$listModel = &$this->getModel ( 'list' );			
			$groupModel = &$this->getModel ( 'groupusers' );	
			$view->setModel( $listGroupsModel );	
			$view->setModel( $listModel );		
			$view->setModel( $groupModel );	
			$mstApp = & MstFactory::getApplication();
			$recc = $mstApp->getRecC('com_mailster');
			
			$mstRecipients = &MstFactory::getRecipients();
			$repCount = $mstRecipients->getTotalRecipientsCount($list_id);		
			
			if($repCount > $recc){
				JError::raiseWarning( 19, JText::_( 'COM_MAILSTER_TOO_MUCH_RECIPIENTS' ));
			}
			$view->display();			
		}
		
		function newListGroups()
		{
			$app = JFactory::getApplication();			
			$recipCountChanged = false;
			$newGroupIds = JRequest::getVar('groups_selectRight', array());
			$groupCount = count($newGroupIds);
			$list_id = JRequest::getInt('listID');
			$mstApp = & MstFactory::getApplication();
			$recc = $mstApp->getRecC('com_mailster');
			$mstRecipients = & MstFactory::getRecipients();
			$oldRecipCount = $mstRecipients->getTotalRecipientsCount($list_id); // get old, cached number
			for($i=0; $i < $groupCount; $i++)
			{
				$group_id = $newGroupIds[$i];
				$groupModel = $this->getModel('groupusers');
				$groupUserCount = count($groupModel->getData($group_id));
				if($oldRecipCount + $groupUserCount <=  $mstApp->getRecC('com_mailster')){
					$recipCountChanged = true;
					$model = $this->getModel('listgroups');
					$listGroup = new stdClass();
					$listGroup->group_id	= $group_id;
					$listGroup->list_id		= $list_id;			
					
					$returnid = $model->store($listGroup);			
					$oldRecipCount = $oldRecipCount + $groupUserCount; 
					$app->enqueueMessage( JText::_( 'COM_MAILSTER_ADDED_GROUP' ) . ' ( ID=' . $group_id . ' )');
				}else{
					$app->enqueueMessage( JText::_( 'COM_MAILSTER_COULD_NOT_ADD_GROUP_X' ) . ' (ID = ' . $group_id . ') -  ' . JText::_( 'COM_MAILSTER_TOO_MUCH_RECIPIENTS' )  , 'notice');
				}		
				
			}
			if($recipCountChanged){
				$mstRecipients->recipientsUpdated($list_id);  // update cache state
			}
			$this->setRedirect( 'index.php?option=com_mailster&controller=listmembers&task=listmembers&listID='.$list_id );			
		}
				
		function newListMembers()
		{			
			$log = & MstFactory::getLogger();
			$app = JFactory::getApplication();			
			$list_id = JRequest::getInt('listID');
			$pairlist_precodes = JRequest::getString('pairlist_precodes', '');
			$plist_pcodes = explode(';', $pairlist_precodes);
			$newUserCr = 0;		
			$mstApp = & MstFactory::getApplication();	
			$recc = $mstApp->getRecC('com_mailster');
			$mstRecipients = &MstFactory::getRecipients();
			$repCount = $mstRecipients->getTotalRecipientsCount($list_id);		
			for($i=0; $i < count($plist_pcodes); $i++){
				
				if($plist_pcodes[$i] == 'users')
				{
					$newUserIds = JRequest::getVar('users_selectRight', array());
					$userCount = count($newUserIds);
					for($j=0; $j < $userCount; $j++)
					{
						$userIdsStr	= $newUserIds[$j];
						if($userIdsStr != '')
						{
							if($repCount < $recc)
							{
								$userIdsArr = explode(';', $userIdsStr);
								$user_id = $userIdsArr[0];
								$is_joomla_user = $userIdsArr[1];
								$model = $this->getModel('listmembers');
								$mstRecipients->recipientsUpdated($list_id); // update cache state
								$listMember = new stdClass();
								$listMember->user_id		= $user_id;
								$listMember->list_id		= $list_id;					
								$listMember->is_joomla_user	= $is_joomla_user;									
								$success = $model->store($listMember);
								if($success){
									$newUserCr++;
									$repCount++;
									$listMember = new stdClass();
									$model = $this->getModel('listmembers');
								}
							}else{	
								$app->enqueueMessage( JText::_( 'COM_MAILSTER_COULD_NOT_ADD_MORE_USERS' ) . ' - ' . JText::_( 'COM_MAILSTER_TOO_MUCH_RECIPIENTS' ) . ' )' , 'notice');								
								break;
							}		
						}
					}
				}
			}	
					
			$app->enqueueMessage( $newUserCr . ' ' . JText::_( 'COM_MAILSTER_X_USERS_ADDED' ));								
			$this->listMembers();			
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
			
			$listmembers = & JTable::getInstance('mailster_list_members', '');
			$listmembers->bind(JRequest::get('post'));

			$this->setRedirect( 'index.php?option=com_mailster&view=listmembers' );
		}
		
		function removeUsers()
		{
			$log = & MstFactory::getLogger();
			$app = JFactory::getApplication();			
			$list_id = JRequest::getInt('listID');
			$user_ids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
			$log->debug('Remove users called with ' . count($user_ids) . ' user ids');
			
			$is_joomla_user_flags_str = JRequest::getString('is_j_us_str', "");
			$is_joomla_user_flags = explode(";", $is_joomla_user_flags_str);
			$log->debug('is joomla user flags str: ' . $is_joomla_user_flags_str . ' -> ' . count($is_joomla_user_flags) . ' users');
			
			$total = count( $user_ids );
			$mstRecipients = & MstFactory::getRecipients(); 
			$mstRecipients->recipientsUpdated($list_id); // update cache state

			if (!is_array( $user_ids ) || count( $user_ids ) < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}

			$model = $this->getModel('listmembers');
			if(!$model->delete($list_id, $user_ids, $is_joomla_user_flags)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}

			$msg = $total.' '.JText::_( 'COM_MAILSTER_MEMBERS_REMOVED_FROM_LIST');
			$app->enqueueMessage($msg );

			$this->listMembers();
		}	

		function removeGroups()
		{
			$app = JFactory::getApplication();			
			$list_id = JRequest::getInt('listID');			
			$group_ids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
			$total = count( $group_ids );
			$mstRecipients = & MstFactory::getRecipients(); 
			$mstRecipients->recipientsUpdated($list_id); // update cache state

			if (!is_array( $group_ids ) || count( $group_ids ) < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}

			$model = $this->getModel('listgroups');
			if(!$model->delete($list_id, $group_ids)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}

			$msg = $total.' '.JText::_( 'COM_MAILSTER_GROUPS_REMOVED_FROM_LIST');
			$app->enqueueMessage($msg );
			
			$this->listMembers();
		
		}
		
	}
?>
