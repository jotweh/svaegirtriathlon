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
	 * Mailing Lists Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelLists extends JModel
	{
		/**
		 * Lists data array
		 *
		 * @var array
		 */
		var $_data = null;

		/**
		 * Lists total
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
		}

		/**
		 * Method to get list item data
		 *
		 * @access public
		 * @return array
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
		 * Total nr of lists
		 *
		 * @access public
		 * @return integer
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
		 * Method to get a pagination object for the lists
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

			$query = 'SELECT a.*'
						. ' FROM #__mailster_lists AS a'
						. $where
						. $orderby
						;
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
			$orderby 	= ' ORDER BY a.name';			
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
		 * Additional tasks
		 *
		 * @access private
		 * @param array $rows
		 * @return array
		 */
		function _additionals($rows)
		{
			/* nothing for now */
			$listCount = count ($this->_data);
			for($i=0; $i < $listCount; $i++){
				$list = &$this->_data[$i];
				$list->nrMembers = $this->_getRecipientsCount($list->id);
				$list->nrMails = $this->_getMailsCount($list->id);
				
			}
			return $rows;
		}
		
		function _getRecipientsCount($listId){			
			$mstRecipients = &MstFactory::getRecipients();
			return $mstRecipients->getTotalRecipientsCount($listId);		
		}
		
		function _getMailsCount($listId){			
						
			$query = 'SELECT count( * ) AS totalMails'
					. ' FROM #__mailster_mails'
					. ' WHERE list_id =\'' . $listId . '\''
					. ' AND ('
					. '          (bounced_mail IS NULL AND blocked_mail IS NULL)'
					. '       OR (bounced_mail = \'0\' AND blocked_mail = \'0\')'
					. ' )';
						
			$this->_db->setQuery($query);
			$resObj = $this->_db->loadObject();	
			
			return $resObj->totalMails;
		}

		/**
		 * Method to (un)publish a list
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function publish($cid = array(), $publish = 1)
		{
			$user 	=& JFactory::getUser();
			$userid = (int) $user->get('id');

			if (count( $cid ))
			{
				$cids = implode( ',', $cid );

				$query = 'UPDATE #__mailster_lists'
					. ' SET published = '. (int) $publish
					. ' WHERE id IN ('. $cids .')'
				;

				$this->_db->setQuery( $query );

				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		/**
		 * Method to remove a list
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function delete($cid = array())
		{
			$result = false;

			if (count( $cid ))
			{
				$cids = implode( ',', $cid );
				$query = 'DELETE FROM #__mailster_lists'
						. ' WHERE id IN ('. $cids .')';

				$this->_db->setQuery( $query );

				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				$query = 'DELETE FROM #__mailster_list_groups'
						. ' WHERE list_id IN ('. $cids .')';

				$this->_db->setQuery( $query );

				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				$query = 'DELETE FROM #__mailster_list_members'
						. ' WHERE list_id IN ('. $cids .')';

				$this->_db->setQuery( $query );

				if(!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				$query = 'DELETE FROM #__mailster_notifies'
						. ' WHERE list_id IN ('. $cids .')';

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
