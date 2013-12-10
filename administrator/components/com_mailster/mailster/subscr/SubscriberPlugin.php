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
	
	
class MstSubscriberPlugin
{
	public function __construct() {
		$lang =& JFactory::getLanguage();
		$lang->load('com_mailster',JPATH_ADMINISTRATOR);
	}
	
		
	public function processSubscriber($text, $strPos, $foundCounter)
	{
		$log = & MstFactory::getLogger();
		
		$kvRes = $this->findKeyValues($text, $strPos);
		$keyValuesFound = $kvRes['keyValuesFound'];
		$startPos = $kvRes['startPos'];
		$endPos = $kvRes['endPos'];
		$keyValuesStr = $kvRes['keyValuesStr'];
		
		if($keyValuesFound == False){
			$log->debug('keys/values not found');			
			return $text;				
		}
		
		$plugin = &JPluginHelper::getPlugin('content', 'mailstersubscriber');
		$pluginParams = new JParameter($plugin->params);
		
		$settings = array();
		$settings['allLists'] = true;
		$settings['listIdSpecified'] = false;
		$settings['listNameSpecified'] = false;
    	$settings['hideListName'] = false;
    	$settings['hideNameField'] = false;
		$settings['captcha'] = ($pluginParams->def('captcha', '0') !== '0') ? $pluginParams->def('captcha', '0') : false;
		$settings['nameLabel'] = $pluginParams->def('subscriber_name', '');
		$settings['emailLabel'] = $pluginParams->def('subscriber_email', '');
		$settings['listLabel'] = $pluginParams->def('listLabel', '');
		$settings['buttonTxt'] = $pluginParams->def('subscribe_button', '');
		$settings['submitTxt'] = $pluginParams->def('subscribe_thank_msg', '');
		$settings['errorTxt'] = $pluginParams->def('subscribe_error_msg', '');
		$settings['headerTxt'] = $pluginParams->def('subscribe_header', '');
		$settings['cssPrefix'] = $pluginParams->def('prefix_class', 'mailster_subscriber_');
		$settings['designChoice'] = $pluginParams->def('design_choice', '');
		$settings['smartHide'] = ($pluginParams->def('smart_hide', 0) == 1);
		$settings['listId'] = 0;
				
		if($settings['designChoice'] !== ''){
			$settings['cssPrefix'] = MstConsts::SUBSCR_CSS_DEFAULT . $settings['designChoice'] . '_';
		}
		
		$settings = $this->processKeyValueStr($keyValuesStr, $settings);

		$replaceTxt = $this->getSubscriberHtml($settings, $foundCounter);
				
		$text2Replace = MstConsts::SUBSCR_SUBSCRIBER_TMPL . MstConsts::SUBSCR_PARAM_START . $keyValuesStr . MstConsts::SUBSCR_PARAM_END;
		$text = JString::str_ireplace($text2Replace, $replaceTxt, $text);
		$text2Replace = MstConsts::SUBSCR_SUBSCRIBER_TMPL . ' ' . MstConsts::SUBSCR_PARAM_START . $keyValuesStr . MstConsts::SUBSCR_PARAM_END; // allow one char inbetween
		$text = JString::str_ireplace($text2Replace, $replaceTxt, $text);
		return $text;
	}	
	
	
	public function getSubscriberHtml($res, $foundCounter)
	{
		$log = & MstFactory::getLogger();
		$mstUtils = & MstFactory::getUtils();
		$listUtils = & MstFactory::getMailingListUtils();
		$subscrUtils = & MstFactory::getSubscribeUtils();
		$document =& JFactory::getDocument();		
		$document->addStyleSheet('plugins/content/mailstersubscriber/subscriber.css', 'text/css',"screen");
		
		$noName = JText::_( 'COM_MAILSTER_NAME_MISSING' );
		$noEmail = JText::_( 'COM_MAILSTER_EMAIL_MISSING' );
		$invalidEmail = JText::_( 'COM_MAILSTER_INVALID_EMAIL' );
		$noListChosen = JText::_( 'COM_MAILSTER_NO_MAILING_LIST_CHOSEN' );	
		$tooMuchRecipients = JText::_( 'COM_MAILSTER_TOO_MUCH_RECIPIENTS' );	
		$registrationIncative = JText::_( 'COM_MAILSTER_REGISTRATION_CURRENTLY_NOT_POSSIBLE' );	
		$registrationOnlyForRegisteredUsers = JText::_( 'COM_MAILSTER_REGISTRATION_NOT_POSSIBLE_FOR_GUESTS_PLEASE_LOG_IN' );
		$captchaCodeWrong = JText::_( 'COM_MAILSTER_CAPTCHA_CODE_WRONG_PLEASE_TRY_AGAIN' );	
		$availableInProEdition = JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' );
		
		$errors = 0;
		$myError = "";
		$replaceTxt = "";
		
		$allLists = $res['allLists'];
		$listIdSpecified = $res['listIdSpecified'];
		$listId = $res['listId'];    	
    	$allLists = $res['allLists'];
		$listNameSpecified = $res['listNameSpecified'];
		if($listNameSpecified){
			$listName = $res['listName'];
		}
    	$submitTxt = $res['submitTxt'];
    	$headerTxt = $res['headerTxt'];
    	$buttonTxt = $res['buttonTxt'];
    	$listLabel = $res['listLabel'];
    	$nameLabel = $res['nameLabel'];
    	$emailLabel = $res['emailLabel'];
    	$hideNameField = $res['hideNameField'];
    	$hideListName = $res['hideListName'];
		$captchaType = $res['captcha'];
		$smartHide = $res['smartHide'];
		$cssPrefix = $res['cssPrefix'];
		$designChoice = $res['designChoice'];
		
		
		$insertOk = false;

		if($subscrUtils->isUserLoggedIn()){
			$user =& JFactory::getUser();
			$name = trim($user->name);
			$email = trim($user->email);			
		}else{
			$email = "";
			$name = "";
		}			
		
		if (isset($_POST[MstConsts::SUBSCR_EMAIL_FIELD])) {
			$postId = JRequest::getString(MstConsts::SUBSCR_POST_IDENTIFIER);
			if($postId === 'subscribe'.$foundCounter){
				$postSent = true;
			
				$name =  JRequest::getString( MstConsts::SUBSCR_NAME_FIELD, '' );
				$email = JRequest::getString( MstConsts::SUBSCR_EMAIL_FIELD, '');
				$captchaRes = JRequest::getInt( MstConsts::SUBSCR_CAPTCHA, 0);
				$listId = JRequest::getInt( MstConsts::SUBSCR_ID_FIELD, 0);			
				
				if (($name === "") && ($hideNameField == false)) {
					$myError = $myError . '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $noName . '</span><br/>';
					$errors++;
				}
				if ($email === "") {
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $noEmail . '</span><br/>';
					$errors++;
				}
				else if (!preg_match("/^.+?@.+$/", $email)) {
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $invalidEmail . '</span><br/>';
					$errors++;
				}
				if (($listId === "") || ($listId <= 0)) {
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $noListChosen . '</span><br/>';
					$errors++;
				}
				
				$captchaValid = true;				
				if($captchaRes > 0){
					$mstCaptcha = $mstUtils->getCaptcha($captchaType);
					$captchaValid = $mstCaptcha->isValid();
				}				
				if($captchaValid == false){
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $captchaCodeWrong . '</span><br/>';
					$errors++;
				}
	
				if ($errors <= 0) {
					
					if(($name === "") && ($hideNameField == true)){ // name unknown and doesn't need to be supplied
						$name = $email; // copy email in name to have something in the DB as the name
					}
					
					$list = $listUtils->getMailingList($listId);
					if($list->allow_registration == '1'){
						if($list->public_registration == '1' || ($subscrUtils->isUserLoggedIn())){					
							$log->debug('All OK, we can insert in DB');
							$replaceTxt = '<span class="' . $cssPrefix . 'successMessage ' . $cssPrefix . 'message">'. $submitTxt . '</span>';						
							$success = $subscrUtils->subscribeUser($name, $email, $listId); // subscribing user...
							if($success == false){
								$mstRecipients = & MstFactory::getRecipients();
								$mstApp = & MstFactory::getApplication();
								$cr = $mstRecipients->getTotalRecipientsCount($listId);
								if($cr >= $mstApp->getRecC('plg_mailster_subscriber')){
									$errors = $errors + 1;
									$log->debug('Too many recipients!');
									$myError = '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $tooMuchRecipients . '</span><br/>';
								}
							}else{
								// ####### TRIGGER NEW EVENT #######
								$mstEvents = &MstFactory::getEvents();
								$mstEvents->userSubscribedOnWebsite($name, $email, $listId);
								// #################################
								$insertOk = true;
							}
						}else{
							$errors = $errors + 1;
							$log->debug('Cannot subscribe - registration is not allowed for not logged in users');
							$myError = '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $registrationOnlyForRegisteredUsers . '</span><br/>';
						}
					}else{
						$errors = $errors + 1;
						$log->debug('Cannot subscribe - registration is not allowed!');
						$myError = '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $registrationIncative . '</span><br/>';
					}
				}
			}
		}
		     	
		$url = $this->selfURL();
		$log->debug('url: ' . $url);
		
		if ($errors > 0) {
			$replaceTxt = $myError;
		}
	
		if(!$insertOk){
		
			$smartHideActive = $this->isSmartHideActive('subscribe', $res);
			
			if(!$smartHideActive){  // check whether whole form does not need to be shown
			
				// reset errors now
				$errors = 0;
				
				$replaceTxt = $replaceTxt . '<div class="' . $cssPrefix . 'container subscribeUnsubscribeContainer">'
										. '<form action="' . $url . '" method="post">'
										. '<table border="0" style="border-collapse:collapse">'
										. '<tr><th colspan="2" class="' . $cssPrefix . 'header">' . $headerTxt 
										. '<input type="hidden" name="'.MstConsts::SUBSCR_POST_IDENTIFIER.'" value="subscribe' . $foundCounter . '" /></th>' . "\n";
				if($allLists){
					$lists = $subscrUtils->getMailingLists2RegisterAt(!$subscrUtils->isUserLoggedIn());
					$lists = $this->filterMailingListsForSmartHide($lists, 'subscribe', $res);
					$dropDown = $this->getDropdownFromLists($lists);
					$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'listLabel">' . $listLabel . '</td><td class="' . $cssPrefix . 'listName">' . $dropDown;
			
				}else{
					$list = null;
					if($listIdSpecified){
						$list = $listUtils->getMailingList($listId);
					}else if($listNameSpecified){
						$list = $listUtils->getMailingListByName($listName);
					}
					if(!is_null($list)){
						$replaceTxt = $replaceTxt .  '<input type="hidden" name="' . MstConsts::SUBSCR_ID_FIELD . '" value="' . $list->id . '" />';
						if($hideListName == false){						
							$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'listLabel">' . $listLabel . '</td><td class="' . $cssPrefix . 'listName">'. $list->name.'</td></tr>';		
						}
					}else{
						$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'listLabel">' . $listLabel . '</td><td class="' . $cssPrefix . 'listName"><span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . JText::_( 'COM_MAILSTER_ERROR_UNKNOWN_LIST' ).'</span></td></tr>';
						$errors++;					
					}
				}
				
				if($allLists || (!is_null($list) && $list->allow_registration == '1')){
					if($allLists || ($list->public_registration == '1') || ($subscrUtils->isUserLoggedIn())){
						if($hideNameField == false){
							$replaceTxt = $replaceTxt . "\n" .
									 '<tr><td class="' . $cssPrefix . 'nameLabel">' . $nameLabel . '</td><td class="' . $cssPrefix . 'subscriberName"><input class="" type="text" name="' . MstConsts::SUBSCR_NAME_FIELD .  '" width="250"';
						
						$replaceTxt = $replaceTxt . ' value="'.$name.'" />';
						}
						
						$replaceTxt = $replaceTxt . '</td></tr>' . "\n" . '<tr><td class="' . $cssPrefix . 'emailLabel">' . $emailLabel . '</td><td class="' . $cssPrefix . 'subscriberEmail"><span style="display:none !important;">{emailcloak=off}</span><input class="" type="text" name="' . MstConsts::SUBSCR_EMAIL_FIELD .  '" width="250"';
						$replaceTxt = $replaceTxt . ' value="'.$email.'" /></td></tr>' . "\n";
						
						if($captchaType != false){	
							$captchaTxt = $this->getCaptchaHtml($captchaType, $cssPrefix);
							$replaceTxt = $replaceTxt . $captchaTxt ;
						}
						
						if($errors <= 0){
							$replaceTxt = $replaceTxt . '<tr><td>&nbsp;</td><td class="' . $cssPrefix . 'submitButton"><input class="" type="submit" value="' . $buttonTxt . '" style="width: 100%;" /></td></tr>';
						}
					}else{
						$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'error">&nbsp;</td><td class="' . $cssPrefix . 'error">'.$registrationOnlyForRegisteredUsers.'</td></tr>';
					}
				}else{
					$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'error">&nbsp;</td><td class="' . $cssPrefix . 'error">'.$registrationIncative.'</td></tr>';
				}
				$replaceTxt = $replaceTxt . '</table></form></div>' . "\n";
			}
		}	
	
		return $replaceTxt;
	}

	
	
	
	
	
	
	public function processUnsubscriber($text, $strPos, $foundCounter)
	{
		$log = & MstFactory::getLogger();
		
		$kvRes = $this->findKeyValues($text, $strPos);
		$keyValuesFound = $kvRes['keyValuesFound'];
		$startPos = $kvRes['startPos'];
		$endPos = $kvRes['endPos'];
		$keyValuesStr = $kvRes['keyValuesStr'];
		
		if($keyValuesFound == false){
			$log->debug('keys/values not found');			
			return $text;				
		}	
			
		$plugin = &JPluginHelper::getPlugin('content', 'mailstersubscriber');
		$pluginParams = new JParameter($plugin->params);
		
		
		$settings = array();
		$settings['allLists'] = true;
		$settings['listIdSpecified'] = false;
		$settings['listNameSpecified'] = false;
    	$settings['hideListName'] = false;
		$settings['captcha'] = ($pluginParams->def('captcha', '0') !== '0') ? $pluginParams->def('captcha', '0') : false;
		$settings['emailLabel'] = $pluginParams->def('unsubscriber_email', '');
		$settings['listLabel'] = $pluginParams->def('listLabel', '');
		$settings['buttonTxt'] =  $pluginParams->def('unsubscribe_button', '');
		$settings['submitTxt'] = $pluginParams->def('unsubscribe_thank_msg', '');
		$settings['errorTxt'] = $pluginParams->def('unsubscribe_error_msg', '');
		$settings['headerTxt'] = $pluginParams->def('unsubscribe_header', '');
		$settings['cssPrefix'] = $pluginParams->def('prefix_class', 'mailster_subscriber_');
		$settings['designChoice'] = $pluginParams->def('design_choice', '');
		$settings['smartHide'] = ($pluginParams->def('smart_hide', 0) == 1);
		$settings['listId'] = 0;
		
		if($settings['designChoice'] !== ''){
			$settings['cssPrefix'] = MstConsts::SUBSCR_CSS_DEFAULT . $settings['designChoice'] . '_';
		}
						
		$settings = $this->processKeyValueStr($keyValuesStr, $settings);
		
		$replaceTxt = $this->getUnsubscriberHtml($settings, $foundCounter);
		
		$text2Replace = MstConsts::SUBSCR_UNSUBSCRIBER_TMPL . MstConsts::SUBSCR_PARAM_START . $keyValuesStr . MstConsts::SUBSCR_PARAM_END;
		$text = JString::str_ireplace($text2Replace, $replaceTxt, $text);
		$text2Replace = MstConsts::SUBSCR_UNSUBSCRIBER_TMPL . ' ' . MstConsts::SUBSCR_PARAM_START . $keyValuesStr . MstConsts::SUBSCR_PARAM_END; // allow one char inbetween
		$text = JString::str_ireplace($text2Replace, $replaceTxt, $text);
		return $text;
	}
	
	
	public function getUnsubscriberHtml($res, $foundCounter)
	{
		$log = & MstFactory::getLogger();
		$mstUtils = & MstFactory::getUtils();
		$listUtils = & MstFactory::getMailingListUtils();
		$subscrUtils = & MstFactory::getSubscribeUtils();
		$recips = &MstFactory::getRecipients();
		$document =& JFactory::getDocument();		
		$document->addStyleSheet('plugins/content/mailstersubscriber/subscriber.css', 'text/css',"screen");
		
		$noEmail = JText::_( 'COM_MAILSTER_EMAIL_MISSING' );
		$noListChosen = JText::_( 'COM_MAILSTER_NO_MAILING_LIST_CHOSEN' );		
		$captchaCodeWrong = JText::_( 'COM_MAILSTER_CAPTCHA_CODE_WRONG_PLEASE_TRY_AGAIN' );	
		$notSubscribed = JText::_( 'COM_MAILSTER_EMAIL_NOT_SUBSCRIBED' );
		
		$errors = 0;
		$myError = "";
		$replaceTxt = "";
		
		$allLists = $res['allLists'];
		$listIdSpecified = $res['listIdSpecified'];
		$listId = $res['listId'];    	
    	$allLists = $res['allLists'];
		$listNameSpecified = $res['listNameSpecified'];
		if($listNameSpecified){
			$listName = $res['listName'];
		}
    	$submitTxt = $res['submitTxt'];
    	$headerTxt = $res['headerTxt'];
    	$buttonTxt = $res['buttonTxt'];
    	$listLabel = $res['listLabel'];
    	$emailLabel = $res['emailLabel'];
    	$hideListName = $res['hideListName'];
		$captchaType = $res['captcha'];
    	$smartHide = $res['smartHide'];
		$cssPrefix = $res['cssPrefix'];
		$designChoice = $res['designChoice'];
    	
		$postSent = false;	
		
		if($subscrUtils->isUserLoggedIn()){
			$user =& JFactory::getUser();
			$name = trim($user->name);
			$email = trim($user->email);			
		}else{
			$email = "";
			$name = "";
		}				

		if (isset($_POST[MstConsts::SUBSCR_EMAIL_FIELD])) {
			$postId = JRequest::getString(MstConsts::SUBSCR_POST_IDENTIFIER);
			if($postId === 'unsubscribe'.$foundCounter){
				$postSent = true;
				$email = JRequest::getString( MstConsts::SUBSCR_EMAIL_FIELD, '');
				$captchaRes = JRequest::getInt( MstConsts::SUBSCR_CAPTCHA, 0);
				$listId = JRequest::getInt( MstConsts::SUBSCR_ID_FIELD, 0);
				
				if ($email === "") {
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $noEmail . '</span><br/>';
					$errors++;
				}
				if (($listId === "") || ($listId <= 0)) {
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $noListChosen . '</span><br/>';
					$errors++;
				}
				
				$captchaValid = true;				
				if($captchaRes > 0){
					$mstCaptcha = $mstUtils->getCaptcha($captchaType);
					$captchaValid = $mstCaptcha->isValid();
				}				
				if($captchaValid == false){
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $captchaCodeWrong . '</span><br/>';
					$errors++;
				}
				
				$isRecipient = $recips->isRecipient($listId, $email);
				$log->debug('Check whether person with email ' . $email . ' is recipient of list ' . $listId . ', result: ' . ($isRecipient ? 'Is recipient' : 'NOT a recipient'));
				
				if($isRecipient == false){
					$myError = $myError. '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $notSubscribed . '</span><br/>';
					$errors++;
				}
	
				if ($errors <= 0) {
					$log->debug('All OK, we can delete from DB');
					$replaceTxt = '<span class="' . $cssPrefix . 'successMessage ' . $cssPrefix . 'message">'. $submitTxt . '</span>';							
					$success = $subscrUtils->unsubscribeUser($email, $listId); // unsubscribing user...
					// ####### TRIGGER NEW EVENT #######
					$mstEvents = &MstFactory::getEvents();
					$mstEvents->userUnsubscribedOnWebsite($email, $listId);
					// #################################
				}else{
					$log->debug('ERRORS during unsubscribe process, will show this in HTML: ' . $myError);				
					$replaceTxt = $myError;			
				}
			}
		}
				     	
		$url = $this->selfURL();
		
		if ($errors > 0) {
			$replaceTxt = $myError;
		}
	
		if(!$postSent){
			
			$smartHideActive = $this->isSmartHideActive('unsubscribe', $res);
			
			if(!$smartHideActive){ // check whether whole form does not need to be shown
				
				$replaceTxt = $replaceTxt . '<div class="' . $cssPrefix . 'container subscribeUnsubscribeContainer"><form action="' . $url . '" method="post"><input type="hidden" name="'.MstConsts::SUBSCR_POST_IDENTIFIER.'" value="unsubscribe'. $foundCounter . '" /><table border="0" style="border-collapse:collapse"><tr><th colspan="2" class="' . $cssPrefix . 'header">' . $headerTxt . '</th>' . "\n";
				if($allLists){
					$lists = $subscrUtils->getMailingLists2RegisterAt(!$subscrUtils->isUserLoggedIn());
					$lists = $this->filterMailingListsForSmartHide($lists, 'unsubscribe', $res);
					$dropDown = $this->getDropdownFromLists($lists);
					$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'listLabel">' . $listLabel . '</td><td class="' . $cssPrefix . 'listName">' . $dropDown . '</td></tr>' . "\n";
				
				}else{
					$list = null;
					if($listIdSpecified){
						$list = $listUtils->getMailingList($listId);
					}else if($listNameSpecified){
						$list = $listUtils->getMailingListByName($listName);
					}
					if($list){					
						$replaceTxt = $replaceTxt . '<input type="hidden" name="' . MstConsts::SUBSCR_ID_FIELD . '" value="' . $list->id . '" />';
						if($hideListName == false){
							$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'listLabel">' . $listLabel . '</td><td class="' . $cssPrefix . 'listName">' . $list->name . '</td></tr>' . "\n";
						}
					}else{
						$replaceTxt = $replaceTxt . '<tr><td class="' . $cssPrefix . 'listLabel">' . $listLabel . '</td><td class="' . $cssPrefix . 'listName"><span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . JText::_( 'COM_MAILSTER_ERROR_UNKNOWN_LIST' ) . '</span></td></tr>' . "\n";
						$errors++;					
					}		
				}		
				
				$replaceTxt = $replaceTxt  . '<tr><td class="' . $cssPrefix . 'emailLabel">' . $emailLabel . '</td><td class="' . $cssPrefix . 'subscriberEmail"><span style="display:none !important;">{emailcloak=off}</span><input class="" type="text" name="' . MstConsts::SUBSCR_EMAIL_FIELD .  '" width="250"';
				$replaceTxt = $replaceTxt . ' value="'.$email.'" /></td></tr>' . "\n";
				
				if($captchaType != false){	
						$captchaTxt = $this->getCaptchaHtml($captchaType, $cssPrefix);
						$replaceTxt = $replaceTxt . $captchaTxt ;
				}
				
				if($errors <= 0){
		        	$replaceTxt = $replaceTxt . '<tr><td>&nbsp;</td><td class="' . $cssPrefix . 'submitButton"><input class="" type="submit" value="' . $buttonTxt . '" style="width: 100%;"/></td></tr>';
				}
				$replaceTxt = $replaceTxt . '</table></form></div>' . "\n";
			}
		
		}
		return $replaceTxt;
	}
	
	
	protected function processKeyValueStr($keyValuesStr, $res){    	
		$keyValueArray = $this->getKeyValueArray($keyValuesStr);
		$keyArray = array_keys($keyValueArray);
		
		for($i=0; $i < count($keyArray); $i++)
		{
			$key = $keyArray[$i];
			$val = $keyValueArray[$key];
			$key = strtolower(trim($key));
			$val = trim($val);
			switch ($key) {
    			case strtolower(MstConsts::SUBSCR_ID_KEY):
    				$res['allLists'] = false;
					$res['listIdSpecified'] = true;
					$res['listId'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_LIST_KEY):
    				$res['allLists'] = false;
					$res['listNameSpecified'] = true;
					$res['listName'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_SUBMIT_TEXT):
    				$res['submitTxt'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_HEADER_TEXT):
    				$res['headerTxt'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_BUTTON_TEXT):
    				$res['buttonTxt'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_LIST_LABEL):
    				$res['listLabel'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_NAME_LABEL):
    				$res['nameLabel'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_EMAIL_LABEL):
    				$res['emailLabel'] = $val;
    				break;
    			case strtolower(MstConsts::SUBSCR_CSS_PREFIX):
    				$res['cssPrefix'] = $val;
    				break;  
    			case strtolower(MstConsts::SUBSCR_DESIGN_CHOICE):
    				$res['cssPrefix'] = MstConsts::SUBSCR_CSS_DEFAULT . strtolower($val) . '_';
    				$res['designChoice'] = strtolower($val);
    				break;  
    			case strtolower(MstConsts::SUBSCR_NO_LIST_NAME):
    				$choice = strtolower($val);
    				if($choice === 'yes'){
    					$res['hideListName'] = true;
    				}
    				break;  
    			case strtolower(MstConsts::SUBSCR_NO_NAME_FIELD):
    				$choice = strtolower($val);
    				if($choice === 'yes'){
    					$res['hideNameField'] = true;
    				}
    				break;    
    			case strtolower(MstConsts::SUBSCR_CAPTCHA):
					$res['captcha'] = strtolower($val);
					break;   
    			case strtolower(MstConsts::SUBSCR_SMART_HIDE):
    				$choice = strtolower($val);
					if($choice === 'yes'){
    					$res['smartHide'] = true;
    				}
					break;
			}
		}
		return $res;
	}
	
	
	protected function findKeyValues($text, $strPos){
		$result = array();
		$result['keyValuesFound'] = false;
		$result['startPos'] = 0;
		$result['endPos'] = 0;
		$result['keyValuesStr'] = '';
		$startPos = JString::strpos($text, MstConsts::SUBSCR_PARAM_START, $strPos);
		if( $startPos == $strPos || $startPos == ($strPos+1) )
		{
			$endPos = JString::strpos($text, MstConsts::SUBSCR_PARAM_END, $startPos);
			if ($endPos !== false)
			{
				$endPos = $endPos - 1;
				$result['keyValuesFound'] = true;
				$result['startPos'] = $startPos;
				$result['endPos'] = $endPos;
				$result['keyValuesStr'] = JString::substr($text, $startPos+1, $endPos-$startPos);	
			}
		}
		return $result;
	}
	
	protected function filterMailingListsForSmartHide($lists, $formType, $settings){
		$log			= & MstFactory::getLogger();
		$recipUtils 	= & MstFactory::getRecipients();
		$subscrUtils 	= & MstFactory::getSubscribeUtils();
		
		$filteredLists = array();
    	
		$formType = strtolower(trim($formType));
		if($formType === 'subscribe'){
			$removeFromListWhenMember = true;
			$removeFromListWhenNotMember = false;
		}elseif($formType === 'unsubscribe'){
			$removeFromListWhenMember = false;
			$removeFromListWhenNotMember = true;
		}
		
		if($settings['smartHide'] == true){	    	    		
			if(!is_null($lists) && !empty($lists)){	
				if($subscrUtils->isUserLoggedIn()){
					$log->debug('Smart hide active, user logged in, we can filter mailing list choice');
					$user =& JFactory::getUser();
					$email = $user->email;

					foreach($lists AS $list){
						$isMember = $recipUtils->isRecipient($list->id, $email);
						if($isMember && !$removeFromListWhenMember){
							$filteredLists[] = $list;
						}elseif(!$isMember && !$removeFromListWhenNotMember){
							$filteredLists[] = $list;
						}else{
							$log->debug('List is filtered from '.$formType . ' form: '.$list->name . ' (ID: '.$list->id.')');
						}
					}
					
					return $filteredLists; // filtered lists
				}
			}
	    	
		}
		$log->debug('We can NOT filter mailing list choice');
		return $lists; // unfiltered lists
	}
	
	protected function isSmartHideActive($formType, $settings){
		$log			= & MstFactory::getLogger();
		$listUtils 		= & MstFactory::getMailingListUtils();
		$recipUtils 	= & MstFactory::getRecipients();
		$subscrUtils 	= & MstFactory::getSubscribeUtils();
    	
		$formType = strtolower(trim($formType));
		if($formType === 'subscribe'){
			$memberHidesForm = true;
			$nonMemberHidesForm = false;
		}elseif($formType === 'unsubscribe'){
			$memberHidesForm = false;
			$nonMemberHidesForm = true;
		}
		
		if($settings['smartHide'] == true){
	    	if($settings['allLists'] == false){
	    		$mList = null;
				if($settings['listIdSpecified']){
					$mList = $listUtils->getMailingList($settings['listId']);
				}else if($settings['listNameSpecified']){
					$mList = $listUtils->getMailingListByName($settings['listName']);
				}
				if(!is_null($mList) && $mList){	
					if($subscrUtils->isUserLoggedIn()){
						$user =& JFactory::getUser();
						$email = $user->email;	
						$isMember = $recipUtils->isRecipient($mList->id, $email);
												
						if($memberHidesForm && $isMember){
							return true; // smart hide on
						}
						if($nonMemberHidesForm && !$isMember){
							return true; // smart hide on
						}					
					}
				}
	    	}
		}
		return false; // can not do smart hide
	}
	
	protected function getKeyValueArray($keyValuesStr)
	{
		$keyValueArray = array();
		$keyValueRawArray = explode(MstConsts::SUBSCR_KEY_VALUE_PAIR_DELIMITER, $keyValuesStr); 
		for($i=0; $i < count($keyValueRawArray); $i++)
		{
			$keyValue = $keyValueRawArray[$i];
			$keyValue = trim($keyValue);
			$pos = JString::strpos($keyValue, MstConsts::SUBSCR_KEY_VALUE_DELIMITER);
			if($pos !== false)
			{
				$key = JString::substr($keyValue, 0, $pos);
				$value = JString::substr($keyValue, $pos+1);
				$keyValueArray[$key] = $value;
			}
		}
		return $keyValueArray;
	}
	
	protected function getModifiedOutput(){
		return '<tr><td style="background-color:red;font-weight:bold;text-align:center;height:30px;" colspan="2">' . JText::_( 'COM_MAILSTER_PRODUCT_MODIFIED' ) . '</td></tr>';
	}
	
	protected function getDropdownFromLists($lists)
	{		
		$listCount = count($lists);
		if($listCount > 0){
			$html = '<select name="' . MstConsts::SUBSCR_ID_FIELD . '">';
			for($i=0; $i < $listCount; $i++)
			{
				$html = $html . '<option value="' . $lists[$i]->id . '">' . $lists[$i]->name . '</option>';	
			}
			$html = $html . '</select>';
		}else{
			$html = '<select name="' . MstConsts::SUBSCR_ID_FIELD . '">';
			$html = $html . '<option value="0">' . JText::_( 'COM_MAILSTER_NO_MAILING_LIST_PLACEHOLDER' )  . '</option>';
			$html = $html . '</select>';
		}
		return $html;
	}
	
	protected function getCaptchaHtml($captchaType, $cssPrefix){
		$mstApp = &MstFactory::getApplication();	
		$mstUtils = &MstFactory::getUtils();
		
		$captchaType = strtolower(trim($captchaType));
		
		if(strlen($captchaType) > 0){
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
			$captchaTxt = '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . JText::_( 'COM_MAILSTER_CAPTCHA' ) . ': ' . JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ) . '</span>';
								
			if($pHashOk && $plgHashOk){	
				$mstCaptcha = $mstUtils->getCaptcha($captchaType, $cssPrefix);
				$captchaTxt = $mstCaptcha->getHtml($cssPrefix);
				
				$captchaTxt = $captchaTxt . '<input type="hidden" name="' . MstConsts::SUBSCR_CAPTCHA . '" value="1" />';
				if(!$mstCaptcha->htmlOk()){
					$captchaTxt = '<span style="color: #f00;" class="' . $cssPrefix . 'errorMessage ' . $cssPrefix . 'message">' . $captchaTxt . '</span>';
				}
				if($mstCaptcha->twoCols){
					$captchaTxt = '<tr><td class="' . $cssPrefix . 'captchaQuestion">' . $mstCaptcha->firstCol . '</td>'
								. '<td class="' . $cssPrefix . 'captcha">'
								. $captchaTxt
								. '</td></tr>' . "\n";
				}else{
					$captchaTxt = '<tr><td class="' . $cssPrefix . 'captcha" colspan="2">' 
								. $captchaTxt
								. '</td></tr>' . "\n";
				}
			}
			
			return $captchaTxt;
		}else{
			return '';
		}
	}
		
	
	protected function selfURL() {
		return $this->getActionLink();
	}
	
	protected function getActionLink(){
		$jUri = & JURI::getInstance();
		$href = $jUri->toString(); 
		return $href;
	}	
	
}

?>
