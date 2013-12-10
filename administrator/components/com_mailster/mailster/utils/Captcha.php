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

class MstCaptcha
{
	
	function MstCaptcha($cType=MstConsts::CAPTCHA_ID_RECAPTCHA)
    {
		$this->error = false;
		$this->twoCols = false;
		$this->firstCol = '';
		$this->cType = $cType;
    }
    
    function htmlOk(){
    	return !$this->error; 
    }
    
    function getHtml($cError=null, $cssPrefix=''){
    	$mstUtils = &MstFactory::getUtils();
    	$mstConfig = &MstFactory::getConfig();
    	
        switch($this->cType){
        	case MstConsts::CAPTCHA_ID_RECAPTCHA:
        		$ok = MstFactory::loadLibrary(MstConsts::CAPTCHA_ID_RECAPTCHA);
        		
        		$doc =& JFactory::getDocument();
        		$recaptchaOptions = $mstConfig->getRecaptchaParamString();
        		$recaptchaTheming = " var RecaptchaOptions = { " . $recaptchaOptions . " };";
				$doc->addScriptDeclaration( $recaptchaTheming );
				
        		if($ok){
	        		$keys = $mstConfig->getRecaptchaKeys();
	        		$pubK = $keys['public'];
	        		$priK = $keys['private'];
	        		if((!is_null($pubK)) && (strlen($pubK)>5) 
	        			&& (!is_null($priK)) && (strlen($priK)>5)){
						$this->html = recaptcha_get_html($pubK, $cError);
        			}else{
        				$this->html = JText::_( 'COM_MAILSTER_PLEASE_PROVIDE_RECAPTCHA_API_KEYS_IN_CONFIGURATION' );
        				$this->error = true; 
        			}
        		}else{
        			$this->error = true; 
        		}
        	break;
        	case MstConsts::CAPTCHA_ID_MATH:
        		$this->twoCols = true;
        		$ok = MstFactory::loadLibrary(MstConsts::CAPTCHA_ID_MATH);
        		if($ok){
        			$this->html = MstMathCaptcha::getHTML($cssPrefix);
        			$this->firstCol = MstMathCaptcha::getQuestion();
        		}else{
        			$this->error = true; 
        		}
        		break;
        	default:
        		// don't know that one...
        		$this->error = true;
        		$this->html = JText::_( 'COM_MAILSTER_UNKNOWN_CAPTCHA' ) . ': ' . $this->cType;
        	break;
        }
    	return $this->html;	
    }
    
    function isValid(){
    	$mstUtils = &MstFactory::getUtils();
    	$mstConfig = &MstFactory::getConfig();
    	
    	switch($this->cType){
        	case MstConsts::CAPTCHA_ID_RECAPTCHA:
        		$ok = MstFactory::loadLibrary(MstConsts::CAPTCHA_ID_RECAPTCHA);
        		if($ok){
	        		$keys = $mstConfig->getRecaptchaKeys();
	        		$pubK = $keys['public'];
	        		$priK = $keys['private'];
	        		if((!is_null($pubK)) && (strlen($pubK)>5) 
	        			&& (!is_null($priK)) && (strlen($priK)>5)){
	        			$challenge = $_POST["recaptcha_challenge_field"]; 
	        			$response = $_POST["recaptcha_response_field"];
	        			$serverAddr = $_SERVER["REMOTE_ADDR"];
	        			$resp = recaptcha_check_answer($priK,$serverAddr,$challenge,$response);
				        if ($resp->is_valid) {
				        	return true;
				        } else {
				        	$this->respError = $resp->error;
				        }
        			}else{
        				$this->html = JText::_( 'COM_MAILSTER_PLEASE_PROVIDE_RECAPTCHA_API_KEYS_IN_CONFIGURATION' );
        				$this->error = true; 
        			}
        		}else{
        			$this->error = true; 
        		}
        	break;
        	case MstConsts::CAPTCHA_ID_MATH:
        		$ok = MstFactory::loadLibrary(MstConsts::CAPTCHA_ID_MATH);
        		if($ok){
        			if (MstMathCaptcha::answerCorrect()){
				        return true;
        			}
        		}else{
        			$this->error = true; 
        		}
        	break;
        	default:
        		// don't know that one...
        		$this->error = true;
        	break;
        }
        return false;
    }
    
}
