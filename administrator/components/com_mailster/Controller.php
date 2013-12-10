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

	jimport('joomla.application.component.controller');

	/**
	 * Mailster Component Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterController extends JController
	{
		/**
		* Constructor
		*
		*/
		function __construct(){
			// execute parent's constructor
			parent::__construct();		
		}
		
		/**
		* Gets a reference to a subclass of the controller.
		*
		* @static
		* @param string entity name
		* @param string controller prefix
		* @return Mailster extension controller
		*/
		public static function &getInstance($entity, $prefix='Mailster'){
			// use a static array to store controller instances
			static $instances;
			if (!$instances){
				$instances = array();
			}
			
			// determine subclass name
			$class = $prefix.'Controller'.ucfirst($entity);
			// check if we already instantiated this controller
			if (!isset($instances[$class])){
				// check if we need to find the controller class
				if (!class_exists( $class )){
					$path = JPATH_COMPONENT.DS.'controllers'.DS.strtolower($entity).'.php';
					// search for the file in the controllers path
					jimport('joomla.filesystem.file'); // For using JFile
					if (JFile::exists($path)){	
						require_once $path; // include the class file
						if (!class_exists( $class )){	
							// class file does not include the class
							return JError::raiseWarning('SOME_ERROR', JText::_( 'COM_MAILSTER_INVALID_CONTROLLER' ));
						}
					}
					else{
						return JError::raiseWarning('SOME_ERROR', JText::_( 'COM_MAILSTER_UNKNOWN_CONTROLLER' )); // class file not found
					}
				}
				$instances[$class] = new $class(); // create controller instance
			}
			return $instances[$class]; // return a reference to the controller
		}
	}

?>
