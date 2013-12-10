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
	 * HTML View class for the Info View
	 *
	 * @package Joomla
	 * @subpackage Mailster
	 */
	class MailsterViewInfo extends JView
	{
	  public function display($tpl = null) {
	   
		$mstApp = &MstFactory::getApplication();
		$titleTxt = JText::_( 'COM_MAILSTER_MAILSTER_INFO' );
		
		JToolBarHelper::title($titleTxt, 'info-mailster' );	
				
		$mstUtils = & MstFactory::getUtils();
		$mstUtils->addSubmenu('info');
	
		$appName = JText::_( 'COM_MAILSTER_MAILSTER' );
		$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
		$plgHashOk = $mstApp->checkPluginProductHashes();
		$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
		
		if($pHashOk && $plgHashOk && !$isFree){
			$appName = JText::_( 'COM_MAILSTER_PRO_EDITION' );
		}
		
		$version = $mstApp->getVersionString(false);
		$fullVersion 	= $mstApp->getVersionString();
		$versionStr = JText::_( 'COM_MAILSTER_VERSION' ) . ': ' . $fullVersion;
			
		$compInfos = $mstApp->getInstallInformation();		
				
		$this->assignRef('appName'  , $appName);
		$this->assignRef('versionStr'  , $versionStr);
		$this->assignRef('version'  , $version);
		$this->assignRef('compInfos'  , $compInfos);
	    parent::display($tpl);
	  }
	  
	}
