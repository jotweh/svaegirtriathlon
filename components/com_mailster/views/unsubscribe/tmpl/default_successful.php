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
	
	$mstAdminImgPath = 'administrator/components/com_mailster/assets/images/';
	?>
	<script language="javascript" type="text/javascript">
		
	</script>
	<form action="index.php" method="post">	
		<h2 class="componentheading mailsterUnsubscriberHeader">
			<?php	echo JText::_( 'COM_MAILSTER_UNSUBSCRIPTION_UC'); ?>
		</h2>		
		<div class="contentpane">
			<div id="mailsterContainer">		
				<div id="mailsterUnsubscriber">
					<img src="<?php echo $mstAdminImgPath . '16-tick.png';?>" style="float:left;"/> 
					<div id="mailsterUnsubscriberDescription"><?php echo JText::_( 'COM_MAILSTER_UNSUBSCRIPTION_SUCCESSFUL' ); ?></div>
				</div>
			</div>
		</div>
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="controller" value="subscriptions" />
		<input type="hidden" name="view" value="unsubscribe" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>