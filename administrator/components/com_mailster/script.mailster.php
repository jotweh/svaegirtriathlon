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
	
/**
 * This script is heavily based on the one Nicholas K. Dionysopoulos is using
 * in his direction giving Joomla component Akeeba Backup.
 * 
 * Thanks Nicholas for writing great software like that and inspring others
 * to follow you.
 * 
 * - Holger Brandt
 */	
class com_mailsterInstallerScript
{
	public $parent = null;
	
	protected $_extension = '';
	protected $_install_sql_path = '';
	protected $_script_install = '';
	protected $_script_update = '';
	protected $_script_uninstall = '';
	
	public function __construct() {
		$this->_extension			= 'com_mailster';
		$this->_install_sql_path	= 'install.utf8.sql';
		$this->_script_install		= 'install.mailster.php';
		$this->_script_update		= 'install.mailster.php';
		$this->_script_uninstall	= 'uninstall.mailster.php';
	}
	
	function preflight($type, $parent){
	}
	
 	function postflight($type, $parent){
 	}
	
	function install($parent) {
		// Copy the install/uninstall scripts
		$this->_copyLegacyScripts($parent);
		// Load the installation script
		$this->parent = $parent->getParent();
	//	$this->_scriptLoader($this->_script_install);
		return $this->installAndUpdateScript();
	}

	function update($parent) {
		// Copy the install/uninstall scripts
		$this->_copyLegacyScripts($parent);
		// Joomla! 1.6/1.7 workaround for not running SQL on updates
		$this->_workaroundApplySQL($parent);
		// Load the udpate script
		$this->parent = $parent->getParent();
	//	$this->_scriptLoader($this->_script_update);
		return $this->installAndUpdateScript();
	}
	
	function uninstall($parent) {
		// Load the uninstallation script
		$this->parent = $parent->getParent();
	//	$this->_scriptLoader($this->_script_uninstall);
	}
	
	function installAndUpdateScript(){
		
			global $mailster_install_running;
			$mailster_install_running = 1;
		
			$inclPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'includes.php';	
			require_once($inclPath);
	
			jimport('joomla.filesystem.folder');	
			jimport('joomla.installer.helper');
			jimport('joomla.language.language');			
			       
			$lang =& JFactory::getLanguage();
			$lang->load('com_mailster', JPATH_ADMINISTRATOR, null, true); // force reload of language files
			
			$inclPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'utils'.DS.'DBUtils.php';	
			require_once($inclPath);
			$dbUtils = new MstDBUtils();
			
			$log = & MstFactory::getLogger();
			$log->info('START POST INSTALL OPERATIONS', MstConsts::LOGENTRY_INSTALLER);
			$lang =& JFactory::getLanguage();
			$lang->load('com_mailster', JPATH_ADMINISTRATOR);
			
			$installer = new JInstaller();
			$installer->setOverwrite(true);
			$pluginPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'extensions'.DS;
			$plugins = array( 	'plg_mailster.zip'					=>array('name'=>'Mail Plugin', 'isPlugin'=>true, 'element'=>'mailster', 'folder'=>'system'),
			               		'plg_mailster_subscr.zip'			=>array('name'=>'Subscriber Plugin', 'isPlugin'=>true, 'element'=>'mailstersubscriber', 'folder'=>'content'),
								'plg_mailster_profile.zip'			=>array('name'=>'Profile Subscriber Plugin', 'isPlugin'=>true, 'element'=>'mailsterprofile', 'folder'=>'system'),
								'mod_mailster_subscr.zip'			=>array('name'=>'Subscriber Module', 'isPlugin'=>false, 'element'=>'', 'folder'=>'')		               		
			                 );
			$props = parse_ini_file(JPATH_ADMINISTRATOR . DS . "components" . DS. "com_mailster" . DS . "version.properties");
			$version = $props['major'] . '.' . $props['minor'] . '.' .$props['bugfix'];
			$versionStr = 'v.' . $version . '';
			
			$msgcolor = "#B0FFB0";
		 	$msgtext  = "Mailster " . $versionStr . ' ' . JText::_( 'COM_MAILSTER_INSTALLED_SUCCESSFUL' );
			$imgSrc = 'components/com_mailster/assets/images/16-tick.png';
								
			?>					
			<center>
				<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
					<tr>
						<td valign="top">
				    		<a href="http://www.brandt-oss.com" target="_blank"><img src="<?php echo 'components/com_mailster/assets/images/logo_boss.png'; ?>" height="80" alt="Brandt OSS Logo" align="left"></a>
						</td>
						<td valign="top" width="100%">
				       	 	<strong>a project hosted at <a href="http://www.brandt-oss.com" target="_blank">Brandt OSS (Open Source Software)</a></strong>
						</td>
						<td valign="top" rowspan="2">
							<a href="index.php?option=com_mailster" ><img src="<?php echo 'components/com_mailster/assets/images/biglogo.png'; ?>" height="200" alt="Mailster Logo" align="right"></a>
						</td>
					</tr>	
					<tr>
						<td valign="top">
				    		<a href="http://www.brandt-solutions.de" target="_blank"><img src="<?php echo 'components/com_mailster/assets/images/logo_hbit.png'; ?>" height="80" alt="Brandt IT Solutions Logo" align="left"></a>
						</td>
						<td valign="top" width="100%">
				       	 	<strong>a product of <a href="http://www.brandt-solutions.de" target="_blank">Brandt IT Solutions</a></strong>
						</td>
					</tr>	
				</table>
				<table bgcolor="<?php echo $msgcolor; ?>" width ="100%">
					<tr style="height:30px">
				    	<td width="30px"><img src="<?php echo $imgSrc; ?>" height="20px" width="20px"></td>
				    	<td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
					</tr>
					<?php

					
					// Deactivate all mailing lists as part of the update process
					$res = array();
					$inclPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'mail'.DS.'MailingListUtils.php';	
					require_once($inclPath);
					$mailingListUtils = new MstMailingListUtils();
					$deactivationOk = $mailingListUtils->deactivateAllMailingLists();
				
					$msg = ($deactivationOk ? 'Mailing Lists successfully deactivated' : 'Error: could not deactivate mailing lists'); 
					$msgcolor = ($deactivationOk ? '#B0FFB0' : '#FFB0B0'); 
					$this->addMsgRow($msg, $msgcolor);
					if($deactivationOk){
						$this->addMsgRow(JText::_( 'COM_MAILSTER_DO_NOT_FORGET_TO_REACTIVTE_MAILING_LISTS' ), '#FFFF00', true);
					}
								
					
					$listsTbl 	= '#__mailster_lists';
					$logTbl		= '#__mailster_log';
					$mailsTbl 	= '#__mailster_mails';
					$usersTbl 	= '#__mailster_users';
					$queueTbl 	= '#__mailster_queued_mails';
					$attachsTbl = '#__mailster_attachments';
					
					
					/* ----- Process Updates ----- */
					// Version 0.2.0
					$res = array();
							
					$res[] = $dbUtils->renameCol($listsTbl, 	'public_list', 		'public_registration');
					$res[] = $dbUtils->renameCol($listsTbl, 	'public_members', 	'public_senders');	
					$res[] = $dbUtils->renameCol($mailsTbl, 	'timestamp', 		'receive_timestamp');	
					
// as of 0.2.5:		$res[] = $dbUtils->addColIfNotExists($listsTbl,	'recipients_send_only', 'TINYINT(1) NULL DEFAULT 0',	'public_registration');          
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'reply_to_sender', 		'TINYINT(1) NULL',				'subject_prefix');				
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'copy_to_sender', 		'TINYINT(1) NULL',				'reply_to_sender');				
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'disable_mail_footer', 	'TINYINT(1) NULL',				'copy_to_sender');
// as of 0.2.5:		$res[] = $dbUtils->addColIfNotExists($listsTbl,	'custom_header', 		'TEXT NULL',					'mail_out_use_sec_auth');
// as of 0.2.5:		$res[] = $dbUtils->addColIfNotExists($listsTbl,	'custom_footer', 		'TEXT NULL',					'custom_header');	
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'alibi_to_mail', 		'VARCHAR(45) NULL',				'disable_mail_footer');		
// as of 0.2.5:		$res[] = $dbUtils->addColIfNotExists($listsTbl,	'use_bcc',		 		'TINYINT(1) NULL DEFAULT 1',	'alibi_to_mail');		
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'bcc_count',	 		'INT NULL DEFAULT 10',			'alibi_to_mail');	
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'max_send_attempts',	'INT NULL DEFAULT 5',			'bcc_count');
							
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'blocked_mail',	 		'TINYINT(1) NULL',				'fwd_completed_timestamp');			
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'bounced_mail', 		'TINYINT(1) NULL',				'blocked_mail');
					
					
					$ok = $this->checkUpdateSuccess('0.2.0', $res);						
										
					// Version 0.2.1
					$res = array();
					
					$res[] = $dbUtils->addColIfNotExists($usersTbl,	'notes', 		'VARCHAR(45) NULL',		'email');
										
					$ok = $this->checkUpdateSuccess('0.2.1', $res);		
					
					// Version 0.2.5 / 0.3.0
					$res = array();	
					
					$res[] = $dbUtils->renameCol($listsTbl, 	'use_bcc', 			'addressing_mode');	
					$res[] = $dbUtils->renameCol($listsTbl, 	'custom_header', 	'custom_header_plain');	
					$res[] = $dbUtils->renameCol($listsTbl, 	'custom_footer', 	'custom_footer_plain');	
					$res[] = $dbUtils->renameCol($listsTbl, 	'mail_adress',	 	'list_mail');	
					
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'addressing_mode', 		'TINYINT(1) NULL DEFAULT 1',	'alibi_to_mail');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'filter_mails', 		'TINYINT(1) NULL DEFAULT 0',	'max_send_attempts');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'clean_up_subject', 	'TINYINT(1) NULL DEFAULT 1',	'filter_mails');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'sending_public',		'TINYINT(1) NULL DEFAULT 1',	'public_registration');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'sending_recipients',	'TINYINT(1) NULL DEFAULT 0',	'sending_public');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'sending_admin',		'TINYINT(1) NULL DEFAULT 0',	'sending_recipients');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'sending_group',		'TINYINT(1) NULL DEFAULT 0',	'sending_admin');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'sending_group_id',		'INT NULL',						'sending_group');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'lock_id',				'INT NULL',						'clean_up_subject');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'is_locked',			'TINYINT(1) NULL DEFAULT 0',	'lock_id');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'last_lock',			'TIMESTAMP NULL',				'is_locked');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'last_check',			'TIMESTAMP NULL',				'last_lock');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'cstate',				'INT NULL DEFAULT 0',			'last_check');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'custom_header_plain',	'TEXT NULL',					'cstate');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'custom_footer_plain',	'TEXT NULL',					'custom_header_plain');					
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'custom_header_html',	'TEXT NULL',					'custom_footer_plain');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'custom_footer_html',	'TEXT NULL',					'custom_header_html');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'mail_format_conv',		'INT NULL DEFAULT 1',			'custom_footer_html');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'mail_format_altbody',	'TINYINT(1) NULL DEFAULT 1',	'mail_format_conv');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'archive_mode',			'INT NULL DEFAULT 0',			'addressing_mode');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'bounce_mode',			'INT NULL DEFAULT 0',			'archive_mode');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,	'bounce_mail',			'VARCHAR(255) NULL',			'bounce_mode');
										
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'html', 			'TEXT NULL',					'body');					
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'message_id', 		'VARCHAR(255) NULL',			'list_id');
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'in_reply_to', 		'VARCHAR(255) NULL',			'message_id');
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'references_to',	'VARCHAR(255) NULL',			'in_reply_to');
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'hashkey', 			'VARCHAR(45) NULL',				'list_id');
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'thread_id', 		'VARCHAR(45) NOT NULL',			'list_id');	
					$res[] = $dbUtils->addColIfNotExists($mailsTbl,	'no_content', 		'TINYINT(1) NULL DEFAULT 0',	'bounced_mail');						
					
					$res[] = $dbUtils->changeColType($listsTbl, 'admin_mail', 		'VARCHAR(255)');
					$res[] = $dbUtils->changeColType($listsTbl, 'list_mail', 		'VARCHAR(255)');
					$res[] = $dbUtils->changeColType($listsTbl, 'mail_in_user', 	'VARCHAR(255)');
					$res[] = $dbUtils->changeColType($listsTbl, 'mail_out_user', 	'VARCHAR(255)');
					$res[] = $dbUtils->changeColType($listsTbl, 'alibi_to_mail', 	'VARCHAR(255)');					
					$res[] = $dbUtils->changeColType($usersTbl, 'email', 			'VARCHAR(255)');
					$res[] = $dbUtils->changeColType($usersTbl, 'name', 			'VARCHAR(255)');
					$res[] = $dbUtils->changeColType($usersTbl, 'notes', 			'VARCHAR(255)');					
					$res[] = $dbUtils->changeColType($queueTbl, 'email', 			'VARCHAR(255)');
					$res[] = $dbUtils->changeColType($queueTbl, 'name', 			'VARCHAR(255)');
					
					$res[] = $dbUtils->deleteColIfExists($listsTbl, 'enable_webform');
					
					$attachConv = $this->convertAttachmentStorage($mailsTbl, $attachsTbl);
					$senderConv = $this->convertSenderControl($listsTbl);
					$res[] = $attachConv;
					$res[] = $senderConv;
					
					if($attachConv > 0){
						$log->info('Converting Attachment Storage successful', MstConsts::LOGENTRY_INSTALLER);
						$delRes = $dbUtils->deleteCol($mailsTbl, 'attachments');
						$res[] = $delRes ? 1 : -1;
						$log->info('Dropping attachments row: ' 			. ($res ? 'ok' : 'failed'), MstConsts::LOGENTRY_INSTALLER);
					}	
									
					if($senderConv > 0){
						$log->info('Converting Sender Control successful', MstConsts::LOGENTRY_INSTALLER);
						$delRes1 = $dbUtils->deleteCol($listsTbl, 'recipients_send_only');
						$delRes2 = $dbUtils->deleteCol($listsTbl, 'public_senders');
						$res[] = $delRes1 ? 1 : -1;
						$res[] = $delRes2 ? 1 : -1;
						$log->info('Dropping recipients_send_only row: ' . ($delRes1 ? 'ok' : 'failed'), MstConsts::LOGENTRY_INSTALLER);
						$log->info('Dropping public_senders row: ' 		. ($delRes2 ? 'ok' : 'failed'), MstConsts::LOGENTRY_INSTALLER);
					}		
				
					$ok = $this->checkUpdateSuccess('0.3.0', $res);	
					
					
					// Version 0.3.1
					$res = array();	
					
					$res[] = $dbUtils->addColIfNotExists($listsTbl,		'mail_from_mode',		'TINYINT(1) NULL DEFAULT 0',	'addressing_mode');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,		'name_from_mode',		'TINYINT(1) NULL DEFAULT 0',	'mail_from_mode');					
					
					$res[] = $dbUtils->addColIfNotExists($queueTbl,		'lock_id',				'INT NULL DEFAULT 0',			'error_count');
					$res[] = $dbUtils->addColIfNotExists($queueTbl,		'is_locked',			'TINYINT(1) NULL DEFAULT 0',	'lock_id');
					$res[] = $dbUtils->addColIfNotExists($queueTbl,		'last_lock',			'TIMESTAMP NULL',				'is_locked');
										
					$res[] = $dbUtils->addColIfNotExists($attachsTbl,	'params',				'VARCHAR(255) NULL',			'subtype');
				
					$ok = $this->checkUpdateSuccess('0.3.1', $res);
					
					
					
					// Version 0.3.2
					$res = array();
					
					$res[] = $dbUtils->addColIfNotExists($listsTbl,		'mail_size_limit',			'INT NULL DEFAULT 0',			'cstate');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,		'throttle_hour',			'TINYINT(2) NULL DEFAULT 0',	'last_check');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,		'throttle_hour_cr',			'INT NULL DEFAULT 0',			'throttle_hour');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,		'throttle_hour_limit',		'INT NULL DEFAULT 0',			'throttle_hour_cr');
					$res[] = $dbUtils->addColIfNotExists($listsTbl,		'notify_not_fwd_sender',	'TINYINT(1) NULL DEFAULT 1',	'mail_size_limit');
				
					$ok = $this->checkUpdateSuccess('0.3.2', $res);
					
					/* ----- General Updates ----- */
					// Create Indexes that are not existent
					$res = array();
					$res = array_merge($res, $this->createIndexes()); 
					$ok = $this->checkUpdateSuccess('General updates', $res, true, false);
						
			
					?>
				</table>
			<table width ="100%">

			<?php 
			foreach( $plugins as $plugin => $pluginParams ):
				$package = JInstallerHelper::unpack( $pluginPath.$plugin );
				$pluginName = $pluginParams['name'];
				$pluginElement = $pluginParams['element'];
				$pluginFolder = $pluginParams['folder'];
				$isPlugin = $pluginParams['isPlugin'];
				
				if( $installer->install( $package['dir'] ) ){
					$msgcolor = "#B0FFB0";
				 	$msgtext  = $pluginName . ' ' . JText::_( 'COM_MAILSTER_INSTALLED_SUCCESSFUL' );
			 		$imgSrc = 'components/com_mailster/assets/images/16-tick.png';
				}
				else{
					$msgcolor = "#FFB0B0";
				 	$msgtext  = JText::_( 'COM_MAILSTER_INSTALL_ERROR' ) . ' ' . $pluginName;
			 		$imgSrc = 'components/com_mailster/assets/images/16-publish_x.png';
				}
				
			 ?>
				
			<tr bgcolor="<?php echo $msgcolor; ?>" style="height:30px">
		    	<td width="30px"><img src="<?php echo $imgSrc; ?>" height="20px" width="20px"></td>
		    	<td width="350px"><font size="2"><b><?php echo $msgtext; ?></b></font></td>
			<?php
				JInstallerHelper::cleanupInstall( $pluginPath.$plugin, $package['dir'] ); 
				if($isPlugin == true){
					$db = &JFactory::getDBO();
					if(version_compare(JVERSION,'1.6.0','ge')) {
						// Joomla! 1.6 / 1.7 / ...
						$query = 'SELECT `extension_id`' .
									' FROM `#__extensions`' .
									' WHERE folder = '.$db->Quote($pluginFolder) .
									' AND element = '.$db->Quote($pluginElement);
						$row =& JTable::getInstance('extension');				
					} else {
						// Joomla! 1.5 
						$query = 'SELECT `id`' .
									' FROM `#__plugins`' .
									' WHERE folder = '.$db->Quote($pluginFolder) .
									' AND element = '.$db->Quote($pluginElement);
						$row =& JTable::getInstance('plugin');
					}	
					$db->setQuery($query);
					if( $db->Query()){
						$id = $db->loadResult();						
						if ($id){							
							if($row->load($id)){ // load it
								if(version_compare(JVERSION,'1.6.0','ge')) {
									// Joomla! 1.6 / 1.7 / ...
									$row->enabled = 1; // set enabled	
								} else {
									// Joomla! 1.5 
									$row->published = 1; // set published
								}	
								if( $row->store() ){ // save it
									$msgcolor = "#C0FFC0";
					 				$msgtext  = JText::_( 'COM_MAILSTER_PLUGIN_PUBLISHED_SUCCESSFUL' );														 			    
								}else{
									$msgcolor = "#FFC0C0";
					 				$msgtext  = JText::_( 'COM_MAILSTER_PLUGIN_PUBLISH_ERROR' ). ': <br />' . $row->getError();
								}							
								?>
								<td bgcolor="<?php echo $msgcolor; ?>" width="300px" style="text-align:center;"><font size="2"><b><?php echo $msgtext; ?></b></font></td>
								<td>&nbsp;</td>
								<?php 							
							}
						}
					}	
				}else{?>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<?php 	
				}
				?>					
				</tr>
				<?php 
			endforeach;   
			
	?>
			</table>
		</center>
	<?php
	
		$log->info('FINISHED POST INSTALL OPERATIONS', MstConsts::LOGENTRY_INSTALLER);		
		
		global $mailster_install_running;
		$mailster_install_running = 0;
		
		return true; // install success
	}
	
	function createIndexes(){		
		$inclPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'utils'.DS.'DBUtils.php';	
		require_once($inclPath);
		$dbUtils = new MstDBUtils();
		
		$groupUsersTbl 	= '#__mailster_group_users';
		$listGroupsTbl 	= '#__mailster_list_groups';
		$listMembersTbl = '#__mailster_list_members';
		$logTbl			= '#__mailster_log';
		$mailsTbl 		= '#__mailster_mails';
		$queuedMailsTbl = '#__mailster_queued_mails';
		$threadsTbl 	= '#__mailster_threads';
		$usersTbl 		= '#__mailster_users';
		
		$res = array();
		
		$cols = array();
		$cols[] = 'group_id';
		$index = 'group_id';
		$res[] 	= $dbUtils->createIndexIfNotExists($groupUsersTbl, $index, $cols);
		$cols = array();
		$cols[] = 'user_id';
		$cols[] = 'is_joomla_user';
		$index = 'user';
		$res[] 	= $dbUtils->createIndexIfNotExists($groupUsersTbl, $index, $cols);
		
		$cols = array();
		$cols[] = 'list_id';
		$index = 'list_id';
		$res[] 	= $dbUtils->createIndexIfNotExists($listGroupsTbl, $index, $cols);		
		$cols = array();
		$cols[] = 'list_id';
		$cols[] = 'group_id';
		$index = 'list_group';
		$res[] 	= $dbUtils->createIndexIfNotExists($listGroupsTbl, $index, $cols);
		
		$cols = array();
		$cols[] = 'list_id';
		$index = 'list_id';
		$res[] 	= $dbUtils->createIndexIfNotExists($listMembersTbl, $index, $cols);
		$cols = array();
		$cols[] = 'user_id';
		$cols[] = 'is_joomla_user';
		$index = 'user';
		$res[] 	= $dbUtils->createIndexIfNotExists($listMembersTbl, $index, $cols);
		
		$cols = array();
		$cols[] = 'list_id';
		$index = 'list_id';
		$res[] 	= $dbUtils->createIndexIfNotExists($mailsTbl, $index, $cols);
		$cols = array();
		$cols[] = 'blocked_mail';
		$index = 'blocked_mail';
		$res[] 	= $dbUtils->createIndexIfNotExists($mailsTbl, $index, $cols);
		$cols = array();
		$cols[] = 'bounced_mail';
		$index = 'bounced_mail';
		$res[] 	= $dbUtils->createIndexIfNotExists($mailsTbl, $index, $cols);
		$cols = array();
		$cols[] = 'fwd_errors';
		$index = 'fwd_errors';
		$res[] 	= $dbUtils->createIndexIfNotExists($mailsTbl, $index, $cols);
		$cols = array();
		$cols[] = 'fwd_completed';
		$index = 'fwd_completed';
		$res[] 	= $dbUtils->createIndexIfNotExists($mailsTbl, $index, $cols);
		$cols = array();
		$cols[] = 'thread_id';
		$index = 'thread_id';
		$res[] 	= $dbUtils->createIndexIfNotExists($mailsTbl, $index, $cols);
		
		$cols = array();
		$cols[] = 'mail_id';
		$index = 'mail_id';
		$res[] 	= $dbUtils->createIndexIfNotExists($queuedMailsTbl, $index, $cols);
		$cols = array();
		$cols[] = 'mail_id';
		$cols[] = 'is_locked';
		$index = 'mail_queued_locked';
		$res[] 	= $dbUtils->createIndexIfNotExists($queuedMailsTbl, $index, $cols);
		$cols = array();
		$cols[] = 'mail_id';
		$cols[] = 'email';
		$index = 'mail_queued';
		$res[] 	= $dbUtils->createIndexIfNotExists($queuedMailsTbl, $index, $cols);
		
		$cols = array();
		$cols[] = 'email';
		$index = 'email';
		$res[] 	= $dbUtils->createIndexIfNotExists($usersTbl, $index, $cols);
				
		$cols = array();
		$cols[] = 'ref_message_id';
		$index = 'ref_message_id';
		$res[] 	= $dbUtils->createIndexIfNotExists($threadsTbl, $index, $cols);
		
		$cols = array();
		$cols[] = 'type';
		$index = 'log_type';
		$res[] 	= $dbUtils->createIndexIfNotExists($logTbl, $index, $cols);		
		$cols = array();
		$cols[] = 'log_time';
		$index = 'log_time';
		$res[] 	= $dbUtils->createIndexIfNotExists($logTbl, $index, $cols);
		
		return $res;
	}

	function convertAttachmentStorage($mailsTbl, $attachsTbl){	
		$log	 = & MstFactory::getLogger();
		
		$inclPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'utils'.DS.'DBUtils.php';	
		require_once($inclPath);
		$dbUtils = new MstDBUtils();
			
		$srcFieldExists = $dbUtils->isColExisting($mailsTbl, 'attachments');
		$log->info('Convert Attachment Storage, source field exists: ' . ($srcFieldExists ? 'Yes' : 'No'), MstConsts::LOGENTRY_INSTALLER);
		
		if($srcFieldExists){
			$db	=& JFactory::getDBO();
			$query = 'SELECT * FROM ' . $mailsTbl  . ' WHERE has_attachments = \'1\' ';
			$db->setQuery( $query );
			if (!$result = $db->query()){
				echo $db->getErrorMsg();
				return -1;
			}		
			$attachMails = $db->loadObjectList();	
			$log->info('Attachment Storage conversion, found  ' . count($attachMails) . ' that need to be converted', MstConsts::LOGENTRY_INSTALLER);
			for($i=0; $i<count($attachMails); $i++){
				$mail = &$attachMails[$i];
				$attachsCSV = $mail->attachments;
				$attachs = explode(';', $attachsCSV);
				$log->info('Mail ' . $mail->id . ' has ' . count($attachs) . ' to store', MstConsts::LOGENTRY_INSTALLER);
				for($j=0; $j < count($attachs); $j++)
				{
					$fileInfos = explode('|', $attachs[$j]);
					$fileName = rawurldecode($fileInfos[0]);
					$filePath = $fileInfos[1];
					$query = 'INSERT INTO ' 
								. $attachsTbl 
								. '(id,' 
								. ' mail_id,' 
								. ' filename,' 
								. ' filepath,' 
								. ' content_id,' 
								. ' disposition)'
								. ' VALUES'
								. ' (NULL,'
								. '  \'' . $mail->id . '\','
								. ' ' . $db->quote($fileName) . ','
								. ' ' . $db->quote($filePath) . ','
								. ' NULL,'
								. ' \'0\''
								. ')';
					$db->setQuery($query);
					$result = $db->query();
					$attachId = $db->insertid(); 
					$log->info('Converted ' . $fileName . ' of mail ' . $mail->id . ', saved as attachment ' . $attachId, MstConsts::LOGENTRY_INSTALLER);
				}
			}			
			return 1;
		}
		return 0;
	}
	
	function convertSenderControl($listsTbl){
		$log	 = & MstFactory::getLogger();
		
		$inclPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'utils'.DS.'DBUtils.php';	
		require_once($inclPath);
		$dbUtils = new MstDBUtils();
			
		$recipSendOnlyExists = $dbUtils->isColExisting($listsTbl, 'recipients_send_only');
		$publicSendersExists = $dbUtils->isColExisting($listsTbl, 'public_senders');
		$log->info('Need to convert sender control: ' . ($publicSendersExists ? 'Yes' : 'No'), MstConsts::LOGENTRY_INSTALLER);
		
		if($publicSendersExists){
			$db	=& JFactory::getDBO();
			$query = 'SELECT * FROM ' . $listsTbl;
			$db->setQuery( $query );
			if (!$result = $db->query()){
				echo $db->getErrorMsg();
				return -1;
			}		
			$lists = $db->loadObjectList();	
			
			
			$log->info('Found  ' . count($lists) . ' lists that need to be converted', MstConsts::LOGENTRY_INSTALLER);
			for($i=0; $i<count($lists); $i++){
				$mList = &$lists[$i];
				$sending_public = 1;
				$sending_recipients = 0;
				$sending_admin = 0;
				$sending_group = 0;
				if($recipSendOnlyExists){
					if($mList->public_senders == 1){
						// everybody is allowed to send
						$sending_public = 1;
					}elseif(($mList->public_senders == 0) && ($mList->recipients_send_only == 0)){
						// only admins are allowed to send
						$sending_public = 0;
						$sending_admin = 1;					
					}else{
						// only recipients are allowed to send
						$sending_public = 0;
						$sending_recipients = 1;					
					}
				}else{
					if($mList->public_senders == 1){
						$sending_public = 1;
					}else{
						$sending_public = 0;
						$sending_recipients = 1;
					}
				}
				$query = 'UPDATE ' . $listsTbl . ' SET'
						. ' sending_public = \'' . $sending_public . '\','
						. ' sending_recipients = \'' . $sending_recipients . '\','
						. ' sending_admin = \'' . $sending_admin . '\','
						. ' sending_group = \'' . $sending_group . '\''
						. ' WHERE id = \'' . $mList->id . '\'';
					
				$db = & JFactory::getDBO();
				$db->setQuery($query);
				$result	 = $db->query();
				$affRows = $db->getAffectedRows();	
				$log->info('Conversion of list ' . $mList->id 
							. ' done (public: ' . $sending_public 
							. ', recipients: ' . $sending_recipients 
							. ', admin: ' . $sending_admin .'), result: ' . $affRows, MstConsts::LOGENTRY_INSTALLER);
			}
			
			$log->info('Converted all lists', MstConsts::LOGENTRY_INSTALLER);						
			return 1;
		}
		return 0;
	}
	
	function checkUpdateSuccess($version, $res, $printErrorsOnly=false, $dbUpdate=true){
		$updError = false;
		$updAction = false;
		
		for($i=0; $i < count($res); $i++){
			if($res[$i] < 0){
				$updError = true;
				$msgcolor = "#FFB0B0";
				$msg =  $version . ': error in step ' . ($i+1) . '';
				if($dbUpdate){
					$msg = JText::sprintf( 'COM_MAILSTER_UPDATE_TO_VERSION_X_ERROR_IN_STEP_Y' , $version, ($i+1) );
				}
			}
			if($res[$i] > 0){
				$msgcolor = "#B0FFB0";
				$msg =  JText::sprintf( 'COM_MAILSTER_UPDATE_TO_VERSION_X_STEP_Y_OK' , $version, ($i+1) );
				$updAction = true;
			}
			if(($res[$i] != 0 && !$printErrorsOnly) || ($res[$i]<0 && $printErrorsOnly)){
				$this->addMsgRow($msg, $msgcolor);
			}	
		}
		
		if($dbUpdate){
			if($updError){
				$msgcolor = "#FFB0B0";
				$msg = JText::sprintf( 'COM_MAILSTER_DATABASE_UPDATE_TO_VERSION_X_FAILED_CONTACT_SUPPORT' , $version );
			}else{
				if($updAction){
					$msgcolor = "#B0FFB0";
					$msg = JText::sprintf( 'COM_MAILSTER_DATABASE_UPDATE_TO_VERSION_X_SUCCESSFUL' , $version );
				}else{
					// no update action for this version done, 
					// probably an older version as the last installed version
				}
			}

			if($updError || $updAction){
				$this->addMsgRow($msg, $msgcolor);
			}
		}
		
		return !$updError;
	}
	
	function addMsgRow($msg, $msgcolor, $onlyHighlightMsg=false){
		if($onlyHighlightMsg){
			echo '<tr style="height:30px"><td>&nbsp;</td><td bgcolor="'. $msgcolor .'" >' . $msg . '</td><td>&nbsp;</td></tr>';
		}else{
			echo '<tr bgcolor="'. $msgcolor .'" style="height:30px"><td>&nbsp;</td><td>' . $msg . '</td><td>&nbsp;</td></tr>';
		}
		
	}
	
	/*
	private function _scriptLoader($scriptfile){	
		echo 'ENTERED scriptLoader <br />';	
		if(file_exists($scriptfile)) {
			require_once($scriptfile);
		} elseif(file_exists(dirname(__FILE__).'/'.$scriptfile)) {
			require_once dirname(__FILE__).'/'.$scriptfile;
		}else {
			JError::raiseWarning('42', "Installer script file $scriptfile not found.");
		}
	}*/
	
	/**
	 * Joomla! 1.6+ won't run the SQL file on updates. It will also run none of
	 * the update SQL files the first time you update an extension to a version
	 * which has update SQL files. Therefore, we need a workaround.
	 * 
	 */
	private function _workaroundApplySQL($parent){
		$db = JFactory::getDBO();
		if(method_exists($parent, 'extension_root')) {
			$sqlfile = $parent->getPath('extension_root').'/'.$this->_install_sql_path;
		} else {
			$sqlfile = $parent->getParent()->getPath('extension_root').'/'.$this->_install_sql_path;
		}
		$buffer = file_get_contents($sqlfile);
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) != 0) {
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}
	}

	/**
	 * Copy the legacy install/uninstall scripts to the component's back-end
	 */
	private function _copyLegacyScripts($parent){
		$installFile = (string)$parent->getParent()->getManifest()->installfile;
		if ($installFile) {
			$path['src']	= $parent->getParent()->getPath('source') . '/' . $installFile;
			$path['dest']	= $parent->getParent()->getPath('extension_administrator') . '/' . $installFile;
			$parent->getParent()->copyFiles(array ($path));
		}
		
		$uninstallFile = (string)$parent->getParent()->getManifest()->uninstallfile;
		if ($uninstallFile) {
			$path['src']	= $parent->getParent()->getPath('source') . '/' . $uninstallFile;
			$path['dest']	= $parent->getParent()->getPath('extension_administrator') . '/' . $uninstallFile;
			$parent->getParent()->copyFiles(array ($path));
		}
		
		$scriptfile = (string)$parent->getParent()->getManifest()->scriptfile;
		if ($scriptfile) {
			$path['src']	= $parent->getParent()->getPath('source') . '/' . $scriptfile;
			$path['dest']	= $parent->getParent()->getPath('extension_administrator') . '/' . $scriptfile;
			$parent->getParent()->copyFiles(array ($path));
		}
	}
	
}
?>
