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

class MstHashUtils
{	
	
	public static function checkUnsubscribeKey($mailId, $salt, $hash){		
		if($mailId > 0){
			$query = 'SELECT * FROM #__mailster_mails WHERE id=\'' . $mailId . '\'';
			$db = & JFactory::getDBO();
			$db->setQuery( $query );
			$mail = $db->loadObject();
			
			if($mail){
				$hKey = $mail->hashkey;
				$saltedKey = $hKey . $salt;
				$originalKey = sha1($saltedKey);
							
				return ( $originalKey === $hash );
			}
		}
		return false;
	}
	
	public static function getUnsubscribeKey($email, $hashkey){
		$plainKey = $email . $hashkey;
		$longKey = sha1($plainKey);
		return substr($longKey, 0, 30); // part of SHA1 hash for unsubscribe link verification
	}	
	
	public static function getMailHashkey(){
		return MstHashUtils::getFixedLengthRandomString(45, true); // for DB
	}
	
	public static function getFixedLengthRandomNumber($strLength=3) {
		$chars = '0123456789';
		return MstHashUtils::getRndString($chars, $strLength);
	}
	
	public static function getFixedLengthRandomString($strLength=8, $uppercase=false) {		
		if($uppercase){
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		}else{
			$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		}
		return MstHashUtils::getRndString($chars, $strLength);
	}
	
	public static function getRndString($chars, $strLength) {
		$charCount = strlen($chars);
		$rndString = '';	
		for ($i = 0; $i < $strLength; $i++) {
			$rndString = $rndString . $chars{mt_rand(0, $charCount - 1)};
		}
		return $rndString;
	}
	
}
