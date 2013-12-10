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
	 * Mailster Component Log Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerLog extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{
			// execute parent's constructor
			parent::__construct();
		}
		
		function clearLog(){
			$app = JFactory::getApplication();
			$model = $this->getModel('log');
			
			if(!$model->clearLog()) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}
					
			$app->enqueueMessage(JText::_( 'COM_MAILSTER_LOG_WAS_CLEARED'));

			$cache = &JFactory::getCache('com_mailster');
			$cache->clean();

			$this->display();
		}
				
		
		/**
		 * logic to remove mails
		 *
		 * @access public
		 * @return void
		 */
		function remove()
		{
			$app = JFactory::getApplication();	
			$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );		
			$total = count( $cid );
				
			if (!is_array( $cid ) || $total < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}
			$model = $this->getModel('log');
			if(!$model->delete($cid)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}

			$msg = $total.' '.JText::_( 'COM_MAILSTER_LOG_ENTRIES_DELETED');
			$app->enqueueMessage($msg);
			
			$cache = &JFactory::getCache('com_mailster');
			$cache->clean();

			$this->display();
		}
	}
?>
