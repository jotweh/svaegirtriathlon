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
	 * Mailster Component Config (Configuration) Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerConfig extends MailsterController
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
		
		function apply()
		{
			$this->save();
		}
		
		
		function save(){
			$mstConfig = &MstFactory::getConfig();
			$task = JRequest::getString('task');
			
			$post = JRequest::get('post');
			$post['option'] = 'com_mailster';
			
			if(trim($post['params']['words_to_filter']) === '' ){
				$post['params']['words_to_filter'] = MstConsts::NO_PARAMETER_SUPPLIED_FLAG;
			}
						
			$table = $mstConfig->getComponentExtensionTblEntry();	
			$table->bind($post);
			
			if ( !$table->check() ){
			    JError::raiseWarning(500, $table->getError());
			    return false;
			}
			 
			if ( !$table->store() ){
			    JError::raiseWarning(500, $table->getError());
			    return false;
			}	
			
			switch ($task){
				case 'apply' :
					$link = 'index.php?option=com_mailster&view=config';
					break;

				default :
					$link = 'index.php?option=com_mailster';
					break;
			}
			$msg = JText::_( 'COM_MAILSTER_MAILSTER_CONFIGURATION_SAVED');

			$this->setRedirect( $link, $msg );
			return true;	
		}
		
					
		/**
		 * logic for cancel an action
		 *
		 * @access public
		 * @return void
		 */
		function cancel()
		{
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			
			$this->setRedirect( 'index.php?option=com_mailster' );
		}
		
		
	}
?>
