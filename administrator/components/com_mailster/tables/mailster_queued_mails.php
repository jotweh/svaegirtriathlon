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
	 * Queued Mail Model class
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class mailster_queued_mails extends JTable
	{
		var $mail_id 				= null;
		var $name					= null;	
		var $email					= null;	
		var $error_count			= null;	
		var $lock_id				= null;	
		var $is_locked				= null;	
		var $last_lock				= null;	
						
		function mailster_users(&$db) {
			parent::__construct('#__mailster_queued_mails', 'id', $db);
		}
		
		function check(){
			$this->name = strip_tags(trim($this->name));

			if ( $this->mail_id == '' ) {
				$this->_error = JText::_( 'COM_MAILSTER_ADD_MAIL_ID' ); 
				JError::raiseWarning('NO MAIL ID SPECIFIED', $this->_error );
				return false;
			}
			
			if ( $this->name == '' ) {
				$this->_error = JText::_( 'COM_MAILSTER_ADD_NAME' );
				JError::raiseWarning('NO USER NAME SPECIFIED', $this->_error );
				return false;
			}
		
			if ( $this->email == '' ) {
				$this->_error = JText::_( 'COM_MAILSTER_ADD_E-MAIL' );
				JError::raiseWarning('NO E-MAIL SPECIFIED', $this->_error );
				return false;
			}
			
			return true;
		}
	}
?>
