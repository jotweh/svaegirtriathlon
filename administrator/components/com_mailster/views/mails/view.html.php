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
	 * HTML View class for the Mails View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewMails extends JView
	{
		function display($tpl = null)
		{	
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();
			
			$list_id = JRequest::getInt("listID", -1);
						
			//Create Toolbar
			JToolBarHelper::title( JText::_( 'COM_MAILSTER_MAIL_ARCHIVE' ), 'mailArchive-mailster' );
			JToolBarHelper::custom( 'resendMails', 'resendMails-mailster.png', 'resendMails-mailster.png', JText::_( 'COM_MAILSTER_RESEND' ), true, false );
			
			JToolBarHelper::deleteList();
	
			$mstUtils = & mstFactory::getUtils();
			$mstUtils->addSubmenu('mails');
						
			$model = &$this->getModel();
			
			 // if list_id is provided only the mails of the mailing list will be retrieved			
			$rows = &$model->getData($list_id);		
			$blockedMails = &$model->getBlockedMailData($list_id);
			$bouncedMails = &$model->getBouncedMailData($list_id);
			
			$pagination = &$model->getPagination();
								
			$mailingListNames = array();
			$mailingListModel = &$this->getModel('lists');
			$mailingLists = $mailingListModel->getData();
						
			$this->assignRef('rows'      	, $rows);
			$this->assignRef('blocked'     	, $blockedMails);
			$this->assignRef('bounced'     	, $bouncedMails);
			$this->assignRef('user'			, $user);
			$this->assignRef('listID'		, $list_id);
			$this->assignRef('mailingLists'	, $mailingLists);
			$this->assignRef('pagination'	, $pagination);
	 
			parent::display($tpl);
		}


	}
?>
