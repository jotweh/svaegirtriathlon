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
	 * Mailing List Model
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterModelList extends JModel
	{
		/**
		 * list id
		 *
		 * @var int
		 */
		var $_id = null;

		/**
		 * list data array
		 *
		 * @var array
		 */
		var $_data = null;

		/**
		 * Constructor
		 *
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
			// Set list id and wipe data
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
			if ($this->_loadData())
			{

			}
			else  $this->_initData();

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
						. ' FROM #__mailster_lists'
						. ' WHERE id = '.$this->_id
						;

				$this->_db->setQuery($query);

				$this->_data = $this->_db->loadObject();

				return (boolean) $this->_data;
			}
			return true;
		}
		
		
		function getAllJoomlaUsers(){
			$query = 'SELECT *'
				. ' FROM #__users'
				. ' WHERE block=\'0\''
				. ' ORDER BY name, email';
			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}
		
		

		/**
		 * Method to initialise the list data
		 *
		 * @access	private
		 * @return	boolean	True on success
		 */
		function _initData()
		{
			// Lets load the content if it doesn't already exist
			if (empty($this->_data))
			{
				$list = new stdClass();
				$list->id						= 0;
				$list->name						= null;
				$list->admin_mail				= null;
				$list->list_mail				= null;
				$list->subject_prefix			= null;
				$list->mail_in_user				= null;
				$list->mail_in_pw				= null;
				$list->mail_in_host				= null;
				$list->mail_in_port				= null;
				$list->mail_in_use_secure		= null;
				$list->mail_in_protocol			= null;
				$list->mail_in_params			= null;
				$list->mail_out_user			= null;
				$list->mail_out_pw				= null;
				$list->mail_out_host			= null;
				$list->mail_out_port			= null;
				$list->mail_out_use_secure		= null;
				$list->custom_header_plain		= MstConsts::TEXT_VARIABLES_NAME . ' <' . MstConsts::TEXT_VARIABLES_EMAIL . '> (' . MstConsts::TEXT_VARIABLES_DATE . '):';
				$list->custom_header_html		= MstConsts::TEXT_VARIABLES_NAME . ' <' . MstConsts::TEXT_VARIABLES_EMAIL . '> (' . MstConsts::TEXT_VARIABLES_DATE . '):';
				$list->custom_footer_plain		= null;
				$list->custom_footer_html		= null;
				$list->mail_format_conv			= null;
				$list->alibi_to_mail			= null;
								
				$list->published				= 1;
				$list->active					= 1;
				$list->use_joomla_mailer		= 1;
				$list->mail_in_use_sec_auth		= 0;
				$list->mail_out_use_sec_auth	= 0;
				$list->public_registration		= 1;
				$list->sending_public			= 1;
				$list->sending_recipients		= 0;
				$list->sending_admin			= 0;
				$list->sending_group			= 0;
				$list->sending_group_id			= 0;
				$list->allow_registration		= 1;	
				$list->reply_to_sender			= 0;	
				$list->copy_to_sender			= 1;			
				$list->disable_mail_footer		= 0;		
				$list->addressing_mode			= 1;
				$list->mail_from_mode			= 0;
				$list->name_from_mode			= 0;
				$list->archive_mode				= 0;			
				$list->bounce_mode				= 0;				
				$list->bounce_mail			= null;
				$list->bcc_count				= 10;	
				$list->max_send_attempts		= 5;
				$list->filter_mails				= 0;
				$list->clean_up_subject			= 1;
				$list->mail_format_altbody		= 1;
				
				$list->lock_id					= 0;
				$list->is_locked				= 0;
				$list->last_lock				= null;
				$list->last_check				= null;
				$list->throttle_hour			= 0;
				$list->throttle_hour_cr			= 0;
				$list->throttle_hour_limit		= 0;
				$list->cstate					= 0;
				$list->mail_size_limit			= 0;
				$list->notify_not_fwd_sender	= 1;
				
				$this->_data					= $list;
				return (boolean) $this->_data;
			}
			return true;
		}
		
		public function getTable($type = 'mailster_lists', $prefix = '', $config = array()){
			return JTable::getInstance($type, $prefix, $config);
		}
						
		/**
		 * Method to store the list
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function store($data){		
			$user		= & JFactory::getUser();
			$config 	= & JFactory::getConfig();
			
			$row  =& $this->getTable();
			
			if (!$row->bind($data)) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}

			$row->id = (int) $row->id;	// sanitise id field		

			if (!$row->check()) {
				$this->setError($row->getError());
				return false;
			}
			
			$mstApp = & MstFactory::getApplication();					
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
			if(!$pHashOk || !$plgHashOk || $isFree){
				$row->disable_mail_footer = 0;
				$row->filter_mails = 0;
				$row->throttle_hour_limit = 0;
				$row->mail_format_altbody = MstConsts::MAIL_FORMAT_ALTBODY_YES;
				$row->mail_format_conv = MstConsts::MAIL_FORMAT_CONVERT_HTML;
				$row->archive_mode = MstConsts::ARCHIVE_MODE_ALL;
			}
			
			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}

			return $row->id;
		}		
	}
?>
