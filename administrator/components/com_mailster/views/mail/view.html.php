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
	 * HTML View class for the Mail View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewMail extends JView
	{
		function display($tpl = null)
		{	
			$attachUtils 	= & MstFactory::getAttachmentsUtils();
			
			//initialise variables
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();
			$list_id 	= JRequest::getInt("listID", -1);
			
			JToolBarHelper::title( JText::_( 'COM_MAILSTER_VIEW_MAIL' ), 'mail-mailster' );				
			JToolBarHelper::cancel();	
		
			// Get data from the model
			$row = &$this->get('Data');			
			
			$row->attachments = $attachUtils->getAttachmentsOfMail($row->id);			
			$this->assignRef('row'      , $row);
			$this->assignRef('user'		, $user);
			$this->assignRef('listID'	, $list_id);
	 
			parent::display($tpl);
		}


	}
?>
