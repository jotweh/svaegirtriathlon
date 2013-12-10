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
	 * HTML View class for the Group Users View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewGroupUsers extends JView
	{
		function display($tpl = null)
		{	
			$mstConfig = &MstFactory::getConfig();
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();
			$group_id = JRequest::getInt('groupID', 0);
					
			// Get data from the model
			$model = &$this->getModel();			
			$rows = &$model->getData($group_id);
			$group = &$model->getGroupData($group_id);
			$nonMembers = &$model->getNonMemberData($group_id);
			$userEntries = array();
			for($i=0; $i < count($nonMembers); $i++)
			{
				$nonMember= $nonMembers[$i];
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
						
			//Create Toolbar
			JToolBarHelper::title( JText::_( 'COM_MAILSTER_USER_GROUP_MEMBERS_GROUP_X' ) . ' (' . $group[0]->name . ')', 'group-mailster' );
			
			JToolBarHelper::custom( 'removeUsers', 'removeUsers-mailster.png', 'removeUsers-mailster.png', JText::_( 'COM_MAILSTER_REMOVE_USERS' ), false, false );
						
	//		JToolBarHelper::help( 'mtr.groupusers', true );
			$mstUtils = & MstFactory::getUtils();
			$mstUtils->addSubmenu('groupusers');
			
			$this->assignRef('rows'      	, $rows);
			$this->assignRef('group'      	, $group);
			$this->assignRef('user'			, $user);
			$this->assignRef('userEntries'	, $userEntries);
			$this->assignRef('group_id'		, $group_id);
	 
			parent::display($tpl);
		}


	}
?>
