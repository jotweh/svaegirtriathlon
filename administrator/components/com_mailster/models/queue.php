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
	 * Queued Mails Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelQueue extends JModel
	{
		/**
		 * Mails data array
		 *
		 * @var array
		 */
		var $_data = null;

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

		
		function getData($overrideLimits=false, $limitedCols=false)
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = $this->_buildQuery($limitedCols);
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
		 * Method to get a pagination object for the mails
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
		 * Total nr of queued mails
		 */
		function getTotal()
		{
			// Lets load the total nr if it doesn't already exist
			if (empty($this->_total)){
				$query = $this->_buildQuery(true);
				$this->_total = $this->_getListCount($query);
			}
			return $this->_total;
		}

		/**
		 * Build the query
		 */
		function _buildQuery($limitedCols=false)
		{	
			// Get the WHERE and ORDER BY clauses for the query
			$where		= $this->_buildContentWhere();
			$orderby	= $this->_buildContentOrderBy();
			
			if($limitedCols){
				$query = 'SELECT m.mail_id, m.name, m.email, m.is_locked, ma.id, ma.subject';	
			}else{
				$query = 'SELECT m.*, ma.*';
			}
			
			$query = $query	. ' FROM #__mailster_queued_mails m	LEFT JOIN #__mailster_mails ma ON (m.mail_id = ma.id)'
							. $where
							. $orderby;	
						
			return $query;
		}

		/**
		 * Build the order clause
		 */
		function _buildContentOrderBy()
		{
			$orderby 	= ' ORDER BY m.mail_id ASC, m.email ASC';
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
		 * Method to remove mails from queue
		 */
		function delete($cid = array())
		{
			$result = false;

			if (count( $cid )){
				
				for($i=0;$i<count($cid);$i++){
					$queueEntry = explode(':',$cid[$i]);
					$mailId = $queueEntry[0];
					$email = $queueEntry[1];
					$query = 'DELETE FROM #__mailster_queued_mails'
							. ' WHERE mail_id = \'' . $mailId . '\''
							. ' AND email = ' . $this->_db->quote($email);
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
