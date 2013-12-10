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
	class MailsterViewCsv extends JView
	{
		function display($tpl = null)
		{	
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();			
			
			$session = &JFactory::getSession();
			$users 			= $session->get('importedusers');
			$importtask 	= $session->get('importtask');		
			$targetgroup 	= $session->get('targetgroup');
			$targetlist 	= $session->get('targetlist');	
			$duplicateopt 	= $session->get('duplicateopt');	
			$newgroupname 	= $session->get('newgroupname');
			$importtarget 	= $session->get('importtarget');					
			$session->clear('importedusers');
  			$session->clear('importtask');
  			$session->clear('targetgroup');
  			$session->clear('targetlist');
  			$session->clear('duplicateopt');
  			$session->clear('newgroupname');
			
			$titleTxt = JText::_( 'COM_MAILSTER_CSV_IMPORT' );
			if($users){
				$titleTxt = $titleTxt . ' (' . JText::_( 'COM_MAILSTER_STEP' ) . ' 2/2)';
			}else{
				$titleTxt = $titleTxt . ' (' . JText::_( 'COM_MAILSTER_STEP' ) . ' 1/2)';
			}
			
			JToolBarHelper::title($titleTxt , 'csv-mailster' );
			$mstUtils = & MstFactory::getUtils();
			$mstUtils->addSubmenu('csv');
			
			$groupsModel = &$this->getModel('groups');
			$listsModel = &$this->getModel('lists');							
			$groups = &$groupsModel->getData();
			$mailingLists = &$listsModel->getData();			
						
			$this->assignRef('importedusers', $users);
			$this->assignRef('importtask', $importtask);
			$this->assignRef('targetgroup', $targetgroup);
			$this->assignRef('targetlist', $targetlist);
			$this->assignRef('duplicateopt', $duplicateopt);
			$this->assignRef('newgroupname', $newgroupname);
			$this->assignRef('importtarget', $importtarget);
			
			$this->assignRef('row'      	, $row);
			$this->assignRef('mailLists' 	, $mailingLists);
			$this->assignRef('groups' 		, $groups);
			$this->assignRef('user'			, $user);
			
			parent::display($tpl);
		}
	}
?>
