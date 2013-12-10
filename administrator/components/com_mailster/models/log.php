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
	 * Log Entries Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelLog extends JModel
	{
		/**
		 * Log entries array
		 *
		 * @var array
		 */
		var $_data = null;

		/**
		 * Log entries total
		 *
		 * @var integer
		 */
		var $_total = null;

		/**
		 * Pagination object
		 *
		 * @var object
		 */
		var $_pagination = null;

		/**
		 * Constructor
		 *
		 */
		function __construct()
		{
			parent::__construct();
			
			// Get the pagination request variables
			$app = JFactory::getApplication();
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
			$limitstart = $app->getUserStateFromRequest(JRequest::getCmd('option','com_e2a').'limitstart','limitstart',0);
				
			$limit = $limit + 0; // make it numerical
			$limitstart = $limitstart + 0; // make it numerical
				
			if($limit == 0){
				$limitstart = 0;
			}
	
			// Set the page pagination variables
			$this->setState('limit',$limit);
			$this->setState('limitstart',$limitstart);
		}

		
		function getData($overrideLimits=false)
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = $this->_buildQuery();
				$limitstart = $this->getState('limitstart');
				$limit = $this->getState('limit');
	
				if(!$overrideLimits){
					$this->_data = $this->_getList($query, $limitstart, $limit);
				}else{
					$this->_data = $this->_getList($query, 0, 0);
				}
			}
			return $this->_data;	
		}
		
	
		

		/**
		 * Method to get a pagination object
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
		 * Total nr entries
		 *
		 * @access public
		 * @return integer
		 */
		function getTotal()
		{
			// Lets load the total nr if it doesn't already exist
			if (empty($this->_total)){
				$query = $this->_buildQuery();
				$this->_total = $this->_getListCount($query);
			}
			return $this->_total;
		}

		/**
		 * Build the query
		 *
		 * @access private
		 * @return string
		 */
		function _buildQuery()
		{
			$where		= $this->_buildContentWhere();
			$orderby	= $this->_buildContentOrderBy();
			
			$query = 'SELECT l.*'
						. ' FROM #__mailster_log l'
						. $where
						. $orderby;						
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
			$orderby 	= ' ORDER BY l.log_time ASC, l.id ASC';
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
			$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );	
			return $where;
		}

		/**
		 * Method to remove log entries
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function clearLog()
		{
			$query = 'DELETE FROM #__mailster_log';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		
		/**
		 * Method to remove selected log entries
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function delete($cid = array())
		{	
			if (count( $cid )){
				
				for($i=0;$i<count($cid);$i++){
					$query = 'DELETE FROM #__mailster_log'
							. ' WHERE id = \'' . $cid[$i] . '\'';
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
