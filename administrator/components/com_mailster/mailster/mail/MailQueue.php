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

class MstMailQueue
{		
	
	
	
	public static function saveAndEnqueueMail($mail, $mList){
		$log 		= & MstFactory::getLogger();
		$mstConf 	= & MstFactory::getConfig();
		
		$enqueuingStart = time();
		
		if(empty($mail->thread_id)){
			$mail->thread_id = 0;
		}
		
		$db =& JFactory::getDBO();
		$query = ' INSERT' 
				. ' INTO #__mailster_mails'
				. ' (id,'
				. ' list_id,'
				. ' thread_id,'
				. ' receive_timestamp,'
				. ' hashkey,'
				. ' message_id,'
				. ' in_reply_to,'
				. ' references_to,'
				. ' from_name,'
				. ' from_email,'
				. ' subject,'
				. ' body,'
				. ' html,'
				. ' has_attachments,'
				. ' fwd_errors, fwd_completed,'
				. ' blocked_mail, bounced_mail,'
				. ' fwd_completed_timestamp)'
				. ' VALUES'
				. ' (NULL,'
				. ' \'' . $mList->id . '\','
				. ' \'' . $mail->thread_id . '\','
				. ' \'' . $mail->receive_timestamp . '\','
				. ' ' . $db->quote($mail->hashkey) . ','
				. ' ' . $db->quote($mail->message_id) . ','
				. ' ' . $db->quote($mail->in_reply_to) . ','
				. ' ' . $db->quote($mail->references_to). ','
				. ' ' . $db->quote($mail->from_name) . ',' 
				. ' ' . $db->quote($mail->from_email) . ','
				. ' ' . $db->quote($mail->subject) . ','
				. ' ' . $db->quote($mail->body) . ',' 
				. ' ' . $db->quote($mail->html) . ',' 
				. ' ' . $db->quote($mail->has_attachments) . ','				
				. ' \'0\', \'0\','				
				. ' \'0\', \'0\','
				. ' \'0000-00-00 00:00:00\')';	
		$db->setQuery($query);
		$result = $db->query(); // save email to database
		$mailId = $db->insertid(); 
		if($mailId < 1){
			$log->error('Inserting of mail failed, Error Nr: ' . $db->getErrorNum() . ', Message: ' . $db->getErrorMsg());
		}else{
			$log->info('Saved mail for enqueuing ' . $mail->subject . '  new id: ' . $mailId);	
		}
		
		$mail->id = $mailId;
		$mail->list_id = $mList->id;
		
		// ####### TRIGGER NEW EVENT #######
		$mstEvents = &MstFactory::getEvents();
		$mstEvents->newMailingListMail($mail->id);
		// #################################
		
		self::enqueueMail($mail, $mList);

		return $mailId;
	}
	
	public static function resetMailAsUnblockedAndUnsent($mailId){		
		$log 		= & MstFactory::getLogger();
		$log->debug('Resetting mail status of mail ' . $mailId . ' to unsent');
		$db =& JFactory::getDBO();
		$query = ' UPDATE #__mailster_mails SET'
				. ' fwd_completed_timestamp=NULL,'
				. ' bounced_mail = \'0\','
				. ' blocked_mail = \'0\','
				. ' fwd_errors = \'0\','
				. ' fwd_completed = \'0\''				
				. ' WHERE id=\'' . $mailId . '\'';	
		$db->setQuery($query);
		$result = $db->query(); // save email to database	
		if(!$result){
			$log->error('Resetting of mail status failed, Error Nr: ' . $db->getErrorNum() . ', Message: ' . $db->getErrorMsg());
		}
	}
	
	public static function enqueueMail($mail, $mList){			
		$log 			= & MstFactory::getLogger();
		$mstConf 		= & MstFactory::getConfig();
		$mstApp 		= & MstFactory::getApplication();
		$sender			= & MstFactory::getMailSender();
		$mstRecipients 	= & MstFactory::getRecipients();
		
		$listId = $mList->id;
		$mailId = $mail->id;
		
		$log->debug('Enqueue mail ' . $mailId . ' in list ' . $mailId);
		
		$recipients = $mstRecipients->getRecipients($listId);
		$recipCount = count($recipients);
		$recC =  $mstApp->getRecC('com_mailster');
		if( (is_null($recC)) || (strlen(trim($recC)) == 0) || ($recC <= 0) ){
			$recC = octdec('62');
			$log->info('Had to correct recC, could not be retrieved, recC now: ' . $recC);
		}
		$log->info('Enqueuing recipients, count: ' . $recipCount);	
		if($recipCount > $recC){
			$sender->sendMail2ListAdmin($mList, JText::_( 'COM_MAILSTER_TOO_MUCH_RECIPIENTS_MAIL_SUBJECT' ), JText::_( 'COM_MAILSTER_TOO_MUCH_RECIPIENTS_MAIL_BODY' ));
			$log->error('Too many recipients error,  recipCount: ' . $recipCount . ',  recC: ' . $recC);	
		}		
		if(!$mstApp->checkPluginProductHashes()){
			$sender->sendMail2ListAdmin($mList, JText::_( 'COM_MAILSTER_PRODUCT_MODIFIED_SUBJECT' ), JText::_( 'COM_MAILSTER_PRODUCT_MODIFIED_BODY' ));
			$log->error('Product modified error');		
		}
		
		$db =& JFactory::getDBO();
		$nrInsertsPerQuery = MstConsts::DB_QUEUED_INSERTS_PER_QUERY;

		$log->debug('Copy to sender: ' . ( ($mList->copy_to_sender == 1) ? 'yes' : 'no') );
		
		$query = '';
		$validRecipNr = -1;
		for($i = 0; $i < $recipCount; $i++) {
			$recipient = &$recipients[$i];
			$isValidRecip = self::isValidRecipient($mail, $recipient, $mList);
			if($isValidRecip){
				$validRecipNr = $validRecipNr + 1; // increment recipient nr, first time bringing it to = 0
				$log->debug('enqueueMail: Valid recipient ' . $recipient->email);	
			    if($validRecipNr%$nrInsertsPerQuery == 0){	// if == 0 or can be divisible
					if($validRecipNr > 0){				
						$db->setQuery($query);
						$result = $db->query();	
					}
					$query = ' INSERT' 
							. ' INTO #__mailster_queued_mails'
							. ' (mail_id, name, email, error_count, lock_id, is_locked)'
							. ' VALUES'
							. ' (\''. $mailId . '\','
							. ' ' . $db->quote($recipient->name)  . ',' 
							. ' ' . $db->quote($recipient->email) . ','
							. ' \'0\', \'0\', \'0\'' 
							. ' )';
				 }else{
					$query = $query
							. ', (\''. $mailId . '\','
							. ' ' . $db->quote($recipient->name)  . ',' 
							. ' ' . $db->quote($recipient->email) . ','
							. ' \'0\', \'0\', \'0\'' 
							. ' )';						
				 }
			}else{
				$log->debug('enqueueMail: Do not enqueue, no valid recipient: ' . $recipient->email);	
			}
		}
		if($query != ''){			
			$db->setQuery($query); // we have to execute once again as still one ore more recipients are queued
			$result = $db->query();		
		}
	}
	
	
	public static function isValidRecipient($mail, $recipient, $mList){ 
		$log = & MstFactory::getLogger();
		$recipientIsSender = strtolower(trim($recipient->email)) === strtolower(trim($mail->from_email));
		$copy2Sender = ($mList->copy_to_sender == 1 ? true : false);
		$senderVsRecipientValid = ( !$recipientIsSender || ( $recipientIsSender && $copy2Sender) );
		if(!empty($mail->to_email)){
			$isToRecipient = strtolower(trim($recipient->email)) === strtolower(trim($mail->to_email));
		}else{
			$isToRecipient = false;
		}
		$ccAndToAddressing = ($mList->reply_to_sender == 2 ? true : false);
		$addressingValid = ( !$ccAndToAddressing || ( $ccAndToAddressing && !$isToRecipient) );
		
		$recipientValid = ($senderVsRecipientValid && $addressingValid);
		if($recipientValid){
			$log->debug('recip ' . $recipient->email . ' valid');
		}else{
			$log->debug('recip ' . $recipient->email . ' INVALID'
						. ' - Reason: senderVsRecipientValid: ' . ($senderVsRecipientValid ? 'yes' : 'no') 
						. ', addressingValid: ' . ($addressingValid ? 'yes' : 'no')
						. ', ccAndToAddressing: ' . ($ccAndToAddressing ? 'yes' : 'no') 
						. ', isToRecipient:  ' . ($isToRecipient ? 'yes' : 'no')
						. ', recipientIsSender: ' . ($recipientIsSender ? 'yes' : 'no')
						. ', copy2Sender: ' . ($copy2Sender ? 'yes' : 'no')
						);
		}
		return $recipientValid;
	}
	
	
	
	public static function saveNonQueueMail($mail, $mList, $blocked, $bounced, $filtered){
		$log = & MstFactory::getLogger();
		// The blocked_mail field has the following coding:
		// 0 - not blocked
		// 1 - blocked because of not authorized sender
		// 2 - mail filtered
		$blockedMail = $blocked ? MstConsts::MAIL_FLAG_BLOCKED_BLOCKED: MstConsts::MAIL_FLAG_BLOCKED_NOT_BLOCKED;
		$blockedMail = $filtered ? MstConsts::MAIL_FLAG_BLOCKED_FILTERED : $blockedMail;
		$bouncedMail = $bounced ? MstConsts::MAIL_FLAG_BOUNCED_BOUNCED : MstConsts::MAIL_FLAG_BOUNCED_NOT_BOUNCED;
		$db =& JFactory::getDBO();
		$query = ' INSERT' 
				. ' INTO #__mailster_mails'
				. ' (id, list_id, receive_timestamp,'
				. ' hashkey,'
				. ' message_id,'
				. ' in_reply_to,'
				. ' references_to,'
				. ' from_name,'
				. ' from_email,'
				. ' subject,'
				. ' body,'
				. ' html,'
				. ' has_attachments,'
				. ' fwd_errors, fwd_completed,'
				. ' blocked_mail, bounced_mail,'
				. ' fwd_completed_timestamp)'
				. ' VALUES'
				. ' (NULL, \''. $mList->id . '\', \'' . $mail->receive_timestamp . '\','
				. ' ' . $db->quote($mail->hashkey) . ','
				. ' ' . $db->quote($mail->message_id) . ','
				. ' ' . $db->quote($mail->in_reply_to) . ','
				. ' ' . $db->quote($mail->references_to). ','
				. ' ' . $db->quote($mail->from_name) . ',' 
				. ' ' . $db->quote($mail->from_email) . ','
				. ' ' . $db->quote($mail->subject) . ','
				. ' ' . $db->quote($mail->body) . ',' 
				. ' ' . $db->quote($mail->html) . ',' 
				. ' ' . $db->quote($mail->has_attachments) . ','			
				. ' \'0\', \'0\','				
				. ' \'' . $blockedMail  . '\', \'' . $bouncedMail  . '\','
				. ' \'0000-00-00 00:00:00\')';							
		$db->setQuery($query);
		$result = $db->query(); // save email to database
		$mail->id = $db->insertid(); 
		
		if($mail->id < 1){
			$log->error('Inserting of non-queue mail failed, Error Nr: ' . $db->getErrorNum() . ', Message: ' . $db->getErrorMsg());
		}else{
			$log->info('Saved non-queue mail ' . $mail->subject . '  new id: ' . $mail->id);		
		}		
		
		
		// ####### TRIGGER NEW EVENT #######
		$mstEvents = &MstFactory::getEvents();
		if($bounced){
			$mstEvents->newBouncedMail($mail->id);
		}elseif($blocked){
			$mstEvents->newBlockedMail($mail->id);
		}elseif($filtered){
			$mstEvents->newFilteredMail($mail->id);
		}
		// #################################
		
		
		return $mail->id;
	}
	
	public static function removeMailFromQueue($mailId, $email){
		$log = & MstFactory::getLogger();
		$log->debug('removeMailFromQueue: removing mail with id ' . $mailId . ' and recipient: ' . $email);
		$db = & JFactory::getDBO();		
		$query = 'DELETE FROM  #__mailster_queued_mails'
				. ' WHERE mail_id = \'' . $mailId . '\''
				. ' AND email = ' . $db->quote($email)
				. ' LIMIT 1';
		$db->setQuery($query);
		$result	 = $db->query();		
	}
	
	public static function removeAllMailsFromListFromQueue($listId){
		$log = & MstFactory::getLogger();
		$db = & JFactory::getDBO();		
		$log->debug('removeAllMailsFromListFromQueue: removing all mails from mailing list: ' . $listId);
		$mailsInQueue = self::getPendingMailsOfMailingList($listId);
		for($i=0; $i<count($mailsInQueue); $i++){
			$mail = &$mailsInQueue[$i];
			$query = 'DELETE FROM  #__mailster_queued_mails'
					. ' WHERE mail_id = \'' . $mail->id . '\'';
			$db->setQuery($query);
			$result	 = $db->query();	
		}
		return true;	
	}
	
	public static function getNumberOfQueueEntriesForMail($mailId){
		$log = & MstFactory::getLogger();
		$log->debug('Get number of recipients for mail ' . $mailId . ' in queue');
		$db = & JFactory::getDBO();
		$query = 'SELECT * FROM #__mailster_queued_mails'
				. ' WHERE mail_id=\'' . $mailId . '\' ';
		$db->setQuery($query);
		$recipients = $db->loadObjectList();
		return count($recipients);
	}
	
	public static function getNextRecipientsInQueue($mailId, $limit){
		$log = & MstFactory::getLogger();
		$log->debug('Get ' . $limit . ' recipients from queue for mail ' . $mailId);
		$limitStr = ($limit > 0 ? ('LIMIT ' . $limit) : '');
		$db = & JFactory::getDBO();
		$query = 'SELECT * FROM #__mailster_queued_mails'
				. ' WHERE mail_id=\'' . $mailId . '\' '
				. ' AND '
					. ' ( (is_locked=\'0\') '
						. ' OR ((is_locked=\'1\') AND (last_lock < DATE_SUB(NOW(), INTERVAL 3 MINUTE)) )'
					. ' )' 
				. $limitStr;
		$db->setQuery($query);
		$recipients = $db->loadObjectList();
		
		$lockedRecipients = array();
		
		for($i=0;$i<count($recipients);$i++){
			$recip = $recipients[$i];
			
			$recipCurr = self::getRecipientInfo($recip->mail_id, $recip->email); // get current info
			$log->debug('Get current recipient info: '.print_r($recipCurr, true));
			
			if($recip->lock_id != $recipCurr->lock_id){
				$log->debug('Lock ID changed - another instance worked with recipient, skip it');
				continue;
			}
			
			$log->debug('Will lock recipient of mail '.$recip->mail_id.' ('.$recip->email.'), lock ID to increment: '.$recip->lock_id);
			self::lockRecipient($recip->mail_id, $recip->email);
			$isLockOk = self::checkRecipientLock($recip->mail_id, $recip->email, ($recip->lock_id+1));
			$log->debug('Lock ok: '.($isLockOk ? 'true':'false'));
			if($isLockOk){
				$lockedRecipients[] = $recip;
			}else{
				while(!$isLockOk){
					$log->debug('Search for new recipient to lock...');
					$query = 'SELECT * FROM #__mailster_queued_mails' // don't include invalid locks as we have included already with above query
							. ' WHERE mail_id=\'' . $mailId . '\' '
							. ' AND is_locked=\'0\' '  
							. ' LIMIT 1';
					$db->setQuery($query);
					$recipient = $db->loadObject();
					if($recipient){
						$log->debug('Recipient found: ' . print_r($recipient, true));
						self::lockRecipient($recipient->mail_id, $recipient->email);
						$isLockOk = self::checkRecipientLock($recipient->mail_id, $recipient->email, ($recipient->lock_id+1));
						if($isLockOk){
							$log->debug('This recipient locked successfully');
							$lockedRecipients[] = $recipient;
						}else{
							$log->debug('Recipient NOT locked successfully');
						}
					}else{ // no more recipients for this email in queue, so get out of this loop
						$log->debug('No more recipient found');
						$isLockOk = true; 
						break; // just to be sure...
					}
				}
			}
		}
		$log->debug('Found and locked recipients: ' . count($lockedRecipients) . ' -> ' . print_r($lockedRecipients, true));
		return $lockedRecipients;
	}
	 
	public static function lockRecipient($mailId, $email){
		$db = & JFactory::getDBO();
		$query = ' UPDATE #__mailster_queued_mails SET'
				. ' is_locked = \'1\','
				. ' lock_id = lock_id+1,' // increment lock ID
				. ' last_lock = NOW()'					
				. ' WHERE mail_id=\'' . $mailId . '\''
				. ' AND email=' . $db->quote($email) . '';				
		$db->setQuery( $query );
		$result = $db->query();	
	}
	
	public static function checkRecipientLock($mailId, $email, $lockId){		
		$recip = self::getRecipientInfo($mailId, $email);
		if(($recip->is_locked > 0) && ($recip->lock_id == $lockId)){
			return true;
		}
		return false;
	}
	
	public static function getRecipientInfo($mailId, $email){
		$db = & JFactory::getDBO();
		$query = 'SELECT * FROM #__mailster_queued_mails'
				. ' WHERE mail_id=\'' . $mailId . '\' '
				. ' AND email=' . $db->quote($email) . '';		
		$db->setQuery($query);
		$recip = $db->loadObject();		
		return $recip;
	}
	
	public static function incrementError($mailId, $email, $maxSendAttempts){
		$log 		= & MstFactory::getLogger();
		$db = & JFactory::getDBO();		
		$query = 'SELECT * FROM  #__mailster_queued_mails'
				. ' WHERE mail_id = \'' . $mailId . '\''
				. ' AND email = ' . $db->quote($email)
				. ' LIMIT 1';
		$db->setQuery( $query );
		$queuedMail = $db->loadObject();	
		$errorCount = $queuedMail->error_count;
		$log->debug('Increment error count for ' . $mailId 
					. ', before: ' . $errorCount 
					. ' (maxSendAttempts: ' .  $maxSendAttempts . ')');	
		if($errorCount < $maxSendAttempts){
			// increment error and unlock message
			$query = 'UPDATE #__mailster_queued_mails'
				. ' SET error_count=error_count+1,'
				. ' is_locked=\'0\''
				. ' WHERE mail_id = \'' . $mailId . '\''
				. ' AND email = ' . $db->quote($email)
				. ' LIMIT 1';
			$db->setQuery($query);
			$result = $db->query();
			$affRows = $db->getAffectedRows();
			$query = 'UPDATE #__mailster_mails'
				. ' SET fwd_errors=fwd_errors+1'
				. ' WHERE id = \'' . $mailId . '\'';
			$db->setQuery($query);
			$result = $db->query();
			$affRows = $db->getAffectedRows();
		}else{
			self::removeMailFromQueue($mailId, $email);
		}
	}	
	
	public static function sendingComplete($mailId){
		$log 		= & MstFactory::getLogger();
		$listUtils = &MstFactory::getMailingListUtils();
		$listId = $listUtils->getMailingListIdByMailId($mailId);
		$mList = $listUtils->getMailingList($listId);
		if($mList->archive_mode == MstConsts::ARCHIVE_MODE_ALL){
			$log->debug('Set mail as sent, keep content for archive');
			$query = 'UPDATE #__mailster_mails'
					. ' SET fwd_completed = \'1\','
					. ' fwd_completed_timestamp = NOW( )'
					. ' WHERE id = \'' . $mailId . '\'';	
		}elseif($mList->archive_mode == MstConsts::ARCHIVE_MODE_NO_CONTENT){
			$log->debug('Set mail as sent, do not keep content for archive');
			$attachUtils = &MstFactory::getAttachmentsUtils();
			$attachUtils->deleteAttachmentsOfMail($mailId);
			$query = 'UPDATE #__mailster_mails'
					. ' SET fwd_completed = \'1\','
					. ' fwd_completed_timestamp = NOW( ),'
					. ' body = null,'
					. ' html = null,'
					. ' no_content = \'1\''
					. ' WHERE id = \'' . $mailId . '\'';	
		}
		$db = & JFactory::getDBO();
		$db->setQuery($query);
		$result	 = $db->query();
		$affRows = $db->getAffectedRows();	
		return $affRows;	
	}
	
	public static function getPendingMails() {
		$query = ' SELECT m.*'
				. ' FROM #__mailster_mails m'
				. ' WHERE m.list_id in ('
					. ' SELECT id'
					. ' FROM #__mailster_lists' 
					. ' WHERE active =\'1\' )'
				. ' AND m.fwd_completed =\'0\''
				. ' AND ('
					. '          (bounced_mail IS NULL AND blocked_mail IS NULL)'
					. '       OR (bounced_mail = \'0\' AND blocked_mail = \'0\')'
					. '  )'
				. ' AND m.fwd_errors < (' 
					. ' SELECT max_send_attempts'
					. ' FROM #__mailster_lists l' 
					. ' WHERE m.list_id = l.id LIMIT 1)'
				. ' ORDER BY m.receive_timestamp';
				
		
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$mails = $db->loadObjectList();
		return $mails;	
	}
	
	public static function getPendingMailsOfMailingList($listId) {
		$query = ' SELECT m.*'
				. ' FROM #__mailster_mails m'
				. ' WHERE m.list_id =\'' . $listId . '\''
				. ' AND m.fwd_completed =\'0\''
				. ' AND ('
					. '          (bounced_mail IS NULL AND blocked_mail IS NULL)'
					. '       OR (bounced_mail = \'0\' AND blocked_mail = \'0\')'
					. '  )'
				. ' AND m.fwd_errors < (' 
					. ' SELECT max_send_attempts'
					. ' FROM #__mailster_lists l' 
					. ' WHERE m.list_id = l.id LIMIT 1)'
				. ' ORDER BY m.receive_timestamp';
		
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$mails = $db->loadObjectList();
		return $mails;	
	}
	
	public static function getAllPendingMails($limitedCols = false){
		if($limitedCols){
			$query = 'SELECT m.mail_id, m.name, m.email, m.is_locked, ma.id, ma.subject';			
		}else{
			$query = 'SELECT m.*, ma.*';
		}
		$query = $query
					. ' FROM #__mailster_queued_mails m'
					. ' LEFT JOIN #__mailster_mails ma ON (m.mail_id = ma.id)';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$mails = $db->loadObjectList();
		return $mails;	
	}
	
	
	
}
