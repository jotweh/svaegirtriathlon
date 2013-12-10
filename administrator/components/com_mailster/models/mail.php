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
	 * Mail Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelMail extends JModel
	{
		/**
		 * group id
		 * @var int
		 */
		var $_id = null;

		/**
		 * group data array
		 * @var array
		 */
		var $_data = null;

		/**
		 * Constructor
		 */
		function __construct()
		{
			parent::__construct();

			$array = JRequest::getVar('cid',  0, '', 'array');
			$this->setId((int)$array[0]);
		}

		/**
		 * Method to set the identifier
		 *
		 * @access	public
		 * @param	int identifier
		 */
		function setId($id)
		{
			// Set mail id and wipe data
			$this->_id	    = $id;
			$this->_data	= null;
		}		
		

		/**
		 *
		 *
		 * @access public
		 * @return array
		 */
		function &getData()
		{
			if (!$this->_loadData()){
				$this->_initData();
			}
			return $this->_data;
		}

		/**
		 * 
		 *
		 * @access	private
		 * @return	boolean	True on success
		 */
		function _loadData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$query = 'SELECT *'
						. ' FROM #__mailster_mails'
						. ' WHERE id = '.$this->_id
						;
				$this->_db->setQuery($query);
				$this->_data = $this->_db->loadObject();

				return (boolean) $this->_data;
			}
			return true;
		}
		
		

		/**
		 * Method to initialise the mail data
		 *
		 * @access	private
		 * @return	boolean	True on success
		 */
		function _initData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$mail = new stdClass();
				$mail->id						= 0;
				$mail->list_id					= null;
				$mail->thread_id				= null;
				$mail->hashkey					= null;
				$mail->message_id				= null;
				$mail->in_reply_to				= null;
				$mail->references_to			= null;
				$mail->receive_timestamp		= null;
				$mail->from_name				= null;
				$mail->from_email				= null;
				$mail->subject					= null;
				$mail->body						= null;
				$mail->html						= null;
				$mail->has_attachments			= 0;
				$mail->attachments				= null;
				$mail->fwd_errors				= 0;
				$mail->fwd_completed			= 0;
				$mail->fwd_completed_timestamp	= 0;
				$mail->blocked_mail				= 0;
				$mail->bounced_mail				= 0;
							
				$this->_data		= $mail;
				return (boolean) $this->_data;
			}
			return true;
		}
			
		public function getTable($type = 'mailster_mails', $prefix = '', $config = array()){
			return JTable::getInstance($type, $prefix, $config);
		}

				
		/**
		 * Method to store the mail
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
