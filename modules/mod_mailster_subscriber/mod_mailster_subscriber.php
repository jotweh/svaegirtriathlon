<?php
	/**
	 * @package Joomla
	 * @subpackage Mailster Subscriber Module
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
	 
	// include the helper file
	require_once(dirname(__FILE__).DS.'helper.php'); 
	// include component utilities
	require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_mailster'.DS.'mailster'.DS.'includes.php');
	
	$subscrUtils = &MstFactory::getSubscriberPlugin();
	
	$settings = array();
	
	$subscriberType = strtolower(trim($params->get('subscriber_type', 'subscribe')));
	$designChoice = $params->get('design_choice', '');
	$cssPrefix = $params->get('prefix_class', 'mailster_subscriber_');
	$listLabel = $params->get('list_label', JText::_( 'MOD_MAILSTER_NEWSLETTER' ));
	$listChoice = $params->get('list_choice', 0);
	$captcha = $params->get('captcha', 0);
	$hideListName = $params->get('hide_list_name', 0);
	
	if($subscriberType === 'subscribe'){
		$headerTxt = $params->get('subscribe_header', JText::_( 'MOD_MAILSTER_SUBSCRIBE_TO_THE_NEWSLETTER' ));
		$hideSubscriberName = $params->get('hide_subscriber_name', 0);
		$nameLabel = $params->get('subscriber_name', JText::_( 'MOD_MAILSTER_NAME' ));
		$emailLabel = $params->get('subscriber_email', JText::_( 'MOD_MAILSTER_EMAIL' ));
		$buttonTxt = $params->get('subscribe_button', JText::_( 'MOD_MAILSTER_SUBSCRIBE' ));
		$submitTxt = $params->get('subscribe_thank_msg', JText::_( 'MOD_MAILSTER_THANK_YOU_FOR_SUBSCRIBING' ));
		$errorTxt = $params->get('subscribe_error_msg', JText::_( 'MOD_MAILSTER_SUBSCRIPTION_ERROR_OCCURED_PLEASE_TRY_AGAIN' ));
		$smartHide =  $params->get('subscriber_smart_hide', 0);
	}else{
		$headerTxt = $params->get('unsubscribe_header', JText::_( 'MOD_MAILSTER_UNSUBSCRIBE_FROM_NEWSLETTER' ));
		$hideSubscriberName = 0;
		$nameLabel = '';
		$emailLabel = $params->get('unsubscriber_email', JText::_( 'MOD_MAILSTER_EMAIL' ));
		$buttonTxt = $params->get('unsubscribe_button', JText::_( 'MOD_MAILSTER_UNSUBSCRIBE' ));
		$submitTxt = $params->get('unsubscribe_thank_msg', JText::_( 'MOD_MAILSTER_SORRY_THAT_YOU_DECIDED_TO_UNSUBSCRIBE_HOPE_TO_SEE_YOU_AGAIN_IN_THE_FUTURE' ));
		$errorTxt = $params->get('unsubscribe_error_msg', JText::_( 'MOD_MAILSTER_UNSUBSCRIPTION_ERROR_OCCURED_PLEASE_TRY_AGAIN' ));
		$smartHide =  $params->get('unsubscriber_smart_hide', 0);
	}
	
	if($listChoice == 0){
		$settings['allLists'] = true;
		$settings['listIdSpecified'] = false;
		$settings['listNameSpecified'] = false;
		$settings['listId'] = 0;
	}else{
		$settings['allLists'] = false;
		$settings['listIdSpecified'] = true;
		$settings['listNameSpecified'] = false;
		$settings['listId'] = $listChoice;
	}
	
	if($captcha){
		// if captcha = 1 then take recaptcha (backward compatibility)
		$settings['captcha'] = (($captcha == 1) ? MstConsts::CAPTCHA_ID_RECAPTCHA :  $captcha);
	}else{
		$settings['captcha'] = false;
	}
	
	$settings['hideListName'] = ($hideListName == 1);
	$settings['hideNameField'] = ($hideSubscriberName == 1);
	$settings['smartHide'] = ($smartHide == 1);
	
	$settings['nameLabel'] 		= $nameLabel;
	$settings['emailLabel'] 	= $emailLabel;
	$settings['listLabel'] 		= $listLabel;
	$settings['buttonTxt'] 		= $buttonTxt;
	$settings['submitTxt'] 		= $submitTxt;
	$settings['errorTxt'] 		= $errorTxt;
	$settings['headerTxt'] 		= $headerTxt;
	$settings['cssPrefix'] 		= $cssPrefix;
	$settings['designChoice'] 	= $designChoice;
	
	if($subscriberType === 'subscribe'){
		$html = $subscrUtils->getSubscriberHtml($settings, 111);
	}else{
		$html = $subscrUtils->getUnsubscriberHtml($settings, 222);
	}
	
	echo $html;
	
	// include the template for display
	//require(JModuleHelper::getLayoutPath('mod_mailster_subscriber'));

?>
