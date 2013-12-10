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
	 * Mailster Component List Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerLists extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{
			parent::__construct();
		}

		
		/**
		 * logic for cancel an action
		 */
		function cancel()
		{
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			
			$group = & JTable::getInstance('mailster_lists', '');
			$group->bind(JRequest::get('post'));

			$this->setRedirect( 'index.php?option=com_mailster&view=lists' );
		}

		/**
		 * logic to create the new mailing list screen
		 */
		function add( )
		{
			$this->edit();
		}
		
		/**
		 * logic to create the edit mailing list screen
		 */
		function edit( )
		{
			JRequest::setVar( 'view', 'list' );
			JRequest::setVar( 'hidemainmenu', 1 );
			
			$model = $this->getModel ( 'list' );
			$view  = $this->getView  ( 'list', 'html'  );
			$view->setModel( $model, true );  // default model		
			$groupModel = &$this->getModel ( 'groups' );	
			$view->setModel( $groupModel );	
			$view->display();		
		}
	
		/**
		 * To be called from Ajax call, logic to toggle active/deactivate mailing lists
		 */
		function toggleActive( )
		{
      		$app = JFactory::getApplication();  // Get the application object.
			$mstUtils = &MstFactory::getUtils();	
			$resultArray = array();
			$debug = 'Debug';
			$resultArray['res'] = JText::_( 'COM_MAILSTER_TOGGLE_ACTIVE_CALLED' );	
			$cid = JRequest::getVar( 'cid', array(), 'get', 'array' );
			$model = $this->getModel('list');
			$debug = $debug . print_r($cid,  true);
			for($i=0;$i<count($cid);$i++){
				$model->setId($cid[$i]);
				$row = $model->getData();
				if($row && $row->id > 0){
					$debug = $debug . ' ' . $row->id;
					if($row->active > 0){
						$row->active = 0;	
					}else{
						$row->active = 1;
					}
					if($model->store($row)){
						$resultArray['res'] = 'true';	
					}else{
						$resultArray['res'] = 'false';	
					}
				}
			}	
			$resultArray['debug'] = $debug;
			$jsonStr  = $mstUtils->jsonEncode($resultArray);
			echo "[" . $jsonStr . "]";	
        	$app->close(); // Close the application.
		}
		
		
		/**
		 * logic to copy mailing lists
		 */
		function copy( )
		{
			$nrCopies = 0;
			$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
			$model = $this->getModel('list');
			for($i=0;$i<count($cid);$i++){
				$model->setId($cid[$i]);
				$row = $model->getData();
				if($row && $row->id > 0){
					$row->id = null;
					$row->active = 0;
					$row->name = $row->name . ' - ' . JText::_( 'COM_MAILSTER_COPY');
					if($model->store($row)){
						$nrCopies++;
					}
				}
			}
			$link = 'index.php?option=com_mailster&view=lists';
			$msg = JText::sprintf( 'COM_MAILSTER_COPIED_X_MAILING_LISTS', $nrCopies);
			$this->setRedirect( $link, $msg );
		}
		
		function apply()
		{
			$this->save();
		}
			

		/**
		 * logic to save a mailing list
		 */
		function save()
		{
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			$log = &MstFactory::getLogger();
			
			$task		= JRequest::getVar('task');

			$post = JRequest::get( 'post' );
			$post['custom_header_html'] = JRequest::getVar( 'custom_header_html', '', 'post', 'string', JREQUEST_ALLOWHTML );
			$post['custom_footer_html'] = JRequest::getVar( 'custom_footer_html', '', 'post', 'string', JREQUEST_ALLOWHTML );
			$post['datdescription'] = JRequest::getVar( 'datdescription', '', 'post','string', JREQUEST_ALLOWRAW );
			$post['datdescription']	= str_replace( '<br>', '<br />', $post['datdescription'] );
			$tab = JRequest::getString('tab', '#first');
			$model = $this->getModel('list');
			if ($returnid = $model->store($post)) {

				switch ($task)
				{
					case 'apply' :
						$link = 'index.php?option=com_mailster&controller=lists&task=edit&cid[]='.$returnid.$tab;
						break;

					default :
						$link = 'index.php?option=com_mailster&view=lists';
						break;
				}
				$msg	= JText::_( 'COM_MAILSTER_MAILING_LIST_SAVED');

				$cache = &JFactory::getCache('com_mailster');
				$cache->clean();

			} else {

				$msg 	= '';
				$link = 'index.php?option=com_mailster&view=lists';

			}
			
			// Store notifications
			
			$notifyUtils = & MstFactory::getNotifyUtils();
			$listId = JRequest::getInt('id', -1);
			
			for($i=0; $i<100; $i++){ // FIXME hope for the best that we do not have to deal with more than 100 notifys
				$notifyIdInput = 'notifyId' . $i;
				$notifyId = JRequest::getInt($notifyIdInput, -1);
				if($notifyId >= 0){
					$triggerTypeInput = 'triggerType'.$i;
					$targetTypeInput = 'targetType'.$i;
					$targetIdInput = 'targetId'.$i;
					$triggerType = JRequest::getInt($triggerTypeInput, -1);
					$targetType = JRequest::getInt($targetTypeInput, -1);
					$targetId = JRequest::getInt($targetIdInput, -1);
					
					$notify = $notifyUtils->createNewNotify();
					$notify->id = $notifyId;
					$notify->list_id = $listId;
					$notify->notify_type = MstNotify::NOTIFY_TYPE_LIST_BASED;
					$notify->trigger_type = $triggerType;
					$notify->target_type = $targetType;
					$notify->setTargetId($targetId);
					
					$notifyUtils->storeNotify($notify);
				}
			}
			
			$this->setRedirect( $link, $msg );
		}

		/**
		 * logic to remove mailing list
		 */
		function remove()
		{
			$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

			$total = count( $cid );

			if (!is_array( $cid ) || count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}

			$model = $this->getModel('lists');
			if(!$model->delete($cid)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}

			$msg = $total.' '.JText::_( 'COM_MAILSTER_MAILING_LIST_DELETED');

			$cache = &JFactory::getCache('com_mailster');
			$cache->clean();

			$this->setRedirect( 'index.php?option=com_mailster&view=lists', $msg );
		}
		
		/**
		 * To be called by an Ajax call
		 */
		function removeNotify(){
      		$app = JFactory::getApplication();  // Get the application object.
			$notifyUtils = & MstFactory::getNotifyUtils();
			$mstUtils = &MstFactory::getUtils();	
			$resultArray = array();
			$res = JText::_( 'COM_MAILSTER_REMOVE_NOTIFY_CALLED' );	
			$ajaxParams = JRequest::getString('mtrAjaxData');
			$ajaxParams = $mstUtils->jsonDecode($ajaxParams);
			$task = $ajaxParams->{'task'};			
			if($task == 'removeNotify'){			
				$notifyId = $ajaxParams->{'notifyId'};
				$rowNr = $ajaxParams->{'rowNr'};
				$res = ($notifyUtils->deleteNotify($notifyId) ? 'true' : 'false');
			}
			$resultArray['res'] = $res;	
			$resultArray['notifyId'] = $notifyId;	
			$resultArray['rowNr'] = $rowNr;				
			$jsonStr  = $mstUtils->jsonEncode($resultArray);
			echo "[" . $jsonStr . "]";	
        	$app->close(); // Close the application.
		}
		
		/**
		 * To be called by an Ajax call
		 */
		function removeFirstMailFromMailbox(){
      		$app = JFactory::getApplication();  // Get the application object.
			$mstUtils 	= &MstFactory::getUtils();	
			$listUtils	= &MstFactory::getMailingListUtils();
			$mailbox 	= &MstFactory::getMailingListMailbox();
			$resultArray = array();
			$res = JText::_( 'COM_MAILSTER_REMOVE_FIRST_MAIL_FROM_MAILBOX_CALLED' );	
			$ajaxParams = JRequest::getString('mtrAjaxData');
			$ajaxParams = $mstUtils->jsonDecode($ajaxParams);
			$res = true;
			
			$listId = $ajaxParams->{'listId'};
			$mList = $listUtils->getMailingList($listId);
			if($listUtils->lockMailingList($mList->id)){
				if($mailbox->open($mList)){
					$mailbox->removeFirstMailFromMailbox();
					$mailbox->close();
				}else{
					$res = false;
				}
				$listUtils->unlockMailingList($mList->id);
			}else{
				$res = false;
			}
				
			$res = ($res ? 'true' : 'false');
			
			$resultArray['res'] = $res;			
			$jsonStr  = $mstUtils->jsonEncode($resultArray);
			echo "[" . $jsonStr . "]";	
        	$app->close(); // Close the application.
		}
		
		/**
		 * To be called by an Ajax call
		 */
		function removeAllMailsFromMailbox(){
      		$app = JFactory::getApplication();  // Get the application object.
			$mstUtils 	= &MstFactory::getUtils();	
			$listUtils	= &MstFactory::getMailingListUtils();
			$mailbox 	= &MstFactory::getMailingListMailbox();
			$resultArray = array();
			$res = JText::_( 'COM_MAILSTER_REMOVE_ALL_MAILS_FROM_MAILBOX_CALLED' );	
			$ajaxParams = JRequest::getString('mtrAjaxData');
			$ajaxParams = $mstUtils->jsonDecode($ajaxParams);
			$res = true;
			
			$listId = $ajaxParams->{'listId'};
			$mList = $listUtils->getMailingList($listId);
			if($listUtils->lockMailingList($mList->id)){
				if($mailbox->open($mList)){
					$mailbox->removeAllMailsFromMailbox();
					$mailbox->close();
				}else{
					$res = false;
				}
				$listUtils->unlockMailingList($mList->id);
			}else{
				$res = false;
			}
				
			$res = ($res ? 'true' : 'false');
			
			$resultArray['res'] = $res;			
			$jsonStr  = $mstUtils->jsonEncode($resultArray);
			echo "[" . $jsonStr . "]";	
        	$app->close(); // Close the application.
		}
		
		/**
		 * To be called by an Ajax call
		 */
		function removeAllMailsInSendQueue(){
      		$app = JFactory::getApplication();  // Get the application object.
			$mstUtils 	= &MstFactory::getUtils();	
			$mstQueue = &MstFactory::getMailQueue();
			$resultArray = array();
			$res = JText::_( 'COM_MAILSTER_REMOVE_ALL_MAILS_FROM_QUEUE_CALLED' );	
			$ajaxParams = JRequest::getString('mtrAjaxData');
			$ajaxParams = $mstUtils->jsonDecode($ajaxParams);
			$res = true;
			
			$listId = $ajaxParams->{'listId'};
			$res = $mstQueue->removeAllMailsFromListFromQueue($listId);
				
			$res = ($res ? 'true' : 'false');
			
			$resultArray['res'] = $res;			
			$jsonStr  = $mstUtils->jsonEncode($resultArray);
			echo "[" . $jsonStr . "]";	
        	$app->close(); // Close the application.
		}
	}
?>
