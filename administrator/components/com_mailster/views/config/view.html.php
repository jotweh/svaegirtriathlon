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
	 * HTML View class for the Config (Configuration) View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewConfig extends JView
	{
	  public function display($tpl = null) {
	   
		$titleTxt = JText::_( 'COM_MAILSTER_MAILSTER_CONFIGURATION' );
		
		JToolBarHelper::title($titleTxt , 'config' );
			
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		
		$mstUtils = & MstFactory::getUtils();
		$mstUtils->addSubmenu('config');
	    
		$mstConfig = &MstFactory::getConfig();
		$params = $mstConfig->getAllParameters();
				
		if(trim($params->get('words_to_filter')) === MstConsts::NO_PARAMETER_SUPPLIED_FLAG){
			$params->set('words_to_filter', ' ');
		}
		
	    $this->assignRef('params', $params);
	
	    JHTML::_('behavior.tooltip');
	    jimport('joomla.html.pane');
	    
	    parent::display($tpl);
	  }
	  
	}
