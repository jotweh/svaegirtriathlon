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

	jimport('joomla.application.component.model');
	jimport('joomla.utilities.date');
	
	/**
	 * Mailster Main Model - For Start Page
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelMailster extends JModel
	{
		/**
		 * Mailster data array
		 *
		 * @var array
		 */
		var $_data = null;

		
		/**
		 * Constructor
		 *
		 */
		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Method to get Mailster data
		 *
		 * @access public
		 * @return array
		 */
		function getData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{			
				$this->_data = new stdClass();
				$this->_data = $this->_getGeneralListStats($this->_data);
				$this->_data = $this->_getDetailedListStats($this->_data);
				$this->_data = $this->_getMailStats($this->_data);
			}
			return $this->_data;
		}
		
		private function _getGeneralListStats($data)
		{		
			$plgUtils = &MstFactory::getPluginUtils();
				
			$query = 'SELECT count( * ) AS totalLists'
						. ' FROM #__mailster_lists'
						. ' WHERE 1';
						
			$this->_db->setQuery($query);

			$totalLists = $this->_db->loadObject();
									
			$query = 'SELECT count( * ) AS unpublishedLists'
						. ' FROM #__mailster_lists'
						. ' WHERE published =\'0\'' ;
			$this->_db->setQuery($query);

			$unpublishedLists = $this->_db->loadObject();
									
			$query = 'SELECT count( * ) AS inactiveLists'
						. ' FROM #__mailster_lists'
						. ' WHERE active =\'0\'' ;
			$this->_db->setQuery($query);

			$inactiveLists = $this->_db->loadObject();
						
			$data->totalLists 				= $totalLists->totalLists;
			$data->unpublishedLists 		= $unpublishedLists->unpublishedLists;
			$data->inactiveLists 			= $inactiveLists->inactiveLists;
			$data->mailPluginStatus 		= $plgUtils->isPluginActive('mailster', 'system');
			$data->subscriberPluginStatus 	= $plgUtils->isPluginActive('mailstersubscriber', 'content');
			$data->profilePluginStatus 		= $plgUtils->isPluginActive('mailsterprofile', 'system');
			$data->cbBridgePluginStatus 	= $plgUtils->isPluginActive('mailstercb', 'system');
			$jApp =& JFactory::getApplication();			
			$pluginUtils = MstFactory::getPluginUtils();		
			$currentTime = JFactory::getDate(time());	
			$dateUtils = &MstFactory::getDateUtils();
			$data->curTime	 = $dateUtils->formatDateAsConfigured($currentTime->toFormat());
			$data->nextRetrieveRun	 = $dateUtils->formatDateAsConfigured($pluginUtils->getNextMailCheckTime());
			$data->nextSendRun	 = $dateUtils->formatDateAsConfigured($pluginUtils->getNextMailSendTime());
				
			return $data;
		}
		
		private function _getDetailedListStats($data)
		{
			$data->lists = array();
			
			$query = 'SELECT * '
						. ' FROM #__mailster_lists'
						. ' WHERE 1'
						. ' ORDER BY name';
						
			$lists = $this->_getList($query);
			
			for($i=0; $i < count($lists); $i++)
			{
				$list = &$lists[$i];
						
				$query = 'SELECT count( * ) AS totalMails'
						. ' FROM #__mailster_mails'
						. ' WHERE list_id =\'' . $list->id . '\''
						. ' AND ('
						. '          (bounced_mail IS NULL AND blocked_mail IS NULL)'
						. '       OR (bounced_mail = \'0\' AND blocked_mail = \'0\')'
						. ' )';
							
				$this->_db->setQuery($query);
	
				$totalMails = $this->_db->loadObject();		
				
				
				$query = 'SELECT count( * ) AS unsentMails'
							. ' FROM #__mailster_mails'
							. ' WHERE fwd_completed =\'0\''
							. ' AND list_id =\'' . $list->id . '\''
							. ' AND ('
							. '          (bounced_mail IS NULL AND blocked_mail IS NULL)'
							. '       OR (bounced_mail = \'0\' AND blocked_mail = \'0\')'
							. ' )';
							
				$this->_db->setQuery($query);
	
				$unsentMails = $this->_db->loadObject();
				
				$query = 'SELECT count( * ) AS blockedFilteredBounced'
							. ' FROM #__mailster_mails'
							. ' WHERE ('
							. ' 		(blocked_mail IS NOT NULL AND blocked_mail != \'' . MstConsts::MAIL_FLAG_BLOCKED_NOT_BLOCKED . '\')'
							. '		OR 	(bounced_mail IS NOT NULL AND bounced_mail != \'' . MstConsts::MAIL_FLAG_BOUNCED_NOT_BOUNCED . '\')'
							. ' )'
							. ' AND list_id =\'' . $list->id . '\'';
							
				$this->_db->setQuery($query);
	
				$blockedFilteredBounced = $this->_db->loadObject();	
				
				
				$query = 'SELECT count( * ) AS errorMails'
							. ' FROM #__mailster_mails'
							. ' WHERE fwd_errors >\'0\''
							. ' AND list_id =\'' . $list->id . '\'';
							
				$this->_db->setQuery($query);
	
				$errorMails = $this->_db->loadObject();
				
				$query = 'SELECT count( * ) AS bouncedMails'
							. ' FROM #__mailster_mails'
							. ' WHERE (bounced_mail IS NOT NULL AND bounced_mail != \'' . MstConsts::MAIL_FLAG_BOUNCED_NOT_BOUNCED . '\')'
							. ' AND list_id =\'' . $list->id . '\'';
							
				$this->_db->setQuery($query);
	
				$bouncedMails = $this->_db->loadObject();
				
				$query = 'SELECT count( * ) AS blockedMails'
							. ' FROM #__mailster_mails'
							. ' WHERE blocked_mail = \'' . MstConsts::MAIL_FLAG_BLOCKED_BLOCKED . '\''
							. ' AND list_id =\'' . $list->id . '\'';
							
				$this->_db->setQuery($query);
	
				$blockedMails = $this->_db->loadObject();
				
				$query = 'SELECT count( * ) AS filteredMails'
							. ' FROM #__mailster_mails'
							. ' WHERE blocked_mail = \'' . MstConsts::MAIL_FLAG_BLOCKED_FILTERED . '\''
							. ' AND list_id =\'' . $list->id . '\'';
							
				$this->_db->setQuery($query);
	
				$filteredMails = $this->_db->loadObject();
				
				$mstRecipients = &MstFactory::getRecipients();
				$recipients = $mstRecipients->getTotalRecipientsCount($list->id);
				
				$data->lists[$i] = $list;
				$data->lists[$i]->totalMails = $totalMails->totalMails;
				$data->lists[$i]->blockedFilteredBounced = $blockedFilteredBounced->blockedFilteredBounced; 
				$data->lists[$i]->unsentMails = $unsentMails->unsentMails;
				$data->lists[$i]->errorMails = $errorMails->errorMails;
				$data->lists[$i]->bouncedMails = $bouncedMails->bouncedMails;
				$data->lists[$i]->blockedMails = $blockedMails->blockedMails;
				$data->lists[$i]->filteredMails = $filteredMails->filteredMails;
				$data->lists[$i]->recipients = $recipients;
			}
			return $data;
		}
	
		private function _getMailStats($data)
		{
					
			$query = 'SELECT count( * ) AS totalMails'
						. ' FROM #__mailster_mails'
						. ' WHERE 1';
						
			$this->_db->setQuery($query);

			$totalMails = $this->_db->loadObject();			
			
			
			$query = 'SELECT count( * ) AS queuedMails'
						. ' FROM #__mailster_queued_mails';
						
			$this->_db->setQuery($query);

			$queuedMails = $this->_db->loadObject();	
			
			
			$query = 'SELECT count( * ) AS unsentMails'
						. ' FROM #__mailster_mails'
						. ' WHERE fwd_completed =\'0\'';
						
			$this->_db->setQuery($query);

			$unsentMails = $this->_db->loadObject();	
			
			
			$query = 'SELECT count( * ) AS errorMails'
						. ' FROM #__mailster_mails'
						. ' WHERE fwd_errors >\'0\'';
						
			$this->_db->setQuery($query);

			$errorMails = $this->_db->loadObject();
			
			$data->totalMails = $totalMails->totalMails;
			$data->queuedMails = $queuedMails->queuedMails;
			$data->unsentMails = $unsentMails->unsentMails;
			$data->errorMails = $errorMails->errorMails;
			
			return $data;
		}

		
	}//Class end
?>
