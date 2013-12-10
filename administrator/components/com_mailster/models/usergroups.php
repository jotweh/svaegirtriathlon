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
	 * Mailing List User Groups Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelUserGroups extends JModel
	{
		/**
		 * User Groups data array
		 *
		 * @var array
		 */
		var $_data = null;
				
		/**
		 * Pagination object
		 *
		 * @var object
		 */
		var $_pagination = null;
				
		/**
		 * User ID
		 *
		 * @var integer
		 */
		var $_userID = null;
		
		/**
		 * is_joomla_user Flag
		 *
		 * @var integer
		 */
		var $_is_joomla_user = null;

		/**
		 * Constructor
		 *
		 */
		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Method to get user groups item data
		 *
		 * @access public
		 * @return array
		 */
		function getData($userID, $is_joomla_user)
		{
			$this->_userID = $userID;
			$this->_is_joomla_user = $is_joomla_user;
			
			if (empty($this->_data))
			{
				$query = $this->_buildQuery();
				$this->_data = $this->_getList($query, 0, 0);
			}
			return $this->_data;			
		}

				
		/**
		 * Method to get a pagination object for the User Groups
		 *
		 * @access public
		 * @return integer
		 */
		function getPagination()
		{
			if (empty($this->_pagination)){
				jimport('joomla.html.pagination');
				$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
			}

			return $this->_pagination;
		}
			

		function _buildQuery()
		{
			// Get the WHERE and ORDER BY clauses for the query
			$where		= $this->_buildContentWhere();
			$orderby	= $this->_buildContentOrderBy();
			
		
			$query = 'SELECT g.*'
						. ' FROM #__mailster_groups g ';					
			$query = $query	. $where;
			$query = $query	. $orderby;
			return $query;
		}

		
		function _buildContentOrderBy()
		{
			$orderby 	= ' ORDER BY g.name, g.id';			
			return $orderby;
		}
			
		public function getTable($type = 'mailster_group_users', $prefix = '', $config = array()){
			return JTable::getInstance($type, $prefix, $config);
		}
		
		/**
		 * Method to store the user group connections
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function store($groupIds, $userID=null, $is_joomla_user=null)
		{		
			$user		= & JFactory::getUser();
			$config 	= & JFactory::getConfig();

			if(is_null($userID)){
				$userID = $this->_userID;
			}
			if(is_null($is_joomla_user)){
				$is_joomla_user = $this->_is_joomla_user;	
			}
			
			// Delete from table if existing
			$this->delete($groupIds, $userID, $is_joomla_user);
			
			for($i=0;$i<count($groupIds);$i++){

				$data = new stdClass();
				$data->group_id = $groupIds[$i];
				$data->user_id = $userID;
				$data->is_joomla_user = $is_joomla_user;
				
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
			return true;
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
			$where[] = ' g.group_id IN ('
							. ' SELECT DISTINCT group_id FROM #__mailster_group_users'
							. ' WHERE user_id = ' . $this->_userID
							. ' AND is_joomla_user = ' . $this->_is_joomla_user
							. ')';
			$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );			
			return $where;
		}
		
		/**
		* Get all mailing lists that use one of the groups
		*
		*/
		function getListsWithGroups($groupIds)
		{
			$ids = implode( ',', $groupIds );
			$query = 'SELECT l.*'
			. ' FROM #__mailster_lists l '
			. ' WHERE l.id IN ('
			. '		SELECT DISTINCT lg.list_id'
			. '		FROM #__mailster_list_groups lg '
			. '		WHERE lg.group_id IN ('.$ids.')'
			. ' )';
			$lists = $this->_getList($query, 0, 0);
			return $lists;
		}

		

		/**
		 * Method to remove groups from user
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */		
		function delete($groupIds=array(), $userID=null, $is_joomla_user=null)
		{
			if(is_null($userID)){
				$userID = $this->_userID;
			}
			if(is_null($is_joomla_user)){
				$is_joomla_user = $this->_is_joomla_user;	
			}
			
			$result = false;
			$groupCount = count( $groupIds );
			
			for($i=0; $i < $groupCount; $i++){
				$query = 'DELETE FROM #__mailster_group_users'
						. ' WHERE user_id =\''. $userID . '\''
						. ' AND group_id =\''. $groupIds[$i] . '\''
						. ' AND is_joomla_user =\''. $is_joomla_user . '\'';
				$this->_db->setQuery( $query );
				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}

			return true;
		}
		
		
		
	}//Class end
?>
