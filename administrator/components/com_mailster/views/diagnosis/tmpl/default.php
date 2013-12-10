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
	
	
	$mstApp 	= & MstFactory::getApplication();	
	$mstUtils 	= & MstFactory::getUtils();
	$env 		= & MstFactory::getEnvironment();
	$dbUtils 	= & MstFactory::getDBUtils();
	$db = &JFactory::getDBO();	
	$version = new JVersion();	
					
	$pHashOk 	= $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
	$plgHashOk 	= $mstApp->checkPluginProductHashes();
	
	$appName = JText::_( 'COM_MAILSTER_MAILSTER' );
	$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');	
	if(!$isFree){
		$appName = JText::_( 'COM_MAILSTER_PRO_EDITION' );
	}
	$compInfos = $mstApp->getInstallInformation();
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminform">	
		<tr><th><?php echo JText::_( 'COM_MAILSTER_SYSTEM_PROPERTIES' ); ?></th><th></th><th>&nbsp;</th></tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo $appName; ?>:</td>
			<td width="450px"><?php echo $mstApp->getVersionString() 
										. ' (PHash: ' . ($pHashOk ? 'ok':'NOT OK') 
										. ', PlgHash: ' . ($plgHashOk ? 'ok':'NOT OK') . ')'; ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_DATE' ); ?>:</td>
			<td width="450px"><?php echo $compInfos->creationdate; ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_OS' ); ?>:</td>
			<td width="450px"><?php echo php_uname(); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_PHP_VERSION' ); ?>:</td>
			<td width="450px"><?php echo phpversion(); ?> </td>
			<td>&nbsp;</td>
		</tr>
		<tr>			
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_MAX_EXECUTION_TIME' ); ?>:</td>
			<td width="450px"><?php echo ini_get('max_execution_time'); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_DATABASE_VERSION' ); ?>:</td>
			<td width="450px"><?php echo $db->getVersion(); ?> </td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_DATABASE_COLLATION' ); ?>:</td>
			<td width="450px"><?php echo $db->getCollation(); ?>
					<?php  					
						if($dbUtils->userTableCollationOk()){
							echo ' - Collation  OK';
						}else{
							echo ' - ERROR! Collation NOT OK, contact support!' . '<br/><br/>'; 
							echo JText::_( 'COM_MAILSTER_NAME' ) . ' (core):' . $dbUtils->getCollation('#__users', 'name') . '<br/>'; 
							echo JText::_( 'COM_MAILSTER_NAME' ) . ':' . $dbUtils->getCollation('#__mailster_users', 'name') . '<br/>';  
							echo JText::_( 'COM_MAILSTER_EMAIL' ) . ' (core):' . $dbUtils->getCollation('#__users', 'email') . '<br/>';  
							echo JText::_( 'COM_MAILSTER_EMAIL' ) . ':' . $dbUtils->getCollation('#__mailster_users', 'email') . '<br/>';  
						}
					?>
					<br/>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_JOOMLA_VERSION' ); ?>:</td>
			<td width="450px"><?php echo $version->getLongVersion(); ?> </td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_URL' ); ?>:</td>
			<td width="450px"><?php echo $env->getUrl(); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_IMAP' ); ?>:</td>
			<td width="450px"><?php echo $env->imapExtensionInstalled() ? JText::_( 'COM_MAILSTER_LOADED' ) : JText::_( 'COM_MAILSTER_JNO' ); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;">&nbsp;</td>
			<td width="450px"><pre><?php echo $env->getImapVersion(); ?></pre></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_OPEN_SSL' ); ?>:</td>
			<td width="450px"><?php echo $env->openSSLExtensionInstalled() ? JText::_( 'COM_MAILSTER_LOADED' ) : JText::_( 'COM_MAILSTER_JNO' ); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;">&nbsp;</td>
			<td width="450px"><pre><?php echo $env->getOpenSSLVersion(); ?></pre></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="text-align:right;"><?php echo JText::_( 'COM_MAILSTER_CONNECTION_CHECK' ); ?>:</td>
			<td width="450px">
				<pre><?php 
		        		require_once('inbox_test.php');
		        	 ?>
				</pre>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="diagnosis" />
	<input type="hidden" name="view" value="diagnosis" />
	<input type="hidden" name="task" value="" />	
	</form>
	<?php
	//keep session alive while editing
	JHTML::_('behavior.keepalive');
	?>
