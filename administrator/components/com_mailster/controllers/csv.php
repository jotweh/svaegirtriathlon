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

	jimport('joomla.application.component.controller');

	/**
	 * Mailster Component CSV Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerCsv extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{
			// execute parent's constructor
			parent::__construct();			
		}
		
		function import()
		{
			$importtarget 	= JRequest::getString('importtarget', '');
			$session = &JFactory::getSession();
  			$session->set('importtarget', $importtarget);	
  			
			$groupModel = &$this->getModel ( 'groups' );
			$listsModel = &$this->getModel ( 'lists' );		
			$groupUsersModel = $this->getModel ( 'groupusers' );
			$view  = $this->getView  ( 'csv', 'html' );
			$view->setModel( $groupModel );  
			$view->setModel( $listsModel );		
			$view->setModel( $groupUsersModel );				
			$view->display();
		}
		
		function reviewimports()
		{
			$this->import();
		}
			
				
		/**
		 * logic for CSV import (step 1/2)
		 *
		 * @access public
		 * @return void
		 */
		function startimport()
		{
			$log = &MstFactory::getLogger();
			$mailUtils = &MstFactory::getMailUtils();
			$app = JFactory::getApplication();	
			
			$filePath 		= JRequest::getString('filepath');
			$delimiter 		= JRequest::getString('delimiter');
			$dataorder 		= JRequest::getString('dataorder');
			$importtask 	= JRequest::getString('importtask');
			$targetgroup 	= JRequest::getString('targetgroup');
			$duplicateopt	= JRequest::getString('duplicateopt');
			$targetlist 	= JRequest::getString('targetlist');
			$newGroupName 	= JRequest::getString('newgroupname');
			$dataSource 	= JRequest::getString('datasource');
			
			$uploaded = false;
			$log->debug('Starting CSV import...');		
			$log->debug('Data Source: ' . $dataSource);		
			
			if($dataSource == 'local_file'){
				$log->debug('Source: Local file, we need to upload');
				// local file, so we have to upload it first
				$filePath		= JRequest::getVar('filepath_local', null, 'files', 'array' );
				
				$log->debug('File Uploads in PHP: ' . (bool) ini_get('file_uploads'));
				// Make sure that file uploads are enabled in php
				if (!(bool) ini_get('file_uploads')) {
					JError::raiseWarning('SOME_ERROR_CODE', JText::_( 'COM_MAILSTER_FILE_UPLOADS_NOT_ENABLED' ));
					return false;
				}
				
				$log->debug('File Path: ' . print_r($filePath, true));
				// If there is no uploaded file, we have a problem...
				if( ! is_array($filePath) ){				
					JError::raiseWarning('SOME_ERROR_CODE', JText::_( 'COM_MAILSTER_NO_FILE_SELECTED' ));
					return false;
				}
				
				// Check if there was a problem uploading the file.
				if ( $filePath['error'] || $filePath['size'] < 1 ){
					JError::raiseWarning('SOME_ERROR_CODE', JText::_( 'COM_MAILSTER_UPLOAD_ERROR_OCCURED' ));
					return false;
				}	
				
				// Build the appropriate paths
				$config		=& JFactory::getConfig();
				$tmp_dest 	= $config->getValue('config.tmp_path').DS.$filePath['name'];
				$tmp_src	= $filePath['tmp_name'];
		
				$log->debug('Source File: ' . $tmp_src . ', Destination File: ' . $tmp_dest);
				// Move uploaded file
				jimport('joomla.filesystem.file');
				$uploaded = JFile::upload($tmp_src, $tmp_dest);				
				$filePath = $tmp_dest; // source file for import
			}else{
				$log->debug('Source: Server file, NO need to upload');
				// server file, should be uploaded
				$uploaded = true;
				$filePath = JPATH_SITE . DS . $filePath; // source file for import	
			}
			$log->debug('Filepath: ' . $filePath);
			$log->debug('Uploaded: ' . $uploaded);
			
			// Auto detect line endings, to deal with Mac line endings...
			$oldLineEndingSetting =  ini_get('auto_detect_line_endings');
			ini_set('auto_detect_line_endings',TRUE);	
				
			if($uploaded == true){		
				$filePointer = @fopen($filePath, "r");
				if($filePointer){
					$log->debug('Got file handle of file ' . $filePath);
					$users = array();
					$i = 0;
					$log->debug('Working with delimiter: ' . $delimiter);
					while ( ($data = fgetcsv($filePointer, 500, $delimiter) ) !== false ){ 	
						if(!is_null($data))	{	
						$log->debug('Data No ' . $i . ' #cols: ' . count($data));
							if(isset($data[0]) && isset($data[1]))	{
								$users[$i] = array();
								
								$firstEnc  = mb_detect_encoding($data[0].' ', 'UTF-8, ISO-8859-1, ISO-8859-15');
								$secondEnc = mb_detect_encoding($data[1].' ', 'UTF-8, ISO-8859-1, ISO-8859-15');
								if ($firstEnc == 'UTF-8'){
									$first = utf8_decode($data[0]); 
									if(substr($first,0,1)=='?'){
										$first = substr($first,1);
									}
								}else{							
									$first = mb_convert_encoding($data[0],'UTF-8',$firstEnc);
								}
								if ($secondEnc == 'UTF-8'){
									$second = utf8_decode($data[1]);
								}else{
									$second = mb_convert_encoding($data[1],'UTF-8',$secondEnc);
								}
								
								$first = htmlentities($first);
								$second = htmlentities($second);
								
								if(strtolower($dataorder) == 'name_del_email'){
									$name = $first;
									$email = $second;
								}else{
									$name = $second;
									$email = $first;
								}
								
								if($mailUtils->isValidEmail($email)){								
									$users[$i]['name'] = $name;
									$users[$i]['email'] = $email;									
									$i++;
								}else{
									$log->debug('CSV file contains for user "' . $name . '" invalid email: ' . $email);
									JError::raiseWarning( 100,  JText::sprintf( 'COM_MAILSTER_INVALID_EMAIL_ADDRESS_X', $email) );
								}
							}else{								
								$log->debug('Skipping incomplete line: ' . print_r($data, true));
							}
						}else{
							$log->debug('Skipping invalid line');		
						}				
					}
					$log->debug('Close file handle');	
					fclose($filePointer);	
					
					$session = &JFactory::getSession();
	  				$session->set('importedusers', $users);		
	  				$session->set('importtask', $importtask);	
	  				$session->set('targetgroup', $targetgroup);		
	  				$session->set('duplicateopt', $duplicateopt);	
	  				$session->set('targetlist', $targetlist);
	  				$session->set('newgroupname', $newGroupName);	
					$log->debug('File imported successfully, #data sets loaded: ' . count($users));
	  				$app->enqueueMessage(  JText::_( 'COM_MAILSTER_FILE_LOADED_SUCCESSFULLY' ) );		
					$this->reviewimports();
				}else{
	  				$app->enqueueMessage( JText::_( 'COM_MAILSTER_FILE_NOT_FOUND' ), 'error');				
					$log->debug('Could NOT get file handle for file ' . $filePath);
					$this->import();
				}	
			}else{
	  			$app->enqueueMessage( JText::_( 'COM_MAILSTER_FILE_NOT_FOUND' ), 'error');	
				$log->debug('Could NOT upload file ' . $filePath);
				$this->import();
			}	
			ini_set('auto_detect_line_endings',$oldLineEndingSetting);			
		}
		
		/**
		 * logic for CSV import (step 2/2)
		 *
		 * @access public
		 * @return void
		 */
		function saveimport()
		{
			$importtask 	= JRequest::getString('importtask');
			$targetgroup 	= JRequest::getInt('targetgroup');
			$targetlist 	= JRequest::getInt('targetlist');
			$userCr			= JRequest::getInt('usercount');
			$newGroupName 	= JRequest::getString('newgroupname');
			$duplicateopt	= JRequest::getString('duplicateopt');
			$addedToList = false;
			$addedToGroup = false;
			
			if($importtask == 'importonly'){
				$fwdLink = 'index.php?option=com_mailster&view=users';
			}elseif($importtask == 'add2group'){				
				$fwdLink = 'index.php?option=com_mailster&controller=groupusers&task=groupusers&groupID=';
				if($targetgroup != 0){ 
					$fwdLink = $fwdLink . $targetgroup;
				}
			}elseif($importtask == 'add2list'){
				$fwdLink = 'index.php?option=com_mailster&controller=listmembers&task=listmembers&listID=' . $targetlist;
			}
			
			$importedCr = 0;
			
			for($i = 0; $i < $userCr; $i++){
				$name 		= JRequest::getString('name' . $i);
				$email	 	= JRequest::getString('email' . $i);
				
				if($email != ''){
					$duplicate = true;
					$model	= $this->getModel('user');
					if($duplicateopt == 'merge'){
						$user	= $model->isDuplicateEntry($email, true);
						if(!$user){
							$duplicate = false;
						}else{
							// we have a duplicate and load the existent user identifiers
							$userId = $user->id;
							$isJoomlaUser = $user->is_joomla_user;
						}
					}
					if(($duplicateopt == 'ignore') || ($duplicate == false)){
						// Create a new Mailster User
						$user = new stdClass();
						$user->id		= 0;
						$user->name		= $name;
						$user->email	= $email;
						$userId 		= $model->store($user);
						$isJoomlaUser = 0;
					}					
					
					if($importtask == 'add2group'){
						if($targetgroup == 0){ 
							// Create a new Group
							$model = $this->getModel('group');
							$group = new stdClass();
							$group->name = $newGroupName;
							$targetgroup = $model->store($group);
							$fwdLink = $fwdLink . $targetgroup;
						}	
						// Insert User in Group
						$model = $this->getModel('groupusers');
						$groupUser = new stdClass();
						$groupUser->user_id			= $userId;
						$groupUser->group_id		= $targetgroup;					
						$groupUser->is_joomla_user	= $isJoomlaUser;						
						$success = $model->store($groupUser);
						$addedToGroup = true;						
					}elseif($importtask == 'add2list'){
						// Insert User in Mailing List
						$model = $this->getModel('listmembers');
						$listMember = new stdClass();
						$listMember->user_id		= $userId;
						$listMember->list_id		= $targetlist;					
						$listMember->is_joomla_user	= $isJoomlaUser;						
						$success = $model->store($listMember);
						$addedToList = true;
					}					
					$importedCr++;
				}
			}			
			
			if($addedToList){
				$mstRecipients = & MstFactory::getRecipients(); 
				$mstRecipients->recipientsUpdated($targetlist); // update cache state
			}
			if($addedToGroup){
				$mstRecipients 		= & MstFactory::getRecipients(); 
				$groupUsersModel	= $this->getModel('groupusers');
				$listsToUpdRecips 	= $groupUsersModel->getListsWithGroup($targetgroup);
				
				for($k=0; $k < count($listsToUpdRecips); $k++)
				{		
					$currList = &$listsToUpdRecips[$k];											
					$mstRecipients->recipientsUpdated($currList->id); // update cache state
				}	
			}
			
			$this->setRedirect( $fwdLink, $importedCr . ' ' .  JText::_( 'COM_MAILSTER_X_USERS_IMPORTED' ) );
		}
				
					
		/**
		 * logic for cancel an action
		 *
		 * @access public
		 * @return void
		 */
		function cancel()
		{
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			
			// TODO get Params to determine where to return to (e.g. users, groups, list....)
			$this->setRedirect( 'index.php?option=com_mailster&view=groupusers' );
		}
		
		
	}
?>
