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
<div style="display: none; padding-left:20px; padding-top:5px;" id="second" class="tabDiv">				
	<table class="adminform tabContentTbl">
		<tr>
			<td width="150px">
				<label for="mail_in_host">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_HOST' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<input class="inputbox hTip" name="mail_in_host"
				 value="<?php echo $this->row->mail_in_host; ?>"
				 size="50" maxlength="45" id="mail_in_host" 
				 title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_HOST_MAILBOX_SERVER_ADDRESS' ); ?>" />
				<span style="font-weight:bold; color:red;">*</span>
			</td>	
			<td rowspan="8">
				<a id="inboxConnectionCheckImg" href="#" tabindex="-1"><img src="<?php
				$imgPath = 'components/com_mailster/assets/images/'; 
				$checkImg = $imgPath . 'icon-32-updatecheck-mailster.png';
				echo $checkImg; ?>" style="vertical-align:middle;" /></a>
				<a id="inboxConnectionCheck" href="#" tabindex="-1"><?php echo JText::_( 'COM_MAILSTER_CHECK_INBOX_CONNECTION_SETTINGS' );?></a>
				<div id="progressIndicator1" style="display:inline; margin:5px; padding-right:20px;min-height:30px;width:30px;">&nbsp;</div>
				<br/><br/>
				<a tabindex="-1" href="http://www.brandt-oss.com/products/mailster/mail-settings" title="<?php echo JText::_( 'COM_MAILSTER_LIST_OF_MAILBOX_SETTINGS_OF_OTHER_USERS' );?>" target="_blank"><?php echo JText::_( 'COM_MAILSTER_NOT_SURE_WHICH_SETTINGS_YOU_NEED' );?><br/><?php echo JText::_( 'COM_MAILSTER_HAVE_A_LOOK_AT_WHAT_OTHER_MAILBOX_MAILSTER_USERS_HAVE_USED' );?></a>
			</td>			
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_in_user">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USER' ).':'; ?>
				</label>
			</td>
			<td>
				<input class="inputbox hTip" name="mail_in_user" 
					value="<?php echo $this->row->mail_in_user; ?>" 
					size="50" maxlength="45" id="mail_in_user" 
					title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USER_NAME_OF_THE_MAILBOX' ); ?>" />
			</td>						
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_in_pw">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PW' ).':'; ?>
				</label>
			</td>
			<td>
				<input type="password" class="inputbox hTip"
				name="mail_in_pw" 
				value="<?php echo $this->row->mail_in_pw; ?>"
				size="50" maxlength="45" id="mail_in_pw"
				title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PW_MAILBOX_PASSWORD' ); ?>" />
			</td>						
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_in_port">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PORT' ).':'; ?>
				</label>
			</td>
			<td>
				<input class="inputbox hTip" name="mail_in_port"
				value="<?php echo $this->row->mail_in_port; ?>"
				style="width:80px;" maxlength="45" id="mail_in_port"
				title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PORT_NUMBER_OF_THE_CHOSEN_PROTOCOL' ); ?>" />
				<span style="font-weight:bold; color:red;">*</span>
			</td>						
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>						
			<td width="150px">
				<label for="mail_in_protocol">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PROTOCOL' ).':'; ?>
				</label>
			</td>
			<td>
				<?php echo $this->Lists['protocol_in']; ?>
			</td>						
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>						
			<td width="150px">
				<label for="mail_in_use_secure">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE' ).':'; ?>
				</label>
			</td>
			<td>
				<?php echo $this->Lists['smtpsecure_in']; ?>
			</td>						
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>						
			<td width="150px">
				<label for="mail_in_use_sec_auth">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE_AUTHENTICATION' ).':'; ?>
				</label>
			</td>
			<td>
				<?php echo JHTML::_('select.booleanlist',
						 'mail_in_use_sec_auth', 
						 'class="hTip" title="' . JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE_AUTHENTICATION_NO_PLAINTEXT_PW' )  . '"',
						 $this->row->mail_in_use_sec_auth ); ?>
			</td>						
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_in_params" title="<?php echo JText::_( 'COM_MAILSTER_OPTIONAL_SPECIAL_PARAMETER_LIST' ); ?>">
					<?php echo JText::_( 'COM_MAILSTER_SPECIAL_PARAMETERS' ).':'; ?>
				</label>
			</td>
			<td>
				<input class="inputbox hTip" name="mail_in_params" 
				value="<?php echo $this->row->mail_in_params; ?>" 
				size="50" maxlength="45" 
				id="mail_in_params" title="<?php echo JText::_( 'COM_MAILSTER_OPTIONAL_SPECIAL_PARAMETER_LIST' ); ?>" />						
			</td>						
			<td colspan="1">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="4">&nbsp;</td>
		</tr>	
	</table>
</div>
      
