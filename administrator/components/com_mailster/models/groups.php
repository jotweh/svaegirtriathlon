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
	class MailsterModelGroups extends JModel
	{
		/**
		 * Groups data array
		 */
		var $_data = null;

		/**
		 * Groups total
		 */
		var $_total = null;

		/**
		 * Pagination object
		 */
		var $_pagination = null;

		
		function __construct()
		{
			parent::__construct();

		}

		/**
		 * Method to get group item data
		 */
		function getData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = $this->_buildQuery();
				$this->_data = $this->_getList($query, 0, 0);
				$this->_data = $this->_additionals($this->_data);
			}
			return $this->_data;
		}

		/**
		 * Total nr of groups
		 */
		function getTotal()
		{
			// Lets load the total nr if it doesn't already exist
			if (empty($this->_total))
			{
				$query = $this->_buildQuery();
				$this->_total = $this->_getListCount($query);
			}

			return $this->_total;
		}

		/**
		 * Method to get a pagination object for the groups
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
		 * Build the query
		 */
		function _buildQuery()
		{
			// Get the WHERE and ORDER BY clauses for the query
			$where		= $this->_buildContentWhere();
			$orderby	= $this->_buildContentOrderBy();
			
			$query = 'SELECT g.*, Count(gu.group_id) as memberCount'
						. ' FROM #__mailster_groups AS g'
						. ' LEFT JOIN #__mailster_group_users AS gu'
						. ' ON (gu.group_id = g.id) GROUP BY g.id'
						. $where
						. $orderby
						;
			return $query;
		}

		/**
		 * Build the order clause
		 */
		function _buildContentOrderBy()
		{
			$orderby 	= ' ORDER BY g.name';
			return $orderby;
		}

		/**
		 * Build the where clause
		 */
		function _buildContentWhere()
		{
			$where = array();			
			$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
			return $where;
		}

		/**
		 * Additional tasks
		 */
		function _additionals($rows)
		{
			/* nothing for now */
			return $rows;
		}

		

		/**
		 * Method to remove a group from the list
		 */
		function delete($cid = array())
		{
			$result = false;

			if (count( $cid ))
			{
				$cids = implode( ',', $cid );
				$query = 'DELETE FROM #__mailster_groups'
						. ' WHERE id IN ('. $cids .')';

				$this->_db->setQuery( $query );

				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				$query = 'DELETE FROM #__mailster_group_users'
						. ' WHERE group_id IN ('. $cids .')';

				$this->_db->setQuery( $query );

				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				$query = 'DELETE FROM #__mailster_list_groups'
						. ' WHERE group_id IN ('. $cids .')';

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
