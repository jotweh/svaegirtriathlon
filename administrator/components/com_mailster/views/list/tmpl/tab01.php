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
?>
<div style="display: none; padding-left:20px; padding-top:5px;" id="first" class="tabDiv">
	<table class="adminform tabContentTbl">
		<tr>
			<td width="150px">
				<label for="name">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_NAME' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<input class="inputbox hTip" name="name"
				 value="<?php echo $this->row->name; ?>" 
				 size="50" maxlength="45" id="name"
					 title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_NAME_CHOOSE_UNIQUE_NAME_CAN_BE_SEEN_IN_FRONTEND' ); ?>" />
				<span style="font-weight:bold; color:red;">*</span>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="list_mail">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_ADDRESS' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<input class="inputbox hTip" name="list_mail" 
					value="<?php echo $this->row->list_mail; ?>"
					size="50" maxlength="255" id="list_mail" 
					 title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_ADDRESS_USED_FOR_SENDING_LIST_EMAILS_AND_IS_TARGET_FOR_INBOX' ); ?>" />
				<span style="font-weight:bold; color:red;">*</span>
			</td>						
			<td>&nbsp;</td>
		</tr>	
		<tr>
			<td width="150px">
				<label for="admin_mail">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_ADMIN_MAIL' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<input class="inputbox hTip" name="admin_mail"
					 value="<?php echo $this->row->admin_mail; ?>" 
					 size="50" maxlength="255" id="admin_mail" 
					 title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_ADMIN_MAIL_PLEASE_PROVIDE_EXISTING_ADDRESS' ); ?>" />
				<span style="font-weight:bold; color:red;">*</span>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>	
			<td width="150px">
				<label for="active">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_ACTIVE' ).':'; ?>
				</label>
			</td>
			<td>
				<?php
				echo JHTML::_('select.booleanlist', 'active', 
								'class="hTip" title="' . JText::_( 'COM_MAILSTER_MAILING_LIST_ACTIVE_DETERMINES_WHETHER_MAILS_ARE_FORWARDED_OR_NOT' ) .'"',
								 $this->row->active );
				?>
			</td>						
			<td>&nbsp;</td>
		</tr>	
		<!-- 
		<tr>	
			<td width="150px">
				<label for="published">
					<?php echo JText::_( 'COM_MAILSTER_PUBLISHED' ).':'; ?>
				</label>
			</td>
			<td>
				<?php
				$html = JHTML::_('select.booleanlist', 'published', '', $this->row->published );
				echo $html;
				?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		 -->
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>
	</table>
</div>
        
