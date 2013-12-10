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

	jimport( 'joomla.application.component.view');

	/**
	 * HTML View class for the User View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewUser extends JView
	{
		function display($tpl = null)
		{	
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();
			$userId 	= JRequest::getInt( 'id',  0 );
			$isJUser 	= JRequest::getInt( 'isjuser', 0);
			
			//build toolbar		
			if ( $userId > 0) {
				JToolBarHelper::title( JText::_( 'COM_MAILSTER_EDIT_USER' ), 'user-mailster' );
			} else {
				JToolBarHelper::title( JText::_( 'COM_MAILSTER_ADD_USER' ), 'user-mailster' );
			}
			
			//Create Submenu
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();	
			
			// Get data from the model
			$model = &$this->getModel();
			if($isJUser == 0){
				$model->setId($userId);
				$row = &$this->get('Data');
			}else{
				$row = $model->getJUserData($userId);	
			}
			$memberInfo = $model->getMemberInfo($userId, $isJUser);
			$groupMemberInfo = $model->getGroupMemberInfo($userId, $isJUser);
			$listMemberInfo = $model->getListMemberInfo($userId, $isJUser);
							
			$this->assignRef('row'      		, $row);
			$this->assignRef('isJUser'      	, $isJUser);
			$this->assignRef('memberInfo'  		, $memberInfo);
			$this->assignRef('listMemberInfo'  , $listMemberInfo);
			$this->assignRef('groupMemberInfo'  , $groupMemberInfo);
			$this->assignRef('user'				, $user);
	 
			parent::display($tpl);
		}


	}
?>
