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
	 * Mailster Component Connnection Check Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerConnCheck extends MailsterController
	{		
		
		/**
		 * Connection Check Logic (called with Ajax request)
		 */
		function chk( )
		{			
      		$app = JFactory::getApplication();  // Get the application object.
			$mstUtils = &MstFactory::getUtils();	
			$resultArray = array();
			$res = JText::_( 'COM_MAILSTER_CONNECTION_CHECK_CALLED' );
			$mailSettingsLink = 'http://www.brandt-oss.com/products/mailster/mail-settings';		
			$ajaxParams = JRequest::getString('mtrAjaxData');
			$ajaxParams = $mstUtils->jsonDecode($ajaxParams);
			$task = $ajaxParams->{'task'};
			
			if($task == 'inboxConnCheck'){
				$res = JText::_( 'COM_MAILSTER_INBOX_CHECK_CALLED' );
			
				$in_host 	 = trim($ajaxParams->{'in_host'});
				$in_port 	 = trim($ajaxParams->{'in_port'});
				$in_user 	 = $ajaxParams->{'in_user'};
				$in_pw 		 = $ajaxParams->{'in_pw'};
				$in_secure	 = $ajaxParams->{'in_secure'};
				$in_sec_auth = $ajaxParams->{'in_sec_auth'};
				$in_protocol = $ajaxParams->{'in_protocol'};
				$in_params 	 = $ajaxParams->{'in_params'};
				
				$useSecAuth = $in_sec_auth !== '0' ? '/secure' : '';
				$useSec	 = $in_secure !== '' ? '/' . $in_secure : '';
				$protocol = $in_protocol !== '' ? '/' . $in_protocol : '';		
				
				if (extension_loaded('imap')){	
					$host = '{' . $in_host . ':' . $in_port . $useSecAuth . $protocol . $useSec . $in_params . '}'. 'INBOX';
					$mBox = @imap_open ($host, $in_user, $in_pw);
					$imapErrors = imap_errors();				
					if($mBox){
						$res = JText::_( 'COM_MAILSTER_INBOX_SETTINGS_OK' );		
						imap_close($mBox); // close mail box
					}else{						
						if($imapErrors){
							$errorMsg = "\n"."\n".JText::_( 'COM_MAILSTER_ERRORS' ).":\n";
							foreach($imapErrors as $error){
								$errorMsg =  $errorMsg."\n" . $error;
							}
						}else{
							$errorMsg = "\n"."\n".JText::_( 'COM_MAILSTER_NO_ERROR_MESSAGES_AVAILABLE' );
						}
						$errorMsg =  $errorMsg."\n";
						$res = JText::_( 'COM_MAILSTER_SETTINGS_NOT_OK_CHECK_YOUR_SETTINGS' ) . $errorMsg;
					}	
				}else{
					$res = JText::_( 'COM_MAILSTER_CONNECTION_NOT_POSSBILE_NOT_IMAP_EXTENSION_INSTALLED' )."\n";
					$res .= JText::_( 'COM_MAILSTER_INSTALL_PHP_WITH_IMAP_SUPPORT' ); // "Install PHP with IMAP support.";
				}
			
			}else if($task == 'outboxConnCheck'){
				$list_name 		 = $ajaxParams->{'list_name'};
				$admin_email 	 = $ajaxParams->{'admin_email'};
				$use_j_mailer 	 = $ajaxParams->{'use_j_mailer'};
				$out_email 	 	 = $ajaxParams->{'out_email'};
				$out_user 	 	 = $ajaxParams->{'out_user'};
				$out_pw 	 	 = $ajaxParams->{'out_pw'};
				$out_host 		 = $ajaxParams->{'out_host'};
				$out_secure 	 = $ajaxParams->{'out_secure'};
				$out_sec_auth  	 = $ajaxParams->{'out_sec_auth'};
				$out_port	  	 = $ajaxParams->{'out_port'};
				$out_name 		 = JFactory::getApplication()->getCfg('sitename');
				
				$res = JText::_( 'COM_MAILSTER_SENDER_CHECK_CALLED' );
				 
				$body = JText::_( 'COM_MAILSTER_THIS_IS_A_TEST_MESSAGE_SENT_FROM_THE_MAILING_LIST_X' ) . ' \'' . $list_name  . '\' ';
				$body .= JText::_( 'COM_MAILSTER_AT_THE_WEBPAGE_X') . ' \'' . $out_name . '\'' . "\n";
				$body .= JText::_( 'COM_MAILSTER_YOU_RECEIVE_THIS_EMAIL_BECAUSE_YOU_ARE_THE_ADMIN_OF_THE_LIST' ) . "\n\n";
				$body .= JText::_( 'COM_MAILSTER_WORKING_SETTINGS') . ":\n";
				$body .= '----------------------' . "\n";
				if($use_j_mailer !== '1')
				{
					$body .= JText::_( 'COM_MAILSTER_MAILING_LIST_HOST' ).': ' . $out_host . "\n";
					$body .= JText::_( 'COM_MAILSTER_MAILING_LIST_PORT' ).': ' . $out_port . "\n";
					$body .= JText::_( 'COM_MAILSTER_MAILING_LIST_USER' ) . ': ' . $out_user . "\n";
					$body .= JText::_( 'COM_MAILSTER_MAILING_LIST_PW' ) . ': ****** ('.JText::_( 'COM_MAILSTER_HIDDEN' ). ")\n";;
					$body .= JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE' ).': ' . ( $out_secure != '' ? strtoupper($out_secure) : JText::_( 'COM_MAILSTER_JNONE' ) ). "\n";
					$body .= JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE_AUTHENTICATION' ).': ' . ($out_sec_auth == '0' ? JText::_( 'COM_MAILSTER_JNO' ) : JText::_( 'COM_MAILSTER_JYES' )) . "\n\n";
					$body .= JText::_( 'COM_MAILSTER_IF_YOU_USE_A_PUBLIC__MAIL_PROVIDER_CONSIDER_SHARING_YOUR_SETTINGS_WITH_OTHER_USERS' )."\n";
					$body .= ' (-> ' . $mailSettingsLink . ')'. "\n";
				}else{
				 	$body = $body . 'Joomla Mailer'. "\n";
				}
				
				$mail2send =& JFactory::getMailer();
				if($use_j_mailer !== '1')
				{
					$mail2send->From  = $out_email;
					$mail2send->useSMTP($out_sec_auth == '0' ? false : true, $out_host, $out_user, $out_pw, $out_secure, $out_port); 
					$mail2send->setSender(array($out_email, $list_name));	
				}
			    $mail2send->setSubject(JText::_( 'COM_MAILSTER_MAILSTER_TEST_MAIL_OUTGOING' ));
				$mail2send->setBody($body);
				$mail2send->AddAddress($admin_email, $list_name . ' ' . JText::_( 'COM_MAILSTER_ADMIN' ));
				$mail2send->addReplyTo(array($out_email, $list_name));
				
				
				ob_start(); // activate output buffering
				$mail2send->SMTPDebug = 2;
				$sendOk = $mail2send->Send();
				$smtpDebugOutput = ob_get_contents();
				if (ob_get_level()) {
					ob_end_clean();  // deactivate output buffering
				}
				$smtpDebugOutput = 'SMTP Debug Output: ' . $smtpDebugOutput;
				
				$error =  $mail2send->IsError();	
				if($error === true) { // send errors?
					$res = JText::_( 'COM_MAILSTER_ERRORS_WHILE_SENDING_TEST_MAIL_TO_LIST_ADMIN' ) . ' ';
					$res .= '(' . $admin_email . ').' . "\n" . JText::_( 'COM_MAILSTER_SETTINGS_NOT_OK_CHECK_YOUR_SETTINGS' ) ."\n\n";
					$res .= JText::_( 'COM_MAILSTER_ERRORS' ) . ":\n" . $mail2send->ErrorInfo . " \n" . $smtpDebugOutput;
				}else{
					$res = JText::_( 'COM_MAILSTER_SENDER_SETTINGS_OK' ) . ".\n";
					$res .= JText::_( 'COM_MAILSTER_SENT_TEST_MAIL_TO_LIST_ADMIN' ) . "\n";
					$res .= '(' . JText::_( 'COM_MAILSTER_EMAIL') . ': ' . $admin_email . ') ' . "\n\n";
					$res .= JText::_( 'COM_MAILSTER_GO_CHECK_IT');
				}			
			}
			
			$resultArray['checkresult'] = $res;				
			$jsonStr  = $mstUtils->jsonEncode($resultArray);
			echo "[" . $jsonStr . "]";	
        	$app->close(); // Close the application.
		}
		
	}
?>
