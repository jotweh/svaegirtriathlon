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


class MstMailSender
{			
	function sendMails($minDuration, $execEnd) {
		$mstQueue = & MstFactory::getMailQueue();
		$mailList = $mstQueue->getPendingMails();	
		$this->sendPendingMails($mailList, $minDuration, $execEnd);
	}
	
	function sendMailsOfMailingList($listId, $minDuration, $execEnd) {
		$mstQueue = & MstFactory::getMailQueue();
		$mailList = $mstQueue->getPendingMailsOfMailingList($listId);	
		$this->sendPendingMails($mailList, $minDuration, $execEnd);
	}
	
	function sendPendingMails($mailList, $minDuration, $execEnd) {
		$log = & MstFactory::getLogger();
		$mailCount = count($mailList);
		if($mailCount > 0){
			$log->debug('Mail Count to send: ' . $mailCount, MstConsts::LOGENTRY_MAIL_SEND);
			for($i = 0; $i < $mailCount; $i++) {
				$log->debug('Time left to run: ' . ($execEnd - time()) . ' for sending mails (execEnd: ' . $execEnd . ', minDuration: ' . $minDuration . ')', MstConsts::LOGENTRY_MAIL_SEND);	
				if(($execEnd - time()) > $minDuration){		
					$mail = $mailList[$i];	
					$this->sendPendingMail($mail, $minDuration, $execEnd);	
				}else{
					$log->debug('Timeout, do not work on next pending mail', MstConsts::LOGENTRY_MAIL_SEND);
					break;
				}
			}
		}else{
			$log->debug('No mails to send', MstConsts::LOGENTRY_MAIL_SEND);
		}
	}
	
	function getSessionTriggerSrcInfo(){
		$sessionInfo = microtime() . ' ';
		if(isset($_SERVER['REQUEST_TIME'])){
			$sessionInfo .= '-rt-'.$_SERVER['REQUEST_TIME'];
		}
		if(isset($_SERVER['REMOTE_ADDR'])){
			$sessionInfo .= ' -ra-'.$_SERVER['REMOTE_ADDR'];
		}
		if(isset($_SERVER['REMOTE_PORT'])){
			$sessionInfo .= ' -rp-'.$_SERVER['REMOTE_PORT'];
		}
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$sessionInfo .= ' -hua-'.$_SERVER['HTTP_USER_AGENT'];
		}
		if(isset($_SERVER['HTTP_COOKIE'])){
			$sessionInfo .= ' -hc-'.$_SERVER['HTTP_COOKIE'];
		}
		return $sessionInfo;
	}
	
	function sendPendingMail($mail, $minDuration, $execEnd){
		$log				= & MstFactory::getLogger();
		$mstQueue 			= & MstFactory::getMailQueue();
		$mstConfig			= & MstFactory::getConfig();
		$mailingListUtils 	= & MstFactory::getMailingListUtils();
		$timeout = false;
		
		$log->debug('Mail to sent is from list ' . $mail->list_id. ', mail to be sent now: ' . $mail->id, MstConsts::LOGENTRY_MAIL_SEND);
		$mList = $mailingListUtils->getMailingList($mail->list_id); 
		
		$throttleActive = $mailingListUtils->isSendThrottlingActive($mail->list_id);
		$limitReached = ($throttleActive && $mailingListUtils->isSendLimitReached($mail->list_id));
		if($limitReached){
			$log->debug('Do not prepare email further, send limit for list '.$mail->list_id.' reached');
			return; // exit function
		}
		
		$maxSendAttempts 	= $mList->max_send_attempts > 0 ? $mList->max_send_attempts : 3;
		$nrBCC 				= $mList->bcc_count > 0 ? $mList->bcc_count : 10;
				
		if($mList->addressing_mode == MstConsts::ADDRESSING_MODE_TO){
			$log->debug('Not using BCC or CC, send one mail to one recipient at a time', MstConsts::LOGENTRY_MAIL_SEND);
			$nrRecipients = 1;
		}elseif($mList->addressing_mode == MstConsts::ADDRESSING_MODE_BCC){
			$log->debug('Using BCC send to ' . $nrBCC . ' recipients at a time', MstConsts::LOGENTRY_MAIL_SEND);
			$nrRecipients = $nrBCC;
		}elseif($mList->addressing_mode == MstConsts::ADDRESSING_MODE_CC){
			$log->debug('Using CC send to all recipients with one mail', MstConsts::LOGENTRY_MAIL_SEND);
			$nrRecipients = 0; // no limit, all recipients
		}
		
		if($this->needToCorrectBCCAndCCOnWindows($mList)){
			$log->warning('Running on Windows with Joomla Mailer -> no CC and BCC possible', MstConsts::LOGENTRY_MAIL_SEND);	
			$nrRecipients = 1;						
		}
		
		$recipCount = 1;
		$sendError = false;
		$preparedMail = $this->prepareMail($mail, $mList);			
		
		$log->debug('Session info before: ' . $this->getSessionTriggerSrcInfo(), MstConsts::LOGENTRY_MAIL_SEND);
				
		while( ($recipCount > 0) && ($timeout == false) && ($limitReached == false) ){
			$recipients = $mstQueue->getNextRecipientsInQueue($mail->id, $nrRecipients);					
			$recipCount = count($recipients);
			$log->debug('recipCount of this mail: ' . $recipCount, MstConsts::LOGENTRY_MAIL_SEND);
			if(($execEnd - time()) <= $minDuration){		
				$timeout = true;
				$log->info('Timeout in before sending next mail (time left: ' . ($execEnd-time()) . ')', MstConsts::LOGENTRY_MAIL_SEND);	
				break;
			}	 
			if($recipCount > 0){	
				$mail2send = $this->prepareMail4Recipients($preparedMail, $recipients, $mList);
				if(is_null($mail2send)){
					$log->info('Will NOT send mail ' . $mail->subject . ' (id: ' . $mail->id . ', list id: ' . $mail->list_id . ') to: ' . print_r($recipients, true), MstConsts::LOGENTRY_MAIL_SEND);
					$this->processSendResults(false, $mail, $recipients, $maxSendAttempts);	// no error, although not sent, remove from queue
				}else{
					$log->info('Sending mail ' . $mail->subject . ' (id: ' . $mail->id . ', list id: ' . $mail->list_id . ') to ' . count($recipients) . ' recipients', MstConsts::LOGENTRY_MAIL_SEND);
					
					$loggingLevel = $mstConfig->getLoggingLevel(); // get current Logging Level
					$isDebugMode = ($loggingLevel == $log->getLoggingLevel(MstLog::DEBUG));
					$log->debug('Logging level: ' . $loggingLevel . ', is debug: ' . ($isDebugMode ? 'true' : 'false'), MstConsts::LOGENTRY_MAIL_SEND);
					if (ob_get_level()) {
						ob_end_clean(); // clean output buffering
					}
					$smtpDebugOutput = '- Not active -';
					if($isDebugMode){
						$log->debug('*** Start SMTP debug ***', MstConsts::LOGENTRY_MAIL_SEND);
						ob_start(); // activate output buffering
						$mail2send->SMTPDebug = 2;
					}
					$sendOk = $mail2send->Send();
					if($isDebugMode){
						$smtpDebugOutput = ob_get_contents();
						if (ob_get_level()) {
							ob_end_clean();  // deactivate output buffering
						}
						$log->debug('SMTP Debug Output: ' . $smtpDebugOutput, MstConsts::LOGENTRY_MAIL_SEND);
						$log->debug('*** Stop SMTP debug ***', MstConsts::LOGENTRY_MAIL_SEND);
					}
					
					$error	= $mail2send->IsError();
					$this->processSendResults($error, $mail, $recipients, $maxSendAttempts);	
					if($error == true) { // send errors?
						$errorMsg  = 'Sending of mail ' . $mail->id . ' failed!';
						$errorMsg .= ' Last error: '. $mail2send->ErrorInfo;
						$log->error($errorMsg, MstConsts::LOGENTRY_MAIL_SEND);					
						// ####### TRIGGER NEW EVENT #######
						$mstEvents = &MstFactory::getEvents();
						$mstEvents->sendError($mail->id, $errorMsg . '   SMTP DEBUG: ' . $smtpDebugOutput);
						// #################################
						$sendError = true;
					}else{
						if($throttleActive){
							$mailingListUtils->add2SendCounter($mail->list_id, 1);
						}
					}
					unset($mail2send); // we don't need this object anymore now
					$log->info('Time left after sending this mail: ' . ($execEnd - time()), MstConsts::LOGENTRY_MAIL_SEND);
				}
			}
			$limitReached = ($throttleActive && $mailingListUtils->isSendLimitReached($mail->list_id));
		}
		if( ($timeout == false) && ($sendError == false) && ($limitReached == false) ){	
			// either all recipients are currently locked or there are no more recipients
			$recipsInQueue = $mstQueue->getNumberOfQueueEntriesForMail($mail->id);
			if($recipsInQueue <= 0){ // no more recipients?
				$log->info('Sending of mail ' . $mail->id . ' COMPLETE', MstConsts::LOGENTRY_MAIL_SEND);	
				$mstQueue->sendingComplete($mail->id);	// all recpipients done
			}else{
				$log->debug('There are still recipients in the queue, probably locked ones', MstConsts::LOGENTRY_MAIL_SEND);
			}
		}
	}
	
	function needToCorrectBCCAndCCOnWindows($mList){
		$log = & MstFactory::getLogger();
		if($mList->use_joomla_mailer != '1') {	
			$log->debug('We are not using the Joomla mailer, no need to correct BCC settings', MstConsts::LOGENTRY_MAIL_SEND);	
			return false; // using smtp is fine
		}		
		$jMailer =& JFactory::getMailer();
		$jMailerType = strtolower($jMailer->Mailer);
		$log->debug('Joomla Mailer Type: ' . $jMailerType, MstConsts::LOGENTRY_MAIL_SEND);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {	
			if($jMailerType !== 'smtp'){	
				$log->info('Using PHP mail or sendmail function with windows -> we cannot use BCC addresses -> reset to 1', MstConsts::LOGENTRY_MAIL_SEND);	
				return true; // only one recipient per mail possible with mail() function on windows, smtp works
			}
		}
		return false;
	}
	
	function prepareMail($mail, $mList){// Prepare E-Mail without specifying recipient...		
		$log 			= & MstFactory::getLogger();
		$mailUtils 		= & MstFactory::getMailUtils();
		$mstConfig 		= & MstFactory::getConfig();
		$threadUtils 	= & MstFactory::getThreadUtils();
		$attachUtils 	= & MstFactory::getAttachmentsUtils();
		$env 			= & MstFactory::getEnvironment();		
		
		$log->debug('Prepare general mail content, working with: ' . print_r($mail, true), MstConsts::LOGENTRY_MAIL_SEND);
		
		// add/remove/convert parts according to list settings (i.e. HTML only mail without plain text part or vice versa)		
		$mail = $mailUtils->addRemoveConvertBodyParts($mail, $mList); 
		// do modifications (header, footer, subject)
		$mail = $mailUtils->modifyMailContent($mList, $mail);
		// load template
		$mail2send = $this->getListMailTmpl($mList);	
		
		$noFromName = ( is_null($mail->from_name) || (trim($mail->from_name) === '') );
		
		$mailingListAsFrom = false;		
		if($mList->mail_from_mode == MstConsts::MAIL_FROM_MODE_GLOBAL){
			if($mstConfig->useMailingListAddressAsFromField()){	
				$mailingListAsFrom = true;				
			}else{	
				$mailingListAsFrom = false;
			}
		}elseif($mList->mail_from_mode == MstConsts::MAIL_FROM_MODE_MAILING_LIST){	
			$mailingListAsFrom = true;
		}elseif($mList->mail_from_mode == MstConsts::MAIL_FROM_MODE_SENDER_EMAIL){	
			$mailingListAsFrom = false;
			
		}
		
		$mailingListAsName = false;
		if($mList->name_from_mode == MstConsts::NAME_FROM_MODE_GLOBAL){
			if($mstConfig->useMailingListNameAsFromField()){	
				$mailingListAsName = true;				
			}else{	
				$mailingListAsName = false;
			}
		}elseif($mList->name_from_mode == MstConsts::NAME_FROM_MODE_MAILING_LIST_NAME){	
			$mailingListAsName = true;
		}elseif($mList->name_from_mode == MstConsts::NAME_FROM_MODE_SENDER_NAME){	
			$mailingListAsName = false;			
		}

		if($mailingListAsFrom){
			$mail2send->From = trim($mList->list_mail);
			$log->debug('Set as FROM address (should be mailing list address): ' . $mail2send->From, MstConsts::LOGENTRY_MAIL_SEND);
		}else{
			$mail2send->From = trim($mail->from_email);
			$log->debug('Set as FROM address (should be the sender address): ' . $mail2send->From, MstConsts::LOGENTRY_MAIL_SEND);
		}
		
		if($mailingListAsName){
			$mail2send->FromName = trim($mList->name);
			$log->debug('Set as FROM name (should be the mailing list name): ' . $mail2send->FromName, MstConsts::LOGENTRY_MAIL_SEND);
		}else{
			if($noFromName){			
				if($mstConfig->insertSenderAddressForEmptySenderName()){
					$mail2send->FromName = trim($mail->from_email);
					$log->debug('Set as FROM name (should be sender email address): ' . $mail2send->FromName, MstConsts::LOGENTRY_MAIL_SEND);
				}else{
					$mail2send->FromName = '';
					$log->debug('Set as FROM name (should be empty): ' . $mail2send->FromName, MstConsts::LOGENTRY_MAIL_SEND);
				}
			}else{
				$mail2send->FromName = trim($mail->from_name);
				$log->debug('Set as FROM name (should be the sender name): ' . $mail2send->FromName, MstConsts::LOGENTRY_MAIL_SEND);
			}
		}
			
		// Bounce Handling here
		$bounceAddress = trim($mList->list_mail); // default
		if($mList->bounce_mode == MstConsts::BOUNCE_MODE_LIST_ADDRESS){
			$bounceAddress = trim($mList->list_mail); // bounces return to list	
		}elseif($mList->bounce_mode == MstConsts::BOUNCE_MODE_DEDICATED_ADDRESS){
			$bounceAddress = trim($mList->bounce_mail); // bounces go to dedicated and fixed address
		}
		
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_RETURN_PATH . ': <' . $bounceAddress . '>'); // try to set return path
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_ERRORS_TO . ': ' . $bounceAddress); // try to ensure return/error path
		$mail2send->Sender = $bounceAddress;
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_SENDER . ': ' . $bounceAddress); //  make sure Sender is really set correct		
			
		if($mstConfig->addMailsterMailHeaderTag()){
			$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_MAILSTER_TAG); // tag mail as a Mailster mail
		}	
		
		if((!is_null($mail->in_reply_to)) && (strlen($mail->in_reply_to)>0)){
			// This mail is a reply
			$log->debug('This is a reply, adding In-Reply-To header...', MstConsts::LOGENTRY_MAIL_SEND);
			$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_IN_REPLY_TO . ': ' . $mail->in_reply_to);
			if($mList->clean_up_subject > 0){
				$mail->subject = $threadUtils->getThreadSubject($mail->thread_id);
				// as we just undo the subject modifications (e.g. prefix), we have to do this again:
				$mail->subject = $mailUtils->modifyMailSubject($mail, $mList);
			}
			$replyPrefix = $mstConfig->getReplyPrefix();
			if($mstConfig->addSubjectPrefixToReplies()){	
				$log->debug('Adding reply prefix: ' . $replyPrefix, MstConsts::LOGENTRY_MAIL_SEND);
				$mail->subject = $replyPrefix . ' ' . $mail->subject;
			}else{
				$log->debug('Do not add reply prefix (' . $replyPrefix . ')', MstConsts::LOGENTRY_MAIL_SEND);
			}
		}
		
		$mail2send->setSubject($mail->subject);
		
		if(is_null($mail->html) || strlen(trim($mail->html))<1){
			$log->debug('Send as plain text mail', MstConsts::LOGENTRY_MAIL_SEND);
			$mail2send->setBody($mail->body);	
		}else{
			$log->debug('Send as html mail', MstConsts::LOGENTRY_MAIL_SEND);
			$mail2send->IsHTML(true);
			$mail2send->Body = $mail->html;
			$mail2send->AltBody=$mail->body;
		}
		
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_PRECEDENCE . ': list');		
		$mail2send->addCustomHeader($mailUtils->getListIDMailHeader($mList));		
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_LIST_UNSUBSCRIBE . ': <mailto:' . trim($mList->admin_mail) . '?subject=unsubscribe>'); // admin gets unsubscribe requests
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_LIST_ARCHIVE . ': <http://' . $env->getDomainName() . '>'); // archive currently not directly linked
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_LIST_POST . ': <mailto:' . trim($mList->list_mail) . '>'); // address for posting new posts/replies
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_LIST_HELP . ': <mailto:' . trim($mList->admin_mail) . '?subject=help>'); // admin gets help requests
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_LIST_SUBSCRIBE . ': <mailto:' . trim($mList->admin_mail) . '?subject=subscribe>'); // admin gets subscribe requests
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_MSG_ID . ': ' . $mail->id); // insert mail ID, this can be used to identify the mail within Mailster
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_BEEN_THERE . ': ' . trim($mList->list_mail)); // we have been here...
	//	$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_MAILSTER_DEBUG . ': ' . $this->getSessionTriggerSrcInfo()); // we have been here...

		$mstRef 	= $threadUtils->getThreadReference($mail->thread_id);
		$references = $threadUtils->getAllReferencesOfThread($mail->thread_id, 30); // max 30 references
		$references = $mail->message_id . ' ' . $mstRef . ' ' . $references;	
		$log->debug('All references: ' . $references, MstConsts::LOGENTRY_MAIL_SEND);
		$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_REFERENCES . ': ' . $references);
		
		if($mList->addressing_mode == MstConsts::ADDRESSING_MODE_CC){
			$mail2send->addCustomHeader(MstConsts::MAIL_HEADER_CC . ': ' . trim($mList->name) . ' <' . trim($mList->list_mail) . '>');
			$log->debug('CC addressing: Added List address ' . trim($mList->list_mail) . ' as CC recipient because of CC addressing mode', MstConsts::LOGENTRY_MAIL_SEND);
		}elseif($mList->addressing_mode == MstConsts::ADDRESSING_MODE_BCC){
			$mail2send->AddAddress(trim($mList->list_mail), trim($mList->name));	
			$log->debug('BCC addressing: Added List address ' . trim($mList->list_mail) . ' as To recipient...', MstConsts::LOGENTRY_MAIL_SEND);
		}else{
			$log->debug('TO addressing: Do not add list mail as addtional TO addressee', MstConsts::LOGENTRY_MAIL_SEND);
		}
		
			
		if($mList->reply_to_sender != 2){ 
			$replyTo = $mailUtils->getReplyToArray($mList, $mail->from_email, $mail->from_name);	
			$mail2send->addReplyTo($replyTo); // only in the cases where exact one address should be the reply-to destination
		}
		
		if($mail->has_attachments === '1') { // add attachments...
			$attachs = $attachUtils->getAttachmentsOfMail($mail->id);	
			$log->debug('prepareMail: has ' . count($attachs) . ' attachments...', MstConsts::LOGENTRY_MAIL_SEND);		
			for($k = 0; $k < count($attachs); $k++) {
				$log->debug('prepareMail: ----------- add attachment ' . ($k+1) . ' -----------', MstConsts::LOGENTRY_MAIL_SEND);			
				$attach = &$attachs[$k];
				$filePath = JPATH_ROOT.DS.$attach->filepath.DS.$attach->filename;				
				$newFilename = rawurldecode($attach->filename);
				$typeStr = $attachUtils->getAttachmentTypeString($attach->type, $attach->subtype);
				$params = trim($attach->params);
				$log->debug('prepareMail: has type: ' . $typeStr, MstConsts::LOGENTRY_MAIL_SEND);		
				
				if($attach->disposition == MstConsts::DISPOSITION_TYPE_ATTACH){
					if(strtoupper(trim($attach->subtype)) === 'CALENDAR' ){
						$log->debug('prepareMail: adding as calendar entry: ' . $filePath, MstConsts::LOGENTRY_MAIL_SEND);
						if(strtolower(trim($attach->filename)) === strtolower(trim(MstConsts::ATTACHMENT_NO_FILENAME_FOUND))){							
							$log->debug('prepareMail: we have no filename for this calendar entry, take "meeting.ics"', MstConsts::LOGENTRY_MAIL_SEND);
							$newFilename = 'meeting.ics';
						}
					}
					$log->debug('prepareMail: adding as attachment: ' . $newFilename . ', type: ' . $typeStr . ', params: ' . $params . ' (' . $filePath . ')', MstConsts::LOGENTRY_MAIL_SEND);
					$mail2send->AddAttachment($filePath, $newFilename, 'base64', $typeStr . $params);
					if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
						// Joomla! 1.6 / 1.7 / ...
						// no need to make modification ... and attachment variable is protected...
					} else {
						// Joomla! 1.5 
						$mail2send->attachment[$k][2]=$newFilename;
						$mail2send->attachment[$k][4]=$typeStr . $params;
					}
					
				}elseif($attach->disposition == MstConsts::DISPOSITION_TYPE_INLINE){
					$contentId = $attach->content_id;
					$noContentIdProvided =  (is_null($contentId) || (trim($contentId) === '') );
					if($noContentIdProvided){
						$log->debug('No content id provided, take file name as content id (filename is ' . $newFilename . ')');
						$contentId = $newFilename;
					}
					$log->debug('prepareMail: adding as inline attachment: content id: ' . $contentId  . ', type: ' . $typeStr . ' (' . $filePath . ')', MstConsts::LOGENTRY_MAIL_SEND);
					$mail2send->AddEmbeddedImage($filePath, $contentId, '', 'base64', $typeStr . $params);
					
					if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
						// Joomla! 1.6 / 1.7 / ...
						// no need to make modification ... and attachment variable is protected...
					} else {
						// Joomla! 1.5 
						$mail2send->attachment[$k][4]=$typeStr . $params;
					}
					
				}
			}
		}	
		
		$log->debug('Prepared Mail: ' . print_r($mail2send, true), MstConsts::LOGENTRY_MAIL_SEND);
		return $mail2send;
	}
	

	function prepareMail4Recipients($mail2send, $recipients, $mList){
		$log = & MstFactory::getLogger();
		$listEmail = $mList->list_mail;
		$recipientMail = clone($mail2send);
		$recipCount = count($recipients);
		$nrRecipsAdded = 0;
		
		for($i=0; $i < $recipCount; $i++){
			$recipient = &$recipients[$i];
			if(strtolower(trim($recipient->email)) !== strtolower(trim($listEmail))){
				$nrRecipsAdded = $nrRecipsAdded + 1;
				$log->debug('Next recipient: ' . $recipient->name . ' ('.$recipient->email.', #errors:'.$recipient->error_count.')', MstConsts::LOGENTRY_MAIL_SEND);
				if($mList->addressing_mode == MstConsts::ADDRESSING_MODE_TO){
					$log->debug('Add ' . $recipient->email . ' to TO recipients', MstConsts::LOGENTRY_MAIL_SEND);			
					$recipientMail->AddAddress($recipient->email, $recipient->name);	
				}elseif($mList->addressing_mode == MstConsts::ADDRESSING_MODE_BCC){
					$log->debug('Add ' . $recipient->email . ' to BCC recipients', MstConsts::LOGENTRY_MAIL_SEND);				
					$recipientMail->AddBCC($recipient->email, $recipient->name);
				}elseif($mList->addressing_mode == MstConsts::ADDRESSING_MODE_CC){
					$log->debug('Add ' . $recipient->email . ' to CC recipients', MstConsts::LOGENTRY_MAIL_SEND);			
					$recipientMail->AddCC($recipient->email, $recipient->name); // because of "add at least one recipient" error message, seems to be buggy, at least under Windows
					$recipientMail->addCustomHeader(MstConsts::MAIL_HEADER_CC . ': ' . trim($recipient->name) . ' <' . trim($recipient->email) . '>'); // actual addressing
				}
			}else{
				$log->warning('Do not add recipient, recipient ' . $recipient->email 
					. ' is the email address of the mailing list (' . $listEmail . ')', MstConsts::LOGENTRY_MAIL_SEND);				
			}
		}
		if($nrRecipsAdded > 0){	 // check if we have added at least one recipient
			$log->debug('Mail with recipients: ' . print_r($recipientMail, true), MstConsts::LOGENTRY_MAIL_SEND);
			return $recipientMail;
		}
		return null; // no recipient, null will indicate that this does not need to be sent
	}

	function processSendResults($error, $mail, $recipients, $maxSendAttempts){
		$mstQueue 	= & MstFactory::getMailQueue();
		$recipCount = count($recipients);
		for($i=0; $i < $recipCount; $i++){
			$recipient = &$recipients[$i];
			if($error == false) { // send errors?
				$mstQueue->removeMailFromQueue($mail->id, $recipient->email);
			}else{	
				$mstQueue->incrementError($mail->id, $recipient->email, $maxSendAttempts);											
			}	
		}	
	}
	
	public static function getListMailTmpl($mList){
		$mail2send =& JFactory::getMailer();					
		$mail2send->ClearAllRecipients();	  
		$mail2send->From = $mList->list_mail; // not $mail->from_email because of PHPMAILER_FROM_FAILED error
		if($mList->use_joomla_mailer != '1') {	
			$mail2send->useSMTP($mList->mail_out_use_sec_auth == '1' ? true : false, 
								$mList->mail_out_host, 
								$mList->mail_out_user, 
								$mList->mail_out_pw, 
								$mList->mail_out_use_secure, 
								$mList->mail_out_port); 
		}	
		return $mail2send;	
	}
	
	public static function sendMail2ListAdmin($mList, $subject, $body){
		$mail2send = self::getListMailTmpl($mList);					  
		$mail2send->FromName = 'Mailster';
		$mail2send->addReplyTo(array($mList->list_mail, $mList->name));
		$mail2send->AddAddress($mList->admin_mail, $mList->name . ' Admin');
		$mail2send->setSubject($subject);
		$mail2send->setBody($body);							
		$sendOk = $mail2send->Send();
		$error =  $mail2send->IsError();
		return !$error;
	}
	
}
?>
