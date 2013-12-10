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
	 * Mailster Component Plugins Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerPlugins extends MailsterController
	{		
		/**
		 * Reset Plugin Timer Logic (called with Ajax request)
		 */
		function resetplgtimer( )
		{	
			$app = JFactory::getApplication();  // Get the application object.
			
			$log = & MstFactory::getLogger();	
			$pluginUtils = & MstFactory::getPluginUtils();
			$mstUtils = &MstFactory::getUtils();		
			$resultArray = array();
			$res = JText::_( 'COM_MAILSTER_RESET_TIMER_CALLED' );		
			$ajaxParams = JRequest::getString('mtrAjaxData');
			$ajaxParams = $mstUtils->jsonDecode($ajaxParams);
			$task = $ajaxParams->{'task'};
			if($task == 'resetPlgTimer'){
				if($pluginUtils->resetMailPluginTimes()){
					$res = JText::_( 'COM_MAILSTER_MST_RESET' ) . ' ' . JText::_( 'COM_MAILSTER_OK' );
				}else{
					$res = JText::_( 'COM_MAILSTER_MST_RESET' ) . ' ' . JText::_( 'COM_MAILSTER_NOT_OK' );
				}
			}else{
				$res = JText::_( 'COM_MAILSTER_UNKNOWN_TASK' ) . ': ' . $task;
			}	
			$resultArray['checkresult'] = $res;			
			$jsonStr  = $mstUtils->jsonEncode($resultArray);
			echo "[" . $jsonStr . "]";
			
        	$app->close(); // Close the application.
		}
		
	}
?>
