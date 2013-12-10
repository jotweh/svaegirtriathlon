<?php
	/**
	 * @package Joomla
	 * @subpackage Mailster Profile Plugin
	 * @copyright (C) 2011 Holger Brandt IT Solutions
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
	
	class plgSystemMailsterprofile extends JPlugin {
		
		function onAfterInitialise(){
			$lang =& JFactory::getLanguage();
			$lang->load('com_mailster',JPATH_ADMINISTRATOR);
			require_once(JPATH_SITE  . DS . 'administrator' . DS . 'components' . DS . 'com_mailster' . DS . 'mailster' . DS . 'includes.php');
			$log = MstFactory::getLogger();
			
			$u = &JURI::getInstance(); // try to get params from URL
			$option = trim(strtolower($u->getVar('option')));
				
			if(strlen($option  < 1)){ // for SEF...
				$option = trim(strtolower(JRequest::getString('option')));
			}
			if( ($option === 'com_users') || ($option === 'com_user') ){
				$task = trim(strtolower(JRequest::getString('task')));
				if(($task === 'save') || ($task === 'apply') || ($task === 'user.save') || ($task === 'user.apply')){
					$app =& JFactory::getApplication();	
					$appName = trim(strtolower($app->getName()));
					if($appName === 'administrator'){
						$userId = $this->getBackendUserId();
						$this->onBackendContactSaveDetected($userId);
					}
					if($appName === 'site'){
						$user =& JFactory::getUser();
						if (!$user->guest) {
							$userId = $user->id;
							$this->onFrontendContactSaveDetected($userId);
						}
					}
				}
			}
		}
		
		function onAfterDispatch() {
			$app =& JFactory::getApplication();			
			if(trim(strtolower($app->getName())) === 'administrator') {
				$u = &JURI::getInstance(); // try to get params from URL
				$option = trim(strtolower($u->getVar('option')));
				$view  	= trim(strtolower($u->getVar('view')));
					
				if(strlen($option  < 1)){ // for SEF...
					$option = trim(strtolower(JRequest::getString('option')));
				}
				if(strlen($view  < 1)){ // for SEF...
					$view = trim(strtolower(JRequest::getString('view')));
				}
				if($option === 'com_users'){
					if($view === 'user'){
						$this->extendUserProfile();
					}
				}			
			}
		}
		
		function onAfterRoute(){
		}
	
		function onFrontendContactSaveDetected($userId){		
			$mstRecipients = & MstFactory::getRecipients();
			$mstRecipients->updateRecipientInLists($userId, 1);	// update cache states (to correct name/email)
		}
		
		function onBackendContactSaveDetected($userId){	
			$mstRecipients = & MstFactory::getRecipients();
			$subscribeUtils = &MstFactory::getSubscribeUtils();
    		$mailingListUtils = &MstFactory::getMailingListUtils();
    		$mLists = $mailingListUtils->getAllMailingLists();
			$mstRecipients->updateRecipientInLists($userId, 1);	// update cache states (to correct name/email)
    		for($i=0;$i<count($mLists);$i++){
    			$list = &$mLists[$i];
    			$subscribed = JRequest::getInt('list_'.$list->id);
    			if($subscribed > 0){
    				$subscribeUtils->subscribeUserId($userId, 1, $list->id);
    			}else{
    				$subscribeUtils->unsubscribeUserId($userId, 1, $list->id);
    			}
    		}
		}
		
		function getBackendUserId(){
			$userId = JRequest::getVar( 'cid', array(0), '', 'array' );
			$userId = $userId[0];
			if(!$userId || $userId <= 0){				
				$userId = JRequest::getInt('id',0);
			}
			return $userId;
		}
		
		function extendUserProfile(){
			$srchPattern = '';
			$userId = $this->getBackendUserId();
			
			$doc =& JFactory::getDocument();
			$srchPattern = '<div class="col width-55">';
			$srchPatternEnd = '</div>';
			$buffer = $doc->getBuffer('component');				
			
			$pos = strpos($buffer, $srchPattern);
			$pos2 = strpos($buffer, $srchPatternEnd, $pos);
			$part1 = substr($buffer, 0, $pos2);
			$part2 = substr($buffer, $pos2);
								
    		$html = '<fieldset class="adminform">
						<legend>Mailster</legend>
						<table class="admintable">
							<tbody>
								<tr>
									<td class="paramlist_key" width="40%">
									<span class="editlinktip">
									<label id="paramslanguage-lbl" class="hasTip" for="paramslanguage">'.JText::_( 'COM_MAILSTER_MAILING_LIST_SUBSCRIPTIONS' ).'</label>
									</span>
									</td>
									<td>&nbsp;</td></tr>';
    		$mailingListUtils = &MstFactory::getMailingListUtils();
    		$mLists = $mailingListUtils->getAllMailingLists();
    		
    		JLoader::import('joomla.application.component.model'); 
    		JLoader::import( 'user', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_mailster' . DS . 'models' );
    		$userModel = JModel::getInstance( 'user', 'MailsterModel' );
    		$memberInfo = $userModel->getMemberInfo($userId, 1); // get subscribe infos for Joomla user
    		
    		for($i=0;$i<count($mLists);$i++){
    			$list = $mLists[$i];
    			$subscribed = '';
    			$unsubscribed = 'checked="checked"';
    			if($this->isSubscribed($memberInfo, $list->id)){
    				$subscribed = $unsubscribed;
    				$unsubscribed = '';
    			}
    			$subscribedHtml = '<input type="radio" id="list_'.$list->id.'_0" name="list_'.$list->id.'" value="0" ' . $unsubscribed . '><label for="list_'.$list->id.'_0">'.JText::_( 'COM_MAILSTER_NOT_SUBSCRIBED' ).'</label><input type="radio" id="list_'.$list->id.'_1" name="list_'.$list->id.'" value="1" ' . $subscribed . '><label for="list_'.$list->id.'_1">'.JText::_( 'COM_MAILSTER_SUBSCRIBED' ).'</label>';
    			$html .= '<tr><td class="paramlist_key" width="40%">' . $list->name . '</td><td>' . $subscribedHtml . '</td></tr>';
    		}
    			$html .=	'</tbody>
						</table>
					</fieldset>';
    		$buffer = $part1 . $html . $part2;
			$doc->setBuffer($buffer, 'component');			
		}
		
		function isSubscribed($memberInfo, $listId){
			$lists = &$memberInfo['lists'];
			for($i=0;$i<count($lists);$i++){
				if($lists[$i]->id == $listId){
					return true;
				}				
			}
			$listGroups = &$memberInfo['listGroups'];
			for($i=0;$i<count($listGroups);$i++){
				if($listGroups[$i]->id == $listId){
					return true;
				}				
			}
			return false;
		}
		
	}
?>