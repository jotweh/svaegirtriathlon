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
	
	function subMenu($identifier)
	{
		$identifier = strtolower($identifier);
		$mainMenue 		= false;
		$mailingLists 	= false;
		$users		 	= false;
		$groups		 	= false;
		$mailArchive 	= false;
		$config		 	= false;
		$info			= false;
		
		//Create Submenu
		if($identifier == "" || $identifier == "mailster")
		{
			$mainMenue = true;
		}
		if($identifier == "mailinglists")
		{
			$mailingLists = true;
		}
		if($identifier == "users")
		{
			$users = true;
		}
		if($identifier == "groups")
		{
			$groups = true;
		}
		if($identifier == "groupusers")
		{
			$groups = true;
		}
		if($identifier == "listmembers")
		{
			$mailingLists = true;
		}
		if($identifier == "mails")
		{
			$mailArchive = true;
		}
		if($identifier == "config")
		{
			$config = true;
		}
		if($identifier == "info")
		{
			$info = true;
		}
		if($identifier == "csv")
		{
			
		}
		if($identifier == "diagnosis")
		{
			$info = true;
		}
		if($identifier == "resend")
		{
			
		}
		if($identifier == "mailqueue")
		{
			
		}
		if($identifier == "log")
		{
			$info = true;	
		}
		JSubMenuHelper::addEntry( JText::_( 'COM_MAILSTER_START_CENTER' ), 'index.php?option=com_mailster', $mainMenue);
		JSubMenuHelper::addEntry( JText::_( 'COM_MAILSTER_MAILING_LISTS' ), 'index.php?option=com_mailster&view=lists', $mailingLists);
		JSubMenuHelper::addEntry( JText::_( 'COM_MAILSTER_USERS' ), 'index.php?option=com_mailster&view=users', $users);
		JSubMenuHelper::addEntry( JText::_( 'COM_MAILSTER_USER_GROUPS' ), 'index.php?option=com_mailster&view=groups', $groups);
		JSubMenuHelper::addEntry( JText::_( 'COM_MAILSTER_MAIL_ARCHIVE' ), 'index.php?option=com_mailster&controller=mails&task=mails', $mailArchive);
		JSubMenuHelper::addEntry( JText::_( 'COM_MAILSTER_MAILSTER_CONFIGURATION' ), 'index.php?option=com_mailster&view=config', $config);
		JSubMenuHelper::addEntry( JText::_( 'COM_MAILSTER_MAILSTER_INFO' ), 'index.php?option=com_mailster&view=info', $info);
	}

?>
