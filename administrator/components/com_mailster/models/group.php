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
	 * Group Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelGroup extends JModel
	{
		/**
		 * group id
		 */
		var $_id = null;

		/**
		 * group data array
		 */
		var $_data = null;

		
		function __construct()
		{
			parent::__construct();

			$array = JRequest::getVar('cid',  0, '', 'array');
			$this->setId((int)$array[0]);
		}

		/**
		 * Method to set the identifier
		 */
		function setId($id)
		{
			// Set group id and wipe data
			$this->_id	    = $id;
			$this->_data	= null;
		}

		/**
		 * Get or init group Data
		 */
		function &getData()
		{
			if ($this->_loadData())
			{

			}
			else  $this->_initData();

			return $this->_data;
		}

		
		/**
		 * Get group by group name
		 */
		function &getGroupByName($name){
			// reset object
			$this->setId(0);
			$this->_initData();
			
			$query = 'SELECT id'
					. ' FROM #__mailster_groups'
					. ' WHERE name = \''.$name.'\'';
			
			$this->_db->setQuery($query);			
			$groupId = $this->_db->loadResult();
			
			if($groupId && $groupId > 0){				
				$this->setId($groupId); // group found
			}		
							
			return $this->getData(); // auto-load object
		}

		/**
		 * Load data
		 */
		function _loadData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = 'SELECT *'
						. ' FROM #__mailster_groups'
						. ' WHERE id = '.$this->_id;

				$this->_db->setQuery($query);

				$this->_data = $this->_db->loadObject();

				return (boolean) $this->_data;
			}
			return true;
		}
		
		

		/**
		 * Method to initialise the group data
		 */
		function _initData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$group = new stdClass();
				$group->id			= 0;
				$group->name		= null;
				
				$this->_data		= $group;
				return (boolean) $this->_data;
			}
			return true;
		}
			
		public function getTable($type = 'mailster_groups', $prefix = '', $config = array()){
			return JTable::getInstance($type, $prefix, $config);
		}
		
		/**
		 * Method to store the group
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

			// sanitise id field
			$row->id = (int) $row->id;			

			if (!$row->check()) {
				$this->setError($row->getError());
				return false;
			}

			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}

			return $row->id;
		}
		
		
	}
?>
