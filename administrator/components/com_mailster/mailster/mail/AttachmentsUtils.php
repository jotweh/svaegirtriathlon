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

class MstAttachmentsUtils
{		
	
	public static function getDispositionType($dispositionStr){
		$dispositionStr = trim(strtoupper($dispositionStr));
		if($dispositionStr === 'ATTACHMENT'){
			return MstConsts::DISPOSITION_TYPE_ATTACH;
		}elseif($dispositionStr === 'INLINE'){
			return MstConsts::DISPOSITION_TYPE_INLINE;
		}
		return MstConsts::DISPOSITION_TYPE_ATTACH;
	}
	
	public static function storeAttachments($baseDir, $attachs){		
		$log 		= & MstFactory::getLogger();	
		$mstUtils 	= & MstFactory::getUtils();
		$hashUtils 	= & MstFactory::getHashUtils();
		$mstEnv 	= & MstFactory::getEnvironment();
		$convUtils 	= & MstFactory::getConverterUtils();
		
		// For using JFile
		jimport('joomla.filesystem.file');
		
		$dir = $baseDir.DS.date("Ymd");
		$nrAttachs = count($attachs);
		$log->debug('storeAttachments called with ' . $nrAttachs . ' attachments in the queue');
		$log->debug('attachment base dir: ' . $baseDir );
		$savedAttachs = array();			
		if ($nrAttachs > 0) {			
			$a = 0;
			if (!is_dir($dir)) { //create specific folders
				$log->debug('we have to create (day) dir ' . $dir);
				$ok = JFolder::create(JPATH_ROOT.DS.$dir);
				$ok ? $log->debug('dir creation was ok') : $mstEnv->isDirWritable(JPATH_ROOT.DS.$dir, true, 'ERROR');
			}
			$rndStr = $hashUtils->getFixedLengthRandomString();
			$dirpath = $dir.DS.$rndStr;	
			$log->debug('target dir ' . $dirpath);		
			$cr = 0; 
			while(is_dir($dirpath) && $cr < 10) {
				$log->warning('dir already existing, build new random name, try no ' . ($cr+1));		
				$rndStr = $hashUtils->getFixedLengthRandomString();
				$dirpath = $dir . DS . $rndStr;
				$log->debug('target dir ' . $dirpath);		
				$cr = $cr + 1;  // do not try forever
			}	
			if(!is_dir($dirpath)){		
				$log->debug('creating (mail) dir ' . $dirpath);
				$ok = JFolder::create(JPATH_ROOT.DS.$dirpath);
				$ok ? $log->debug('dir creation was ok') : $mstEnv->isDirWritable(JPATH_ROOT.DS.$dirpath, true, 'ERROR');
				while ($a <= $nrAttachs-1) {
					$filename = "";
					$filename = utf8_decode($convUtils->imapUtf8($attachs[$a]['filename']));
					if ($filename == '') {
						$log->warning('We have to insert attachment file name "' .  MstConsts::ATTACHMENT_NO_FILENAME_FOUND . '" as no name is given...');
						$filename = MstConsts::ATTACHMENT_NO_FILENAME_FOUND;
					}	
					$dispositionNr = self::getDispositionType($attachs[$a]['disposition']);
					$contentId 	= $attachs[$a]['content_id'];
					$input 		= $attachs[$a]['filedata'];
					$type	 	= $attachs[$a]['type'];
					$subtype	= $attachs[$a]['subtype'];
					$params		= $attachs[$a]['params'];
					
					$filePath = $dirpath.DS.$filename;
					$attachPath = JPATH_ROOT.DS.$filePath;		
					$log->info('storeAttachments: ' . $filename . ' -> ' . $attachPath);
					
					$newSavedAttac = new stdClass();
					$newSavedAttac->filename = $filename;
					$newSavedAttac->filepath = $dirpath;
					$newSavedAttac->content_id = $contentId;
					$newSavedAttac->disposition = $dispositionNr;	
					$newSavedAttac->type = $type;
					$newSavedAttac->subtype = $subtype;	
					$newSavedAttac->params = $params;	
										
					$log->debug('storeAttachments: File Info: ' . print_r($newSavedAttac, true));
					$log->debug('Will save file to: ' . $attachPath);
					
					jimport('joomla.filesystem.file'); // For using JFile
					if (JFile::write($attachPath, $input)) {
						$newSavedAttac->success = true;
						$log->info('File stored successfully');
					} else {
						$newSavedAttac->success = false;
						$log->error('ERROR, file was not stored');
						$mstEnv->isFileWritable(JPATH_ROOT.DS.$filePath, $attachPath, true, 'ERROR');
					}
					$savedAttachs[] =  $newSavedAttac;
									
					$a++; // next attachment, no matter if succeeded or not
				}
			}else{
				$log->error('Could not create target dir in ' . $dir);
			}			 
		}
		$log->debug('leaving storeAttachments with ' . count($savedAttachs) . ' attachments stored');
		return $savedAttachs;
	}
	
	public static function getAttachmentTypeString($type, $subtype){		
		$log 		= & MstFactory::getLogger();
		$mailUtils 	= & MstFactory::getMailUtils();		
		$typeStr 	= $mailUtils->getContentTypeString($type);
		$typeStr 	= trim(strtolower($typeStr)) . '/' . trim(strtolower($subtype));
		return $typeStr;
	}
	
	public static function saveAttachmentsInDB($mailId, $attachs){		
		$log = & MstFactory::getLogger();
		$db =& JFactory::getDBO();
		
		for($i=0; $i < count($attachs); $i++){
			$attach = &$attachs[$i];
			$log->debug('Saving to database: ' . print_r($attach, true));
			$query = 'INSERT INTO ' 
							. '#__mailster_attachments' 
							. '(id,' 
							. ' mail_id,' 
							. ' filename,' 
							. ' filepath,' 
							. ' content_id,' 
							. ' disposition,'
							. ' type,'
							. ' subtype,'
							. ' params'
							. ' )VALUES'
							. ' (NULL,'
							. '  \'' . $mailId . '\','
							. ' ' . $db->quote($attach->filename) . ','
							. ' ' . $db->quote($attach->filepath) . ','
							. ' ' . $db->quote($attach->content_id). ','
							. ' ' . $db->quote($attach->disposition). ','
							. '  \'' . $attach->type . '\','
							. ' ' . $db->quote($attach->subtype). ','
							. ' ' . $db->quote($attach->params). ''
							. ')';
			$db->setQuery($query);
			$result = $db->query(); // save email to database
			$attachId = $db->insertid(); 
			if($attachId < 1){
				$log->error('Inserting of attachment failed, Error Nr: ' . $db->getErrorNum() . ', Message: ' . $db->getErrorMsg());
			}else{
				$log->info('Saved attachment ' . $attach->filename . ' of mail ' . $mailId . '  new id: ' . $attachId);	
			}		
		}
	}
	
	public static function getAttachmentsOfMail($mailId){		
		$log = & MstFactory::getLogger();
		$db	=& JFactory::getDBO();		
		$query = 'SELECT * from #__mailster_attachments WHERE mail_id = \''.$mailId.'\'';
		$db->setQuery($query);
		$result = $db->query();	
		$attachs = $db->loadObjectList();
		return $attachs;		
	}
	
	public static function getAttachment($attachId){		
		$log = & MstFactory::getLogger();
		$db	=& JFactory::getDBO();		
		$query = 'SELECT * from #__mailster_attachments WHERE id = \''.$attachId.'\'';
		$db->setQuery($query);
		$result = $db->query();	
		$attach = $db->loadObject();
		return $attach;		
	}
	
	public static function deleteAttachmentsOfMail($mailId){		
		$log = & MstFactory::getLogger();		
		$mailModel = & MstFactory::getModel('mail');
		$mailModel->setId($mailId);
		$mail = $mailModel->getData();
		if($mail->has_attachments == 1){
			$log->debug('Mail ' . $mailId . ' has attachments, delete them');
			$attachs = self::getAttachmentsOfMail($mailId);
			for($i=0; $i < count($attachs); $i++){
				$attach = &$attachs[$i];
				$filePath = $attach->filepath;
				$fileName = $attach->filename;
				$fname = JPATH_ROOT.DS.$filePath.DS.$fileName;
				if(!is_dir($fname)){
					if(file_exists($fname)){
						if(!touch($fname)){
							$log->error('can not touch ' . $fname);
						}							
						if(unlink($fname)){
							$log->debug('attachment successfully deleted/unlinked: ' . $fname);					
						}else{
							$log->error('attachment not deleted/unlinked: ' . $fname);					
						}
					}else{
						$log->error('attachment does not exist: ' . $fname);
					}			
				}else{				
					$log->error('attachment not a file: ' . $fname);
				}
			}
			
			// try to delete directory that contained attachments
			$filePath = JPATH_ROOT.DS.$filePath;
			if(is_dir($filePath)){
				if(rmdir($filePath)){
					$log->debug('attachment dir successfully deleted: ' . $filePath);		
				}else{
					$log->error('attachment dir not deleted: ' . $filePath);		
				}
			}
		}else{
			$log->debug('Mail ' . $mailId . ' has no attachments to delete');
		}		
	}
	
	
}
