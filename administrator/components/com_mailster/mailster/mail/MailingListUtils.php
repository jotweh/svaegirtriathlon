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

class MstMailingListUtils
{			
	public static function getMailingListIdByMailId($mailId){
		$query = 'SELECT * FROM #__mailster_mails WHERE id=\'' . $mailId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$mail = $db->loadObject();
		if($mail){
			return $mail->list_id;
		}else{
			return false;
		}		
	}
	
	public static function getMailingList($listId){
		$mstList = & MstFactory::getMailingList();
		$query = 'SELECT * FROM #__mailster_lists WHERE id=\'' . $listId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$mList = $mstList->getInstance($db->loadObject());
		return $mList;		
	}
	
	public static function getMailingListByName($listName){
		$mstList = & MstFactory::getMailingList();
		$listName = strtolower($listName);
		$db = & JFactory::getDBO();
		$query = ' SELECT *'
				. ' FROM #__mailster_lists'
				. ' WHERE lower(name) =\'' . $db->getEscaped($listName) . '\'';
		$db->setQuery( $query );
		$lists = $db->loadObjectList();
		if($lists)
		{
			if(count($lists) > 0){
				$mList = $mstList->getInstance($lists[0]);
				return $mList;
			}		
		}
		return null;
	}

	public static function getActiveMailingLists($orderByLastCheck=true){
		$mstList = & MstFactory::getMailingList();
		$query =  ' SELECT *'
				. ' FROM #__mailster_lists' 
				. ' WHERE active =\'1\' ';
		$query = $query . ($orderByLastCheck ? ' ORDER BY last_check ASC' : ' ');
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$mLists = $db->loadObjectList();
		$nrLists = count($mLists);
		for($i = 0; $i < $nrLists; $i++){
			$mLists[$i] = $mstList->getInstance($mLists[$i]);
		}
		return $mLists;	
	}

	public static function getAllMailingLists($orderByName=true){
		$mstList = & MstFactory::getMailingList();
		$query =  ' SELECT *'
				. ' FROM #__mailster_lists';
		$query = $query . ($orderByName ? ' ORDER BY name ASC' : ' ');
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$mLists = $db->loadObjectList();
		$nrLists = count($mLists);
		for($i = 0; $i < $nrLists; $i++){
			$mLists[$i] = $mstList->getInstance($mLists[$i]);
		}
		return $mLists;	
	}
	
	public static function lockMailingList($listId, $setLastCheckOnSuccess=true){
		$log = & MstFactory::getLogger();
		$log->debug('Checking whether list is already locked...');
		if(self::isListLocked($listId)){
			if(self::isListLockInvalid($listId)){
				$log->error('List lock invalid, unlock list...');
				self::unlockMailingList($listId);
			}
		}
		if(!self::isListLocked($listId)){
			$log->debug('List not locked, attempt locking...');
			$lockId = self::attemptListLock($listId);
			$log->debug('Attempted to lock with lockId ' . $lockId);
			if(self::checkListLock($listId, $lockId)){
				$log->debug('Locking went fine!');
				if($setLastCheckOnSuccess){
					self::setLastCheck($listId);
				}
				return true;
			}
		}
		$log->debug('Locking failed!');
		return false;
	}
	
	public static function deactivateAllMailingLists(){	
		$query = ' UPDATE #__mailster_lists SET'
				. ' active = \'0\'';	
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$result = $db->query();
		if($result != false){
			return true;
		}
		return false;
	}
	
	public static function unlockMailingList($listId){	
		if(self::isListLocked($listId)){		
			$query = ' UPDATE #__mailster_lists SET'
					. ' is_locked = \'0\','
					. ' lock_id = \'0\','
					. ' last_check = last_check,'	
					. ' last_lock = last_lock'					
					. ' WHERE id=\'' . $listId . '\'';	
			$db = & JFactory::getDBO();
			$db->setQuery( $query );
			$result = $db->query();
			if($result != false){
				return true;
			}
		}
		return false;
	}
	
	public static function setLastCheck($listId){	
		$query = ' UPDATE #__mailster_lists SET'
				. ' last_check = NOW(),'	
				. ' last_lock = last_lock'					
				. ' WHERE id=\'' . $listId . '\'';	
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$result = $db->query();
	}
	
	public static function attemptListLock($listId){
		$lockId = rand(1, 123456);			
		$query = ' UPDATE #__mailster_lists SET'
				. ' is_locked = \'1\','
				. ' lock_id = \'' . $lockId . '\','
				. ' last_check = last_check,'	
				. ' last_lock = NOW()'					
				. ' WHERE id=\'' . $listId . '\'';	
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$result = $db->query();
		return $lockId;
	}	
	
	public static function checkListLock($listId, $lockId){
		$mList = self::getMailingList($listId);
		if(($mList->is_locked > 0) && ($mList->lock_id == $lockId)){
			return true;
		}
		return false;
	}
	
	public static function isListLocked($listId){	
		$query =  ' SELECT is_locked'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$isLocked = $db->loadResult();
		if($isLocked > 0){
			return true;
		}
		return false;
	}
	
	public static function isListLockInvalid($listId){	
		$log = & MstFactory::getLogger();
		$query =  ' SELECT last_lock'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\''
				. ' AND last_lock < DATE_SUB(NOW(), INTERVAL 5 MINUTE)';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$db->query();
		if($db->getNumRows() > 0){
			return true; // list lock invalid
		}
		
		$query =  ' SELECT last_lock'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\'';
		$db->setQuery( $query );
		$lastLock = $db->loadResult();
		
		$query =  ' SELECT NOW() As lock_time_now';
		$db->setQuery( $query );
		$timeNow = $db->loadResult();
		
		$log->debug('List lock not invalid, last lock at: ' . $lastLock . ', now: ' . $timeNow . ' (not 5 min diff)');
		
		return false; // list lock valid		
	}
	
	public static function isSendThrottlingActive($listId){		
		$log = & MstFactory::getLogger();
		$query =  ' SELECT throttle_hour_limit'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$hourLimit = $db->loadResult();
		if($hourLimit > 0){
			$log->debug('Send throttling active');
			return true;
		}
		$log->debug('Send throttling NOT active');
		return false;
	}
	
	public static function isSendLimitReached($listId){		
		$log = & MstFactory::getLogger();
		$query =  ' SELECT throttle_hour_cr >= throttle_hour_limit'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$limitReached = $db->loadResult();
		if($limitReached > 0){
			if(self::isNewSendHourReached($listId)){
				$log->debug('New send hour reached, reset counter');
				self::resetSendCounter($listId); // new hour, reset counter
				return false; // limit not reached (counter is now zero)
			}else{
				$log->debug('Send limit (send: '.self::getSendCounter($listId)
							.', limit: '.self::getSendLimit($listId). ') reached, stop sending for list ' 
							. $listId . ' while hour '.self::getCurrentHour());
				return true; // limit reached
			}
		}
		$log->debug('Send limit not reached (send: '.self::getSendCounter($listId)
							.', limit: '.self::getSendLimit($listId). ')');
		return false; // limit not reached
	}
	
	public static function isNewSendHourReached($listId){	
		$log = & MstFactory::getLogger();
		$query =  ' SELECT HOUR( NOW( ) ) <> '.self::getSendLimitHour($listId);
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$notEqual = $db->loadResult();
		if($notEqual > 0){
			return true;
		}
		return false;
	}
	
	public static function add2SendCounter($listId, $incCr){	
		$log = & MstFactory::getLogger();	
		$log->debug('Add '.$incCr . ' to send counter');	
		$query = ' UPDATE #__mailster_lists SET'
				. ' throttle_hour_cr = throttle_hour_cr+'.$incCr
				. ' WHERE id=\'' . $listId . '\'';	
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$result = $db->query();
		return $result;
	}
	
	public static function resetSendCounter($listId){	
		$log = & MstFactory::getLogger();	
		$hour = self::getCurrentHour();
		$query = ' UPDATE #__mailster_lists SET'
				. ' throttle_hour_cr = \'0\','
				. ' throttle_hour = \'' . $hour . '\''	
				. ' WHERE id=\'' . $listId . '\'';	
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$result = $db->query();
		return $result;
	}
	
	public static function getCurrentHour(){	
		$log = & MstFactory::getLogger();		
		$query =  'SELECT HOUR( NOW( ) )';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$hour = $db->loadResult();
		$log->debug('Current hour: '.$hour);
		return $hour;
	}
	
	public static function getSendLimitHour($listId){	
		$log = & MstFactory::getLogger();
		$query =  ' SELECT throttle_hour'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$hour = $db->loadResult();
		$log->debug('Send limit hour: '.$hour);
		return $hour;
	}
	
	public static function getSendLimit($listId){	
		$query =  ' SELECT throttle_hour_limit'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$limit = $db->loadResult();
		return $limit;
	}
	
	public static function getSendCounter($listId){	
		$query =  ' SELECT throttle_hour_cr'
				. ' FROM #__mailster_lists' 
				. ' WHERE id=\'' . $listId . '\'';
		$db = & JFactory::getDBO();
		$db->setQuery( $query );
		$cr = $db->loadResult();
		return $cr;
	}
}
