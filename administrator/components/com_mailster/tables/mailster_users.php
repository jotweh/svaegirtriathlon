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

	defined('_JEXEC') or die('Restricted access');

	/**
	 * Users Model class
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class mailster_users extends JTable
	{
		var $id 					= null;
		var $name					= null;	
		var $email					= null;	
		var $notes					= null;	
		
		function mailster_users(&$db) {
			parent::__construct('#__mailster_users', 'id', $db);
		}
		
		function check(){
			
			$mailUtils = &MstFactory::getMailUtils();
			$this->name = strip_tags(trim($this->name));

			if ( $this->email == '' ) {
				$this->_error = JText::_( 'COM_MAILSTER_NO_EMAIL_PROVIDED_USER_CAN_NOT_BE_SAVED' );
				JError::raiseWarning('NO USER E-MAIL SPECIFIED', $this->_error );
				return false;
			}
			if ( !$mailUtils->isValidEmail($this->email) ) {
				$this->_error = JText::_( 'COM_MAILSTER_NO_EMAIL_PROVIDED_USER_CAN_NOT_BE_SAVED' );
				JError::raiseWarning('NO USER E-MAIL SPECIFIED', JText::sprintf( 'COM_MAILSTER_INVALID_EMAIL_ADDRESS_X', $this->email) );
				return false;
			}
			
			return true;
		}
	}
?>
