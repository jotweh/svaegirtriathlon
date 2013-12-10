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

class MstEnvironment
{
	public static function imapExtensionInstalled(){
		return (extension_loaded('imap') && function_exists('imap_open'));
	}
	
	public static function domExtensionInstalled(){
		return extension_loaded('dom');
	}
	
	public static function openSSLExtensionInstalled(){
		return extension_loaded('openssl');
	}
	
	public static function getImapVersion(){
		$version = '';
		if(MstEnvironment::imapExtensionInstalled()){
			$phpInfo = array();
			$phpInfo = MstEnvironment::getPHPInfoArray();
			$version = print_r($phpInfo['imap'], true);
		}
		return $version;
	}
	
	public static function getOpenSSLVersion(){
		$version = '';
		if(MstEnvironment::openSSLExtensionInstalled()){
			$phpInfo = array();
			$phpInfo = MstEnvironment::getPHPInfoArray();
			$version = print_r($phpInfo['openssl'], true);
		}
		return $version;
		
	}
	
	public static function getDomainName(){
		$domain = '';
		if($_SERVER['HTTP_HOST']){
			$domain = $_SERVER['HTTP_HOST'];
		}elseif($_SERVER['HTTP_REFERER']){
			$domain = $_SERVER['HTTP_REFERER'];
		}
		return $domain;
	}
	
	public static function getUrl(){
		return $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $_SERVER['HTTP_HOST'];
	}
	
	public static function getPHPInfoArray(){
		ob_start();
		phpinfo(-1);		 
		$pi = preg_replace(
		array('#^.*<body>(.*)</body>.*$#ms', '#<h2>PHP License</h2>.*$#ms',
			'#<h1>Configuration</h1>#',  "#\r?\n#", "#</(h1|h2|h3|tr)>#", '# +<#',
			"#[ \t]+#", '#&nbsp;#', '#  +#', '# class=".*?"#', '%&#039;%',
			'#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a>'
			.'<h1>PHP Version (.*?)</h1>(?:\n+?)</td></tr>#',
			'#<h1><a href="(?:.*?)\?=(.*?)">PHP Credits</a></h1>#',
			'#<tr>(?:.*?)" src="(?:.*?)=(.*?)"(?:.*?)Zend Engine (.*?),(?:.*?)</tr>#',
			"# +#", '#<tr>#', '#</tr>#'),
			array('$1', '', '', '', '</$1>' . "\n", '<', ' ', ' ', ' ', '', ' ',
			'<h2>PHP Configuration</h2>'."\n".'<tr><td>PHP Version</td><td>$2</td></tr>'.
			"\n".'<tr><td>PHP Egg</td><td>$1</td></tr>',
			'<tr><td>PHP Credits Egg</td><td>$1</td></tr>',
			'<tr><td>Zend Engine</td><td>$2</td></tr>' . "\n" .
			'<tr><td>Zend Egg</td><td>$1</td></tr>', ' ', '%S%', '%E%'),
			ob_get_clean());
		$sections = explode('<h2>', strip_tags($pi, '<h2><th><td>'));
		unset($sections[0]);
		$pi = array();
		foreach($sections as $section){
			$n = substr($section, 0, strpos($section, '</h2>'));
			preg_match_all(
			'#%S%(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?%E%#',
			$section, $askapache, PREG_SET_ORDER);
			foreach($askapache as $m)
				$pi[$n][$m[1]]=(!isset($m[3])||$m[2]==$m[3])?$m[2]:array_slice($m,2);
		}
		return $pi;
	}

	public static function isDirWritable($dir2check, $logResults=false, $level='WARNING'){	
		$log = & MstFactory::getLogger();
		$lastChar = substr($dir2check, -1);
		if($lastChar !== '/' && $lastChar !== '\\'){
			$dir2check .= DS;
		}
	
		if(!file_exists($dir2check)){
			if($logResults){
				$log->log('Dir ' . $dir2check . ' NOT existing!', $level);
			}
            return false;  // Directory not existing
        }
		if(!is_writable($dir2check)){
			if($logResults){
				$log->log('Dir ' . $dir2check . ' NOT writable!', $level);
			}
            return false;  // Directory not writable
        }
	
		if($logResults){
			$log->log('Dir ' . $dir2check . ' writable');
		}    
        return true; // Directory writable
	}

	public static function isFileWritable($targetDir, $completeFilepath, $logResults=false, $level='WARNING'){	
		$log = & MstFactory::getLogger();		
		$ok = MstEnvironment::isDirWritable($targetDir,$logResults,$level);				
		
		if($ok){
			if(file_exists($completeFilepath) && !is_writable($completeFilepath)){
				if($logResults){
					$log->log('File ' . $completeFilepath . ' existing, but not writable', $level);
				}  
            	return false;  // File exists but is not writable
            }			
			return true; // File writable
		}else{
			return false; // Dir not writable, therefore file not writable
		}
	}
}
