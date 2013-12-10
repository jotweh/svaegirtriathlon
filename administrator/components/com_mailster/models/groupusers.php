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
	 * Mailing List Group Users Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelGroupUsers extends JModel
	{
		/**
		 * Group Users data array
		 *
		 * @var array
		 */
		var $_data = null;
		
		/**
		 * Group data array
		 *
		 * @var array
		 */
		var $_groupData = null;
		
		/**
		 * Non Member data array
		 *
		 * @var array
		 */
		var $_nonMemberData = null;

		/**
		 * Pagination object
		 *
		 * @var object
		 */
		var $_pagination = null;
		
		
		/**
		 * Group ID
		 *
		 * @var integer
		 */
		var $_groupID = null;

		/**
		 * Constructor
		 *
		 */
		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Method to get group user item data
		 *
		 * @access public
		 * @return array
		 */
		function getData($groupID)
		{
			$this->_groupID = $groupID;
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = $this->_buildQuery();
				$this->_data = $this->_getList($query, 0, 0);
			}
			return $this->_data;
			
		}

		/**
		 * Method to get general group data
		 *
		 * @access public
		 * @return array
		 */
		function getGroupData($groupID)
		{
			$this->_groupID = $groupID;
			// Lets load the content if it doesn't already exist
			if (empty($this->_groupData))
			{
				$query = $this->_buildGroupQuery();
				$this->_groupData = $this->_getList($query, 0, 0);
			}
			return $this->_groupData;
			
		}
		
		/**
		 * Method to get all users that are not not in the group
		 *
		 * @access public
		 * @return array
		 */
		function getNonMemberData($groupID)
		{
			$this->_groupID = $groupID;
			// Lets load the content if it doesn't already exist
			if (empty($this->_nonMemberData))
			{
				$query = $this->_buildNonMemberQuery();
				$this->_nonMemberData = $this->_getList($query, 0, 0);
			}
			return $this->_nonMemberData;
			
		}


		
		/**
		 * Method to get a pagination object for the Group Users
		 *
		 * @access public
		 * @return integer
		 */
		function getPagination()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_pagination))
			{
				jimport('joomla.html.pagination');
				$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
			}

			return $this->_pagination;
		}
		
		
		/**
		 * Build the Non Member Data query
		 *
		 * @access private
		 * @return string
		 */
		function _buildNonMemberQuery()
		{				
			$query = 'SELECT u.id, u.name, u.email, u.notes, FORMAT(0,0) AS is_joomla_user'
				. ' FROM #__mailster_users u'
				. ' WHERE u.id NOT IN'
				. ' 	(SELECT gu.user_id'
				. ' 	FROM #__mailster_group_users gu'
				. ' 	WHERE gu.group_id = \'' . $this->_groupID . '\''
				. ' 	AND gu.is_joomla_user = \'0\' )';
			$query = $query
				. ' UNION'
				. ' SELECT ju.id, ju.name, ju.email, " ", FORMAT(1,0)'
				. ' FROM #__users ju'
				. ' WHERE ju.id NOT IN'
				. '		(SELECT gu.user_id'
				. ' 	FROM #__mailster_group_users gu'
				. ' 	WHERE gu.group_id = \'' . $this->_groupID . '\''
				. '		AND gu.is_joomla_user = \'1\' )'
				. ' ORDER BY name, email';
			return $query;
		}
		
		/**
		 * Build the Group Data query
		 *
		 * @access private
		 * @return string
		 */
		function _buildGroupQuery()
		{				
			$query = 'SELECT g.*'
						. ' FROM #__mailster_groups g '
						. ' WHERE g.id=\'' . $this->_groupID . '\'';
						;	
			return $query;
		}

		/**
		 * Build the query
		 *
		 * @access private
		 * @return string
		 */
		function _buildQuery()
		{
			// Get the WHERE and ORDER BY clauses for the query
			$where		= $this->_buildContentWhere();
			$orderby	= $this->_buildContentOrderBy();
			
		
			$query = 'SELECT gu.*,'
						. ' CASE WHEN gu.is_joomla_user = \'1\''
						. ' THEN ju.name ELSE u.name END AS name,'
						. ' CASE WHEN gu.is_joomla_user = \'1\''
						. ' THEN ju.email ELSE u.email END AS email,'
						. ' CASE WHEN gu.is_joomla_user = \'1\''
						. ' THEN " " ELSE u.notes END AS notes'
						. ' FROM #__mailster_group_users gu '
						. ' LEFT JOIN #__mailster_users u ON (gu.user_id = u.id)'
						. ' LEFT JOIN #__users ju ON (gu.user_id = ju.id)'
						;					
			$query = $query	. $where;
			$query = $query	. $orderby;
			return $query;
		}

		/**
		 * Build the order clause
		 *
		 * @access private
		 * @return string
		 */
		function _buildContentOrderBy()
		{
			$orderby 	= ' ORDER BY name, email, gu.is_joomla_user';			
			return $orderby;
		}
			
		public function getTable($type = 'mailster_group_users', $prefix = '', $config = array()){
			return JTable::getInstance($type, $prefix, $config);
		}
		
		/**
		 * Method to store the group user connection
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function store($data)
		{		
			$user		= & JFactory::getUser();
			$config 	= & JFactory::getConfig();
					
			// Delete from table if existing
			$this->delete($data->group_id, array($data->user_id), array($data->is_joomla_user));
						
			$row  =& $this->getTable();
			
			if (!$row->bind($data)) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}

			if (!$row->check()) {
				$this->setError($row->getError());
				return false;
			}

			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
			return true;
		}
		
		
		/**
		 * Get all mailing lists that use this group as recipient group
		 *
		 */
		function getListsWithGroup($groupID)
		{			
			$query = 'SELECT l.*'
						. ' FROM #__mailster_lists l '
						. ' WHERE l.id IN ('
						. '		SELECT lg.list_id'
						. '		FROM #__mailster_list_groups lg '
						. '		WHERE lg.group_id=\'' . $groupID. '\''
						. ' )';	
			$lists = $this->_getList($query, 0, 0);
			return $lists;
		}
		

		/**
		 * Build the where clause
		 *
		 * @access private
		 * @return string
		 */
		function _buildContentWhere()
		{	
			$where = array();			
			$where[] = ' gu.group_id = \'' . $this->_groupID . '\'';
			$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );			
			return $where;
		}

		

		/**
		 * Method to remove Users from the group
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function delete($group_id, $user_ids = array(), $is_joomla_user_flags = array())
		{
			$result = false;
			$userCount = count( $user_ids );
			if ($userCount)
			{
				for($i=0; $i < $userCount; $i++)
				{
					$query = 'DELETE FROM #__mailster_group_users'
							. ' WHERE user_id =\''. $user_ids[$i] . '\''
							. ' AND group_id =\''. $group_id . '\''
							. ' AND is_joomla_user =\''. $is_joomla_user_flags[$i] . '\'';
					$this->_db->setQuery( $query );

					if(!$this->_db->query()) {
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
			}

			return true;
		}
		
		
		
	}//Class end
?>
