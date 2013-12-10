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
	
		
	function com_install() {
	
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			// Joomla! 1.6 / 1.7 / ...
			return;
		} else {
			// Joomla! 1.5 
			if(!class_exists('com_mailsterInstallerScript')){
				$fileLong = dirname(__FILE__).DS.'script.mailster.php';
				$fileMedium = '.'.DS.'script.mailster.php';
				$fileShort = 'script.mailster.php';
				if(file_exists($fileLong)){
					require_once($fileLong);
				}else{
					if(file_exists($fileMedium)){
						require_once($fileMedium);
					}else{
						if(file_exists($fileShort)){
							require_once($fileShort);
						}
					}
				}
			}			
			$installerScript = new com_mailsterInstallerScript();
			return $installerScript->installAndUpdateScript();	
		}
		
	} // end of com_install
	

?>
