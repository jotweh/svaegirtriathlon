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

	class MstLog
	{
		
		const DEBUG = 'M_DE';
		const INFO = 'M_INF';
		const WARNING = 'M_WARN';
		const ERROR = 'M_ERR';
		
	    public static function log($msg, $level='INFO', $typeNr=0)
	    {   
	    	$logFileName = 'mailster.log';
	    	// Unit test functionality
	    	if( $_SERVER['REMOTE_ADDR'] === '10.86.194.17.blubb'){
	    		if( strtolower($_SERVER['MAILSTER_CONSOLE_DEBUG_ON']) === 'yes'){
	    			echo $msg . "\n"; // log to console
	    		}
	    		if( strtolower($_SERVER['MAILSTER_LOGGING_ON']) === 'no'){
	    			return; // do not log to file
	    		}
	    	}
	    	
	    	$isInstallEntry = ($typeNr == MstConsts::LOGENTRY_INSTALLER) ? true : false;
	    	
	    	global $mailster_install_running;
			$isInstallEntry = ($isInstallEntry || ($mailster_install_running == 1));
	    	
	    	if($isInstallEntry == true){
	    		return self::installLog($msg, $level); // during install don't do other tests...
	    	}
	    	
	    	if( self::loggingLevelSufficient($level) ){	

    			$level = strtoupper($level);
		        if(is_array($msg)){
		        	$msg = print_r($msg, true);
		        }
		        if(is_object($msg)){
		        	$msg = serialize($msg);
		        }
	    		
		        if( self::isLog2File() ){
		            if( self::loggingPossible() || self::isLoggingForced() ){ 
						if(version_compare(JVERSION,'1.7.0','ge')) {
							jimport('joomla.log.log'); // Include the log library (J1.7+)							
							JLog::addLogger(array('text_file' => $logFileName));
							JLog::add($msg, self::getLogEntryPriority($level));	
						} else {
							// Joomla! 1.6 and 1.5
				        	jimport('joomla.error.log'); // Include the log library
				        	$log = &JLog::getInstance($logFileName);
							$log->addEntry(array('comment' => $msg, 'level' => $level));	
						}			        			        
		            }
		        }
	            
		        if( self::isLog2Database() ){
		            $levelNr = self::getLoggingLevel($level);
		            self::log2Database($msg, $levelNr, $typeNr); // Log to database
		        }
	    	}
	    }
	    
	    public static function installLog($msg, $level){
	    	$installLogFile = 'mailster.install.log';
	    	if(self::loggingPossible($installLogFile)){    	
				if(version_compare(JVERSION,'1.7.0','ge')) {
					jimport('joomla.log.log'); // Include the log library (J1.7+)				
					JLog::addLogger(array('text_file' => $installLogFile));
					JLog::add($msg, self::getLogEntryPriority($level));	
				} else {
					// Joomla! 1.6 and 1.5 
					jimport('joomla.error.log'); // Include the log library
		        	$log = &JLog::getInstance($installLogFile); // Logger for Mailster's install log
					$log->addEntry(array('comment' => $msg, 'level' => $level));	
				}
	    	}
	    }
	    
	    public static function getAllLogEntries(){
	    	$db =& JFactory::getDBO();
	    	$query = 'SELECT * FROM #__mailster_log';
			$db->setQuery($query);
			$res = $db->loadObjectList();
			return $res;
	    }
	    
	    public static function log2Database($msg, $levelNr, $typeNr){
	    	$db =& JFactory::getDBO();	
		
			$query = 'INSERT INTO ' 
							. '#__mailster_log' 
							. '(id,' 
							. ' level,' 
							. ' type,' 
							. ' log_time,' 
							. ' msg' 
							. ' )VALUES'
							. ' (NULL,'
							. '  \'' . $levelNr . '\','
							. '  \'' . $typeNr . '\','
							. '  NOW(),'
							. ' ' . $db->quote($msg). ''
							. ')';
			$db->setQuery($query);
			$result = $db->query();
	    }	    
	    
	    public static function warning($msg, $typeNr=0)
	    {
	        self::log($msg, self::WARNING, $typeNr);
	    }
	    public static function error($msg, $typeNr=0)
	    {
	        self::log($msg, self::ERROR, $typeNr);
	    }
	    public static function debug($msg, $typeNr=0)
	    {
	        self::log($msg, self::DEBUG, $typeNr);
	    }
	    public static function info($msg, $typeNr=0)
	    {
	        self::log($msg, self::INFO, $typeNr);
	    }
	    
	    public static function getLoggingLevel($entryType){
	    	switch($entryType){
	    		case self::ERROR:
	    			return MstConsts::LOG_LEVEL_ERROR;
	    		case self::WARNING:
	    			return MstConsts::LOG_LEVEL_WARNING;
	    		case self::INFO:
	    			return MstConsts::LOG_LEVEL_INFO;
	    		case self::DEBUG:
	    			return MstConsts::LOG_LEVEL_DEBUG;
	    	}
	    	return 0;
	    }
	    
	    public static function getLogEntryPriority($entryType){
	    	// for Joomla 1.6+ only
	    	switch($entryType){
	    		case self::ERROR:
	    			return JLog::ERROR;
	    		case self::WARNING:
	    			return JLog::WARNING;
	    		case self::INFO:
	    			return JLog::INFO;
	    		case self::DEBUG:
	    			return JLog::DEBUG;
	    		default:
	    			return JLog::INFO;
	    	}
	    	return 0;
	    }
	    
	    public static function getLoggingLevelStr($entryTypeNr){
	    	switch($entryTypeNr){
	    		case MstConsts::LOG_LEVEL_ERROR:
	    			return JText::_( 'COM_MAILSTER_LOG_LEVEL_ERROR' );
	    		case MstConsts::LOG_LEVEL_WARNING:
	    			return JText::_( 'COM_MAILSTER_LOG_LEVEL_WARNING' );
	    		case MstConsts::LOG_LEVEL_INFO:
	    			return JText::_( 'COM_MAILSTER_LOG_LEVEL_INFO' );
	    		case MstConsts::LOG_LEVEL_DEBUG:
	    			return JText::_( 'COM_MAILSTER_LOG_LEVEL_DEBUG' );
	    	}
	    	return 0;
	    }
	    
	    public static function getLoggingTypeStr($typeNr){
	    	switch($typeNr){
	    		case MstConsts::LOGENTRY_INSTALLER:
	    			return JText::_( 'COM_MAILSTER_LOGENTRY_INSTALLER' );
	    		case MstConsts::LOGENTRY_PLUGIN:
	    			return JText::_( 'COM_MAILSTER_LOGENTRY_PLUGIN' );
	    		case MstConsts::LOGENTRY_MAIL_RETRIEVE:
	    			return JText::_( 'COM_MAILSTER_LOGENTRY_MAIL_RETRIEVE' );
	    		case MstConsts::LOGENTRY_MAIL_SEND:
	    			return JText::_( 'COM_MAILSTER_LOGENTRY_MAIL_SEND' );
	    	}
	    	return "";
	    }
	 
	    public static function loggingLevelSufficient($entryType){
			$mstConf = &MstFactory::getConfig();			
	    	$loggingLevel = $mstConf->getLoggingLevel(); // get current Logging Level
	    	if($loggingLevel > 0){ // when Logging Level is zero then nothing will be logged
		    	$entryLevel = self::getLoggingLevel($entryType); // get Level that this entry needs
	            if($entryLevel <= $loggingLevel){ 
	            	return true; // entry can be logged
	            }
	    	}
            return false; // entry may not be logged
		}
	    
	    public static function loggingPossible($logFileName = 'mailster.log'){
			$config =& JFactory::getConfig();			
            $logPath = $config->getValue('config.log_path') . DS;
            $logPath .= DS;
            $logFile = $logPath . $logFileName;
            
			if(!file_exists($logPath)){
            	return false;  // Log folder path wrong configured
            }
			if(!is_writable($logPath)){
            	return false;  // Directory not writable
            }
			if(file_exists($logFile) && !is_writable($logFile)){
            	return false;  // Log file exists but is not writable
            }
                        
            return true; // Directory writable, file may exist or not  
		}
	    
	    public static function isLoggingForced(){
			$mstConf = &MstFactory::getConfig();			
			return $mstConf->isLoggingForced();
	    }
		
	    public static function isLog2File(){
			$mstConf = &MstFactory::getConfig();			
			return $mstConf->isLog2File();
	    }
		
	    public static function isLog2Database(){
			$mstConf = &MstFactory::getConfig();			
			return $mstConf->isLog2Database();
	    }
	    
	}
?>
