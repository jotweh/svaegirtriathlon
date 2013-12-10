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
							

class MstMailingListMailbox
{
	private $mBox;
	private $mList;
		
	function open($mailingList){
		
		$this->mList	= $mailingList;
		
		$log = & MstFactory::getLogger();	
		$useSecAuth = $this->mList->mail_in_use_sec_auth !== '0' ? '/secure' : '';
		$useSec = $this->mList->mail_in_use_secure !== '' ? '/' . $this->mList->mail_in_use_secure : '';
		$protocol = $this->mList->mail_in_protocol !== '' ? '/' . $this->mList->mail_in_protocol : '';
		$host =  '{'. trim($this->mList->mail_in_host) . ':' 
					. trim($this->mList->mail_in_port) 
					. $useSecAuth 
					. $protocol 
					. $useSec 
					. $this->mList->mail_in_params 
					. '}'. 'INBOX';
		$log->debug($this->mList->name . ': ' . $host . '   user: ' . $this->mList->mail_in_user, MstConsts::LOGENTRY_MAIL_RETRIEVE);	
		$this->mBox = @imap_open ($host, $this->mList->mail_in_user, $this->mList->mail_in_pw);
		if($this->mBox){
			return true;
		}else{
			return false;
		}
	}
	
	function close(){			
		$log = & MstFactory::getLogger();	
		$log->debug('Deleting mails marked for deletion...', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		imap_expunge($this->mBox); // delete mails marked for deletion		
		$errorMsgs = $this->getErrors(); // clear (useless) notices/warnings		
		$log->debug('Cleared errors before closing: ' . $errorMsgs, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$res = imap_close($this->mBox); // close mailbox	
		$log->debug('Mailbox closed: ' . ($res ? 'Yes' : 'No'), MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$this->mBox = null;
	}
	
	function getErrors(){
		$errorMsg = "<br/>Error Messages:";
		$imapErrors = imap_errors(); 
		if($imapErrors){
			foreach($imapErrors as $error){
				$errorMsg =  $errorMsg.'<br/>'.$error;
			}
		}else{
			$errorMsg =  $errorMsg.'<br/>'.JText::_( 'COM_MAILSTER_NO_ERROR_MESSAGES_AVAILABLE' );
		}
		$errorMsg =  $errorMsg.'<br/>';
		return $errorMsg;
	}
	
	function removeFirstMailFromMailbox(){		
		$log = & MstFactory::getLogger();
		$log->debug('Remove first mail from mailbox ' . $this->mList->name . ' (id: ' . $this->mList->id . ')', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$imapcheck = imap_check($this->mBox);
		$nMsgs = $imapcheck->Nmsgs; // number of messages in mailbox
		$log->debug('Mailbox OK (id ' . $this->mList->id . '), #New Mails: ' . $nMsgs, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$nr = 1;
		if($nMsgs > 0){
			$log->debug('Deleting mail ' . $nr . ' from mailbox', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$res = $this->markOrDeleteOrMoveMail($nr, false, '', true, false, '');
		}else{
			$log->debug('No mail to remove from mailbox', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}
	}
	
	function removeAllMailsFromMailbox(){		
		$log = & MstFactory::getLogger();
		$log->debug('Remove all mail from mailbox ' . $this->mList->name . ' (id: ' . $this->mList->id . ')', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$imapcheck = imap_check($this->mBox);
		$nMsgs = $imapcheck->Nmsgs; // number of messages in mailbox
		$log->debug('Mailbox OK (id ' . $this->mList->id . '), #New Mails: ' . $nMsgs, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$nr = 1; // mail nr 		
		while ($nr <= $nMsgs && $timeout == false) {
			$log->debug('Deleting mail ' . $nr . ' from mailbox', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$res = $this->markOrDeleteOrMoveMail($nr, false, '', true, false, '');
			$nr++;
		}
	}
		
	function retrieveAllMessages($minDuration, $execEnd){			
		$log = & MstFactory::getLogger();	
		$timeout = false;
		$imapcheck = imap_check($this->mBox);
		$nMsgs = $imapcheck->Nmsgs; // number of messages in mailbox
		if($nMsgs > 0){
			$log->info('Mailbox OK (id ' . $this->mList->id . '), #New Mails: ' . $nMsgs, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}else{
			$log->debug('Mailbox OK (id ' . $this->mList->id . '), #New Mails: ' . $nMsgs, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}
		$nr = 1; // mail nr 		
		while ($nr <= $nMsgs && $timeout == false) {
			$timeLeft = $execEnd - time();
			$log->debug('Time left to run: ' .  $timeLeft
						. ' for retrieving mails (execEnd: ' . $execEnd 
						. ', minDuration: ' . $minDuration . ')', MstConsts::LOGENTRY_MAIL_RETRIEVE);				
			if(($execEnd - time()) > $minDuration){	
				$dropMail = $this->isMailToBeDroppedUnretrieved($nr);
				if($dropMail){
					$log->debug('Deleting mail from mailbox', MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$res = $this->markOrDeleteOrMoveMail($nr, false, '', true, false, '');
					$log->info('Mail to drop was removed from mailbox: ' . ($res ? 'Yes' : 'No'), MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$nr++;
					continue;
				}
				$mail = $this->getMessage($nr);
				$mail = $this->preprocessMessage($mail);
				if($this->storeMessageAndAttachments($mail)){	
					$log->debug('Deleting mail from mailbox', MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$res = $this->markOrDeleteOrMoveMail($nr, false, '', true, false, '');
					$log->info('Mail removed from mailbox: ' . ($res ? 'Yes' : 'No'), MstConsts::LOGENTRY_MAIL_RETRIEVE);
				} 	
			}else{
				$log->info('Timeout while retrieving mails...', MstConsts::LOGENTRY_MAIL_RETRIEVE);	
				$timeout = true;
			}
			$nr++;
		}
	}
	
	function isMailToBeDroppedUnretrieved($nr){
		$log = & MstFactory::getLogger();	
		$convUtils 	= & MstFactory::getConverterUtils();
		$dropMail = false;					
		$rawHeader = imap_fetchheader($this->mBox, $nr); 
		preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)\r\n/m', $rawHeader, $ftHeader);
		$headerFields = $ftHeader[1];
		$header = $convUtils->object2Array(imap_headerinfo($this->mBox,$nr,255,255)); 
		$subject = $convUtils->imapUtf8($header['subject']);
		if(in_array(MstConsts::MAIL_HEADER_MSG_ID, $headerFields)){
			$dropMail = true;		
			$log->debug('Found ' . MstConsts::MAIL_HEADER_MSG_ID . ' in header of mail: ' . $subject, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}
		if(!$dropMail){
			$mailSizeLimit = $this->mList->mail_size_limit;
			if(trim($mailSizeLimit) === ''){
				$mailSizeLimit = 0;
			}
			if($mailSizeLimit > 0){
				$log->debug('Mail size limit defined for this list: ' . $mailSizeLimit . 'kByte');
				// we have defined an email size limit
				$mailSize = false;
				$structure 	= $convUtils->object2Array(imap_fetchstructure($this->mBox, $nr)); // get complete mail structure
				if(array_key_exists('bytes', $structure)){ // check if total mail size is supplied by server
					$mailSize = $structure['bytes'];
					$log->debug('Total email size in structure: ' . $mailSize);
				}
				if(!$mailSize){ // if we do not have the mail size we have to look at the parts
					$log->debug('Total email size was not in structure, adding sub-parts...');
					$mailSize = 0; // set size to zero
					$parts = $structure['parts'];											 
					foreach($parts as $p){ // go through each part on the highest mail part level
						if(array_key_exists('bytes', $p)){
							$log->debug('Part has size ' . $p['bytes']);
							$mailSize += $p['bytes']; // add up all subpart sizes
						}
					}
				}
				if($mailSize > 0){
					// we know the mail size
					$mailSize = floor($mailSize/1024); // bytes to kBytes
					$log->debug('We have an email size available for size check: ' . $mailSize . 'kByte');
					if($mailSize > $mailSizeLimit){
						$log->info('Email has ' . $mailSize . 'kByte -> larger than allowed maximum of ' . $mailSizeLimit . 'kByte, will be dropped');
						$dropMail = true; // mail too large, will be dropped	
						
						$senderName	= $convUtils->imapUtf8(array_key_exists('personal', $header['from'][0]) ? $header['from'][0]['personal'] : '');
						$senderEmail = $convUtils->imapUtf8($header['from'][0]['mailbox'] . '@' . $header['from'][0]['host']);	
						$emailTooLarge = true;		
						// ####### TRIGGER NEW EVENT #######
						$mstEvents = &MstFactory::getEvents();
						$mstEvents->mailIsNotForwarded($this->mList->id, $subject, $senderName, $senderEmail, false, false, $emailTooLarge);
						// #################################						
					}else{
						$log->debug('Email has ' . $mailSize . 'kByte -> smaller than allowed maximum of ' . $mailSizeLimit . 'kByte');
					}
				}
			}
		}
		return $dropMail;
	}
	
	function getMessage($nr){					
		$log 		= & MstFactory::getLogger();	
		$mstUtils 	= & MstFactory::getUtils();
		$mailUtils 	= & MstFactory::getMailUtils();
		$hashUtils 	= & MstFactory::getHashUtils();
		$convUtils 	= & MstFactory::getConverterUtils();

		$mail = new stdClass;
		$mail->isBouncedMail 	= false;
		$mail->isFilteredMail 	= false;
		$mail->hasUnauthSender 	= false;
		
		
		$header 	= $convUtils->object2Array(imap_headerinfo($this->mBox,$nr,255,255)); // get most important mail header fields
		$rawHeader	= imap_fetchheader($this->mBox, $nr); // get raw header
		$structure 	= $convUtils->object2Array(imap_fetchstructure($this->mBox, $nr)); // get complete mail structure
		
		
		$log->debug(' ################## NEW_MAIL_START ################## ', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$log->debug('Working on mail #No ' . $nr, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$log->debug('Mail Header:', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$log->debug(print_r($header, true), MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$log->debug('Raw Mail Header:', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$log->debug($rawHeader, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$log->debug('Mail Structure:', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$log->debug(print_r($structure, true), MstConsts::LOGENTRY_MAIL_RETRIEVE);
		
		$mail = new stdClass;			
		$mail->udate_timestamp		= $header['udate'];
		$mail->receive_timestamp 	= &JFactory::getDate(date("Y-m-d H:i",$header['udate'])); 
		$mail->receive_timestamp 	= $mail->receive_timestamp->toMySQL();
		$mail->subject				= $convUtils->getStringAsNativeUtf8($header['subject']);
		$mail->to_name				= $convUtils->imapUtf8(array_key_exists('personal', $header['to'][0]) ? $header['to'][0]['personal'] : '');
		$mail->to_email				= $convUtils->imapUtf8($header['to'][0]['mailbox'] . '@' . $header['to'][0]['host']);
		$mail->from_name			= $convUtils->imapUtf8(array_key_exists('personal', $header['from'][0]) ? $header['from'][0]['personal'] : '');
		$mail->from_email			= $convUtils->imapUtf8($header['from'][0]['mailbox'] . '@' . $header['from'][0]['host']);		
		$mail->rawHeader			= $rawHeader;	
		$mail->structure			= $structure;	
		$mail->message_id			= array_key_exists('message_id', $header) ? $header['message_id'] : null;
		$mail->in_reply_to 			= array_key_exists('in_reply_to', $header) ? $header['in_reply_to'] : null;
		$mail->references_to		= array_key_exists('references', $header) ? $header['references'] : null;
		$mail->hashkey 				= $hashUtils->getMailHashkey();
		$mail->listId				= $this->mList->id;
		
		$mail->type 		= $structure['type'];
		$mail->encoding 	= $structure['encoding'];
		$mail->parameters 	= $structure['parameters'];
		$mail->charset	 	= $mailUtils->extractCharset($mail->parameters);
		$mail->hasSubtype 	= $structure['ifsubtype'] == 1 ? true : false;
		$mail->subtype 		= $mail->hasSubtype ? $structure['subtype'] : null;
		
		$content = $this->getBodyAndAttachmentsOfMail($nr, $mail);
		$mail->body 		= $content['body'];
		$mail->html 		= $content['html'];
		$mail->attachments 	= $content['attachments'];
		
		$mail->has_attachments = ( count($mail->attachments) > 0 ? '1' : '0');
		
		return $mail;
	}
	
	function preprocessMessage($mail){			
		$log 		= & MstFactory::getLogger();	
		$mstUtils 	= & MstFactory::getUtils();
		$mailUtils 	= & MstFactory::getMailUtils();
		$threadUtils = &MstFactory::getThreadUtils();
		
		$mail->isFilteredMail = false;
		
		$filterMails = ($this->mList->filter_mails == 1 ? true : false);
		if($filterMails){					
			$mail->isFilteredMail = $mailUtils->checkMailWithWordsToFilter($mail);
			$log->info('Mail Filtering active for this list, mail blocked because of word filter: ' 
				. ($mail->isFilteredMail ? 'yes' : 'no'), MstConsts::LOGENTRY_MAIL_RETRIEVE);	
				
			if($mail->isFilteredMail){
				$emailFilteredByWords = true;	
				// ####### TRIGGER NEW EVENT #######
				$mstEvents = &MstFactory::getEvents();
				$mstEvents->mailIsNotForwarded($this->mList->id, $mail->subject, $mail->from_name, $mail->from_email, false, $emailFilteredByWords, false);
				// #################################						
			}	
		}
		
		
		$mail->isBouncedMail 	= $mailUtils->isBouncedMail($mail->rawHeader);
		$mail->hasUnauthSender 	= !($this->isAllowed2Send($mail->from_email));
		
		$log->debug('Unauth. Sender: ' . ($mail->hasUnauthSender ? 'Yes' : 'No') 
					. ', is bounced mail: ' . ($mail->isBouncedMail ? 'Yes' : 'No'), MstConsts::LOGENTRY_MAIL_RETRIEVE);
					
		if($mail->hasUnauthSender){
			$senderBlocked = true;	
			// ####### TRIGGER NEW EVENT #######
			$mstEvents = &MstFactory::getEvents();
			$mstEvents->mailIsNotForwarded($this->mList->id, $mail->subject, $mail->from_name, $mail->from_email, $senderBlocked, false, false);
			// #################################						
		}
		
		// try to remove all mail modifications that were applied previously
		// -> only when this is an answer to a mail that was processed with Mailster 			
		$mail 	= $mailUtils->undoSubjectModifications($mail, $this->mList);			  
		$mail 	= $mailUtils->undoMailBodyModifications($mail, $this->mList);
		
		// Clean up empty or incomplete references
		$mail->references_to_orig = $mail->references_to; // backup references
		$mail->references_to = $threadUtils->cleanUpReferencesString($mail->references_to);
		// Remove Mailster Message Reference
		$mail->references_to = $threadUtils->removeMailsterThreadReference($mail->references_to);
		
		return $mail;
	}
	
	function storeMessageAndAttachments($mail){		
		$log 			= & MstFactory::getLogger();	
		$mstUtils 		= & MstFactory::getUtils();
		$mstQueue 		= & MstFactory::getMailQueue();
		$threadUtils 	= & MstFactory::getThreadUtils();
		$insertId = 0;
		
		$isAssigned2Thread = true; // default
		
		if( ($mail->isBouncedMail == false) && ($mail->hasUnauthSender == false) && ($mail->isFilteredMail == false) ){
			$log->debug('Insert mail data into DB', MstConsts::LOGENTRY_MAIL_RETRIEVE);	
			$insertId = $mstQueue->saveAndEnqueueMail($mail, $this->mList);						
		}else{
			if($mail->isBouncedMail){
				$isAssigned2Thread = false; // bounced emails have no thread!
			}
			$log->debug('Not enqueuing mail, handling/saving as bounced or blocked', MstConsts::LOGENTRY_MAIL_RETRIEVE);	
			$insertId = $mstQueue->saveNonQueueMail($mail, $this->mList, $mail->hasUnauthSender, $mail->isBouncedMail, $mail->isFilteredMail);	
		}
		
		$log->debug('Insert ID: ' . $insertId, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$mail->id = $insertId;		
		
		$mail = $this->storeAttachments($mail);
		
		if($isAssigned2Thread){
			$log->debug('Need to assign a thread to the mail...', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$threadId = $threadUtils->getThreadIdOfMail($mail);
			if(!$threadId){
				$threadId = $threadUtils->createNewThread($mail); // we have to create a new thread
			}		
			$mail->thread_id = $threadId;		
			$threadUtils->updateThreadId($mail->id, $threadId);
		}else{
			$log->debug('No thread will be assigned to the email', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}
				
		if ($insertId > 0) {
			return true;
		} 	
		return false;
		
	}
	
	function storeAttachments($mail){	
		$log 			= & MstFactory::getLogger();	
		$mstConfig 		= & MstFactory::getConfig();
		$attachUtils 	= & MstFactory::getAttachmentsUtils();
		
		$log->debug('Has attachments to save: ' . ((count($mail->attachments) > 0) ? 'Yes' : 'No'), MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$baseDir = $mstConfig->getAttachmentBaseDir() . DS . $this->mList->id . DS . $this->mList->name;
		$savedAttachs = $attachUtils->storeAttachments($baseDir, $mail->attachments); // Save attachments to files
		$attachUtils->saveAttachmentsInDB($mail->id, $savedAttachs); // Save attachments in DB	
		$mail->has_attachments = ( count($mail->attachments) > 0 ? '1' : '0');
		
		return $mail;
	}
	
	
	function markOrDeleteOrMoveMail($nr, $markIt, $markType, $deleteIt, $moveIt, $moveTarget){
		$res = false;
		// Zeitgest. Löschen/Abrufen impl.
		// criterias: 'ALL UNDELETED' and 'SINCE "' . date('r', $lastCheck) . '" UNDELETED'
		// imap_search($mBox, $criterias, SE_UID);
		if($markIt){				
		    switch($markType) {
		      case 'unread':
		        $res = imap_clearflag_full($this->mBox, $nr, '\\SEEN', ST_UID);
		      break;
		      case 'read':
		        $res = imap_setflag_full($this->mBox, $nr, '\\SEEN', ST_UID);
		      break;
		      case 'flagged':
		        $res = imap_setflag_full($this->mBox, $nr, '\\FLAGGED', ST_UID);
		      break;
		      case 'unflagged':
		        $res = imap_clearflag_full($this->mBox, $nr, '\\FLAGGED', ST_UID);
		      break;
		      case 'answered':
		        $res = imap_setflag_full($this->mBox, $nr, '\\Answered', ST_UID);
		      break;
		    }										
		}
		if($deleteIt){
			$res = imap_delete($this->mBox, $nr);
		}
		if($moveIt){
			$res = imap_mail_move($this->mBox, $nr, $moveTarget);	
		}
		
		return $res;
	}
	
	
	function getBodyAndAttachmentsOfMail($nr, $mail){
		$log = & MstFactory::getLogger();
		$mstConfig = &MstFactory::getConfig();
		
		$res = array();
				
		if($mail->type == MstConsts::MAIL_TYPE_PLAIN) { 
			$log->debug('Is not a multipart mail', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$res = $this->getMessageContentOfMail($nr, $mail);
		}elseif($mail->type == MstConsts::MAIL_TYPE_MULTIPART) {  
			$log->debug('Is multipart mail', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$res = $this->getMultipartMessageContentOfMail($nr, $mail);
		}
		if($mstConfig->isUndoLineWrapping()){
			$res['html'] = (!is_null($res['html'])) ? str_replace(' '.CHR(13).CHR(10),' ',$res['html']) : $res['html'];
			$res['body'] = (!is_null($res['body'])) ? str_replace(' '.CHR(13).CHR(10),' ',$res['body']) : $res['body'];
		}
		$log->debug('Plain text (if available) - remove html entities...', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$res['body'] = html_entity_decode($res['body']);
		
		return $res;
	}
	
	function getMessageContentOfMail($nr, $mail){
		$log		= & MstFactory::getLogger();
		$mstUtils 	= & MstFactory::getUtils();
		$mailUtils 	= & MstFactory::getMailUtils();
		$convUtils 	= & MstFactory::getConverterUtils();
		
		$singleContent = array();
		$attachs = array();
		$singleContent['body'] = null;
		$singleContent['html'] = null;
		$singleContent['attachments'] = array();
		
		$body = imap_body($this->mBox, $nr, FT_INTERNAL);
		$log->debug('Single Part Mail Raw Body: ' . $body, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$encBody = $convUtils->encodeText($body, $mail->encoding, $mail->charset);
		$log->debug('Single Part Mail Encoded Body: ' . $encBody, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		
		if($mail->hasSubtype && (strtoupper($mail->subtype) === 'HTML')){
			$log->debug('Is HTML single part mail', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$singleContent['html'] = $encBody; // html text
		}else{
			if(strtoupper($mail->subtype) !== 'CALENDAR'){
				$log->debug('Is plain text single part mail', MstConsts::LOGENTRY_MAIL_RETRIEVE);
				$singleContent['body'] = $encBody; // plain text
			}else{
				$structure = $mail->structure;
				$log->debug('This mail has subtype CALENDAR, is a an meeting invitation, consider message as attachment...', MstConsts::LOGENTRY_MAIL_RETRIEVE);
				$fileName = $mailUtils->getAttachmentFilename($structure); // extract attachment filename
				$fileName = $convUtils->imapUtf8($fileName);
				$fileName = rawurlencode($fileName);
				$contentId = $mailUtils->getContentId($structure);
				$type = $structure['type'];
				$disposition = 'ATTACHMENT';
	
				$params = '';						
				if(array_key_exists('parameters', $structure)){
					$parameters = $structure['parameters'];
					$log->debug('Found ' . count($parameters) . ' parameters for attachment', MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$params = $mailUtils->getAttachmentParameters($parameters, 255);
				}
				$attachs[] = array("filename" => $fileName, "filedata" => $encBody,
									"disposition" => $disposition, "content_id" => $contentId,
									"type" => $type, "subtype" => strtoupper($mail->subtype), "params" => $params);
				$log->debug('Meeting as attachment: ' . print_r($attachs, true), MstConsts::LOGENTRY_MAIL_RETRIEVE);
			}
		}
		$singleContent['attachments'] = $attachs;
		return $singleContent;			
	}
	
	function getMultipartMessageContentOfMail($nr, $mail){
		$log 		= & MstFactory::getLogger();
		$mstUtils 	= & MstFactory::getUtils();
		$mailUtils 	= & MstFactory::getMailUtils();
		$convUtils 	= & MstFactory::getConverterUtils();
		
		$struct = $mail->structure;
		$parts = $struct['parts'];
		$i = 0;
		$endwhile = false;		
		$attachs = array();				
		$stack = array(); // Stack while parsing message
		$body = '';
		$html = '';
		
		$rawBody = imap_body($this->mBox, $nr, FT_INTERNAL);
		$log->debug('Raw Body: ' . $rawBody, MstConsts::LOGENTRY_MAIL_RETRIEVE);
								
		while (!$endwhile) {
			if (/*!$parts[$i]*/ !array_key_exists($i, $parts)) {
				if (count($stack) > 0) {
					$log->debug('next in stack', MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$parts = $stack[count($stack)-1]["p"];
					$i     = $stack[count($stack)-1]["i"] + 1;
					array_pop($stack);
				} else {
					$log->debug('no more stack content, finished', MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$endwhile = true;
				} 
			}
			if (!$endwhile) {
				$partstring = "";							 
				foreach ($stack as $s) {
					$partstring .= ($s["i"]+1) . ".";
					$log->debug('new partstring: ' . $partstring, MstConsts::LOGENTRY_MAIL_RETRIEVE);
				}
				$partstring .= ($i+1);	
				
				if(!is_null($parts[$i])){
					$disposition 	= array_key_exists('disposition', 	$parts[$i]) ? trim(strtoupper($parts[$i]['disposition'])) 	: null;
					$subtype 		= array_key_exists('subtype', 		$parts[$i]) ? trim(strtoupper($parts[$i]['subtype'])) 		: null;
					$encoding 		= $parts[$i]['encoding'];
					$type	 		= $parts[$i]['type'];
					$typeStr = $mailUtils->getContentTypeString($type);
					$log->debug(print_r($parts[$i], true), MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$log->debug('going for part no ' . $i. ' with disposition: ' . $disposition . ', type: ' . $typeStr . ', subtype: ' . $subtype . ', encoding: ' . $encoding, MstConsts::LOGENTRY_MAIL_RETRIEVE);
				}else{
					$disposition = null;
					$subtype = null;
					$encoding = null;
					$type = null;
				}

				if (($subtype == "PLAIN") && ($disposition != "ATTACHMENT")) { // Part is Message
					$charset = $mailUtils->extractCharset($parts[$i]['parameters']);
					$log->debug('Part no ' . $i . ' (partstr: ' . $partstring . ') is plain text with charset ' . $charset . ' and encoding ' . $encoding, MstConsts::LOGENTRY_MAIL_RETRIEVE);	
					$bodyPart = imap_fetchbody($this->mBox, $nr, $partstring, FT_INTERNAL);
					$bodyPart = $convUtils->encodeText($bodyPart, $encoding, $charset);
					$log->debug('Converted Plain Text:<br/>\n'.$bodyPart, MstConsts::LOGENTRY_MAIL_RETRIEVE);	
					$body .= $bodyPart;				
					
				} elseif (($subtype == "HTML") && ($disposition != "ATTACHMENT")) { // Part is HTML Message	
					$charset = $mailUtils->extractCharset($parts[$i]['parameters']);
					$log->debug('Part no ' . $i . ' (partstr: ' . $partstring . ') is HTML text with charset ' . $charset . ' and encoding ' . $encoding, MstConsts::LOGENTRY_MAIL_RETRIEVE);	
					$htmlPart = imap_fetchbody($this->mBox, $nr, $partstring, FT_INTERNAL);	
					$htmlPart = $convUtils->encodeText($htmlPart, $encoding, $charset);
					$log->debug('Converted HTML Text:<br/>\n'.$htmlPart, MstConsts::LOGENTRY_MAIL_RETRIEVE);	
				//	$htmlPart = htmlentities($htmlPart, ENT_NOQUOTES);
				//	$log->debug('After htmlentities:<br/>\n'.$htmlPart, MstConsts::LOGENTRY_MAIL_RETRIEVE);						
					$html .= $htmlPart;						
				} else{ 
					$contentId = $mailUtils->getContentId($parts[$i]);
					$log->debug('Part no ' . $i . ' is no message part, content id: ' . $contentId, MstConsts::LOGENTRY_MAIL_RETRIEVE);
					$hasDisposition = (!is_null($disposition)) 	&& (strlen($disposition) > 0);
					$hasSubtype 	= (!is_null($subtype)) 		&& (strlen($subtype) > 0);
					$isAlternative 	= ($subtype === 'ALTERNATIVE');
					$isRelated	 	= ($subtype === 'RELATED');
					$log->debug('PART INFO disposition: ' . $hasDisposition . ', subtype: ' . $hasSubtype . ', alternative: '. $isAlternative . ', related: '. $isRelated . '', MstConsts::LOGENTRY_MAIL_RETRIEVE);
					if(!$hasDisposition && $hasSubtype && !$isAlternative && !$isRelated){
						$log->debug('Part no ' . $i . ' with subtype ' . $subtype . ' has no disposition', MstConsts::LOGENTRY_MAIL_RETRIEVE);
						if( (!is_null($contentId)) && (strlen(trim($contentId))>0) ){
							$log->debug('Assuming part with subtype ' . $subtype . ' is INLINE attachment, has content id: ' . $contentId, MstConsts::LOGENTRY_MAIL_RETRIEVE);
							$disposition = "INLINE";
						}else{
							$log->debug('Assuming part with subtype ' . $subtype . ' is an ATTACHMENT, has NO content id', MstConsts::LOGENTRY_MAIL_RETRIEVE);
							$disposition = "ATTACHMENT";
						}
					}
					if($disposition === "ATTACHMENT" || $disposition === "INLINE") { // Part is attachment
						$log->debug('Part no ' . $i . ' is attachment', MstConsts::LOGENTRY_MAIL_RETRIEVE);
						$fileName = $mailUtils->getAttachmentFilename($parts[$i]); // extract attachment filename
						$fileName = $convUtils->imapUtf8($fileName);
						$fileName = rawurlencode($fileName);
						$log->debug('Attachment (part ' . $i . ') filename: ' . $fileName. ' has content id: ' . ($contentId?$contentId:'No'), MstConsts::LOGENTRY_MAIL_RETRIEVE);	
						$attachPart = imap_fetchbody($this->mBox, $nr, $partstring);
						if(($subtype != "PLAIN") && ($subtype != "CALENDAR") && ($encoding != 0) && ($encoding != 1)){	// encoding 0 und 1 hier? Oder lieber mit subtypes arbeiten?
							$log->debug('Binary attachment, encoding: ' . $encoding, MstConsts::LOGENTRY_MAIL_RETRIEVE);
							$attachPart = base64_decode($attachPart);
						}else{	
							$log->debug('Plain text attachment, encoding: ' . $encoding, MstConsts::LOGENTRY_MAIL_RETRIEVE);	
							$attachPart = $convUtils->encodeText($attachPart, $encoding, '');
						}
						
						$params = '';						
						if(array_key_exists('parameters', $parts[$i])){
							$parameters = $parts[$i]['parameters'];
							$log->debug('Found ' . count($parameters) . ' parameters for attachment', MstConsts::LOGENTRY_MAIL_RETRIEVE);
							$params = $mailUtils->getAttachmentParameters($parameters, 255);
						}
						
						$newAttach = array("filename" => $fileName, "filedata" => $attachPart,
											"disposition" => $disposition, "content_id" => $contentId,
											"type" => $type, "subtype" => $subtype, "params" => $params);
						$log->debug('NEW_ATTACH_READY: ' . print_r($newAttach, true));
						$attachs[] = $newAttach;
						
					}																			
				}			
			}
			if( is_array($parts) && array_key_exists($i, $parts) && is_array($parts[$i]) && array_key_exists('parts', $parts[$i]) ){
				$stack[] = array("p" => $parts, "i" => $i);
				$parts = $parts[$i]['parts'];
				$i = 0;
			} else {
				$i++;
				$log->debug('no sub parts detected, next on same level', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			}
		}
		$multiContent = array();
		$multiContent['body'] = $body;
		$multiContent['html'] = $this->buildHTML($html);
		$multiContent['attachments'] = $attachs;
		$log->debug('######################### Main_Body_Parts ############################ ' . print_r($multiContent, true), MstConsts::LOGENTRY_MAIL_RETRIEVE);
		return $multiContent;
	}
	
	
	function buildHTML($str) {	
		$log = & MstFactory::getLogger();
		if(!is_null($str) && strlen($str)>0){ 	
		   	if (strpos(strtolower($str),"<html") === false){
		  		$header = "<html><head></head>\n";
			   	if (strpos(strtolower($str),"<body") === false){
			   		$body = "\n<body>\n";
			   		$str = $header . $body . $str ."\n</body></html>";
			   	} else {
			   		$str = $header . $str ."\n</html>";
			   	}
		   	}
			$log->debug('HTML after tag insertion: ' . $str, MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}
	   	return $str;
	}
	
	function isAllowed2Send($sender){
		$log = & MstFactory::getLogger();
		$sender = strtolower(trim($sender));
		$log->debug('Checking whether ' . $sender . ' is allowed to send...', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		$allowed = false;
		
		if($this->mList->sending_public == 1){ // everybody is allowed to send
			$log->debug('isAllowed2Send: everybody', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			return true;
		}else{
			$log->debug('isAllowed2Send: restricted', MstConsts::LOGENTRY_MAIL_RETRIEVE);
		}
		
		if($this->mList->sending_admin == 1){ // only admins are allowed to send
			$adminMail = strtolower(trim($this->mList->admin_mail));
			$log->debug('isAllowed2Send: admin only (' . $adminMail . ')', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$allowed = (($sender === $adminMail) ? true : false);
			if($allowed) return true;
		}
		if($this->mList->sending_recipients == 1){ // only recipients are allowed to send
			$log->debug('isAllowed2Send: recipients only', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			$mstRecipients = &MstFactory::getRecipients();
			$allowed = $mstRecipients->isRecipient($this->mList->id, $sender);
			if($allowed) return true;
		}	
		if($this->mList->sending_group == 1){ // only members of certain group are allowed to send
			$log->debug('isAllowed2Send: group only', MstConsts::LOGENTRY_MAIL_RETRIEVE);
			JLoader::import('joomla.application.component.model');
			JLoader::import( 'groupusers', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_mailster' . DS . 'models' );
			$groupusersModel = JModel::getInstance( 'groupusers', 'MailsterModel' );
			$groupUsers = $groupusersModel->getData($this->mList->sending_group_id);
			for($i=0; $i<count($groupUsers); $i++){
				$groupUser = &$groupUsers[$i];
				$guMail = strtolower(trim($groupUser->email));
				if($guMail === $sender){
					$allowed = true;
					break;
				}
			}
			if($allowed) return true;
		}
		return $allowed;
	}	
	
}
?>
