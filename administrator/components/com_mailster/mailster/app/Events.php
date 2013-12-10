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
	
	class MstEvents
	{
	
		public static function sendError($mailId, $errorMsg){
			$mailUtils = &MstFactory::getMailUtils();
			$mailingListUtils = &MstFactory::getMailingListUtils();
			$mail = $mailUtils->getMail($mailId);
			$mList = $mailingListUtils->getMailingList($mail->list_id);
			$subject = JText::sprintf( 'COM_MAILSTER_SEND_ERROR_NOTIFICATION_SUBJECT_WITH_MAILING_LIST', $mList->name);
			$body = JText::sprintf('COM_MAILSTER_SEND_ERROR_NOTIFICATION_BODY_WITH_MAIL_SUBJECT_AND_ERROR_MESSAGE', $mail->subject, $errorMsg);
			$triggerType = MstEventTypes::SEND_ERROR;
			self::newMailingListEvent($mail->list_id, $triggerType, $subject, $body);
		}
		
		public static function newMailingListMail($mailId){
			$mailUtils = &MstFactory::getMailUtils();
			$mailingListUtils = &MstFactory::getMailingListUtils();
			$mail = $mailUtils->getMail($mailId);
			$mList = $mailingListUtils->getMailingList($mail->list_id);
			$subject = JText::sprintf( 'COM_MAILSTER_NEW_MAILING_LIST_MAIL_NOTIFICATION_SUBJECT_WITH_MAILING_LIST', $mList->name);
			$body = JText::sprintf('COM_MAILSTER_NEW_MAILING_LIST_MAIL_NOTIFICATION_BODY_WITH_MAIL_SUBJECT', $mail->subject);
			$triggerType = MstEventTypes::NEW_LIST_MAIL;
			self::newMailingListEvent($mail->list_id, $triggerType, $subject, $body);
		}
		
		public static function newBouncedMail($mailId){	
			$mailUtils = &MstFactory::getMailUtils();
			$mailingListUtils = &MstFactory::getMailingListUtils();
			$mail = $mailUtils->getMail($mailId);
			$mList = $mailingListUtils->getMailingList($mail->list_id);
			$subject = JText::sprintf( 'COM_MAILSTER_NEW_BOUNCED_MAIL_NOTIFICATION_SUBJECT_WITH_MAILING_LIST', $mList->name);
			$body = JText::sprintf( 'COM_MAILSTER_NEW_BOUNCED_MAIL_NOTIFICATION_BODY_WITH_MAIL_SUBJECT', $mail->subject);
			$triggerType = MstEventTypes::NEW_BOUNCED_MAIL;
			self::newMailingListEvent($mail->list_id, $triggerType, $subject, $body);
		}
		
		public static function newBlockedMail($mailId){	
			$mailUtils = &MstFactory::getMailUtils();
			$mailingListUtils = &MstFactory::getMailingListUtils();
			$mail = $mailUtils->getMail($mailId);
			$mList = $mailingListUtils->getMailingList($mail->list_id);
			$subject = JText::sprintf( 'COM_MAILSTER_NEW_BLOCKED_MAIL_NOTIFICATION_SUBJECT_WITH_MAILING_LIST', $mList->name);
			$body = JText::sprintf( 'COM_MAILSTER_NEW_BLOCKED_MAIL_NOTIFICATION_BODY_WITH_MAIL_SUBJECT', $mail->subject);
			$triggerType = MstEventTypes::NEW_BLOCKED_MAIL;
			self::newMailingListEvent($mail->list_id, $triggerType, $subject, $body);
		}
		
		public static function newFilteredMail($mailId){
			$mailUtils = &MstFactory::getMailUtils();
			$mailingListUtils = &MstFactory::getMailingListUtils();
			$mail = $mailUtils->getMail($mailId);	
			$mList = $mailingListUtils->getMailingList($mail->list_id);
			$subject = JText::sprintf( 'COM_MAILSTER_NEW_FILTERED_MAIL_NOTIFICATION_SUBJECT_WITH_MAILING_LIST', $mList->name);
			$body = JText::sprintf( 'COM_MAILSTER_NEW_FILTERED_MAIL_NOTIFICATION_BODY_WITH_MAIL_SUBJECT', $mail->subject);
			$triggerType = MstEventTypes::NEW_FILTERED_MAIL;
			self::newMailingListEvent($mail->list_id, $triggerType, $subject, $body);
		}
		
		public static function userSubscribedOnWebsite($name, $email, $listId){	
			$mailingListUtils = &MstFactory::getMailingListUtils();
			$mList = $mailingListUtils->getMailingList($listId);
			$subject = JText::sprintf( 'COM_MAILSTER_USER_SUBSCRIBED_ON_WEBSITE_NOTIFICATION_SUBJECT_WITH_MAILING_LIST', $mList->name);
			$body = JText::sprintf('COM_MAILSTER_USER_SUBSCRIBED_ON_WEBSITE_NOTIFICATION_BODY_WITH_LIST_NAME_AND_USER_NAME_AND_EMAIL', $name, $email, $mList->name);
			$triggerType = MstEventTypes::USER_SUBSCRIBED_ON_WEBSITE;
			self::newMailingListEvent($listId, $triggerType, $subject, $body);
		}
		
		public static function userUnsubscribedOnWebsite($email, $listId){	
			$mailingListUtils = &MstFactory::getMailingListUtils();
			$mList = $mailingListUtils->getMailingList($listId);
			$subject = JText::sprintf( 'COM_MAILSTER_USER_UNSUBSCRIBED_ON_WEBSITE_NOTIFICATION_SUBJECT_WITH_MAILING_LIST', $mList->name);
			$body = JText::sprintf('COM_MAILSTER_USER_UNSUBSCRIBED_ON_WEBSITE_NOTIFICATION_BODY_WITH_LIST_NAME_AND_EMAIL', $email, $mList->name);
			$triggerType = MstEventTypes::USER_UNSUBSCRIBED_ON_WEBSITE;
			self::newMailingListEvent($listId, $triggerType, $subject, $body);
		}
		
		private static function newMailingListEvent($listId, $triggerType, $subject, $body){
			$log = & MstFactory::getLogger();
			$mailUtils = &MstFactory::getMailUtils();
			$notifyUtils = &MstFactory::getNotifyUtils();
			$mailingListUtils = &MstFactory::getMailingListUtils();
			
			$mstApp = & MstFactory::getApplication();	
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
			
			if($pHashOk && $plgHashOk && !$isFree){
			
				$mailingListNotifies = $notifyUtils->getNotifiesOfMailingList($listId);
				$mList = $mailingListUtils->getMailingList($listId);
				$replyTo = array($mList->admin_mail, '');
				$log->debug(print_r($mailingListNotifies, true));			
				for($i=0; $i<count($mailingListNotifies); $i++){
					$notify = $mailingListNotifies[$i];
					if($notifyUtils->notifyMatches($notify, MstNotify::NOTIFY_TYPE_LIST_BASED, $triggerType)){
						$log->debug('Notify will be sent out: ' . $notifyUtils->getTriggerTypeStr($triggerType));
						$mail = $notifyUtils->getNotifyMailTmpl($notify, $subject, $body, $replyTo, $listId);
						$sendOk = $mail->Send(); // send notificaton
						$error =  $mail->IsError();
						if($error == true) { // send errors?
							$log->error('Sending of notify failed! Last error: ' . $mail->ErrorInfo);
						}	
					}
				}
			}
		}
		
		public static function mailIsNotForwarded($listId, $subject, $senderName, $senderEmail, $senderBlocked=false, $emailFilteredByWords=false, $emailTooLarge=false){	
			$log = & MstFactory::getLogger();
			$mailUtils = &MstFactory::getMailUtils();
			$notifyUtils = &MstFactory::getNotifyUtils();
			$mailingListUtils = &MstFactory::getMailingListUtils();
						
			$mList = $mailingListUtils->getMailingList($listId);
			
			if($mList->notify_not_fwd_sender == 1){
				$log->debug('Notification to sender of not fowarded email will be sent out');
				$replyTo = array($mList->admin_mail, '');
				
				if($senderBlocked){	
					$cause = JText::sprintf( 'COM_MAILSTER_SENDER_BLOCKED_AS_NOT_ALLOWED_TO_SEND_TO_LIST_WITH_SENDER_X', $senderEmail );
				}elseif($emailFilteredByWords){
					$cause = JText::_( 'COM_MAILSTER_FILTERED_MAIL_BECAUSE_OF_CONTENT' );					
				}elseif($emailTooLarge){
					$maxEmailSize = $mList->mail_size_limit;
					$cause = JText::sprintf( 'COM_MAILSTER_EMAIL_SIZE_LIMIT_OF_X_KB_WAS_EXCEEDED', $maxEmailSize );					
				}else{ // Unknown cause?! Whatever, notify sender anyway 					
					$cause = JText::_( 'COM_MAILSTER_MAIL_NOT_FORWARDED' );
				}
				
				$log->debug('Cause for not forwarded email: ' . $cause);
				
				$notificationSubject = JText::sprintf( 'COM_MAILSTER_MAIL_NOT_FORWARDED_NOTIFICATION_FOR_SENDER_SUBJECT_WITH_MAILING_LIST', $mList->name);
				$notificationBody = JText::sprintf('COM_MAILSTER_MAIL_NOT_FORWARDED_NOTIFICATION_FOR_SENDER_BODY_WITH_MAIL_SUBJECT_AND_CAUSE', $subject, $cause);

				$mail = $notifyUtils->getSenderNotifyMailTmpl($senderName, $senderEmail, $notificationSubject, $notificationBody, $replyTo, $listId);
				$log->debug('Prepared notify mail: ' . print_r($mail, true));
				$sendOk = $mail->Send(); // send notificaton
				$error =  $mail->IsError();
				if($error == true) { // send errors?
					$log->error('Sending of notify failed! Last error: ' . $mail->ErrorInfo);
				}else{
					$log->debug('Sender was successfully notified');
				}
			}
		}		
		
	}

?>
