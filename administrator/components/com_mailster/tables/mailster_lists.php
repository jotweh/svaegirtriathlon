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
	 * Lists Model class
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class mailster_lists extends JTable
	{
		/**
		 * Primary Key
		 * @var int
		 */
		var $id 					= null;
		var $name					= null;	
		var $admin_mail				= null;	
		var $list_mail				= null;	
		var $subject_prefix			= null;
		var $mail_in_user			= null;	
		var $mail_in_pw				= null;	
		var $mail_in_host			= null;	
		var $mail_in_port			= null;	
		var $mail_in_use_secure		= null;
		var $mail_in_protocol		= null;
		var $mail_out_user			= null;	
		var $mail_out_pw			= null;	
		var $mail_out_host			= null;	
		var $mail_out_port			= null;	
		var $mail_out_use_secure	= null;
		var $mail_in_params			= null;	
		var $published 				= null;	
		var $active 				= null;
		var $use_joomla_mailer		= null;
		var $mail_in_use_sec_auth	= null;
		var $mail_out_use_sec_auth	= null;
		var $public_registration	= null;
		var $sending_public			= null;
		var $sending_recipients		= null;	
		var $sending_admin			= null;
		var $sending_group			= null;	
		var $sending_group_id		= null;	
		var $allow_registration		= null;
		var $reply_to_sender		= null;
		var $copy_to_sender			= null;
		var $disable_mail_footer	= null;
		var $custom_header_plain	= null;
		var $custom_header_html		= null;
		var $custom_footer_plain	= null;
		var $custom_footer_html		= null;
		var $mail_format_conv		= null;
		var $mail_format_altbody	= null;
		var $alibi_to_mail			= null;
		var $addressing_mode		= null;
		var $mail_from_mode			= null;
		var $name_from_mode			= null;
		var $archive_mode			= null;
		var $bounce_mode			= null;
		var $bounce_mail			= null;
		var $bcc_count				= null;
		var $max_send_attempts		= null;
		var $filter_mails			= null;
		var $clean_up_subject		= null;
		var $lock_id				= null;
		var $is_locked				= null;
		var $last_lock				= null;
		var $last_check				= null;
		var $throttle_hour			= null;
		var $throttle_hour_cr		= null;
		var $throttle_hour_limit	= null;
		var $cstate					= null;
		var $mail_size_limit		= null;
		var $notify_not_fwd_sender 	= null;
		
		
		function mailster_lists(&$db) {
			parent::__construct('#__mailster_lists', 'id', $db);
		}
		
		function check(){			
			$this->name = strip_tags(trim($this->name));

			if ( $this->name == '' ) {
				$this->_error = JText::_( 'COM_MAILSTER_ADD_NAME' );
				JError::raiseWarning('NO MAILING LIST NAME SPECIFIED', $this->_error );
				return false;
			}
			
			return true;
		}
	}
?>
