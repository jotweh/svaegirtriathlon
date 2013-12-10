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
	 * HTML View class for the Main View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewMailster extends JView
	{
		function display($tpl = null)
		{	
			$log = & MstFactory::getLogger();
			$plgUtils = &MstFactory::getPluginUtils();
			
			$title = JText::_( 'COM_MAILSTER_MAILSTER' );
			$mstApp = &MstFactory::getApplication();
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
			$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
			
			if($pHashOk && $plgHashOk && !$isFree){
				$title = JText::_( 'COM_MAILSTER_PRO_EDITION' );
			}
			
			JToolBarHelper::title($title, 'mailster' );	
			$mstUtils = &MstFactory::getUtils();
			$mstUtils->addSubmenu("");
			
			$row = &$this->get('Data');
			$mailPlugin = $plgUtils->getPlugin('mailster', 'system');
			$subscrPlugin = $plgUtils->getPlugin('mailstersubscriber', 'content');
			$profilePlugin = $plgUtils->getPlugin('mailsterprofile', 'system');
			$cbBridgePlugin = $plgUtils->getPlugin('mailstercb', 'system');
			
			$systemProblems = $mstApp->detectSystemProblems();
			
			JToolBarHelper::custom( 'updatecheck', 'updatecheck-mailster.png', 'updatecheck-mailster.png', JText::_( 'COM_MAILSTER_CHECK_FOR_UPDATES' ), false, false );
			
			$this->assignRef('row'  , $row);
			$this->assignRef('mailPlugin'  , $mailPlugin);
			$this->assignRef('subscrPlugin'  , $subscrPlugin);
			$this->assignRef('profilePlugin'  , $profilePlugin);
			$this->assignRef('cbBridgePlugin'  , $cbBridgePlugin);
			$this->assignRef('systemProblems'  , $systemProblems);
			parent::display($tpl);
		}


	}
?>
