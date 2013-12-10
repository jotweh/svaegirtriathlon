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
 * Mailing List Thread Model
 *
 * @package Joomla
 * @subpackage Mailster
 */
class MailsterModelThread extends JModel
{

	var $_id = 0;

	/**
	 * Thread mails data array
	 */
	var $_data = null;

	/**
	 * Mails total
	 */
	var $_total = null;

	/**
	 * Pagination object
	 */
	var $_pagination = null;

	
	function __construct()
	{
		parent::__construct();
			
		
		// Get the pagination request variables
		$app = JFactory::getApplication();
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// Set the page pagination variables
		$this->setState('limit',$limit);
		$this->setState('limitstart',$limitstart);
	}


	
	
	/**
	 * Method to get the emails of the thread
	 */
	function getData($threadId, $overrideLimits=false)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data) || $this->_id != $threadId){
			$this->_id = $threadId;
			
			$query = $this->_buildQuery($threadId);
			
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
	 * Total nr of threads
	 */
	function getTotal()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total)){
			$query = $this->_buildQuery($this->_id);
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the mails of the threads
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)){
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Build the thread mails query
	 */
	function _buildQuery($threadId)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere($threadId);
		$orderby	= $this->_buildContentOrderBy();
		
		$query = 'SELECT * '
					. ' FROM #__mailster_mails AS m'				
					. $where
					. $orderby;
		return $query;
	}
	
	/**
	* Build the order clause
	*/
	function _buildContentOrderBy()
	{
		$orderby 	= ' ORDER BY m.receive_timestamp DESC';
		return $orderby;
	}
	
	/**
	 * Build the where clause
	 */
	function _buildContentWhere($threadId)
	{
		$where = array();
		if($threadId > 0){
			$where[] = ' m.thread_id = \'' . $threadId . '\'';
		}
		
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	
		return $where;
	}
	

}//Class end
?>
