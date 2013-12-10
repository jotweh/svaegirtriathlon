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
	jimport( 'joomla.html.parameter' );
	
	class MstPluginUtils
	{
		public static function resetMailPluginTimes(){		
			JPluginHelper::importPlugin('system');
			$mstPlugin = self::getPlugin('mailster', 'system');
			$pluginParams = self::getMailPluginParameter();
			$minSendTime = 	$pluginParams->def('minsendtime', 60);
			$minCheckTime = $pluginParams->def('minchecktime', 240);
			$tNow = time();
			if($tNow > 0){
				$lastExecRetrieve = $tNow - $minCheckTime;
				$lastExecSending = $tNow - $minSendTime;
				$pluginParams->set('last_exec_retrieve', $lastExecRetrieve);
				$pluginParams->set('last_exec_sending', $lastExecSending);
				if(version_compare(JVERSION,'1.6.0','ge')) {
					// Joomla! 1.6 / 1.7 / ...
					$plgId = $mstPlugin->extension_id;
				} else {
					// Joomla! 1.5 
					$plgId = $mstPlugin->id;
				}
				self::updatePluginParams($plgId, $pluginParams->toString());
				return true;
			}else{
				return false;
			}
		}
		
		public static function isCBMstUserGroupSyncActive(){
			$pluginParams = self::getCBPluginParameter();
			$groupSyncActive = ($pluginParams->def( 'cb_user_group_sync_active', '0' ) > 0) ? true : false;
			return $groupSyncActive;
		}
		
		public static function isCBMstMailingListSyncActive(){
			$pluginParams = self::getCBPluginParameter();
			$mListSyncActive = ($pluginParams->def( 'cb_mailing_list_sync_active', '0' ) > 0) ? true : false;
			return $mListSyncActive;
		}
		
		public static function getNextMailCheckTime(){
			$pluginParams = self::getMailPluginParameter();
			$minCheckTime = 	$pluginParams->def('minchecktime', 240);
			$lastCheckTime =	$pluginParams->def('last_exec_retrieve', 0);
			return $lastCheckTime + $minCheckTime;
		}
		
		public static function getNextMailSendTime(){	
			$pluginParams = self::getMailPluginParameter();
			$minSendTime = 	$pluginParams->def('minsendtime', 60);
			$lastSendTime =	$pluginParams->def('last_exec_sending', 0);
			return $lastSendTime + $minSendTime;
		}
		
		public static function getMailPluginParameter(){			
			JPluginHelper::importPlugin('system');
			$jPlugin = JPluginHelper::getPlugin('system', 'mailster');
			$params = (is_null($jPlugin) || (is_array($jPlugin)&&(count($jPlugin)<=0))) ? null : $jPlugin->params;
			$pluginParams = new JParameter( $params );
			return $pluginParams;
		}
		
		public static function getCBPluginParameter(){			
			JPluginHelper::importPlugin('system');
			$jPlugin = JPluginHelper::getPlugin('system', 'mailstercb');
			$params = (is_null($jPlugin) || (is_array($jPlugin)&&(count($jPlugin)<=0))) ? null : $jPlugin->params;
			$pluginParams = new JParameter( $params );
			return $pluginParams;
		}
		
		public static function getPluginById($pluginId){
			$db = & JFactory::getDBO();
			if(version_compare(JVERSION,'1.6.0','ge')) {
				// Joomla! 1.6 / 1.7 / ...
				$query = 'SELECT * FROM #__extensions WHERE extension_id=\'' . $pluginId . '\'';
			} else {
				// Joomla! 1.5 
				$query = 'SELECT * FROM #__plugins WHERE id=\'' . $pluginId . '\'';
			}
			$db->setQuery( $query );
			$plugin = $db->loadObject();
			return $plugin;		
		}
		
		public static function isCBPluginActive(){
			return self::isPluginActive('mailstercb', 'system');
		}
		
		public static function isPluginActive($element, $folder){
			$plugin =&JPluginHelper::getPlugin( $folder, $element );
			if($plugin){
				$enabled =&JPluginHelper::isEnabled( $folder, $element );
				return $enabled;
			} 
			return false;
		}
		
		public static function isCBPluginInstalled(){
			return self::isPluginInstalled('mailstercb', 'system');
		}
		
		public static function isMailPluginInstalled(){
			return self::isPluginInstalled('mailster', 'system');
		}
		
		public static function isProfilePluginInstalled(){
			return self::isPluginInstalled('mailsterprofile', 'system');
		}
		
		public static function isSubscriberPluginInstalled(){
			return self::isPluginInstalled('mailstersubscriber', 'content');
		}
		
		public static function isPluginInstalled($element, $folder){
			$plg = self::getPlugin($element, $folder);
			if($plg && !is_null($plg)){
				if(version_compare(JVERSION,'1.6.0','ge')) {
					// Joomla! 1.6 / 1.7 / ...
					$plgId = $plg->extension_id;
				}else{
					// Joomla! 1.5 
					$plgId = $plg->id;	
				}
				if($plgId > 0){
					return true;
				}	
			}
			return false;
		}
		
		public static function getPlugin($element, $folder){
			$db = & JFactory::getDBO();
			if(version_compare(JVERSION,'1.6.0','ge')) {
				// Joomla! 1.6 / 1.7 / ...
				$query = 'SELECT * FROM #__extensions WHERE element=' . $db->quote($element)
						. ' AND folder=' . $db->quote($folder) . '';
			} else {
				// Joomla! 1.5 
				$query = 'SELECT * FROM #__plugins WHERE element=' . $db->quote($element)
						. ' AND folder=' . $db->quote($folder) . '';
			}
			$db->setQuery( $query );
			$plugin = $db->loadObject();
			return $plugin;		
		}
		
		public static function updatePluginParams($pluginId, $params){
			$log = & MstFactory::getLogger();
			$db = & JFactory::getDBO();
			if(version_compare(JVERSION,'1.6.0','ge')) {
				// Joomla! 1.6 / 1.7 / ...
				$query = 'UPDATE #__extensions SET params = ' . $db->quote($params) . ' WHERE extension_id=\'' . $pluginId . '\'';	
			} else {
				// Joomla! 1.5 
				$query = 'UPDATE #__plugins SET params = ' . $db->quote($params) . ' WHERE id=\'' . $pluginId . '\'';	
			}			
			$db->setQuery( $query );
			$result = $db->query();		
			if(!$result){
				$log->error('Updating plugin parameters of plugin ' . $pluginId . ' failed: ' . $db->getErrorMsg());
				return false;
			}
			return true;
		}
		
	}

?>
