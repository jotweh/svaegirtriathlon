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
	
	class MstNotifyUtils
	{
		
		public function MstNotifyUtils(){
			$notify = &MstFactory::getNotify(); // load class into classpath
		}
		
		public function createNewNotify(){
			$notify = new MstNotify(); // create a really new instance
			return $notify;
		}
		
		public function getNotifyMailTmpl($notify, $subject, $body, $replyTo, $listId=null){
			if(!is_null($listId)){
				$mailSender = &MstFactory::getMailSender();
				$mailingListUtils = &MstFactory::getMailingListUtils();
				$mList = $mailingListUtils->getMailingList($listId);
				$mail = $mailSender->getListMailTmpl($mList);
			}else{
				$mail =& JFactory::getMailer();					
				$mail->ClearAllRecipients();	  
			}
			$mail->addReplyTo($replyTo);
			$mail->FromName = 'Mailster';
			
			$mail->addCustomHeader(MstConsts::MAIL_HEADER_RETURN_PATH . ': <>'); // try to set return path to NULL
			$mail->addCustomHeader(MstConsts::MAIL_HEADER_AUTO_SUBMITTED . ': auto-generated'); // indicate this was generated and we do not want a response
		
			$recipients = $this->getNotifyRecipients($notify);
			$mail->SingleTo = true; // one mail per recipient
			
			for($j=0; $j<count($recipients); $j++){
				$recip = &$recipients[$j];
				$mail->AddAddress($recip->email, $recip->name); // add all recipients of this notify
			}
			
			$mail->setSubject($subject);
			$mail->setBody($body);	
			
			return $mail;
		}
		
		public function getSenderNotifyMailTmpl($senderName, $senderEmail, $subject, $body, $replyTo, $listId=null){
			if(!is_null($listId)){
				$mailSender = &MstFactory::getMailSender();
				$mailingListUtils = &MstFactory::getMailingListUtils();
				$mList = $mailingListUtils->getMailingList($listId);
				$mail = $mailSender->getListMailTmpl($mList);
			}else{
				$mail =& JFactory::getMailer();					
				$mail->ClearAllRecipients();	  
			}
			$mail->addReplyTo($replyTo);
			$mail->FromName = 'Mailster';
			
			$mail->addCustomHeader(MstConsts::MAIL_HEADER_RETURN_PATH . ': <>'); // try to set return path to NULL
			$mail->addCustomHeader(MstConsts::MAIL_HEADER_AUTO_SUBMITTED . ': auto-generated'); // indicate this was generated and we do not want a response
					
			$mail->AddAddress($senderEmail, $senderName); // add all recipients of this notify
			
			$mail->setSubject($subject);
			$mail->setBody($body);	
			
			return $mail;
		}
		
		public function getNotifyRecipients($notify){
			$db = & JFactory::getDBO();
			$recipients = array();
			if(is_null($notify->target_type)){
				return '';
			}
			switch($notify->target_type){
				case MstNotify::TARGET_TYPE_LIST_ADMIN:
					$listUtils = &MstFactory::getMailingListUtils();
					$mList = $listUtils->getMailingList($notify->list_id);
					$recip = new stdClass();
					$recip->email = $mList->admin_mail;
					$recip->name = '';
					$recipients[] = $recip;
					break;
				case MstNotify::TARGET_TYPE_JOOMLA_USER:
					$query = 'SELECT * FROM #__users WHERE id=\'' . $notify->user_id . '\''; // load from core users
					$db->setQuery( $query );
					$user = $db->loadObject();
					$recip = new stdClass();
					$recip->email = $user->email;
					$recip->name = $user->name;
					$recipients[] = $recip;
					break;
				case MstNotify::TARGET_TYPE_USER_GROUP:
					$groupUsersModel = & MstFactory::getModel('groupusers');
					$users = $groupUsersModel->getData($notify->group_id);
					$recipients = $users; // replace array
					break;
			}
			return $recipients;
		}
		
		public function notifyMatches($notify, $notifyType, $triggerType){
			if($notify->notify_type == $notifyType){
				if($notify->trigger_type == $triggerType){
					return true;
				}
			}
			return false;
		}
		
		public function storeNotify($notify){
			$log = & MstFactory::getLogger();	
			$db = & JFactory::getDBO();
			
			if($notify->id > 0){ // already existing, need to update	
				$query = 'UPDATE #__mailster_notifies SET '
							. ' notify_type =\'' . $notify->notify_type . '\','
							. ' trigger_type =\'' . $notify->trigger_type . '\','
							. ' target_type =\'' . $notify->target_type . '\','
							. ' list_id =\'' . $notify->list_id . '\','
							. ' user_id =\'' . $notify->user_id . '\','
							. ' group_id =\'' . $notify->group_id . '\''
							. ' WHERE id=\'' . $notify->id . '\'';		
				$db->setQuery( $query );
				$result = $db->query();		
				if(!$result){
					$log->error('Updating of notification failed, Error Nr: ' . $db->getErrorNum() . ', Message: ' . $db->getErrorMsg());
					return false;
				}else{
					$log->debug('Successfully updated ' . $notify->id);
					return true;
				}				
			}else{ // new notify
				$query = 'INSERT INTO #__mailster_notifies ('
							. ' id,'
							. ' notify_type,'
							. ' trigger_type,'
							. ' target_type,'
							. ' list_id,'
							. ' user_id,'
							. ' group_id'
						. ') VALUES ('
							. ' NULL, \'' 	
							. $notify->notify_type . '\', \''
						 	. $notify->trigger_type . '\', \'' 
						 	. $notify->target_type . '\', \'' 
						 	. $notify->list_id . '\', \'' 
						 	. $notify->user_id . '\', \'' 
						 	. $notify->group_id . '\''
						. ')'; 
				$db->setQuery($query);
				$result = $db->query();
				$notifyId = $db->insertid(); 
				if($notifyId < 1){
					$log->error('Failed to insert new notification, error: ' . $db->getErrorNum() . ', Message: ' . $db->getErrorMsg());
					return false;
				}else{
					$log->debug('New notify id: ' . $notifyId);
					return true;
				}
			}
			return false;
		}
		
		
		
		public function deleteNotify($notifyId){
			$log = & MstFactory::getLogger();	
			$log->debug('Deleting notify ' . $notifyId . '...');
			$db = & JFactory::getDBO();
			$query = ' DELETE '
					. ' FROM #__mailster_notifies'
					. ' WHERE id = \'' . $notifyId . '\'';
			$db->setQuery( $query );
			$result = $db->query();
			$affRows = $db->getAffectedRows();
			if($affRows > 0){
				$log->debug('Successfully deleted notify ' . $notifyId);
				return true;
			}
			return false;
		}
		
		public function getNotifiesOfMailingList($listId){
			$db =& JFactory::getDBO();
			$query = 'SELECT * FROM #__mailster_notifies '
					. ' WHERE list_id =\'' . $listId . '\'';
			$db->setQuery( $query );
			$notifies = $db->loadObjectList();
			
			for($i=0; $i<count($notifies); $i++){
				$dbNotify = &$notifies[$i];
				$notify = $this->createNewNotify();
				foreach ($dbNotify as $varName => $val) { // add db data to instance
		            $notify->$varName = $val;
		        }
		        $notifies[$i] = $notify;
			}
			
			return $notifies;	
		}
		
		public function getNotifyTypeStr($notifyType){
			if(!is_null($notifyType)){
				switch($notifyType){
					case MstNotify::NOTIFY_TYPE_GENERAL:
						return JText::_( 'COM_MAILSTER_GENERAL_NOTIFICATION' );
						break;
					case MstNotify::NOTIFY_TYPE_LIST_BASED:
						return JText::_( 'COM_MAILSTER_MAILING_LIST_RELATED_NOTIFICATION' );
						break;
				}
			}
			return '';
		}
		public function getTriggerTypeStr($triggerType){
			if(!is_null($triggerType)){
				switch($triggerType){
					case MstEventTypes::NEW_LIST_MAIL:
						return JText::_( 'COM_MAILSTER_NEW_MAILING_LIST_MAIL' );
						break;
					case MstEventTypes::NEW_BLOCKED_MAIL:
						return JText::_( 'COM_MAILSTER_NEW_BLOCKED_SENDER_MAIL' );
						break;
					case MstEventTypes::NEW_BOUNCED_MAIL:
						return JText::_( 'COM_MAILSTER_NEW_BOUNCED_MAIL' );
						break;
					case MstEventTypes::NEW_FILTERED_MAIL:
						return JText::_( 'COM_MAILSTER_NEW_FILTERED_MAIL' );
						break;
					case MstEventTypes::USER_SUBSCRIBED_ON_WEBSITE:
						return JText::_( 'COM_MAILSTER_USER_SUBSCRIBED_ON_WEBSITE' );
						break;
					case MstEventTypes::USER_UNSUBSCRIBED_ON_WEBSITE:
						return JText::_( 'COM_MAILSTER_USER_UNSUBSCRIBED_ON_WEBSITE' );
						break;
					case MstEventTypes::SEND_ERROR:
						return JText::_( 'COM_MAILSTER_SEND_ERROR' );
						break;
				}
			}
			return '';
		}
		
		public function getAvailableTriggerTypes(){
			$triggerTypes = array();
			$triggers = MstEventTypes::getAllTriggerTypes();			
			foreach ($triggers as $t) {
				$trigger = new stdClass();
				$trigger->type = $t;
				$trigger->name = $this->getTriggerTypeStr($t);
				$triggerTypes[] = $trigger;
			}
			return $triggerTypes;
		}
		
		public function getTargetTypeStr($targetType){
			if(!is_null($targetType)){
				switch($targetType){
					case MstNotify::TARGET_TYPE_LIST_ADMIN:
						return JText::_( 'COM_MAILSTER_MAILING_LIST_ADMIN' );
						break;
					case MstNotify::TARGET_TYPE_JOOMLA_USER:
						return JText::_( 'COM_MAILSTER_JOOMLA_USER' );
						break;
					case MstNotify::TARGET_TYPE_USER_GROUP:
						return JText::_( 'COM_MAILSTER_USER_GROUP' );
						break;
				}
			}
			return '';
		}	
		
		public function getAvailableTargetTypes(){
			$targetTypes = array();
			$targets = array();
			$targets[] = MstNotify::TARGET_TYPE_JOOMLA_USER;
			$targets[] = MstNotify::TARGET_TYPE_LIST_ADMIN;
			$targets[] = MstNotify::TARGET_TYPE_USER_GROUP;
			
			foreach ($targets as $t) {
				$target = new stdClass();
				$target->type = $t;
				$target->name = $this->getTargetTypeStr($t);
				$targetTypes[] = $target;
			}
			return $targetTypes;
		}
		
	}

?>
