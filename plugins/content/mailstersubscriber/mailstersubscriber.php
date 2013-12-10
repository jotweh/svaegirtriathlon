<?php
	/**
	 * @package Joomla
	 * @subpackage Mailster Subscriber
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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'includes.php');
 
function chkSubscrIntegrity(){
	$inclPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'includes.php';	
	if (isset($inclPath) && is_file($inclPath)) {		
	 	require_once($inclPath);
		$mstApp = & MstFactory::getApplication();
		$pHashOk = $mstApp->isProductHashCorrect('plg_mailster_subscriber', 'cc0d0553ca6b9e32598a4c7f3ca8fa91');
		if(!$pHashOk){
			return false;
		}
	} 
	return true;
}


class plgContentMailstersubscriber extends JPlugin {	
	
	function plgContentMailstersubscriber( &$subject, $config ) {
		parent::__construct( $subject, $config );
	}
	
	// Joomla 1.5 event
	public function onPrepareContent( &$row, &$params, $limitstart = 0 ){
		$article->text = $this->contentPluginAction($row, $params, $limitstart); 
		return true;
	}
 
	// Joomla 1.6+ event
	public function onContentPrepare($context, &$row, &$params, $page = 0){
		// $row in Joomla! 1.6/1.7 may be a string, not an article object! - thanks Nicholas :-)
		if(is_object($row)) {
			return $this->contentPluginAction($row, $params, $page);
		} else {
			$row = $this->contentPluginAction($row);
		}
		return true;
	}
	
	function contentPluginAction(&$row, &$params, $page=0) {
		$subscrPlugin = & MstFactory::getSubscriberPlugin();
		
		if (is_object($row)) {
			$text = &$row->text;
		}else {
			$text = &$row;
		}
		global $mainframe;
		$subscriberTmplFound = true;
		$unsubscriberTmplFound = true;
		$foundCounter = 0;
		
		while(($subscriberTmplFound || $unsubscriberTmplFound) && $foundCounter < 300 ){
			$strPos = JString::strpos($text, MstConsts::SUBSCR_SUBSCRIBER_TMPL);
			if ($strPos === false) {
				$subscriberTmplFound = false;
				$strPos = JString::strpos($text, MstConsts::SUBSCR_UNSUBSCRIBER_TMPL);
				if ($strPos === false) {
					$unsubscriberTmplFound = false;
				}else{
					$foundCounter++;
					$unsubscriberTmplFound = true;
					$strPos = $strPos + strlen(MstConsts::SUBSCR_UNSUBSCRIBER_TMPL);
				}
			}else{
				$foundCounter++;
				$subscriberTmplFound = true;
				$strPos = $strPos + strlen(MstConsts::SUBSCR_SUBSCRIBER_TMPL);
			}
			
			if($subscriberTmplFound == true){
				$text = $subscrPlugin->processSubscriber($text, $strPos, $foundCounter);
			}
			if($unsubscriberTmplFound == true){
				$text = $subscrPlugin->processUnsubscriber($text, $strPos, $foundCounter);
			}
		}
		
		return $text;
	}
	
}
