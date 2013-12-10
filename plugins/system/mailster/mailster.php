<?php
	/**
	 * @package Joomla
	 * @subpackage Mailster Plugin
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
	
	jimport('joomla.event.plugin');
	jimport('joomla.filesystem.folder');
		
	require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'includes.php');
	require_once(dirname(__FILE__) . DS . 'mailster' . DS . 'utils.php' );
	
	class plgSystemMailster extends JPlugin {
		var $execEnd;
		var $jPlugin;
		var $mstPlugin;
		var $pluginParams;
		var $minDuration;
		var $maxExecTime;
		var $plgId;
		
		private function initMailPlugin(){	
			$time = time();	
			$pluginUtils = & MstFactory::getPluginUtils();
			$this->jPlugin = & JPluginHelper::getPlugin( 'system', 'mailster' );	
			$this->mstPlugin = $pluginUtils->getPlugin('mailster', 'system');
			
						
			$this->pluginParams = $pluginUtils->getMailPluginParameter();
			$this->minDuration = $this->pluginParams->def( 'minduration', '2' );
			$this->maxExecTime = $this->pluginParams->def( 'maxexectime', '10' );			
			$this->execEnd = $time + $this->maxExecTime;	
			
			
			$lang =& JFactory::getLanguage();
			$lang->load('com_mailster',JPATH_ADMINISTRATOR);
			
			if(version_compare(JVERSION,'1.6.0','ge')) {
				// Joomla! 1.6 / 1.7 / ...
				$this->plgId = $this->mstPlugin->extension_id;
			} else {
				// Joomla! 1.5 
				$this->plgId = $this->mstPlugin->id;
			}	
		}
				
		function onAfterInitialise() {	
			$log 			= & MstFactory::getLogger();	
			$senderObj 		= & MstFactory::getMailSender();	
			$retrieverObj 	= & MstFactory::getMailRetriever();
			$pluginUtils 	= & MstFactory::getPluginUtils();
			
			$this->initMailPlugin();
			
			
			if($this->isPluginRunAllowed()){
				$log->debug('--- --- --- --- --- Plugin run allowed (config. trigger source: ' . $this->pluginParams->def( 'trigger_source', 'all' ) . ') --- --- --- --- ---', MstConsts::LOGENTRY_PLUGIN);
				if($this->isMailRetrievingRequired()){
					$log->debug('Time left to run: ' . $this->timeLeft() 
								. ' for retrieving mails (execEnd: ' 
								. $this->execEnd . ', now: ' . time() . '), PHP max. exec: ' . ini_get('max_execution_time'), MstConsts::LOGENTRY_PLUGIN);						
										
					$retrieverObj->retrieveMailsOfActiveMailingLists($this->minDuration, $this->execEnd);
					
					$this->pluginParams->set('last_exec_retrieve', time());
					$pluginUtils->updatePluginParams($this->plgId, $this->pluginParams->toString());
					
					if($this->isMailRetrievingRequired()){
						$lastExecRetrieve 	= 	$this->pluginParams->def('last_exec_retrieve', -1);
						$timeSinceLastRetrieve 	= (time() - $lastExecRetrieve);
						$log->warning('Mail retrieving still required after reset, last exec: ' . $lastExecRetrieve
									 . ', time since last exec: ' . $timeSinceLastRetrieve, MstConsts::LOGENTRY_PLUGIN);
					}
					
				}				
				
				if($this->isMailSendingRequired()){
					$log->debug(($this->isTimeLeft() ? ('Time left to run: ' . $this->timeLeft() . ' for sending mails (execEnd: ' 
															. $this->execEnd . ', now: ' . time() 
															. '), PHP max. exec: ' . ini_get('max_execution_time') )
													: 'No time left for sending mails'), MstConsts::LOGENTRY_PLUGIN);
					if($this->isTimeLeft()){
						$senderObj->sendMails($this->minDuration, $this->execEnd);
						
						$this->pluginParams->set('last_exec_sending', time());
						$pluginUtils->updatePluginParams($this->plgId, $this->pluginParams->toString());
						
						if($this->isMailSendingRequired()){
							$lastExecSending 	= 	$this->pluginParams->def('last_exec_sending', -1);
							$timeSinceLastSending	= (time() - $lastExecSending);
							$log->warning('Mail sending still required after reset, last exec: ' . $lastExecSending 
										. ', time since last exec: ' . $timeSinceLastSending, MstConsts::LOGENTRY_PLUGIN);
						}
						
					}	
				}		
				
			}else{			
				if($this->isNoExecutionFlagSet()){
					$log->debug('*** *** *** *** *** *** Plugin run NOT allowed (no execution flag set) *** *** *** *** *** ***', MstConsts::LOGENTRY_PLUGIN);
				}elseif($this->isCronjobRunning()){
					$log->debug('*** *** *** *** *** *** Plugin run NOT allowed (cronjob running) *** *** *** *** *** ***', MstConsts::LOGENTRY_PLUGIN);
				}elseif($this->isInstallationRunning()){
					$log->debug('*** *** *** *** *** *** Plugin run NOT allowed (installation running) *** *** *** *** *** ***', MstConsts::LOGENTRY_PLUGIN);
				}else{	
					$log->debug('*** *** *** *** *** *** Plugin run NOT allowed (config. trigger source: ' . $this->pluginParams->def( 'trigger_source', 'all' ) . ') *** *** *** *** *** ***', MstConsts::LOGENTRY_PLUGIN);
				}
			}			
			return true; // Job done
		}
		
		private function isTimeLeft(){
			$timeLeft = $this->timeLeft();
			return ($timeLeft > $this->minDuration);
		}
		
		private function timeLeft(){	
			$tNow = time();		
			$t1 =  $this->execEnd-$tNow;
			if($t1 < -30000){ // is time incorrectly considered negative?	
				$log->warning('Timestamp negative (tNow: ' . $tNow . ', t1: ' . $t1 . ', execEnd: ' . $this->execEnd . ')', MstConsts::LOGENTRY_PLUGIN);			
				$t1 = $this->minDuration+3; // add 3 seconds to have more than min duration
			}
			return $t1;
		}
		
		private function isPluginRunAllowed(){
			if($this->isInstallationRunning()) return false; // check for running installation
			if($this->isNoExecutionFlagSet()) return false; // check for manual set no execution flag
			if($this->isCronjobRunning()) return false; // check for running cronjob
			
			$app = & JFactory::getApplication();
			$triggerSrc = $this->pluginParams->def( 'trigger_source', 'all' );
			
			$triggerSrcOk = false;
			
			if($triggerSrc === MstConsts::PLUGIN_TRIGGER_SRC_ALL){
				$triggerSrcOk = true;
			}elseif($triggerSrc === MstConsts::PLUGIN_TRIGGER_SRC_BACKEND){
				$triggerSrcOk = ($app->isAdmin() ? true : false);
			}elseif($triggerSrc === MstConsts::PLUGIN_TRIGGER_SRC_CRONJOB){
				$triggerSrcOk = false; // do not execute during (Pro) cronjobs
			}			
			return $triggerSrcOk;
		}
						
		private function isNoExecutionFlagSet(){
			$noExecution = JRequest::getBool(MstConsts::PLUGIN_FLAG_NO_EXECUTION);
			return $noExecution;
		}		
		
		private function isInstallationRunning(){
			$u = &JURI::getInstance(); // try to get params from URL
			$option = trim(strtolower($u->getVar('option')));
				
			if(strlen($option  < 1)){ // for SEF...
				$option = trim(strtolower(JRequest::getString('option')));
			}
			
			if($option === 'com_installer'){
				return true; // we are in com_installer and paranoid - just exit without checking task...
			}
			return false;
		}
						
		private function isCronjobRunning(){
			$app = & JFactory::getApplication();
			if($app->isSite()){
				$controller = JRequest::getString('controller');
				if(strtolower($controller) === 'cron'){
					return true; // cron job active
				}
			}
			return false;
		}
		
		private function isMailRetrievingRequired(){
			$log = & MstFactory::getLogger();	
			$minCheckTime 		= 	$this->pluginParams->def('minchecktime', 240);
			$lastExecRetrieve 	= 	$this->pluginParams->def('last_exec_retrieve', -1);
			
			$req = $this->runRequired($minCheckTime, $lastExecRetrieve);
			$log->debug('Mail retrieving req: ' . ($req ? 'yes' : 'no') 
						. ', last retrieve: ' . $lastExecRetrieve . ', now: ' . time()
						. ', min check time: ' . $minCheckTime, MstConsts::LOGENTRY_PLUGIN);
			return $req;		
		}
		
		private function isMailSendingRequired(){
			$log = & MstFactory::getLogger();	
			$minSendTime		= 	$this->pluginParams->def('minsendtime', 60);
			$lastExecSending 	= 	$this->pluginParams->def('last_exec_sending', -1);
			
			$req = $this->runRequired($minSendTime, $lastExecSending);
			$log->debug('Mail sending req: ' . ($req ? 'yes' : 'no') 
						. ', last sending: ' . $lastExecSending . ', now: ' . time()
						. ', min send time: ' . $minSendTime, MstConsts::LOGENTRY_PLUGIN);
			return $req;
		}
		
		private function runRequired($minDiff, $lastExec){
			$log = & MstFactory::getLogger();			
			$timeSinceLastExec	= (time() - $lastExec);
			if($lastExec < 0){
				$log->warning('Last exec timestamp negative: ' . $lastExec, MstConsts::LOGENTRY_PLUGIN);
			}
			if($timeSinceLastExec < 0){
				$log->warning('Time since last exec negative: ' . $timeSinceLastExec, MstConsts::LOGENTRY_PLUGIN);
			}
			return ( ($lastExec < 0) || ($timeSinceLastExec >= $minDiff) );
		}
	
	}
?>
