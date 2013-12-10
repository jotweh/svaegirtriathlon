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

class MstConverterUtils{

	public static function object2Array($obj2conv){
		$array=array();
		if(!is_scalar($obj2conv)) {
			if($obj2conv){
				foreach($obj2conv as $id => $object) {
					if(is_scalar($object)) {
						$array[$id]=$object;
					} else {
						$array[$id]=MstConverterUtils::object2Array($object);
					}
				}
				return $array;
			}
			return $obj2conv;
		} else {
			return $object;
		}
	}

	public static function imapUtf8($str){
		$testStr = 'h';
		$res = imap_utf8($testStr); // TEST for uppercase bug...
		if($res == 'H'){  // If it is converted to uppercase we got a PHP version with the bug
			$ret = '';
		    $headerParts = imap_mime_header_decode($str);
		    for($i=0;$i<count($headerParts);$i++){
		    	if ($headerParts[$i]->charset == 'default')	{
		    		$headerParts[$i]->charset = 'iso-8859-1';
		    	}
		    	$ret .= iconv($headerParts[$i]->charset, 'UTF-8', $headerParts[$i]->text);
	    	}
		}else{ // Test went ok, we can use the normal method
			$ret = imap_utf8($str);
		}
	    return $ret;
	}
	
	public static function getStringAsNativeUtf8($str){
		$conv = '';
		$subParts = preg_split('/[\r\n]+/',$str);
		for($i=0;$i<count($subParts);$i++){
			if(function_exists('mb_internal_encoding') && function_exists('mb_decode_mimeheader')){
				mb_internal_encoding("UTF-8");
				$convPart =  mb_decode_mimeheader(trim($subParts[$i])); 
			}else{
				$convPart = self::imapUtf8(trim($subParts[$i]));
			}
			$conv .= $convPart;
		}
		return $conv;
	} 
	
	public static function decode_qprint($str) {
	    $str = preg_replace("/\=([A-F][A-F0-9])/","%$1",$str);
	    $str = urldecode($str);
	    $str = utf8_encode($str);
	    return $str;
	}
	
	public static function encodeText($text, $encoding, $charset){
		$log = & MstFactory::getLogger();
		$log->info('encodeText: Mail encoding: ' . $encoding . ', Charset: ' . $charset);	
		/*
		 	Encoding 
		  	0	7BIT
			1	8BIT
			2	BINARY
			3	BASE64
			4	QUOTED-PRINTABLE
			5	OTHER											 
		 */	
		if ($encoding == 1) {	
			$charset = strtoupper($charset);
			if($charset == ""){
				$log->info('encodeText: Encoding with imapUtf8 (1)');	
				$text = MstConverterUtils::imapUtf8($text);
			}else{				
				if($charset !== "UTF-8"){
					if($charset === "ISO-8859-2"){
						$log->info('encodeText: Trying to convert from ISO-8859-2 to UTF-8...');	
						$text = iconv("ISO-8859-2", "UTF-8//TRANSLIT", $text);
					}else{
						$log->info('encodeText: Encoding with decode_qprint');	
						$text = MstConverterUtils::decode_qprint($text);
					}
				}else{
					$log->info('encodeText: Is UTF-8, don\'t encode now');	
				}
			}						
		}elseif ($encoding == 3) {
			$log->info('encodeText: Encoding with base64_decode');	
			$text = base64_decode($text);
			$log->debug('Result: ' . $text);	
			$text = MstConverterUtils::encodeText($text, 1, $charset);
		}elseif ($encoding == 4) {
			$log->info('encodeText: Encoding with quoted_printable_decode');	
			$text = quoted_printable_decode($text);
			$log->debug('Result: ' . $text);	
			$text = MstConverterUtils::encodeText($text, 1, $charset);
		}	
		return $text;
	}
	
}
