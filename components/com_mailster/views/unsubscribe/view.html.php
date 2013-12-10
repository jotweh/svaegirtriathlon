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
	 * HTML View for unsubscribing
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewUnsubscribe extends JView
	{
		function display($tpl = null)
		{		
			$listUtils = & MstFactory::getMailingListUtils();	
			$hashUtils = & MstFactory::getHashUtils();
			$mailId	= JRequest::getInt('m', 0);
			$salt 	= JRequest::getInt('s', rand());
			$hash 	= JRequest::getString('h', "");
			$listId = $listUtils->getMailingListIdByMailId($mailId);
			$list 	= $listUtils->getMailingList($listId);

			$hashOk = $hashUtils->checkUnsubscribeKey($mailId, $salt, $hash);
			$this->assignRef('list'     	, $list);
			$this->assignRef('list_id'     	, $listId);
			$this->assignRef('mail_id'		, $mailId);
			$this->assignRef('salt'      	, $salt);
			$this->assignRef('hash'			, $hash);
			$this->assignRef('hash_ok'     	, $hashOk);
			
			parent::display($tpl);
		}

		function unsubscriptionOk(){
			parent::display('successful');
		}

		function unsubscriptionFailed(){
			parent::display('failed');
		}

	}
?>
