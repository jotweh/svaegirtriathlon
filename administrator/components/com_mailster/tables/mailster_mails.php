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
	 * Mails Model class
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class mailster_mails extends JTable
	{
		var $id 						= null;
		var $list_id					= null;	
		var $thread_id					= null;	
		var $hashkey					= null;	
		var $message_id					= null;	
		var $in_reply_to				= null;	
		var $references_to				= null;	
		var $receive_timestamp			= null;	
		var $from_name					= null;
		var $from_email					= null;
		var $subject					= null;
		var $body						= null;
		var $html						= null;
		var $attachments				= null;
		var $has_attachments			= null;
		var $fwd_errors					= null;
		var $fwd_completed				= null;
		var $fwd_completed_timestamp	= null;
		var $blocked_mail				= null;
		var $bounced_mail				= null;
		
		
		function mailster_mails(&$db) {
			parent::__construct('#__mailster_mails', 'id', $db);
		}
		
		function check(){
			
			if ( $this->list_id == '' || $this->list_id == null) {
				$this->_error = JText::_( 'COM_MAILSTER_ADD_LIST_ID' );
				JError::raiseWarning('NO LIST ID SPECIFIED', $this->_error );
				return false;
			}
			
			return true;
		}
	}
?>
