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
	 * Mailster Component Mails Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerMails extends MailsterController
	{
		/**
		* Constructor
		*/
		function __construct()
		{			
			parent::__construct();			
		}
		
		function mails()
		{
			$model = $this->getModel ( 'mails' );
			$view  = $this->getView  ( 'mails', 'html'  );
			$view->setModel( $model, true );  // default model		
			$listModel = &$this->getModel ( 'lists' );	
			$view->setModel( $listModel );	
			$view->display();			
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
			
			$group = & JTable::getInstance('mailster_mails', '');
			$group->bind(JRequest::get('post'));
			$this->mails();
		}
		
		function resendMails(){
			$model = $this->getModel ( 'mail' );
			$view  = $this->getView  ( 'resend', 'html'  );
			$view->setModel( $model, true );  // default model		
			$listModel = &$this->getModel ( 'lists' );	
			$view->setModel( $listModel );	
			$view->display();		
		}
		
		function reEnqueueMails()
		{					
			$log 		= & MstFactory::getLogger();
			$mstQueue	= & MstFactory::getMailQueue();
			$mListUtils	= & MstFactory::getMailingListUtils();
			$app = JFactory::getApplication();		
			
			$mailIds = JRequest::getVar( 'mails', array(), 'post', 'array' );
			$listIds = JRequest::getVar( 'targetLists', array(), 'post', 'array' );
			
			if(!is_null($mailIds) && !is_null($listIds)){
				for($i=0; $i<count($mailIds); $i++){
					$mailId = $mailIds[$i];
					$log->debug('Mail ' . $mailId . ' should be resend');
					for($j=0; $j<count($listIds); $j++){
						$model = $this->getModel('mail');	
						$model->setId($mailId);
						$mail = $model->getData();	// need to get mail every loop as it gets modified below
						$listId = $listIds[$j];
						$mList = $mListUtils->getMailingList($listId);
						$log->debug('Re-enqueue mail ' . $mailId . ' (from list ' . $mail->list_id . ') in mailing list: ' . $mList->id);
						
						if($mail->list_id == $listId){
							$log->debug('Email origins in the same list (id '.$mail->list_id.'), therefore set it as unblocked and unsent');
							$mstQueue->resetMailAsUnblockedAndUnsent($mailId);
							$log->debug('Add recipients to queue for reset email...');
							$mstQueue->enqueueMail($mail, $mList);
						}else{
							$log->debug('Email origins in a different list (id '.$mail->list_id.'), thus create a copy and enqueue it as a new email...');
							$mstQueue->saveAndEnqueueMail($mail, $mList); 
						}						
						
					}
				}
			}
			$app->enqueueMessage( JText::sprintf('COM_MAILSTER_SENT_X_MAILS_TO_X_MAILING_LISTS', count($mailIds), count($listIds)));
			$this->mails();
		}
		
		function download()
		{
			$attachUtils 	= & MstFactory::getAttachmentsUtils();
			$attachId = JRequest::getInt('attachId', 0);
			
			$attach = $attachUtils->getAttachment($attachId);
			
			$filePath = $attach->filepath;
			$fileName = $attach->filename;
			
			$filePath = JPATH_ROOT.DS.$filePath.DS.$fileName;
			
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.rawurldecode($fileName) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            exit; 
		}

		/**
		 * logic to show the view mail screen
		 *
		 * @access public
		 * @return void
		 */
		function view( )
		{
			JRequest::setVar( 'view', 'mail' );
			JRequest::setVar( 'hidemainmenu', 1 );

			$model 	= $this->getModel('mail');
			$task 	= JRequest::getVar('task');
							
			$user	=& JFactory::getUser();
		
			parent::display();
		}
		
		/**
		 * logic to show the mails and to filter by the mailing list 
		 *
		 * @access public
		 * @return void
		 */
		function viewByList( )
		{	
			$this->mails();	
		}
				

		/**
		 * logic to remove mails
		 *
		 * @access public
		 * @return void
		 */
		function remove()
		{
			$log 			= & MstFactory::getLogger();
			$attachUtils 	= & MstFactory::getAttachmentsUtils();
			$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
			$total = count( $cid );
				
			if (!is_array( $cid ) || $total < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}
			
			$log->debug('Deleting ' . $total . ' mails...');
			
			for($i=0; $i<$total; $i++){
				$attachUtils->deleteAttachmentsOfMail($cid[$i]);
			}

			$model = $this->getModel('mails');
			if(!$model->delete($cid)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}

			$msg = $total.' '.JText::_( 'COM_MAILSTER_MAIL_DELETED');

			$cache = &JFactory::getCache('com_mailster');
			$cache->clean();

			$this->mails();
		}
	}
?>
