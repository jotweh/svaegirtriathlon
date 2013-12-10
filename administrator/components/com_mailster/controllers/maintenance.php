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
	 * Mailster Component Maintenance Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerMaintenance extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{
			parent::__construct();			
		}
		
		function fixDBCollation(){
			$dbUtils 	= & MstFactory::getDBUtils();
			$dbCollationOk 	= $dbUtils->userTableCollationOk();
			$app = &JFactory::getApplication();
			
			if(!$dbCollationOk){
				$cEmailCollation = $dbUtils->getCollation('#__users', 'email');
				$cNameCollation = $dbUtils->getCollation('#__users', 'name');
				$mEmailCollation = $dbUtils->getCollation('#__mailster_users', 'email');
				$mNameCollation = $dbUtils->getCollation('#__mailster_users', 'name');
				
				if($cEmailCollation !== $mEmailCollation){
					$res = $dbUtils->alterCollation('#__mailster_users', 'email', $cEmailCollation);
					if($res >= 0){
						$app->enqueueMessage(JText::_( 'COM_MAILSTER_CHANGING_COLLATION_SUCCESSFULLY_FOR' ) . ' "email" ' );
					}else{
						$app->enqueueMessage(JText::_( 'COM_MAILSTER_CHANGING_COLLATION_FAILED_FOR' ) . ' "email" ' , 'error');
					}
				}
				if($cNameCollation !== $mNameCollation){
					$dbUtils->alterCollation('#__mailster_users', 'name', $cNameCollation);
					if($res >= 0){
						$app->enqueueMessage(JText::_( 'COM_MAILSTER_CHANGING_COLLATION_SUCCESSFULLY_FOR' ) . ' "name" ');
					}else{
						$app->enqueueMessage(JText::_( 'COM_MAILSTER_CHANGING_COLLATION_FAILED_FOR' ) . ' "name" ' , 'error');
					}
				}
			}
			$app->redirect('index.php?option=com_mailster');
		}
		
	}
?>
