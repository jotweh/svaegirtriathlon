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
		
	$user = 'mailster.test@brandt-oss.com';
	$pw = 'mail4mailster';
	$host = 'imap.brandt-oss.com';
	$port = 143;
	$folder = 'INBOX';
	$options = '/imap';
	$host = '{' . $host . ':' . $port . $options . '/novalidate-cert}'.$folder;
	if (!$mbh = imap_open($host, $user, $pw)) {  // not using @
		echo JText::_( 'COM_MAILSTER_CONNECTION_NOT_OK' ) . '<br/><br/>';   		
		$imapErrors = imap_errors();
		if($imapErrors){
			echo JText::_( 'COM_MAILSTER_ERRORS' ) . ':<br/>';
			foreach($imapErrors as $error){
				echo $error."<br />";
			}
		}else{
			echo JText::_( 'COM_MAILSTER_NO_ERROR_MESSAGES_AVAILABLE' ) . '<br/>';
		}
	} else {		
		echo JText::_( 'COM_MAILSTER_CONNECTION_OK' ); 
		$imapErrors = imap_errors(); // clear (useless) notices/warnings				
		imap_close($mbh);
	}
			
	
?>
