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

	jimport( 'joomla.application.component.view');

	/**
	 * HTML View class for the Group View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewGroup extends JView
	{
		function display($tpl = null)
		{	
			$user 		= & JFactory::getUser();
			$document	= & JFactory::getDocument();
			$db  		= & JFactory::getDBO();
			$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );			
			
			//build toolbar		
			if ( $cid ) {
				JToolBarHelper::title( JText::_( 'COM_MAILSTER_EDIT_GROUP' ), 'group-mailster' );
			} else {
				JToolBarHelper::title( JText::_( 'COM_MAILSTER_ADD_GROUP' ), 'group-mailster' );
			}
			//Create Submenu	
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();		
			
			// Get data from the model
			$row = &$this->get('Data');

			$Lists = array();
		//	$Lists['category'] = JHTML::_('select.genericlist', $catlist, 'cat_id', 'size="1" class="inputbox"', 'value', 'text', $row->cat_id );

			$this->assignRef('row'      	, $row);
			$this->assignRef('user'			, $user);
			$this->assignRef('Lists'		, $Lists);
	 
			parent::display($tpl);
		}


	}
?>
