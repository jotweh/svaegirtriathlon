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
	 * HTML View class for the List Members View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewListMembers extends JView
	{
		function display($tpl = null)
		{				
			$mstConfig = &MstFactory::getConfig();
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();
			$list_id = JRequest::getInt('listID', 0);
			
			// Get data from the models
			$listMemberModel = &$this->getModel();	
			$listGroupModel = &$this->getModel('listgroups');
			$listModel = &$this->getModel('list');
							
			$rows = &$listMemberModel->getData($list_id);
			$nonMembers = &$listMemberModel->getNonMemberData($list_id);
			
			$mstRecipients = &MstFactory::getRecipients();
			$recipients = $mstRecipients->getRecipients($list_id);
			
			$listModel->setId($list_id);
			$listData = $listModel->getData(); 
			
			$groupRows = &$listGroupModel->getData($list_id);
			$nonMemberGroups = &$listGroupModel->getNonMemberGroupsData($list_id);
			$userEntries = array();
			for($i=0; $i < count($nonMembers); $i++)
			{
				$nonMember = $nonMembers[$i];
				$entry = new stdClass();
				$entry->value = $nonMember->id.';'.$nonMember->is_joomla_user;
				$entry->text  = $nonMember->name;
				if($mstConfig->showUserDescription()){ 
					if( (!is_null($nonMember->notes)) && (strlen(trim($nonMember->notes)) > 0) ){
						$entry->text  = $entry->text . ' - ' . $nonMember->notes;
					}
				} 
				$entry->text  = $entry->text.' ('.$nonMember->email.')'; 
				$userEntries[$i] = $entry;
			}
			$groupsEntries = array();
			for($i=0; $i < count($nonMemberGroups); $i++)
			{
				$nonMemberGroup = $nonMemberGroups[$i];
				$entry = new stdClass();
				$entry->value = $nonMemberGroup->id;
				$entry->text  = $nonMemberGroup->name; 
				$groupsEntries[$i] = $entry;
			}
			
			//Create Toolbar
			JToolBarHelper::title( JText::_( 'COM_MAILSTER_MAILING_LIST_MEMBERS_GROUP_X' ) . ' (' . $listData->name . ')', 'listmembers-mailster' );
			JToolBarHelper::custom( 'removeGroups', 'removeGroups-mailster.png', 'removeGroups-mailster.png', JText::_( 'COM_MAILSTER_REMOVE_GROUPS' ), false, false );
			JToolBarHelper::custom( 'removeUsers', 'removeUsers-mailster.png', 'removeUsers-mailster.png', JText::_( 'COM_MAILSTER_REMOVE_USERS' ), false, false );
			
    		
			$mstUtils = & MstFactory::getUtils();
			$mstUtils->addSubmenu('listmembers');
			
			$this->assignRef('rows'      	, $rows);
			$this->assignRef('groupRows'   	, $groupRows);	
			$this->assignRef('user'			, $user);
			$this->assignRef('userEntries'	, $userEntries);
			$this->assignRef('groupsEntries', $groupsEntries);
			$this->assignRef('recipients'	, $recipients);			
			$this->assignRef('list_id'		, $list_id);			
	 
			parent::display($tpl);
		}


	}
?>
