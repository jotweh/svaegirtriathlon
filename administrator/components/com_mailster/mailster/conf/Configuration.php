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
	
	class MstConfiguration
	{

		protected static $mainConfigFile = 'config.xml';

		protected static function _getConfigFilePath(){
			return JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.self::$mainConfigFile;	
		}
		
		public static function getProperty($property, $default){
			$propPath = self::_getConfigFilePath();
			$confIO = & MstFactory::getConfIO();
			return $confIO->getProperty($propPath, $property, $default);
		}	
		
		public static function getComponentParams($option='com_mailster'){
			$mstApp = MstFactory::getApplication();
			$comp =& JComponentHelper::getComponent($option, true); // strict mode to get false when not enabled/existing
			if($comp->enabled == true){
				$comp = $mstApp->getComponent($option);
				return $comp->params;
			}
			return false;
		}
		
		public static function getComponentExtensionTblEntry($ext = 'com_mailster'){
			if(version_compare(JVERSION,'1.6.0','ge')) { 
				// Joomla! 1.6 and 1.7
				$tbl = 'extension';
				$table = &JTable::getInstance($tbl);
    			$optArray = array();
    			$optArray['element'] = $ext;
    			$loadOk = $table->load($table->find($optArray));
			}else{ 
				// Joomla! 1.5
				$tbl = 'component'; 
				$table = &JTable::getInstance($tbl);
				$loadOk = $table->loadByOption($ext);							
			}
			if(!$loadOk){
			    JError::raiseWarning(500, 'Not a valid component');
			    return false;
			}
			return $table;
		}
		
		public static function getAllParameters(){		
			$table = self::getComponentExtensionTblEntry();
			if(version_compare(JVERSION,'1.6.0','ge')) { 
				jimport('joomla.form.form');
				// Joomla! 1.6 and 1.7	
						
				//$registry = new JRegistry;
				//$registry->loadJSON($table->params);
				//$params = $registry;
				//$params = JForm::getInstance('com_mailster.config',  $registry);	
			
				// TODO MAKE THIS BETTER TO GET RID OF JPARAMETER AND FOR CLEAN RENDERING
				$params = new JParameter($table->params, self::_getConfigFilePath());	
			}else{ 
				// Joomla! 1.5
				$params = new JParameter($table->params, self::_getConfigFilePath());	
			}
			return $params;
		}

		public static function showUserDescription(){
			$params = self::getComponentParams();
			if($params){
		    	$showDesc = $params->get('show_user_description'); 
		    	if($showDesc > 0){ 
			    	return true;
		    	}
	            return false;
			}else{
				return false;
			}
		}
		
		public static function getLoggingLevel(){		
			$params = self::getComponentParams();
			if($params){
	    		return $params->get('logging_level'); 
			}else{
				return MstConsts::LOG_LEVEL_INFO;
			}
		}
		
		public static function isLog2File(){		
			$params = self::getComponentParams();
			if($params){
				$logDest = $params->get('log_entry_destination');
		    	if($logDest == MstConsts::LOG_DEST_FILE || $logDest == MstConsts::LOG_DEST_DB_AND_FILE){
		    		return true;
		    	}else{
		    		return false;
		    	}
			}else{
				return true;
			}
		}
		
		public static function isLog2Database(){		
			$params = self::getComponentParams();
			if($params){
				$logDest = $params->get('log_entry_destination');
		    	if($logDest == MstConsts::LOG_DEST_DB || $logDest == MstConsts::LOG_DEST_DB_AND_FILE){
		    		return true;
		    	}else{
		    		return false;
		    	}
			}else{
				return false;
			}
		}
		
		public static function isUseAlternativeTextVars(){			
			$params = self::getComponentParams();
			if($params){
	    		return ($params->get('use_alt_txt_vars') > 0); 
			}else{
				return false;
			}
		}
		
		public static function isLoggingForced(){			
			$params = self::getComponentParams();
			if($params){
	    		return ($params->get('force_logging') > 0); 
			}else{
				return false;
			}
		}
		
		public static function useMailingListAddressAsFromField(){			
			$params = self::getComponentParams();
			if($params){
	    		return ($params->get('mail_from_field') > 0); 
			}else{
				return false;
			}
		}
		
		public static function useMailingListNameAsFromField(){			
			$params = self::getComponentParams();
			if($params){
	    		return ($params->get('name_from_field') > 0); 
			}else{
				return false;
			}
		}
		
		public static function insertSenderAddressForEmptySenderName(){			
			$params = self::getComponentParams();
			if($params){
	    		return ($params->get('mail_from_email_for_from_name_field') > 0); 
			}else{
				return false;
			}
		}
				
		
		public static function getWordsToFilter(){
			$params = self::getComponentParams();
			$words = array();
			if($params){
		    	$wordsStr = $params->get('words_to_filter');
		    	$words = explode(',', $wordsStr);
		    	if($words){
		    		$nrWords = count($words);
			    	for($i=0;$i<$nrWords;$i++){
			    		$word = $words[$i];
			    		$word = trim($word);
			    		if(($word === '') || ($word === MstConsts::NO_PARAMETER_SUPPLIED_FLAG)){
			    			unset($words[$i]); // remove empty element
			    		}
			    	}
			    	$words = array_values($words); // re-index
		    	}else{
		    		$words = array();
		    	}
			}
	    	return $words;
		}
		
		public static function getDateFormat(){		
			$params = self::getComponentParams();
			if($params){
		    	$dateFormat = trim($params->get('date_format'));
		    	if(strlen($dateFormat) < 1){
		    		$dateFormat = '%d.%m.%Y %H:%M:%S';
		    	}
			}else{
				$dateFormat = '%d.%m.%Y %H:%M:%S';
			}
	    	return $dateFormat;
		}
		
		public static function addMailsterMailHeaderTag(){	
			$params = self::getComponentParams();
			if($params){
		    	if ($params->get('tag_mailster_mails') > 0){
		    		return true;
		    	}
		    	return false;
			}else{
				return true;
			}
		}
		
		public static function isUndoLineWrapping(){	
			$params = self::getComponentParams();
			if($params){
		    	if ($params->get('undo_line_wrapping') > 0){
		    		return true;
		    	}
		    	return false;
			}else{
				return false;
			}
		}
		
		public static function loadLocalJSFramework(){	
			$params = self::getComponentParams();
			if($params){
		    	if ($params->get('local_js_framework') > 0){
		    		return true;
		    	}
		    	return false;
			}else{
				return false;
			}
		}
		
		public static function addSubjectPrefixToReplies(){	
			$params = self::getComponentParams();
			if($params){
		    	if ($params->get('add_reply_prefix') > 0){
		    		return true;
		    	}
		    	return false;
			}else{
				return true;
			}
		}		
		
		public static function getReplyPrefix(){		
			$params = self::getComponentParams();
			if($params){
	    		return $params->get('reply_prefix');
			}else{
				return "Re:";
			}
		}
		
		public static function getRecaptchaKeys(){			
			$params = self::getComponentParams();
	    	$keys = array();
	    	$keys['public'] = '';
	    	$keys['private'] = '';
			if($params){
		    	$keys['public'] = $params->get('recaptcha_public_key');
		    	$keys['private'] = $params->get('recaptcha_private_key');
			}
		    return $keys;
		}
		
		public static function getRecaptchaParamString(){			
			$params = self::getComponentParams();
			if($params){
				$theme = $params->get('recaptcha_theme');
				$lang = $params->get('recaptcha_lang');
			}else{
				$theme = 'red';
				$lang = 'en';
			}
			$paramStr =  "theme : '".$theme . "', lang :'" . $lang . "'";			
			return $paramStr;
		}	
		
		public static function getAttachmentBaseDir(){
			$baseDir = 'tmp';
			return $baseDir;
		}
		
	}

?>