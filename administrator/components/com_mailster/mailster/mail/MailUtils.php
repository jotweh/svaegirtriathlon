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

class MstMailUtils
{		

	public static function getMail($mailId){
		$db = & JFactory::getDBO();
		$query = 'SELECT * FROM #__mailster_mails WHERE id=\'' . $mailId . '\'';
		$db->setQuery( $query );
		$mail = $db->loadObject();
		return $mail;		
	}
	
	public static function extractCharset($parameters){
		$charset = '';
		for($p = 0; $p < count($parameters); $p++){
			$keyValArray = $parameters[$p];
			$attr = $keyValArray['attribute'];
			$val = $keyValArray['value'];
			if(strtoupper($attr) == "CHARSET"){
				$charset = $val;
				break;
			}					
		}		
		return $charset;
	}
	
	public static function getHeaderValue($rawHeader, $field){
		$value = null;
		$log = & MstFactory::getLogger();
		$ftHeader = self::getHeaderFieldsAndContents($rawHeader);
		$headerFields = $ftHeader[1];
		$fieldContents = $ftHeader[2];
				
		$index = self::arraySearchWithVariations($headerFields, $field);
				
		if($index !== false){
			$value = $fieldContents[$index];
		}
		return $value;
	}
	
	private static function getHeaderFieldsAndContents($rawHeader){
		preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)\r\n/m', $rawHeader, $ftHeader);
		return $ftHeader;
	}
	
	public static function getContentTypeString($type){
		switch($type){
			case MstConsts::MAIL_TYPE_PLAIN:
				return MstConsts::MAIL_TYPE_PLAIN_STR;
			case  MstConsts::MAIL_TYPE_MULTIPART:
				return MstConsts::MAIL_TYPE_MULTIPART_STR;
			case  MstConsts::MAIL_TYPE_MESSAGE:
				return MstConsts::MAIL_TYPE_MESSAGE_STR;
			case  MstConsts::MAIL_TYPE_APPLICATION:
				return MstConsts::MAIL_TYPE_APPLICATION_STR;
			case  MstConsts::MAIL_TYPE_AUDIO:
				return MstConsts::MAIL_TYPE_AUDIO_STR;
			case  MstConsts::MAIL_TYPE_IMAGE:
				return MstConsts::MAIL_TYPE_IMAGE_STR;
			case  MstConsts::MAIL_TYPE_VIDEO:
				return MstConsts::MAIL_TYPE_VIDEO_STR;
			case  MstConsts::MAIL_TYPE_OTHER:
				return MstConsts::MAIL_TYPE_OTHER_STR;
			default:
				return MstConsts::MAIL_TYPE_OTHER_STR;
		}
	}
	
	public static function getContentId($mailPart){
		$log = & MstFactory::getLogger();
		if(!is_null($mailPart)){
			$contentId = array_key_exists('id', $mailPart) ? trim($mailPart['id']) : null;
			if(!is_null($contentId) && (strlen($contentId)>0)){
				$contentId = str_replace('<', '', $contentId);
				$contentId = str_replace('>', '', $contentId);
				$log->debug('Extracted content id: ' . $contentId);
				return $contentId;
			}
		}
		return false;
	}
	
	public static function getAttachmentParameters($parameters, $maxLength=255){
		$log = & MstFactory::getLogger();
		$params = '';
		for($p = 0; $p < count($parameters); $p++){
			if(array_key_exists('attribute', $parameters[$p])){
				$attribute = (trim($parameters[$p]['attribute']));
				$value = $parameters[$p]['value'];
				$log->debug('Parameter ' . ($p+1) . ': ' . $attribute . '=' . $value);
				if(	(strtoupper($attribute) !== 'NAME')
					&& (strtoupper($attribute) !== 'FILENAME') ){
						$attrVal = '; '.$attribute.'='.$value;
						if((strlen($params)+strlen($attrVal)) <= $maxLength){
							$log->debug('-> Add to parameters...');
							$params .= $attrVal;
						}else{
							$log->warning('Parameters too long to all be stored!');
						}
					}else{											
						$log->debug('-> Parameter of a type we do not want to safe');
					}
			}
		}
		return $params;
	}
	
	public static function getAttachmentFilename($mailPart){
		$log = & MstFactory::getLogger();
		$log->debug('Get Attachment filename...');
		$ifdparameters = $mailPart['ifdparameters'];
		if ($ifdparameters){
			$dparameters = $mailPart['dparameters'];
			if( sizeof ( $dparameters ) > 0 ){
	            foreach ( $dparameters as $param ){
	                if ( (strtoupper($param['attribute']) == 'NAME') || (strtoupper($param['attribute']) == 'FILENAME') ){
						$log->debug('Found in dparameters: ' . $param['value']);
	                    return $param['value'];
	                }
	            }
	        }
		}
		
		$ifparameters = $mailPart['ifparameters'];
		if ($ifparameters){
			$parameters = $mailPart['parameters'];
			if( sizeof ( $parameters ) > 0 ){
	            foreach ( $parameters as $param ){
	                if ( (strtoupper($param['attribute']) == 'NAME') || (strtoupper($param['attribute']) == 'FILENAME') ){
						$log->debug('Found in parameters: ' . $param['value']);	                	
	                    return $param['value'];
	                }
	            }
	        }
		}
		
		$contentId = self::getContentId($mailPart);
		if ($contentId){
			$log->debug('Taking content id as filename: ' . $contentId);
			return $contentId;
		}
		
       	// we are in trouble, no filename found
		$log->warning('Could not find filename for attachment, will return "' . MstConsts::ATTACHMENT_NO_FILENAME_FOUND . '" for: '.print_r($mailPart, true));	
		return MstConsts::ATTACHMENT_NO_FILENAME_FOUND;	
         
	}
	
	private static function arraySearchWithVariations($array, $field){
		$convUtils 	= & MstFactory::getConverterUtils();
		$index = array_search($field, $array);
		
		if($index === false){
			$index = array_search(strtolower($field), $array);
		}
		if($index === false){
			$index = array_search(strtoupper($field), $array);
		}
		if($index === false){
			$index = array_search(ucfirst($field), $array);
		}
		if($index === false){
			$index = array_search(ucwords($field), $array);
		}
		
		return $index;
	}
	
	public static function isBouncedMail($rawHeader) {
		$log = & MstFactory::getLogger();
		$ftHeader = self::getHeaderFieldsAndContents($rawHeader);
		$headerFields = $ftHeader[1];
		$fieldContents = $ftHeader[2];
				
		$returnPath = 'Return-Path';
		$returnPathLowerCase = 'Return-path';
		$xReturnPath = 'X-Return-Path';
		$xEnvelopeFrom = 'X-Envelope-From';
	
		$index = self::arraySearchWithVariations($headerFields, $returnPath);
		
		if($index === false){
			$index = self::arraySearchWithVariations($headerFields, $returnPathLowerCase);
		}		
		if($index === false){
			$index = self::arraySearchWithVariations($headerFields, $xReturnPath);
		}
		if($index === false){
			$index = self::arraySearchWithVariations($headerFields, $xEnvelopeFrom);
		}
		
		if($index !== false){
			$fieldVal = $fieldContents[$index];
			if ( trim($fieldVal) == "<>" ){
				$log->info('isBouncedMail: found <> in: ' .  $headerFields[$index] . ' -> bounced!');
                return true;
	    	}else{
	    		$log->debug('isBouncedMail: not bounced, value in ' . $headerFields[$index] . ': ' . $fieldVal);
	    	}
		}else{
			$log->debug('isBouncedMail: Did not find any headers that can contain <>: ' . print_r($headerFields, true));
		}
        return false;
	}
	
	public static function checkMailWithWordsToFilter($mail){
		$containsBadWords = false;
		$body = $mail->body;
		$subject = $mail->subject;
		$mstConf = &MstFactory::getConfig();			
    	$badWords = $mstConf->getWordsToFilter();
    	if(!is_null($badWords) && is_array($badWords)){
			foreach ($badWords as $word) {
				$word = trim($word); // do not include white spaces before/after word(s)
			    $pos = stripos ($subject, $word); // case insensitive search for bad word in subject
			    if ($pos !== false) {
			    	$containsBadWords = true;
			    	break;
			    }
			    $pos = stripos ($body, $word); // case insensitive search for bad word in body
			    if ($pos !== false) {
			    	$containsBadWords = true;
			    	break;
			    }
			}
			unset($word);
    	}
		return $containsBadWords;
	}
	
	public static function undoSubjectModifications($mail, $mList){
		$log = & MstFactory::getLogger();
		$subject = $mail->subject;
		$log->debug('Subject before modification undo: ' . $subject);
		if ($mList->clean_up_subject == 1){
			$log->debug('Cleaning up subject...');
			if( (is_null($mList->subject_prefix) == false) && (trim($mList->subject_prefix) !== "") && (strlen(trim($mList->subject_prefix)) > 0) ){
				if( (!is_null($mail->in_reply_to)) && (strlen($mail->in_reply_to)>0)){ 
					// deleting everything before prefix	
					$log->debug('Is a reply, deleting everything before mailing list subject prefix...');	
					$subjectPrefix = MstMailUtils::getSubjectPrefix($mList->subject_prefix, $mList, $mail);
					$pos = strpos($subject, $subjectPrefix);
					if($pos !== false){
						$subject = substr($subject, $pos);
						$log->debug('Found prefix, new subject: ' . $subject);	
					}else{
						$log->debug('Prefix not found, subject remains: ' . $subject);	
					}
				}
			}else{
				if( (!is_null($mail->in_reply_to)) && (strlen(trim($mail->in_reply_to))>0)){ 
					// try to guess what to delete
					$log->debug('Is a reply, guessing/deleting common reply prefixes...');	
					$toDelete = array('Re: ', 'RE: ', 'Aw: ', 'AW: ');
					$emptyRep = array('',     '',     '',     '');
					$subject = str_replace($toDelete, $emptyRep, $subject);
					$log->debug('1st result: ' . $subject);
					$toDelete = array('Re:', 'RE:', 'Aw:', 'AW:');
					$emptyRep = array('',     '',     '',     '');
					$subject = str_replace($toDelete, $emptyRep, $subject);
					$log->debug('2nd result: ' . $subject);	
				}
			}			
		}
		if( (is_null($mList->subject_prefix) == false) && (trim($mList->subject_prefix) !== "") && (strlen(trim($mList->subject_prefix)) > 0) ){
			
			$subjectPrefix = MstMailUtils::getSubjectPrefix($mList->subject_prefix, $mList, $mail);
			if(strlen($subjectPrefix) > 0 ){
				$log->debug('subject prefix to search: ' . $subjectPrefix);
				$prefixPos = strpos($subject, $subjectPrefix);
				$log->debug('Search Result: ' . $prefixPos);
				if($prefixPos !== false){
					$part1 = '';
					$part2 = substr($subject, $prefixPos+strlen($subjectPrefix));
					if($prefixPos > 0){
						$part1 = substr($subject, 0, $prefixPos);
					}
					$subject = $part1 . $part2;
					$log->debug('After deleting prefix: ' . $subject);
				}
			}
		}
		
		$log->debug('Subject after modification undo: ' . $subject);
		$mail->subject = $subject;
		return $mail;
	}

	public static function undoMailBodyModifications($mail, $mList){
		$mail = MstMailUtils::removeMailHeader($mail, $mList, true); // html
		$mail = MstMailUtils::removeMailHeader($mail, $mList, false); // plain
		$mail = MstMailUtils::removeMailFooter($mail, $mList, true); // html
		$mail = MstMailUtils::removeMailFooter($mail, $mList, false); // plain
		return $mail;
	}
	
	public static function removeMailHeader($mail, $mList, $htmlRepresentation){		
		$log = & MstFactory::getLogger();
		$mstConfig = &MstFactory::getConfig();
		$altTextVars = $mstConfig->isUseAlternativeTextVars();
		if($htmlRepresentation){
			$body = $mail->html;
			$log->debug('Will search for html header markers...');
			$headerStart = MstConsts::CUSTOM_HTML_MAIL_HEADER_START;
			$headerEnd = MstConsts::CUSTOM_HTML_MAIL_HEADER_STOP;		
			$log->debug('Searching: ' . $headerStart);			
			$headerStartPos = stripos($body, $headerStart); // case insens. search
			if($headerStartPos !== false){		
				$log->debug('Header start: ' . $headerStartPos . ', now searching: ' . $headerEnd);			
				$headerEndPos = stripos($body, $headerEnd); // case insens. search
				$log->debug('Header end: ' . $headerEndPos);
				if($headerEndPos !== false){
					$log->debug('Found start and end pos, start: ' . $headerStartPos . ', end: ' . $headerEndPos);
					$part1 = substr($body, 0, $headerStartPos);
					$part2 = substr($body, ($headerEndPos+strlen($headerEnd)));
					$body = $part1 . $part2;
					$log->debug('After deleting header: ' . $body);
				}else{	
					$log->debug('Could not find header end marker');
				}
			}else{	
				$log->debug('Could not find header start marker');
			}
		}else{
			$body = $mail->body;
			$customHeader = $mList->custom_header_plain;
			if((!is_null($customHeader)) && (strlen(trim($customHeader)) > 0) ){
				$txt_email = MstConsts::TEXT_VARIABLES_EMAIL;
				$txt_name = MstConsts::TEXT_VARIABLES_NAME;
				$txt_date = MstConsts::TEXT_VARIABLES_DATE;
				$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL;
				if($altTextVars){
					$txt_email = MstConsts::TEXT_VARIABLES_EMAIL_ALT;
					$txt_name = MstConsts::TEXT_VARIABLES_NAME_ALT;
					$txt_date = MstConsts::TEXT_VARIABLES_DATE_ALT;
					$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL_ALT;
				}
				$txtVars = array($txt_email, $txt_name, $txt_date, $txt_unsub_url);
				$repVals = array('', '', '', '');
				$customHeader = str_replace($txtVars, $repVals, $customHeader); // eliminate wildcards that now cannot be found
				$header = trim(MstMailUtils::replaceWildcards($customHeader, $mList, $mail));
				if(strlen($header) > 0 ){
					$log->debug('header to search: ' . $header);				
					$headerPos = strpos($body, $header);
					$log->debug('Search Result: ' . $headerPos);
					if($headerPos !== false){
						$part1 = substr($body, 0, $headerPos);
						$part2 = substr($body, $headerPos+strlen($header));
						$body = $part1 . $part2;
						$log->debug('After deleting header: ' . $body);
					}else{	
						$log->debug('Could not find header, nothing will be removed');
					}
				}
			}
		}	
		if($htmlRepresentation){
			$mail->html = $body;
		}else{
			$mail->body = $body;
		}
		return $mail;			
	}
	
	public static function removeMailFooter($mail, $mList, $htmlRepresentation){		
		$lang =& JFactory::getLanguage();
		$lang->load('com_mailster',JPATH_ADMINISTRATOR);
		$log = & MstFactory::getLogger();
		$mstConfig = &MstFactory::getConfig();
		$altTextVars = $mstConfig->isUseAlternativeTextVars();
		if($htmlRepresentation){
			$body = $mail->html;
			$log->debug('Will search for html footer markers...');
			$footerStart = MstConsts::CUSTOM_HTML_MAIL_FOOTER_START;
			$footerEnd = MstConsts::CUSTOM_HTML_MAIL_FOOTER_STOP;	
			$log->debug('Searching: ' . $footerStart);			
			$footerStartPos = strripos($body, $footerStart); // case insens. rev. search
			if($footerStartPos !== false){		
				$log->debug('Footer start: ' . $footerStartPos . ', now searching: ' . $footerEnd);			
				$footerEndPos = strripos($body, $footerEnd); // case insens. rev. search
				$log->debug('Footer end: ' . $footerEndPos);
				if($footerEndPos !== false){
					$log->debug('Found start and end pos, start: ' . $footerStartPos . ', end: ' . $footerEndPos);
					$part1 = substr($body, 0, $footerStartPos);
					$part2 = substr($body, ($footerEndPos+strlen($footerEnd)));
					$body = $part1 . $part2;
					$log->debug('After deleting footer: ' . $body);
				}else{	
					$log->debug('Could not find footer end marker');
				}
			}else{	
				$log->debug('Could not find footer start marker');
			}
		}else{
			$body = $mail->body;
			$customFooter = $mList->custom_footer_plain;
			if( (!is_null($customFooter)) && (strlen(trim($customFooter)) > 0) ){
				
				$txt_email = MstConsts::TEXT_VARIABLES_EMAIL;
				$txt_name = MstConsts::TEXT_VARIABLES_NAME;
				$txt_date = MstConsts::TEXT_VARIABLES_DATE;
				$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL;
				if($altTextVars){
					$txt_email = MstConsts::TEXT_VARIABLES_EMAIL_ALT;
					$txt_name = MstConsts::TEXT_VARIABLES_NAME_ALT;
					$txt_date = MstConsts::TEXT_VARIABLES_DATE_ALT;
					$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL_ALT;
				}
				$txtVars = array($txt_email, $txt_name, $txt_date, $txt_unsub_url); 
				$repVals = array('', '', '', '');
				$customFooter = str_replace($txtVars, $repVals, $customFooter); // eliminate wildcards that now cannot be found
				$footer = trim(MstMailUtils::replaceWildcards($customFooter, $mList, $mail));
				if(strlen($footer) > 0 ){
					$log->debug('footer to search: ' . $footer);
					$footerPos = strpos($body, $footer);
					$log->debug('Search Result: ' . $footerPos);
					if($footerPos !== false){
						$part1 = substr($body, 0, $footerPos);
						$part2 = '';
						if($footerPos+strlen($footer)<strlen($body)){						
							$part2 = substr($body, $footerPos+strlen($footer));
						}
						$body = $part1 . $part2;
						$log->debug('After deleting footer: ' . $body);
					}
				}
			}
			
			$disableFooter = ($mList->disable_mail_footer == 1 ? true : false);
			if(!$disableFooter){
				$s1 = "\x0d\x0a" . "\x0d\x0a" . "\x0d\x0a" 
					. "\x0d\x0a" . "\x0d\x0a" . JText::_( 'COM_MAILSTER_COPYRIGHT_AND_CO'). "\x0d\x0a" ;
				$pos = strpos($body, $s1);
				if($pos===false){
					$s1 = JText::_( 'COM_MAILSTER_COPYRIGHT_AND_CO'). "\x0d\x0a" ;
					$pos = strpos($body, $s1);
				}
				if($pos===false){
					$s1 = JText::_( 'COM_MAILSTER_COPYRIGHT_AND_CO');
					$pos = strpos($body, $s1);
				}
				if($pos!==false){
					$body = str_replace($s1, '', $body);
					$log->debug('Located footer');
				}else{	
					$log->debug('Did not locate footer yet');
					$s1 = JText::_( 'COM_MAILSTER_COPYRIGHT_AND_CO');
					$len = strlen($s1);
					$cutP = floor($len/3);
					$subPos1 = strpos($s1, ' ', $cutP);
					$subPos2 = strpos($s1, ' ', $cutP*2-1);
					if(($subPos1 !== false) && ($subPos2  !== false)
						 && ($subPos1 > 0) && ($subPos2 > 0)){
						$ss1 = substr($s1, 0, $subPos1);
						$ss3 = substr($s1, $subPos2);
						$log->debug('Could split footer for searching, ss1=' . $ss1 . ', ss2=' . $ss3);
						$pos1 = strpos($body, $ss1);
						$pos3 = strpos($body, $ss3);
						if(($pos1 !== false) && ($pos3  !== false)
						 && ($pos1 > 0) && ($pos3 > 0)){
							$log->debug('Found outer parts at pos ' . $pos1 . ' and ' . $pos3 . ' (total: ' . strlen($body) . '), string to remove: '. substr($body,$pos1,$pos3+strlen($ss3)));
						 	$b1 = substr($body, 0, $pos1);
						 	$b2 = substr($body, $pos3+strlen($ss3));
						 	$body = $b1 . $b2;
						 }
					}				
				}	
				$log->debug('After deleting footer: ' . $body);
			}else{				
				$log->debug('Could not find footer, Copyright footer disabled, nothing will be removed');
			}
		}
	
		if($htmlRepresentation){
			$mail->html = $body;
		}else{
			$mail->body = $body;
		}
		return $mail;		
	}
	
	public static function replaceWildcards($text, $mList, $mail){	
		$log = & MstFactory::getLogger();
		if(is_null($text) || (strlen(trim($text)) == 0)){
			return $text;
		}
		$newText = $text;
		
		$mstConfig = & MstFactory::getConfig();
		$dateUtils = &MstFactory::getDateUtils();
		$subscrUrl = & MstFactory::getSubscribeUtils();
		$unsubscrUrl = $subscrUrl->getUnsubscribeURL($mail);
		$atEmail = $mail->from_email;
		$atName = $mail->from_name;
	    $atDate = $dateUtils->formatDateAsConfigured($mail->receive_timestamp);
		$atList = $mList->name;
		$atSite = JFactory::getApplication()->getCfg('sitename');	
		$altTextVars = $mstConfig->isUseAlternativeTextVars();
		
		$txt_email = MstConsts::TEXT_VARIABLES_EMAIL;
		$txt_name = MstConsts::TEXT_VARIABLES_NAME;
		$txt_date = MstConsts::TEXT_VARIABLES_DATE;
		$txt_list = MstConsts::TEXT_VARIABLES_LIST;
		$txt_site = MstConsts::TEXT_VARIABLES_SITE;
		$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL;
		if($altTextVars){
			$txt_email = MstConsts::TEXT_VARIABLES_EMAIL_ALT;
			$txt_name = MstConsts::TEXT_VARIABLES_NAME_ALT;
			$txt_date = MstConsts::TEXT_VARIABLES_DATE_ALT;
			$txt_list = MstConsts::TEXT_VARIABLES_LIST_ALT;
			$txt_site = MstConsts::TEXT_VARIABLES_SITE_ALT;
			$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL_ALT;
		}
		$txtVars = array($txt_email, $txt_name, $txt_date, $txt_list, $txt_site);
		$repVals = array($atEmail, $atName, $atDate, $atList, $atSite);
		
		if($unsubscrUrl){
			$txtVars[] = $txt_unsub_url;
			$repVals[] = $unsubscrUrl;
		}
		$newText = str_replace($txtVars, $repVals, $text);
		return $newText;
	}
	
	public static function getSubjectPrefix($subjectPrefixWithWildcards, $mList, $mail){		
		$mstConfig = &MstFactory::getConfig();
		$altTextVars = $mstConfig->isUseAlternativeTextVars();		
		$txt_email = MstConsts::TEXT_VARIABLES_EMAIL;
		$txt_name = MstConsts::TEXT_VARIABLES_NAME;
		$txt_date = MstConsts::TEXT_VARIABLES_DATE;
		$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL;
		if($altTextVars){
			$txt_email = MstConsts::TEXT_VARIABLES_EMAIL_ALT;
			$txt_name = MstConsts::TEXT_VARIABLES_NAME_ALT;
			$txt_date = MstConsts::TEXT_VARIABLES_DATE_ALT;
			$txt_unsub_url = MstConsts::TEXT_VARIABLES_UNSUBSCRIBE_URL_ALT;
		}
		$txtVars = array($txt_email, $txt_name, $txt_date, $txt_unsub_url);
		$repVals = array('', '', '', '');
		$customPrefix = str_replace($txtVars, $repVals, $subjectPrefixWithWildcards); // eliminate wildcards that now cannot be found
		$subjectPrefix = trim(MstMailUtils::replaceWildcards($customPrefix, $mList, $mail));
		return $subjectPrefix;
	}
	
	public static function getReplyToArray($listObj, $senderMail, $senderName){
		$replyTo = null;
		if($listObj->reply_to_sender == 0){ // reply to list
			$replyTo = array($listObj->list_mail, $listObj->name);
		}elseif($listObj->reply_to_sender == 1){ // reply to sender
			$replyTo = array($senderMail, $senderName);
		}elseif($listObj->reply_to_sender == 2){ // reply to sender (and optional to list with reply-to-all for CC address...)
			$replyTo = array(); // don't set reply to!
		}else{ // reply to list (default)
			$replyTo = array($listObj->list_mail, $listObj->name);
		}
		return $replyTo;
	}
	
	public static function addRemoveConvertBodyParts($mail, $mList){
		if($mList->mail_format_conv == MstConsts::MAIL_FORMAT_CONVERT_HTML){
			// we want to have a HTML email
			if(is_null($mail->html) || strlen(trim($mail->html)) == 0){
				$mail->html = self::getHTMLVersionOfPlaintextBody($mail->body);
			}
			if($mList->mail_format_altbody == MstConsts::MAIL_FORMAT_ALTBODY_YES){ // include alt. body?
				if(is_null($mail->body) || strlen(trim($mail->body)) == 0){ // plain text version there?
					$mail->body = self::getPlainTextVersionOfHTMLBody($mail->html); // generate plain text version
				}
			}else{ // do not include alt. body
				$mail->body = null; // remove plain text part
			}
		}elseif($mList->mail_format_conv == MstConsts::MAIL_FORMAT_CONVERT_PLAIN){
			// we want to have a plain text email
			if(is_null($mail->body) || strlen(trim($mail->body)) == 0){
				$mail->body = self::getPlainTextVersionOfHTMLBody($mail->html);
			}
			$mail->html = null; // remove html part
		}elseif($mList->mail_format_conv == MstConsts::MAIL_FORMAT_CONVERT_NONE){
			// take the parts as given, only change: when html only, then include plain text version if wanted
			if(!is_null($mail->html) && strlen(trim($mail->html)) > 0){ // is html mail
				if(is_null($mail->body) || strlen(trim($mail->body)) == 0){ // no plain text version
					if($mList->mail_format_altbody == MstConsts::MAIL_FORMAT_ALTBODY_YES){ // plain text version needed
						$mail->body = self::getPlainTextVersionOfHTMLBody($mail->html); // generate plain text version
					}
				}
			}
		}
		
		return $mail;
	}
	
	public static function getPlainTextVersionOfHTMLBody($html){
		$plain = $html;
		$ok = MstFactory::loadLibrary(MstConsts::LIB_HTML2TEXT);
		if($ok){
			$h2t = new MstHtml2Text($html);
			$plain = $h2t->get_text(); 
		}
		return $plain;
	}
	
	public static function getHTMLVersionOfPlaintextBody($plain){
		$html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'
				 . '<html>'
					 . '<head>'
					 	. '<meta http-equiv="content-type" content="text/html; charset=utf-8">'
					 . '</head>'
					 . '<body bgcolor="#ffffff" text="#000000">'
					 	. nl2br($plain)
					 . '</body>'
				 . '</html>';
		return $html;
  
	}
	
	public static function getListIDMailHeader($mList){
		$mstUtils 	= & MstFactory::getUtils();
		$env 		= & MstFactory::getEnvironment();
		$config 	=& JFactory::getConfig();
		$site = $config->getValue( 'config.sitename' );
		$listIdHeader = MstConsts::MAIL_HEADER_LIST_ID . ': ' . $mList->name . ' at ' .  $site;
		$listIdHeader .= ' <list' . $mList->id . '.' . $env->getDomainName() . '>';
		return $listIdHeader;      
	}

	private static function appendMailHeader($mList, $body, $isHtml=false){
		if(!is_null($body)){
			if($isHtml){
				$body = MstConsts::CUSTOM_HTML_MAIL_HEADER_START
							. $mList->custom_header_html
							. nl2br("\x0d\x0a") 
							. MstConsts::CUSTOM_HTML_MAIL_HEADER_STOP 
							. $body;	
			}else{
				$body = $mList->custom_header_plain . "\x0d\x0a" . $body;
			}
		}
		return $body;
	}

	private static function appendMailFooter($mList, $body, $isHtml=false){
		$log = & MstFactory::getLogger();		
		$mstApp = & MstFactory::getApplication();
		$disableFooter = ($mList->disable_mail_footer == 1 ? true : false);
		$pTyp = $mstApp->getProductType('com_mailster');
		$isFreeEdition = $mstApp->isFreeEdition('com_mailster', $pTyp, 'f7647518248eb8ef2b0a1e41b8a59f34');
		
		if(!is_null($body)){
			if($isHtml){
				$body .= (MstConsts::CUSTOM_HTML_MAIL_FOOTER_START . nl2br("\x0d\x0a"). $mList->custom_footer_html);
			}else{
				$body .= ("\x0d\x0a". $mList->custom_footer_plain);
			}
			
			if($isFreeEdition || !$disableFooter){
				$body .= ($isHtml ? nl2br("\x0d\x0a" . "\x0d\x0a" . "\x0d\x0a" 
						. "\x0d\x0a" . "\x0d\x0a") : ("\x0d\x0a" . "\x0d\x0a" . "\x0d\x0a" 
						. "\x0d\x0a" . "\x0d\x0a")) . JText::_( 'COM_MAILSTER_COPYRIGHT_AND_CO')
						. ($isHtml ? nl2br("\x0d\x0a") : ("\x0d\x0a"));
			}
			
			if($isHtml){
				$body .= (MstConsts::CUSTOM_HTML_MAIL_FOOTER_STOP);
			}
		}
		
		return $body;
	}
	
	public static function modifyMailContent($mList, $mail) {	
		$log = & MstFactory::getLogger();			
		$lang =& JFactory::getLanguage();
		$lang->load('com_mailster',JPATH_ADMINISTRATOR);
		
		$body = $mail->body;	
		$body = self::appendMailHeader($mList, $body); // add header in plain text part
		$body = self::appendMailFooter($mList, $body); // add footer in plain text part
		
		$html = trim($mail->html);
		if((!is_null($html)) && (strlen($html)>0)){ // has mail HTML part?
			
			// add header in html part
			$startOfHtml = strpos(strtolower($html), strtolower('<html')); // find first occurence of opening html tag
			if($startOfHtml !== false){
				$startOfHtml = strpos(strtolower($html), strtolower('>'), $startOfHtml) + 1;
				$startOfBody = strpos(strtolower($html), strtolower('<body')); // find first occurence of opening body tag
				if($startOfBody !== false){
					$startOfBody = strpos(strtolower($html), strtolower('>'), $startOfBody) + 1;
					$insertPos = max($startOfBody, $startOfHtml);
				}else{
					$insertPos = $startOfHtml;
				}
				$htmlBeforeHeader = substr($html, 0, $insertPos);
				$htmlAfterHeader = substr($html, $insertPos);
				$htmlAfterHeader = self::appendMailHeader($mList, $htmlAfterHeader, true);
				$html = $htmlBeforeHeader . $htmlAfterHeader;
			}			
						
			// add footer in html part
			$endOfHtml = strrpos(strtolower($html), strtolower('</html>')); // find last occurence of closing html tag
			if($endOfHtml){
				$endOfBody = strrpos(strtolower($html), strtolower('</body>')); // find last occurence of closing body tag
				if($endOfBody){
					$insertPos = min($endOfBody, $endOfHtml);
				}else{
					$insertPos = $endOfHtml;
				}
				
				$htmlBeforeFooter = substr($html, 0, $insertPos);
				$htmlAfterFooter = substr($html, $insertPos);
				$htmlBeforeFooter = self::appendMailFooter($mList, $htmlBeforeFooter, true);
				$html = $htmlBeforeFooter . $htmlAfterFooter;
			}			
		}
		
		$mail->body 	= self::replaceWildcards($body, $mList, $mail);
		$mail->html 	= self::replaceWildcards($html, $mList, $mail);
		$mail->subject 	= self::modifyMailSubject($mail, $mList);
		$log->debug(print_r($mail, true));
		
		return $mail;
	}
	
	public static function modifyMailSubject($mail, $mList){
		$subject = $mList->subject_prefix . $mail->subject;
		return self::replaceWildcards($subject, $mList, $mail);
	}
	
	public static function getContentOfHtmlBody($body){
		$env = & MstFactory::getEnvironment();
		$content = $body;
		if($env->domExtensionInstalled()){
			$doc = new DomDocument();
			$doc->preserveWhiteSpace = false;
			$doc->validateOnParse = false;
			libxml_use_internal_errors(true); // disable warnings during HTML loading						
			if($doc->loadHTML($body)){
				$xpath = new DOMXPath($doc);
				$body = $xpath->query('/html/body');
				$element = $body->item(0);		
				$innerHTML = "";
			    $children = $element->childNodes;
			    foreach ($children as $child){
			        $tmp_dom = new DOMDocument();
			        $tmp_dom->appendChild($tmp_dom->importNode($child, true));
			        $innerHTML.=trim($tmp_dom->saveHTML());
			    }
			    $content = $innerHTML;
			}
		}
		return $content;
	}
	
	public static function replaceContentIdsWithAttachments($content, $attachs){
		$log = & MstFactory::getLogger();	
		$cids = array();
		$cidPattern = '"cid:';
		$startPos = 0;
		$pos1 = true;
		while($pos1){			
			$log->debug('search for ' . $cidPattern . ' from '  . $startPos);	
			$pos1 = strpos($content, $cidPattern, $startPos);
			if($pos1 !== false){
				$log->debug('cId pos found at ' . $pos1);
				$pos2 = strpos($content, '"', $pos1+1);
				if($pos2 !== false){
					$cId = substr($content, $pos1+strlen($cidPattern), $pos2-$pos1-strlen($cidPattern));
					$log->debug('cId entry: ' . $cId);
					$cids[] = $cId;
				}
				$startPos = $pos1 + 1;
			}
		}
		
		for($i=0; $i < count($cids); $i++){
			$cId = $cids[$i];					
			for($j=0; $j < count($attachs); $j++){
				$attach = &$attachs[$j];
				$contentId = $attach->content_id;
				if( !is_null($contentId) && (trim($contentId) !== '' )){
					if($contentId === $cId){
						
						$fPath = str_replace('\\', '/', ($attach->filepath.DS));
						$fPath = $fPath.$attach->filename;
						$fPath = rawurlencode($fPath);
						$fPath = str_replace('%2F', '/', $fPath);
						$fPath = JURI::root().$fPath;
						$u =& JURI::getInstance( $fPath );
						$fileUri = $u->toString();
						$content = str_replace($cidPattern.$cId, '"'.$fileUri, $content);
					}
				}
			}
		}
		
		return $content;
	}
	
	public static function isValidEmail($email){
		$email = trim($email);
		
		$atPos = strpos($email, '@');
		if($atPos != false && $atPos > 0 && $atPos < (strlen($email)-1)){
			$namePart = substr($email, 0, $atPos);
			$addressPart = substr($email, $atPos+1);
			
			$dotPos = strpos($addressPart, '.');
			if($dotPos != false && $dotPos > 0 && $dotPos < strlen($addressPart)){
				$domainPart = substr($addressPart, 0, $dotPos);
				$tldPart = substr($addressPart, $dotPos+1);
								
				if( (strlen($namePart) > 0) && (strlen($domainPart) > 0) && (strlen($tldPart) > 0) ){
					return true;
				}
			}
		}
		return false;
	}
	
}
