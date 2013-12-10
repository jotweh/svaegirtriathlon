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
	 * Mailster Component Group Users Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerGroupUsers extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{
			// execute parent's constructor
			parent::__construct();			
		}
		
		function groupUsers()
		{
			
			$model = $this->getModel ( 'groupusers' );
			$view  = $this->getView  ( 'groupusers', 'html'  );
			$view->setModel( $model, true );  // true is for the default model;	
			$listsModel = &$this->getModel ( 'lists' );		
			$view->setModel( $listsModel );				
			$view->display();
		}
		
		function newGroupUsers()
		{	
			$log = &MstFactory::getLogger();
			$cbUtils = &MstFactory::getCBUtils();
			
			$usersAdded = false;
			$newUserCr = 0;
			$app = JFactory::getApplication();	
			$pairlist_precodes = JRequest::getString('pairlist_precodes', '');
			$plist_pcodes = explode(';', $pairlist_precodes);
			for($i=0; $i < count($plist_pcodes); $i++)
			{
				if($plist_pcodes[$i] == 'users')
				{
					$newUserIds = JRequest::getVar('users_selectRight', array());
					$userCount = count($newUserIds);
					$group_id = JRequest::getInt('groupID');
					for($j=0; $j < $userCount; $j++)
					{
						$userIdsStr	= $newUserIds[$j];
						if($userIdsStr != '')
						{							
							$userIdsArr = explode(';', $userIdsStr);
							$user_id = $userIdsArr[0];
							$is_joomla_user = $userIdsArr[1];
							$model = $this->getModel('groupusers');
							$groupUser = new stdClass();
							$groupUser->user_id			= $user_id;
							$groupUser->group_id		= $group_id;					
							$groupUser->is_joomla_user	= $is_joomla_user;							
							$success = $model->store($groupUser);
							$usersAdded = true;	
							$newUserCr++;
														
							$cbUtils->addGroupInCBUser($user_id, $group_id);
							
						}
					}
				}
			}
			if($usersAdded){	
				$mstRecipients = &MstFactory::getRecipients();			
				$listsToUpdRecips = $model->getListsWithGroup($group_id);
				for($k=0; $k < count($listsToUpdRecips); $k++)
				{		
					$currList = &$listsToUpdRecips[$k];	
					$log = &MstFactory::getLogger();
					$log->debug(' updating #'. $k . ' list: ' . $currList->id);										
					$mstRecipients->recipientsUpdated($currList->id); // update cache state
				}		
			}
			$app->enqueueMessage( $newUserCr . ' ' . JText::_( 'COM_MAILSTER_X_USERS_ADDED' ));		
			$this->groupUsers();						
			//$this->setRedirect( 'index.php?option=com_mailster&controller=groupusers&task=groupusers&groupID='.$group_id );			
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
			
			$group = & JTable::getInstance('mailster_group_users', '');
			$group->bind(JRequest::get('post'));

			$this->setRedirect( 'index.php?option=com_mailster&view=groupusers' );
		}
		
					
	
		
		/**
		 * logic to remove group users
		 *
		 * @access public
		 * @return void
		 */
		function removeUsers()
		{
			
			$log = &MstFactory::getLogger();
			$cbUtils = &MstFactory::getCBUtils();
			$app = JFactory::getApplication();	
				
			$group_id = JRequest::getInt('groupID');
			$user_ids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
			$is_joomla_user_flags_str = JRequest::getString('is_j_us_str', "");
			$is_joomla_user_flags = explode(";", $is_joomla_user_flags_str);			
			$total = count( $user_ids );

			if (!is_array( $user_ids ) || count( $user_ids ) < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}

			$model = $this->getModel('groupusers');
			if(!$model->delete($group_id, $user_ids, $is_joomla_user_flags)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}	
					
			for($i=0; $i<count($user_ids); $i++){
				$cbUtils->removeGroupFromCBUser($user_ids[$i], $group_id);
			}	
			
			$mstRecipients = &MstFactory::getRecipients();
			$listsToUpdRecips = $model->getListsWithGroup($group_id);
			for($k=0; $k < count($listsToUpdRecips); $k++)
			{		
				$currList = &$listsToUpdRecips[$k];
				$log = &MstFactory::getLogger();
				$log->debug(' updating #'. $k . ' list: ' . $currList->id);								
				$mstRecipients->recipientsUpdated($currList->id); // update cache state
			}	

			$app->enqueueMessage($total.' '.JText::_( 'COM_MAILSTER_USERS_REMOVED_FROM_GROUP') );	

			$this->groupUsers();
		}		
	}
?>
