<?php
/**
 * @version 1.0.2 Stable $Id$
 * @package Joomla
 * @subpackage EventList
 * @copyright (C) 2005 - 2009 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * EventList is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * EventList is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventList; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Editevents View
 *
 * @package Joomla
 * @subpackage EventList
 * @since 0.9
 */
class EventListViewEditvenue extends JView
{
	/**
	 * Creates the output for venue submissions
	 *
	 * @since 0.5
	 * @param int $tpl
	 */
	function display( $tpl=null )
	{
		global $app;
		
		//only registered user can add events
		$user   = & JFactory::getUser();
		if (!$user->id) {
			$app->redirect(JRoute::_('index.php?option=com_users&view=login'), JText::_('Please login to be able to submit venues') );
		}

		$editor 	= & JFactory::getEditor();
		$doc 		= & JFactory::getDocument();
		$elsettings = & ELHelper::config();

		// Get requests
		$id				= JRequest::getInt('id');			

		//Get Data from the model
		$row 		= $this->Get('Venue');
		JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'locdescription' );

		JHTML::_('behavior.formvalidation');
		JHTML::_('behavior.tooltip');

		//add css file
		$doc->addStyleSheet($this->baseurl.'/components/com_eventlist/assets/css/eventlist.css');
		$doc->addCustomTag('<!--[if IE]><style type="text/css">.floattext{zoom:1;}, * html #eventlist dd { height: 1%; }</style><![endif]-->');

		// Get the menu object of the active menu item
		$menu		= & JSite::getMenu();
		$item    	= $menu->getActive();
		$params 	= & $app->getParams('com_eventlist');

		$id ? $title = JText::_( 'COM_EVENTLIST_EDIT_VENUE' ) : $title = JText::_( 'COM_EVENTLIST_ADD_VENUE' );

		//pathway
		$pathway 	= & $app->getPathWay();
		$pathway->setItemName(1, $item->title);
		$pathway->addItem($title, '');

		//Set Title
		$doc->setTitle($title);

		//editor user
		$editoruser = ELUser::editoruser();
		
		//transform <br /> and <br> back to \r\n for non editorusers
		if (!$editoruser) {
			$row->locdescription = ELHelper::br2break($row->locdescription);
		}

		//Get image
		$limage = ELImage::flyercreator($row->locimage);

		//Set the info image
		$infoimage = JHTML::_('image', 'components/com_eventlist/assets/images/icon-16-hint.png', JText::_( 'COM_EVENTLIST_NOTES' ) );

		$this->assignRef('row' , 					$row);
		$this->assignRef('editor' , 				$editor);
		$this->assignRef('editoruser' , 			$editoruser);
		$this->assignRef('limage' , 				$limage);
		$this->assignRef('infoimage' , 				$infoimage);
		$this->assignRef('elsettings' , 			$elsettings);
		$this->assignRef('item' , 					$item);
		$this->assignRef('params' , 				$params);

		parent::display($tpl);

	}
}
?>