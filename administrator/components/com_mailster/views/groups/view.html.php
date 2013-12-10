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
	 * HTML View class for the Groups View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewGroups extends JView
	{
		function display($tpl = null)
		{				
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();			
				
			//Create Toolbar
			JToolBarHelper::title( JText::_( 'COM_MAILSTER_MAILING_LIST_GROUPS' ), 'groups-mailster' );
			
			JToolBarHelper::custom( 'removeGroups', 'removeGroups-mailster.png', 'removeGroups-mailster.png', JText::_( 'COM_MAILSTER_REMOVE_GROUPS' ), true, false );
			
			$mstUtils = & MstFactory::getUtils();
			$mstUtils->addSubmenu('groups');	
			
			// Get data from the model
			$model = &$this->getModel();			
			$rows = &$model->getData();
			
			$this->assignRef('rows',	$rows);
			$this->assignRef('user', 	$user);
	 
			parent::display($tpl);
		}


	}
?>
