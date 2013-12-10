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

	/**
	 * User Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelUser extends JModel
	{
		/**
		 * user id
		 *
		 * @var int
		 */
		var $_id = null;

		/**
		 * user data array
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

			$array = JRequest::getVar('cid',  0, '', 'array');
			$this->setId((int)$array[0]);
		}

		/**
		 * Method to set the identifier
		 *
		 * @access	public
		 * @param	int identifier
		 */
		function setId($id)
		{
			// Set user id and wipe data
			$this->_id	    = $id;
			$this->_data	= null;
		}

		/**
		 *
		 *
		 * @access public
		 * @return array
		 */
		function &getData()
		{
			if ($this->_loadData())
			{

			}
			else  $this->_initData();

			return $this->_data;
		}

		/**
		 * 
		 *
		 * @access	private
		 * @return	boolean	True on success
		 */
		function _loadData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = 'SELECT *'
						. ' FROM #__mailster_users'
						. ' WHERE id = '.$this->_id;

				$this->_db->setQuery($query);

				$this->_data = $this->_db->loadObject();

				return (boolean) $this->_data;
			}
			return true;
		}
		
		public function getJUserData($userId){
			$query = 'SELECT *'
					. ' FROM #__users'
					. ' WHERE id = \''.$userId . '\'';

			$this->_db->setQuery($query);
			$jUser = $this->_db->loadObject();
			return $jUser;
		}
		
		
		function getGroupMemberInfo($userId, $isJoomlaUser)
		{
			$query = 	'SELECT *, 1 AS is_group_member '
						. ' FROM #__mailster_groups'
						. ' WHERE id IN ('
								. ' SELECT group_id'
								. ' FROM #__mailster_group_users'
								. ' WHERE user_id=\''.$userId . '\' AND is_joomla_user=\''.$isJoomlaUser . '\''
								. ')'
						. ' UNION '
						. 'SELECT *, 0 AS is_group_member '
						. ' FROM #__mailster_groups'
						. ' WHERE id NOT IN ('
								. ' SELECT group_id'
								. ' FROM #__mailster_group_users'
								. ' WHERE user_id=\''.$userId . '\' AND is_joomla_user=\''.$isJoomlaUser . '\''
								. ')'
						. ' ORDER BY name';
			$this->_db->setQuery($query);
			$groups = $this->_db->loadObjectList();
			return $groups;
		}
		
		
		function getListMemberInfo($userId, $isJoomlaUser)
		{
			$query = 	'SELECT *, 1 AS is_list_member '
						. ' FROM #__mailster_lists'
						. ' WHERE id IN ('
								. ' SELECT list_id'
								. ' FROM #__mailster_list_members'
								. ' WHERE user_id=\''.$userId . '\' AND is_joomla_user=\''.$isJoomlaUser . '\''
								. ')'
						. ' UNION '
						. 'SELECT *, 0 AS is_list_member '
						. ' FROM #__mailster_lists'
						. ' WHERE id NOT IN ('
								. ' SELECT list_id'
								. ' FROM #__mailster_list_members'
								. ' WHERE user_id=\''.$userId . '\' AND is_joomla_user=\''.$isJoomlaUser . '\''
								. ')'
						. ' ORDER BY name';
			$this->_db->setQuery($query);
			$groups = $this->_db->loadObjectList();
			return $groups;
		}
		
		/**
		 * Get all groups/lists where user is member of
		 * @access	private
		 * @return	boolean	True on success
		 */
		function getMemberInfo($userId, $isJoomlaUser)
		{
			$memberInfo = array();
			
			$query = 'SELECT * FROM #__mailster_groups WHERE id IN ('
						. ' SELECT group_id FROM #__mailster_group_users'
						. ' WHERE user_id=\'' . $userId . '\''
						. ' AND is_joomla_user=\'' . $isJoomlaUser . '\''
						. ')';

			$this->_db->setQuery($query);
			$groups = $this->_db->loadObjectList();
			
			$query = 'SELECT * FROM #__mailster_lists WHERE id IN ('
						. ' SELECT list_id FROM #__mailster_list_members'
						. ' WHERE user_id=\'' . $userId . '\''
						. ' AND is_joomla_user=\'' . $isJoomlaUser . '\''
						. ')';

			$this->_db->setQuery($query);
			$lists = $this->_db->loadObjectList();
			
			$query = 'SELECT * FROM #__mailster_lists WHERE id IN ('
						. ' SELECT list_id FROM #__mailster_list_groups WHERE group_id IN ('
							. ' SELECT group_id FROM #__mailster_group_users'
							. ' WHERE user_id=\'' . $userId . '\''
							. ' AND is_joomla_user=\'' . $isJoomlaUser . '\''
							. ')'
						. ')';

			$this->_db->setQuery($query);
			$listGroups = $this->_db->loadObjectList();
			
			$memberInfo['groups'] = $groups;
			$memberInfo['lists'] = $lists;
			$memberInfo['listGroups'] = $listGroups;
			return $memberInfo;
		}
		
		/**
		 * Check for duplicate users
		 * @access	private
		 * @return	boolean	True on success
		 */
		function isDuplicateEntry($email, $checkJoomlaUsersToo=true)
		{
			$email = trim($email);
			if($checkJoomlaUsersToo){					
				
				$query = 'SELECT *'
						. ' FROM #__users'
						. ' WHERE email = \''.$email.'\'';
	
				$this->_db->setQuery($query);	
				$user = $this->_db->loadObject();
	
				if($user){
					$user->is_joomla_user = 1;
					return $user;
				}
			}
			
			$query = 'SELECT *'
					. ' FROM #__mailster_users'
					. ' WHERE email = \''.$email.'\'';

			$this->_db->setQuery($query);
			$user = $this->_db->loadObject();

			if($user){
				$user->is_joomla_user = 0;
				return $user;
			}
			
			return null;
		}

		/**
		 * Method to initialise the user data
		 *
		 * @access	private
		 * @return	boolean	True on success
		 */
		function _initData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$user = new stdClass();
				$user->id						= 0;
				$user->name						= null;
				$user->email					= null;
				$user->notes					= null;
				
				$this->_data					= $user;
				return (boolean) $this->_data;
			}
			return true;
		}
			
		public function getTable($type = 'mailster_users', $prefix = '', $config = array()){
			return JTable::getInstance($type, $prefix, $config);
		}

		/**
		 * Method to store the user
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function store($data)
		{		
			$user		= & JFactory::getUser();
			$config 	= & JFactory::getConfig();
			
			$row  =& $this->getTable();
			
			if (!$row->bind($data)) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}

			// sanitise id field
			$row->id = (int) $row->id;
			
			if (!$row->check()) {
				$this->setError($row->getError());
				return false;
			}

			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}

			return $row->id;
		}
		
		
	}
?>
