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
	 * Mailster Component Subscriptions Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerSubscriptions extends MailsterController
	{		
		public function unsubscribe()
		{				
			$hashUtils = & MstFactory::getHashUtils();
			$subscrUtils = & MstFactory::getSubscribeUtils();	
			$listId = JRequest::getInt('list_id');
			$mailId = JRequest::getInt('mail_id');
			$hashOk = JRequest::getBool('hash_ok');
			$salt 	= JRequest::getInt('salt', rand());
			$hash 	= JRequest::getString('hash', "");
			$email 	= JRequest::getString('email');

			$hashOk = $hashUtils->checkUnsubscribeKey($mailId, $salt, $hash);
			
			$view = $this->getView('unsubscribe', 'html');
			
			if($hashOk){
				$success = $subscrUtils->unsubscribeUser($email, $listId);
				if($success){
					$view->unsubscriptionOk();
				}else{
					$view->unsubscriptionFailed();
				}
			}else{
				$view->unsubscriptionFailed();
			}
		}
		
	}
?>
