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

	defined( '_JEXEC' ) or die( 'Restricted access' );

	jimport('joomla.application.component.controller');

	/**
	 * Mailster Component Groups Controller
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterControllerGroups extends MailsterController
	{
		/**
		* Constructor
		*
		*/
		function __construct()
		{
			// execute parent's constructor
			parent::__construct();			
		}
		
		
		
		function newGroups()
		{
			$userCount = JRequest::getInt('groupCount');
			for($i=0; $i < $userCount; $i++)
			{
				$name 	= JRequest::getString('name-' . ($i+1));
				if($name != '')
				{
					$model = $this->getModel('group');
					$group = new stdClass();
					$group->id				= 0;
					$group->name			= $name;
					$returnid = $model->store($group);
				}
			}
			$this->setRedirect( 'index.php?option=com_mailster&view=groups' );			
		}
			
					
		/**
		 * logic for cancel an action
		 *
		 * @access public
		 * @return void
		 */
		function cancel()
		{
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			
			$group = & JTable::getInstance('mailster_groups', '');
			$group->bind(JRequest::get('post'));

			$this->setRedirect( 'index.php?option=com_mailster&view=groups' );
		}

		/**
		 * logic to create the new group screen
		 *
		 * @access public
		 * @return void
		 */
		function add( )
		{
			$this->setRedirect( 'index.php?option=com_mailster&view=group' );
		}
		
			

		/**
		 * logic to create the edit group screen
		 *
		 * @access public
		 * @return void
		 */
		function edit( )
		{
			JRequest::setVar( 'view', 'group' );
			JRequest::setVar( 'hidemainmenu', 1 );

			$model 	= $this->getModel('group');
			$task 	= JRequest::getVar('task');

			$user	=& JFactory::getUser();
			
			parent::display();
		}
		
		
		function apply()
		{
			$this->save();
		}

		/**
		 * logic to save a group
		 *
		 * @access public
		 * @return void
		 */
		function save()
		{
			
			// Check for request forgeries
			JRequest::checkToken() or die( 'Invalid Token' );
			
			$task		= JRequest::getVar('task');

			$post = JRequest::get( 'post' );
			$post['datdescription'] = JRequest::getVar( 'datdescription', '', 'post','string', JREQUEST_ALLOWRAW );
			$post['datdescription']	= str_replace( '<br>', '<br />', $post['datdescription'] );

			$model = $this->getModel('group');
			if ($returnid = $model->store($post)) {

				switch ($task)
				{
					case 'apply' :
						$link = 'index.php?option=com_mailster&controller=groups&task=edit&cid[]='.$returnid;
						break;

					default :
						$link = 'index.php?option=com_mailster&view=groups';
						break;
				}
				$msg	= JText::_( 'COM_MAILSTER_GROUP_SAVED');

				$cache = &JFactory::getCache('com_mailster');
				$cache->clean();

			} else {

				$msg 	= '';
				$link = 'index.php?option=com_mailster&view=groups';

			}

			$this->setRedirect( $link, $msg );
		}
		
		

		/**
		 * logic to remove groups
		 *
		 * @access public
		 * @return void
		 */
		function removeGroups()
		{
			$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

			$total = count( $cid );

			if (!is_array( $cid ) || count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'COM_MAILSTER_SELECT_AN_ITEM_TO_DELETE' ) );
			}

			$model = $this->getModel('groups');
			if(!$model->delete($cid)) {
				echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			}

			$msg = $total.' '.JText::_( 'COM_MAILSTER_GROUP_DELETED');

			$cache = &JFactory::getCache('com_mailster');
			$cache->clean();

			$this->setRedirect( 'index.php?option=com_mailster&view=groups', $msg );
		}
	}
?>
