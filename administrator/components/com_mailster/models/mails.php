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
 * Mailing List Mails Model
 *
 * @package Joomla
 * @subpackage Mailster
 */
class MailsterModelMails extends JModel
{

	var $_id = 0;

	/**
	 * Mails data array
	 *
	 * @var array
	 */
	var $_data = null;
	
	/**
	 * Limit Offset
	 *
	 * @var array
	 */
	var $_limit_offset = null;

	/**
	 * Mails total
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
		$limitstart = $app->getUserStateFromRequest(JRequest::getCmd('option','com_mailster').'limitstart','limitstart',0);
			
		$limit = $limit + 0; // make it numerical
		$limitstart = $limitstart + 0; // make it numerical
			
		if($limit == 0){
			$limitstart = 0;
		}

		// Set the page pagination variables
		$this->setState('limit',$limit);
		$this->setState('limitstart',$limitstart);
	}

	
	
	/**
	 * Method to get mails data
	 */
	function getData($listId, $overrideLimits=false)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data) || $this->_id != $listId){
			$this->_id = $listId;
			$query = $this->_buildQuery($listId);

			$limitstart = $this->getState('limitstart');
			$limit = $this->getState('limit');

			if(!$overrideLimits){
				$this->_data = $this->_getList($query, $limitstart, $limit);
				$this->_limit_offset = $limitstart;
			}else{
				$this->_data = $this->_getList($query, 0, 0);
				$this->_limit_offset = $limitstart;
			}
		}
		return $this->_data;
	}


	/**
	 * Method to get blocked mails
	 */
	function getBlockedMailData($listId, $overrideLimits=false)
	{
		if($this->_id != $listId){
			$this->_id = $listId;
			$this->_data = null;
		}
			
		$firstLast = $this->getFirstLastDate();
		if(!is_null($firstLast)){
			$query = $this->_buildQuery($listId, true, null, $firstLast['first'], $firstLast['last']);
		}else{
			$query = $this->_buildQuery($listId, true, null);
		}
		
		$blockedMails = $this->_getList($query, 0, 0);

		return $blockedMails;
	}

	/**
	 * Method to get bounced mails
	 */
	function getBouncedMailData($listId, $overrideLimits=false)
	{
		if($this->_id != $listId){
			$this->_id = $listId;
			$this->_data = null;
		}
		$firstLast = $this->getFirstLastDate();
		if(!is_null($firstLast)){
			$query = $this->_buildQuery($listId, null, true, $firstLast['first'], $firstLast['last']);
		}else{
			$query = $this->_buildQuery($listId, null, true);
		}
		
		$bouncedMails = $this->_getList($query, 0, 0);
		
		return $bouncedMails;
	}



	function getFirstLastDate(){
		$firstLast = null;
		if(!empty($this->_data)){
			$firstLast = array();
			$first = $this->_data[0];
			$last = $this->_data[count($this->_data)-1];
			$firstTstmp = $first->receive_timestamp; // MySQL format
			$firstParsed = strtotime($firstTstmp); // timestamp
			$lastTstmp = $last->receive_timestamp; // MySQL format
			$lastParsed = strtotime($lastTstmp); // timestamp

			$firstMail = $firstTstmp; // MySQL format
			$lastMail = $lastTstmp; // MySQL format

			if($lastParsed < $firstParsed){ // determine smaller timestamp
				$firstMail = $lastTstmp; // MySQL format
				$lastMail = $firstTstmp; // MySQL format
			}
			$firstLast['first'] = $firstMail; // MySQL format
			$firstLast['last']	= $lastMail; // MySQL format
			
			if($this->_limit_offset == 0){
				// This means the forwarded emails are shown from the beginning
				// To include bounced/blocked emails that are more recent,
				// we have to null the last date timestamp.
				$firstLast['last'] = null;
			}
		}
		return $firstLast;
	}


	/**
	 * Total nr of mail
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
	 * Method to get a pagination object for the mails
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
	 * Build the query
	 */
	function _buildQuery($listId, $isBlocked=null, $isBounced=null, $firstDate=null, $lastDate=null)
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere($listId, $isBlocked, $isBounced, $firstDate, $lastDate);
		$orderby	= $this->_buildContentOrderBy();
			
		$query = 'SELECT m.*, l.name'
					. ' FROM #__mailster_mails AS m'
					. ' LEFT JOIN #__mailster_lists AS l'
					. ' ON (m.list_id = l.id)'
					. $where
					. $orderby;
		
		return $query;
	}

	/**
	 * Build the order clause
	 */
	function _buildContentOrderBy()
	{
		$orderby 	= ' ORDER BY m.receive_timestamp DESC, m.fwd_completed_timestamp DESC';
		return $orderby;
	}

	/**
	 * Build the where clause
	 */
	function _buildContentWhere($listId, $isBlocked=null, $isBounced=null, $firstDate=null, $lastDate=null)
	{
		$where = array();
		if($listId > 0){
			$where[] = ' m.list_id = \'' . $listId . '\'';
		}
		if(!is_null($firstDate)){
			$where[] = ' m.receive_timestamp >= \'' . $firstDate . '\'';
		}
		if(!is_null($lastDate)){
			$where[] = ' m.receive_timestamp <= \'' . $lastDate . '\'';
		}
		if($isBlocked != null){
			if($isBlocked){
				$where[] = ' m.blocked_mail > \'0\'';
			}else{
				$where[] = ' m.blocked_mail = \'1\'';
			}
		}
		if($isBounced != null){
			$bounced = $isBounced ? 1 : 0;
			$where[] = ' m.bounced_mail = \'' . $bounced . '\'';
		}
		if($isBlocked == null && $isBounced == null){
			$where[] = ' ((m.bounced_mail IS NULL AND m.blocked_mail IS NULL) OR (m.bounced_mail = \'0\' AND m.blocked_mail = \'0\'))';
		}
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
	}


	/**
	 * Method to remove a mail from the list
	 */
	function delete($cid = array())
	{
		$result = false;

		if (count( $cid )){
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__mailster_mails'
			. ' WHERE id IN ('. $cids .')';

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
