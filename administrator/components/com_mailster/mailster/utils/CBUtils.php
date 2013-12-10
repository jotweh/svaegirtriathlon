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

class MstCBUtils
{			
	public static function isCBInstalled(){
		$dbUtils = &MstFactory::getDBUtils();
		return $dbUtils->isTableExisting('#__comprofiler');
	}
			
	public static function getCBFieldId($name){
		$db =& JFactory::getDBO();
		$query = 'SELECT fieldid'
		. ' FROM #__comprofiler_fields'
		. ' WHERE name = \''.$name.'\'';
			
		$db->setQuery($query);
		$fieldId = $db->loadResult();
		if($fieldId && $fieldId > 0){
			return $fieldId;
		}
		return false;
	}
		
	public static function getCBFieldByFieldName($name){
		$fieldId = self::getCBFieldId($name);
		if($fieldId){
			return self::getCBField($fieldId);
		}
		return false;
	}
			
	public static function getCBField($fieldId){
		$db =& JFactory::getDBO();
		$query = 'SELECT *'
		. ' FROM #__comprofiler_fields'
		. ' WHERE fieldid = '.$fieldId;
			
		$db->setQuery($query);
		$field = $db->loadObject();
		
		if($field){			
			$field->fieldValues = self::getCBFieldValues($fieldId);
			return $field;
		}else{
			return false;
		}
	}
		
	public static function getCBFieldValuesByFieldName($name){
		$fieldId = self::getCBFieldId($name);
		if($fieldId){
			return self::getCBFieldValues($fieldId);
		}
		return false;
	}	
		
	public static function getCBFieldValues($fieldId){
		$db =& JFactory::getDBO();
		$query = 'SELECT *'
			. ' FROM #__comprofiler_field_values'
			. ' WHERE fieldid = '.$fieldId
			. ' ORDER BY ordering, fieldtitle';				
		$db->setQuery($query);
		$fieldValues = $db->loadObjectList();
		return $fieldValues;
	}	
		
	public static function getAllUsersCBUserGroups($fieldName){
		$db =& JFactory::getDBO();
		$fieldId = self::getCBFieldId($fieldName);
		$field = self::getCBField($fieldId);
		$tbl = $field->table;
		
		$db =& JFactory::getDBO();
		$query = 'SELECT id, '.$fieldName
		. ' FROM ' . $tbl
		. ' WHERE 1';			
		$db->setQuery($query);
		$users = $db->loadObjectList();
		return $users;
	}
	
	public static function syncCBUserGroupsToMailsterGroups($userId, $fieldName, $userGroups=null){
		$log = & MstFactory::getLogger();
		$groupModel = &MstFactory::getModel('group');
		$userGroupsModel = &MstFactory::getModel('usergroups');
				
		$fieldId = self::getCBFieldId($fieldName);
		$fieldObj = self::getCBField($fieldId);
		
		if(!$fieldId || !$fieldObj){
			$log->warning('Field configured does not exist in CB: ' . $cbUserGroupField);
		}else{
			$log->debug('Field configured: ' . print_r($fieldObj, true));
		}	
		
		$allCBUserGroups = self::getCBFieldValues($fieldId);
		$allLinkedUserGroupIds = array();
		for($i=0; $i<count($allCBUserGroups);$i++){
			$group = $groupModel->getGroupByName($allCBUserGroups[$i]->fieldtitle);
			if(!is_null($group) && $group->id > 0){
				$allLinkedUserGroupIds[] = $group->id;
			}
		}
		
		if(is_null($userGroups)){
			$userGroups = self::getCBUserProfileGroups($userId, $fieldName);
		}
				
		// Remove user from all groups existing in CB and Mailster
		$log->debug('Remove user from the following groups: '.print_r($allLinkedUserGroupIds, true));
		$userGroupsModel->delete($allLinkedUserGroupIds, $userId, 1);
		
		$log->debug('User groups to add in Mailster: '.print_r($userGroups, true));
		
		$groupIds = array();
		if(!empty($userGroups)){
			foreach($userGroups as $groupName){
				$group = $groupModel->getGroupByName($groupName);
				if(!is_null($group) && $group->id > 0){
					$log->debug('User group to add: ' . $groupName . ', corresponds to: ' . print_r($group, true));											
					$groupIds[] = $group->id;
				}else{
					$log->debug('No Mailster user group found for: ' . $groupName);
				}
			}
			
			// Add user to all Mailster groups he is linked to in CB groups
			$log->debug('Add user to the following groups: '.print_r($groupIds, true));
			$userGroupsModel->store($groupIds, $userId, 1);	
		}
	}
	
	public static function getCBUserProfileGroups($userId, $fieldName){
		$log = &MstFactory::getLogger();
		$fieldId = self::getCBFieldId($fieldName);
		$field = self::getCBField($fieldId);
		$tbl = $field->table;
		
		$db =& JFactory::getDBO();
		$query = 'SELECT *'
		. ' FROM ' . $tbl
		. ' WHERE id = \''.$userId.'\'';		
		$db->setQuery($query);
		$user = $db->loadObject();
		$log->debug('User found: '.print_r($user, true));
		$log->debug('User field: '.$fieldName);
		$groupsStr = $user->$fieldName;
		$log->debug('User field has content: '.$groupsStr);
		if($user){
			$userGroups = explode('|*|', $groupsStr);
			return $userGroups;
		}
		$log->debug('User profile of user '.$userId.' not found');
		return null;
	}
	
	public static function setCBUserProfileGroups($userId, $fieldName, $userGroups){
		$log = &MstFactory::getLogger();
		$fieldId = self::getCBFieldId($fieldName);
		$field = self::getCBField($fieldId);
		$tbl = $field->table;
		$groupsStr = implode('|*|', $userGroups);
		$db =& JFactory::getDBO();
		$query = 'UPDATE '.$tbl.' SET '.$fieldName.' = ' . $db->quote($groupsStr) . ' WHERE id = \''.$userId.'\'';
		$db->setQuery($query);
		$result = $db->query();		
		if(!$result){
			$log->error('Updating of CB user groups for user ' . $userId . ' failed: ' . $db->getErrorMsg());
			return false; // something went wrong
		}
		$log->debug('User profile of user '.$userId.' updated successfully');
		return true;
	}
	
	public static function addGroupInCBUser($userId, $groupId){		
		$log = &MstFactory::getLogger();
		$plgUtils = &MstFactory::getPluginUtils();
		$cbUtils = &MstFactory::getCBUtils();
		if(self::isCBInstalled()){
			$log->debug('CB is installed');
			if($plgUtils->isCBPluginInstalled() && $plgUtils->isCBPluginActive()){
				$log->debug('CB Plugin installed and active');
				if($plgUtils->isCBMstUserGroupSyncActive()){
					$log->debug('CB-Mailster User Group Sync is active');
					$cbPlgParams = $plgUtils->getCBPluginParameter();
					$groupModel = &MstFactory::getModel('group');
					$groupModel->setId($groupId);
					$group = $groupModel->getData();
					$cbUserGroupField = $cbPlgParams->def( 'cb_user_group_field', 'cb_mailster_group' );
					self::addGroup2CBUserProfile($userId, $cbUserGroupField, $group->name);
				}else{
					$log->debug('CB-Mailster User Group Sync not active');
				}
			}else{
				$log->debug('CB Plugin not installed or not active');
			}
		}else{
			$log->debug('CB NOT is installed, Sync not possible');
		}	
	}
	
	public static function removeGroupFromCBUser($userId, $groupId){		
		$log = &MstFactory::getLogger();
		$plgUtils = &MstFactory::getPluginUtils();
		$cbUtils = &MstFactory::getCBUtils();
		if(self::isCBInstalled()){
			$log->debug('CB is installed');
			if($plgUtils->isCBPluginInstalled() && $plgUtils->isCBPluginActive()){
				$log->debug('CB Plugin installed and active');
				if($plgUtils->isCBMstUserGroupSyncActive()){
					$log->debug('CB-Mailster User Group Sync is active');
					$cbPlgParams = $plgUtils->getCBPluginParameter();
					$groupModel = &MstFactory::getModel('group');
					$groupModel->setId($groupId);
					$group = $groupModel->getData();
					$cbUserGroupField = $cbPlgParams->def( 'cb_user_group_field', 'cb_mailster_group' );
					self::removeGroupFromCBUserProfile($userId, $cbUserGroupField, $group->name);
				}else{
					$log->debug('CB-Mailster User Group Sync not active');
				}
			}else{
				$log->debug('CB Plugin not installed or not active');
			}
		}else{
			$log->debug('CB NOT is installed, Sync not possible');
		}		
	}
	
	private static function addGroup2CBUserProfile($userId, $fieldName, $groupName){
		$log = &MstFactory::getLogger();
		
		$userGroups = self::getCBUserProfileGroups($userId, $fieldName);
		if($userGroups){
			$log->debug('User groups in DB: '.print_r($userGroups, true));
			if(!empty($userGroups)){
				foreach($userGroups as $group){
					if(trim(strtolower($groupName)) === trim(strtolower($group))){
						$log->debug('Group '.$groupName.' already in DB stored');
						// found group, no need to add
						return true; // exit without changing user profile
					}
				}
			}else{
				$userGroups = array();
			}
			$log->debug('Group '.$groupName.' not yet in DB, will be added...');
			$userGroups[] = $groupName; // append new Group
			return self::setCBUserProfileGroups($userId, $fieldName, $userGroups);
		}		
		return false; // user not found
	}
	
	private static function removeGroupFromCBUserProfile($userId, $fieldName, $groupName){
		$log = &MstFactory::getLogger();
		
		$userGroups = self::getCBUserProfileGroups($userId, $fieldName);
		if($userGroups){
			$log->debug('User groups in DB: '.print_r($userGroups, true));
			if(!empty($userGroups)){
				for($i=0;$i<count($userGroups);$i++){
					$group = $userGroups[$i];
					if(trim(strtolower($groupName)) === trim(strtolower($group))){
						$log->debug('Group '.$groupName.' found in CB DB, will be deleted');
						unset($userGroups[$i]);
					}
				}
				$userGroups = array_values($userGroups); // re-index
			}
			$log->debug('User groups that will be stored in DB: '.print_r($userGroups, true));
			return self::setCBUserProfileGroups($userId, $fieldName, $userGroups);
		}		
		return false; // user not found
	}
	
}
