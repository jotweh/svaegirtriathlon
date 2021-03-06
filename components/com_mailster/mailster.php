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

	// Set the table directory
	JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'tables');
	
	// Require the base controller
	require_once (JPATH_COMPONENT.DS.'Controller.php');	
	
	// Get all essential includes
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'includes.php');	
	
	$c = JRequest::getString('controller', JRequest::getString('view'));
	switch($c){
		case 'unsubscribe':
			$c = 'subscriptions'; // special shortcut for subscription controller
			break;
		default:
			$c = $c; // do not change anything
			break;
	}
	$controller = MailsterController::getInstance($c);
	$task = JRequest::getCmd('task', 'view');
	$document = & JFactory::getDocument();	
	$controller->execute($task);
	$controller->redirect();
	
?>
