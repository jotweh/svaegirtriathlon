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

/**
 * Executes additional uninstallation processes
 * @since 0.1
 */
function com_uninstall() {
		jimport( 'joomla.filesystem.folder' );	
		jimport('joomla.installer.helper');
				
		$lang =& JFactory::getLanguage();
		$lang->load('com_mailster', JPATH_ADMINISTRATOR);
		
		$installer = new JInstaller();
		$installer->setOverwrite(true);
		$plugins = array( 	'mailster'=>array('name'=>'Mailster Mail Plugin', 'element'=>'mailster', 'folder'=>'system', 'type'=>'plugin'),
		               		'mailstersubscriber'=>array('name'=>'Mailster Subscriber Plugin', 'element'=>'mailstersubscriber', 'folder'=>'content', 'type'=>'plugin'),
		               		'mailsterprofile'=>array('name'=>'Mailster Profile Plugin', 'element'=>'mailsterprofile', 'folder'=>'system', 'type'=>'plugin'),
							'subscribermodule'=>array('name'=>'Mailster Subscriber Module', 'module'=>'mod_mailster_subscriber', 'type'=>'module')
		                 );
		$msgcolor = "#B0FFB0";
	 	$msgtext  = "Mailster Component " . JText::_( 'COM_MAILSTER_UNINSTALLED_SUCCESSFUL' );
	 	$imgSrc = 'components/com_mailster/assets/images/16-tick.png';
		?>
		<center>
			<table bgcolor="<?php echo $msgcolor; ?>" width ="100%">
				<tr style="height:30px">
			    	<td width="30px"><img src="<?php echo $imgSrc; ?>" height="20px" width="20px"></td>
			    	<td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
				</tr>
			</table>
		<?php 
		foreach( $plugins as $plugin => $pluginParams ):			
			$pluginName = $pluginParams['name'];
			$type = $pluginParams['type'];
			$db = &JFactory::getDBO();
			
			$success = false;
			
			if($type === 'plugin'){
				$pluginElement = $pluginParams['element'];
				$pluginFolder = $pluginParams['folder'];
				if(version_compare(JVERSION,'1.6.0','ge')) {
					// Joomla! 1.6 / 1.7 / ...
					$query = 'SELECT `extension_id` AS `plg_id`, `client_id` AS `cli_id`' 
							. ' FROM `#__extensions`'
							. ' WHERE `type` = '.$db->Quote('plugin')
							. ' AND folder = '.$db->Quote($pluginFolder)
							. ' AND element = '.$db->Quote($pluginElement);
				} else {
					// Joomla! 1.5 
					$query = 'SELECT `id` AS `plg_id`, `client_id` AS `cli_id`'
							. ' FROM `#__plugins`'
							. ' WHERE folder = '.$db->Quote($pluginFolder)
							. ' AND element = '.$db->Quote($pluginElement);
				}	
				
				$db->setQuery($query);
				$id = 0;
				$client_id = 0;
				if( $db->Query()){					
					$pluginObj = $db->loadObject();
					$id = $pluginObj->plg_id;
					$client_id = $pluginObj->cli_id;
					$success = $installer->uninstall( $type, $id, $client_id );
				}
			}elseif($type === 'module'){
				$moduleName = $pluginParams['module'];
				if(version_compare(JVERSION,'1.6.0','ge')) {
					// Joomla! 1.6 / 1.7 / ...
					$query = 'SELECT `extension_id` AS `mod_id`, `client_id` AS `cli_id`' 
							. ' FROM `#__extensions`'
							. ' WHERE `type` = '.$db->Quote('module')
							. ' AND element = '.$db->Quote($moduleName);
				} else {
					// Joomla! 1.5 
					$query = 'SELECT `id` AS `mod_id`, `client_id` AS `cli_id`' 
							. ' FROM `#__modules`'
							. ' WHERE module = '.$db->Quote($moduleName);
				}	
				
				$db->setQuery($query);
				if( $db->Query()){					
					$modules = $db->loadObjectList();
					for($i=0;$i<count($modules);$i++){
						$module = &$modules[$i];				
						$installer = new JInstaller();
						$success = $installer->uninstall('module', $module->mod_id, $module->cli_id);
					}
				}
			}			
			if( $success ){
				$msgcolor = "#B0FFB0";
			 	$msgtext  = $pluginName . ' ' . JText::_( 'COM_MAILSTER_UNINSTALLED_SUCCESSFUL' );
			 	$imgSrc = 'components/com_mailster/assets/images/16-tick.png';
			}else{
				$msgcolor = "#FFB0B0";
			 	$msgtext  = JText::_( 'COM_MAILSTER_UNINSTALL_ERROR' ) . ' ' . $pluginName;
			 	$imgSrc = 'components/com_mailster/assets/images/16-publish_x.png';
			}
		 ?>
			<table bgcolor="<?php echo $msgcolor; ?>" width ="100%">
				<tr style="height:30px">
			    	<td width="30px"><img src="<?php echo $imgSrc; ?>" height="20px" width="20px"></td>
			    	<td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
				</tr>
			</table>
			<?php 
		endforeach;  	
}
?>
	</center>
