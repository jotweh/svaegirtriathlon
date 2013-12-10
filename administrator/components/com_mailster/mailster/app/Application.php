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
	
	class MstApplication
	{
	
		protected static function _loadComponentProperties(){
			$propPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'com.properties';
			$confIO = & MstFactory::getConfIO();
			return $confIO->loadConf($propPath);
		}
		
		protected static function _loadPluginProperties($id){
			$propPath = JPATH_PLUGINS . DS ;
			if($id==='plg_mailster'){
				if(version_compare(JVERSION,'1.6.0','ge')) {
					// Joomla! 1.6 / 1.7 / ...
					$propPath = $propPath . 'system' . DS . 'mailster' . DS . 'mailster' . DS. 'plg.properties';
				} else {
					// Joomla! 1.5 
					$propPath = $propPath . 'system' . DS . 'mailster' . DS. 'plg.properties';
				}				
			}elseif($id==='plg_mailster_subscriber'){				
				if(version_compare(JVERSION,'1.6.0','ge')) {
					// Joomla! 1.6 / 1.7 / ...
					$propPath = $propPath . 'content' . DS . 'mailstersubscriber' . DS . 'mailstersubscriber' . DS. 'plg.properties';
				} else {
					// Joomla! 1.5 
					$propPath = $propPath . 'content' . DS . 'mailstersubscriber' . DS. 'plg.properties';
				}	
			}
			$confIO = & MstFactory::getConfIO();
			return $confIO->loadConf($propPath);
		}
		
		protected static function _loadProperties($id){
			if($id==='com_mailster'){
				$props = self::_loadComponentProperties();
			}else{
				$props = self::_loadPluginProperties($id);
			}
			return $props; 
		} 
		
		public static function getVersionString($addBeta = true){		
			$props = parse_ini_file(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'. DS . 'version.properties');
			$version = $props['major'] . '.' . $props['minor'] . '.' .$props['bugfix'];
			if($addBeta){
				if(isset($props['beta']) && (trim($props['beta']) !== "") && (trim($props['beta']) !== "-")){
					$version .= ' - ' . ucfirst(trim($props['beta']));
				}
			}
			return $version;
		}
		
		/**
		 * This method basically works like JComponentHelper::getComponent but without the caching
		 */
		public static function getComponent($option = 'com_mailster'){
			$comp = null;
			
			if(version_compare(JVERSION,'1.6.0','ge')) {
				// Joomla! 1.6 / 1.7 / ...
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('extension_id AS "id", element AS "option", params, enabled');
				$query->from('#__extensions');
				$query->where('`type` = '.$db->quote('component'));
				$query->where('`element` = '.$db->quote($option));
				$db->setQuery($query);
				$comp = $db->loadObject();
								
				if ($error = $db->getErrorMsg() || !$comp){
					JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error));
					return false;
				}
				
				// Convert the params to an object.
				if (is_string($comp->params)){
					$temp = new JRegistry;
					$temp->loadString($comp->params);
					$comp->params = $temp;
				}				
				
			} else {
				// Joomla! 1.5
				$db = &JFactory::getDBO();				
				$query = 'SELECT *' .
								' FROM #__components' .
								' WHERE `option` = \''.$option.'\'';
				$db->setQuery( $query );
				$comp = $db->loadObject();
				
				// Convert the params to an object.
				$comp->params = new JParameter($comp->params);
			}
						
			return $comp;
		}
		
		public static function detectSystemProblems(){
			$res = new stdClass();
			$res->error = false;
			$res->errorMsg = "";
			$res->autoFixAvailable = false;
			
			$env 		= & MstFactory::getEnvironment();
			$dbUtils 	= & MstFactory::getDBUtils();
			
			$phpVersionOk 	= (floatval(phpversion()) >= 5.0);
			$dbCollationOk 	= $dbUtils->userTableCollationOk();
			$imapExtOk 		= $env->imapExtensionInstalled();
			
			if(!$phpVersionOk){
				$res->error = true;
				$res->errorMsg = JText::_( 'COM_MAILSTER_PHP_VERSION_NOT_SUFFICIENT_CHECK_MINIMUM_SYSTEM_REQUIREMENTS' );
			}elseif(!$dbCollationOk){
				$res->error = true;
				$res->errorMsg = JText::_( 'COM_MAILSTER_DATABASE_COLLATION_ERROR' );
				$res->autoFixAvailable = true;
				$res->autoFixLink = 'index.php?option=com_mailster&controller=maintenance&task=fixDBCollation';
			}elseif(!$imapExtOk){
				$res->error = true;
				$res->errorMsg = JText::_( 'COM_MAILSTER_IMAP_EXTENSION_NOT_INSTALLED_PLEASE_CHECK_SYSTEM_REQUIREMENTS' );
			}
			
			return $res;
		}
		
		public static function getInstallInformation(){
			$xmlFile = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_mailster'.DS.'mailster.xml';
			$compInfos = new stdClass();
			if ($data = JApplicationHelper::parseXMLInstallFile($xmlFile)) {
				foreach($data as $key => $value) {
					$compInfos->$key = $value;
				}
			}
			// For Joomla 1.6+
			if(isset($compInfos->creationDate) &&  ($compInfos->creationDate !== null)){
				$compInfos->creationdate = $compInfos->creationDate;
			}
			return $compInfos;
		}
		
		public static function isProductHashCorrect($id, $checkHash){
			$log = & MstFactory::getLogger();
			$props = self::_loadProperties($id);
			$hBase = self::calcHBase($props);
			if(md5($hBase . $id) != $props['phsh']){
				return false;
			}
			$hash = md5('chsh' . $props['phsh']); 
			return ($checkHash === $hash);
		}
		
		public static function getProductType($id){
			$props = self::_loadProperties($id);
			foreach ($props as $key => $val){
				if($key == 'ptyp'){
					return $val;
				}
			}
		}	
			
		public static function isFreeEdition($id, $pTyp, $checkHash){
			$pHashOk = self::isProductHashCorrect($id, $checkHash);
			$hBase = '';
			$val = self::getProductType($id); 	
			if($pTyp != $val){
				return true;
			}
			return (($val=='free') || ($val!='compro') || (!$pHashOk));
		}
		
		public static function checkPluginProductHashes(){
			$log = & MstFactory::getLogger();
			$propPath = JPATH_PLUGINS . DS ;
			if(version_compare(JVERSION,'1.6.0','ge')) {
				// Joomla! 1.6 / 1.7 / ...
				$mstPlg = $propPath . 'system' . DS . 'mailster' . DS. 'mailster' . DS . 'utils.php';
				$mstPlgSubscr = $propPath . 'content' . DS . 'mailstersubscriber' . DS . 'mailstersubscriber.php';
			} else {
				// Joomla! 1.5 
				$mstPlg = $propPath . 'system' . DS . 'mailster' . DS. 'utils.php';
				$mstPlgSubscr = $propPath . 'content' . DS . 'mailstersubscriber.php';
			}
			$mstPlgIntegrity = false;
			$mstPlgSubscrIntegrity = false;
			if (isset($mstPlg) && is_file($mstPlg)) {
				require_once($mstPlg);
				$mstPlgIntegrity = chkMailerIntegrity();
			}
			if (isset($mstPlgSubscr) && is_file($mstPlgSubscr)) {
				require_once($mstPlgSubscr);
				$mstPlgSubscrIntegrity = chkSubscrIntegrity();
			}
			return ($mstPlgIntegrity && $mstPlgSubscrIntegrity);
		}
		
		public static function getRecC($id){
			$props = self::_loadProperties($id);
			return min($props['recc'], octdec('62'));
		} 
		
		public static function calcHBase($props){
			$hBase = '';
			foreach ($props as $key => $val){
				if($key != 'phsh'){
					$hBase = $hBase . $key . $val;
				}
			}
			return $hBase;
		}
	}

?>
