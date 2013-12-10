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
	
	$adminImgPath = 'administrator/components/com_mailster/assets/images/';
	?>
	<script language="javascript" type="text/javascript">
		
	</script>
	<form action="index.php?option=com_mailster&view=unsubscribe" method="post">	
		<h2 class="componentheading mailsterUnsubscriberHeader">
			<?php	echo JText::_( 'COM_MAILSTER_UNSUBSCRIBE'); ?>
		</h2>		
		<div class="contentpane">
			<div id="mailsterContainer">		
				<div id="mailsterUnsubscriber">
				<?php if($this->hash_ok): ?>
					<div class="unsubscribe_header"><?php echo JText::_( 'COM_MAILSTER_ARE_YOU_SURE_YOU_WANT_TO_UNSUBSCRIBE' ); ?></div>
					<table id="person_details">
					<tr>
						<th><?php echo JText::_( 'COM_MAILSTER_MAILING_LIST' ); ?></th><td><?php echo $this->list->name; ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_( 'COM_MAILSTER_EMAIL' ); ?></th><td><input name="email" type="text" size="45" maxlength="100" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td><td><input type="submit" value="<?php echo JText::_( 'COM_MAILSTER_UNSUBSCRIBE' ); ?>"/></td>
					</tr>
					</table>
			<?php else: ?>
				<div class="unsubscribe_header"><?php echo JText::_( 'COM_MAILSTER_UNSUBSCRIBING_NOT_POSSIBLE' ); ?></div>
				<?php echo JText::_( 'COM_MAILSTER_INVALID_LINK' ); ?>
			<?php endif; ?>
				</div>
			</div>
		</div>
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="controller" value="subscriptions" />
		<input type="hidden" name="view" value="unsubscribe" />
		<input type="hidden" name="task" value="unsubscribe" />
		<input type="hidden" name="hash" value="<?php echo $this->hash; ?>" />
		<input type="hidden" name="salt" value="<?php echo $this->salt; ?>" />
		<input type="hidden" name="mail_id" value="<?php echo $this->mail_id; ?>" />
		<input type="hidden" name="list_id" value="<?php echo $this->list_id; ?>" />
		<input type="hidden" name="hash_ok" value="<?php echo $this->hash_ok; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>