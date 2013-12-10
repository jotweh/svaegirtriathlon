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
 * Mailing List Threads Model
 *
 * @package Joomla
 * @subpackage Mailster
 */
class MailsterModelThreads extends JModel
{

	var $_id = 0;

	/**
	 * Threads data array
	 */
	var $_data = null;

	/**
	 * Threads total
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
	 * Method to get threads
	 */
	function getData($listId, $overrideLimits=false, $orderBy='rpost')
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data) || $this->_id != $listId){
			$this->_id = $listId;
			
			$query = $this->_buildQuery($listId, $orderBy);
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
	 * Method to get a pagination object for the threads
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
	 * Build the threads query
	 */
	function _buildQuery($listId, $orderBy='rpost')
	{
		
		$where	= $this->_buildContentWhere($listId);
		$order	= $this->_buildContentOrderBy($orderBy);
		
		$query = 'SELECT t.first_mail_id, t.last_mail_id, t.ref_message_id, m.*, m.receive_timestamp AS post_timestamp, mai.receive_timestamp AS thread_timestamp '
				. ' FROM #__mailster_threads AS t'
				. ' LEFT JOIN #__mailster_mails AS m'
				. ' ON (m.id = t.last_mail_id)'
				. ' LEFT JOIN #__mailster_mails AS mai'
				. ' ON (mai.id = t.first_mail_id)'
				. ' WHERE t.id in ('
							. 'SELECT DISTINCT ma.thread_id'
							. ' FROM #__mailster_mails AS ma'
							. ' LEFT JOIN #__mailster_lists AS l'
							. ' ON (ma.list_id = l.id)'
							. $where
							. ')'
				. $order;
		
		return $query;
	}
	

	/**
	* Build the order clause
	*/
	function _buildContentOrderBy($orderBy='rpost')
	{
		switch($orderBy){
			case 'thread':
				$orderby 	= ' ORDER BY thread_timestamp ASC';
				break;
			case 'rthread':
				$orderby 	= ' ORDER BY thread_timestamp DESC';
				break;
			case 'post':
				$orderby 	= ' ORDER BY post_timestamp ASC';
				break;
			case 'rpost':
				$orderby 	= ' ORDER BY post_timestamp DESC';
				break;
			default:
				$orderby 	= ' ORDER BY post_timestamp DESC';
				break;
		}
		
		return $orderby;
	}
	
	/**
	 * Build the where clause
	 */
	function _buildContentWhere($listId)
	{
		$where = array();
		if($listId > 0){
			$where[] = 'ma.list_id = \'' . $listId . '\'';
		}
		
		$filterSearch = JRequest::getString('filter_search', '');
		if(strlen(trim($filterSearch)) > 0){
			$where[] = '( '
						. ' ma.subject LIKE \'%'.$filterSearch.'%\''
						. ' OR ma.body LIKE \'%'.$filterSearch.'%\''
						. ' OR ma.html LIKE \'%'.$filterSearch.'%\''
						. ')';
		}
		$startDate = JRequest::getString('filter_start_date', '');
		if(strlen(trim($startDate)) > 0){
			$jStartDate = &JFactory::getDate(strtotime($startDate));
			$startDate = $jStartDate->toMySQL();
			$where[] = 'ma.receive_timestamp >= \'' . $startDate . '\'';
		}
		$endDate = JRequest::getString('filter_end_date', '');
		if(strlen(trim($endDate)) > 0){
			$endDate = strtotime($endDate);
			$endDate = mktime(23, 59, 59, date('m', $endDate), date('d', $endDate), date('Y', $endDate));
			$jEndDate = &JFactory::getDate($endDate);
			$endDate = $jEndDate->toMySQL();
			$where[] = 'ma.receive_timestamp <= \'' . $endDate . '\'';
		}
		
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	
		return $where;
	}
	

}//Class end
?>
