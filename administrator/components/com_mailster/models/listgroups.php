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
	 * Mailing List Groups Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelListGroups extends JModel
	{
		/**
		 * List Groups data array
		 *
		 * @var array
		 */
		var $_data = null;
		
		/**
		 * List data array
		 *
		 * @var array
		 */
		var $_listData = null;
		
		/**
		 * Non Member Groups data array
		 *
		 * @var array
		 */
		var $_nonMemberGroupsData = null;

		/**
		 * Pagination object
		 *
		 * @var object
		 */
		var $_pagination = null;
		
		/**
		 * List ID
		 *
		 * @var integer
		 */
		var $_listID = null;

		/**
		 * Constructor
		 *
		 */
		function __construct()
		{
			parent::__construct();

		}

		/**
		 * Method to get list group item data
		 *
		 * @access public
		 * @return array
		 */
		function getData($listID)
		{
			$this->_listID = $listID;
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = $this->_buildQuery();
				$this->_data = $this->_getList($query, 0, 0);
			}
			return $this->_data;
			
		}

		/**
		 * Method to get general list data
		 *
		 * @access public
		 * @return array
		 */
		function getListData($listID)
		{
			$this->_listID = $listID;
			// Lets load the content if it doesn't already exist
			if (empty($this->_listData))
			{
				$query = $this->_buildListQuery();
				$this->_listData = $this->_getList($query, 0, 0);
			}
			return $this->_listData;
			
		}
		
		/**
		 * Method to get all groups that are not not in the list
		 *
		 * @access public
		 * @return array
		 */
		function getNonMemberGroupsData($listID)
		{
			$this->_listID = $listID;
			// Lets load the content if it doesn't already exist
			if (empty($this->_nonMemberGroupsData))
			{
				$query = $this->_buildNonMemberGroupsQuery();
				$this->_nonMemberGroupsData = $this->_getList($query, 0, 0);
			}
			return $this->_nonMemberGroupsData;
			
		}


		
		/**
		 * Method to get a pagination object for the List Members
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
		 * Build the Non Member Group Data query
		 *
		 * @access private
		 * @return string
		 */
		function _buildNonMemberGroupsQuery()
		{				
			$query = 'SELECT g.*'
					. ' FROM #__mailster_groups g'
					. ' WHERE g.id NOT IN'
					. ' 	(SELECT lg.group_id'
					. ' 	FROM #__mailster_list_groups lg'
					. ' 	WHERE lg.list_id = \'' . $this->_listID . '\')'
					. ' ORDER BY g.name';
			return $query;
		}
		
		/**
		 * Build the List Data query
		 *
		 * @access private
		 * @return string
		 */
		function _buildListQuery()
		{				
			$query = 'SELECT l.*'
						. ' FROM #__mailster_lists l '
						. ' WHERE l.id=\'' . $this->_listID . '\'';
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
			
		
			$query = 	'SELECT g.*'
						. ' FROM #__mailster_groups g'
						. ' WHERE id in'
						. ' ( SELECT lg.group_id'
						. ' FROM #__mailster_list_groups lg '
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
			$orderby 	= ' ORDER BY name';				
			return $orderby;
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
			$where[] = ' lg.list_id = \'' . $this->_listID . '\'';
			$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
			$where .= ') ';
			return $where;
		}
			
		public function getTable($type = 'mailster_list_groups', $prefix = '', $config = array()){
			return JTable::getInstance($type, $prefix, $config);
		}

		/**
		 * Method to store the list member groups connection
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

			if (!$row->check()) {
				$this->setError($row->getError());
				return false;
			}

			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
		}
		
		

		/**
		 * Method to remove member groups from the list
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function delete($list_id, $group_ids = array())
		{
			$result = false;
			$groupCount = count( $group_ids );
			if ($groupCount)
			{
				for($i=0; $i < $groupCount; $i++)
				{
					$query = 'DELETE FROM #__mailster_list_groups'
							. ' WHERE group_id =\''. $group_ids[$i] . '\''
							. ' AND list_id =\''. $list_id . '\'';
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
