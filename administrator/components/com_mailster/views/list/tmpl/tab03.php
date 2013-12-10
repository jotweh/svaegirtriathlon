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
<div style="display: none; padding-left:20px; padding-top:5px;" id="third" class="tabDiv">				
	<table class="adminform tabContentTbl">
		<tr>
			<td width="150px">
				<label for="use_joomla_mailer">
					<?php echo JText::_( 'COM_MAILSTER_USE_JOOMLA_MAILER' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php
				echo JHTML::_('select.booleanlist', 
							'use_joomla_mailer', 
							'class="hTip" title="' . JText::_( 'COM_MAILSTER_USE_JOOMLA_MAILER_CONFIGURED_OR_USE_SMTP_SERVER' )  . '"',
							 $this->row->use_joomla_mailer );
				
				if($this->row->use_joomla_mailer === '1'){
					$enabled = 'disabled="disabled"';
				}else{
					$enabled = '';
				}
				?>
			</td>	
			<td rowspan="8">
			
				<a id="outboxConnectionCheckImg" href="#"><img src="<?php
				$imgPath = 'components/com_mailster/assets/images/'; 
				$checkImg = $imgPath . 'icon-32-updatecheck-mailster.png';
				echo $checkImg; ?>" style="vertical-align:middle;" /></a>
				<a id="outboxConnectionCheck" href="#" tabindex="-1"><?php echo JText::_( 'COM_MAILSTER_CHECK_OUTBOX_CONNECTION_SETTINGS' );?></a>
				<div tabindex="-1" id="progressIndicator2" style="display:inline; margin:5px; padding-right:20px;min-height:30px;width:30px;">&nbsp;</div>
				<br/><br/>
				<a tabindex="-1" href="http://www.brandt-oss.com/products/mailster/mail-settings" title="<?php echo JText::_( 'COM_MAILSTER_LIST_OF_MAILBOX_SETTINGS_OF_OTHER_USERS' );?>" target="_blank"><?php echo JText::_( 'COM_MAILSTER_NOT_SURE_WHICH_SETTINGS_YOU_NEED' );?><br/><?php echo JText::_( 'COM_MAILSTER_HAVE_A_LOOK_AT_WHAT_OTHER_MAILBOX_MAILSTER_USERS_HAVE_USED' );?></a>
			</td>					
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_out_host">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_HOST' ).':'; ?>
				</label>
			</td>
			<td>
				<input <?php echo $enabled; ?> 
				class="inputbox hTip" name="mail_out_host" 
				value="<?php echo $this->row->mail_out_host; ?>" 
				size="50" maxlength="45" id="mail_out_host"
				 title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_HOST_MAILBOX_SERVER_ADDRESS' ); ?>" />
				<span style="font-weight:bold; color:red;">*</span>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_out_user">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USER' ).':'; ?>
				</label>
			</td>
			<td>
				<input <?php echo $enabled; ?> 
				class="inputbox hTip" name="mail_out_user" 
				value="<?php echo $this->row->mail_out_user; ?>" 
				size="50" maxlength="45" id="mail_out_user"
				title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USER_NAME_OF_THE_MAILBOX' ); ?>" />
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_out_pw">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PW' ).':'; ?>
				</label>
			</td>
			<td>
				<input <?php echo $enabled; ?> 
				type="password" class="inputbox hTip" 
				name="mail_out_pw" 
				value="<?php echo $this->row->mail_out_pw; ?>" 
				size="50" maxlength="45" id="mail_out_pw" 
				title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PW_MAILBOX_PASSWORD' ); ?>" />
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_out_port">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PORT' ).':'; ?>
				</label>
			</td>
			<td>
				<input <?php echo $enabled; ?> 
				class="inputbox hTip" name="mail_out_port" 
				value="<?php echo $this->row->mail_out_port; ?>" 
				style="width:80px;" maxlength="45" id="mail_out_port"
				title="<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_PORT_NUMBER_OF_THE_CHOSEN_PROTOCOL' ); ?>" />
				<span style="font-weight:bold; color:red;">*</span>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>						
			<td width="150px">
				<label for="mail_out_use_secure">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE' ).':'; ?>
				</label>
			</td>
			<td>
			<?php echo $this->Lists['smtpsecure_out']; ?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>						
			<td width="150px">
				<label for="mail_out_use_sec_auth">
					<?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE_AUTHENTICATION' ).':'; ?>
				</label>
			</td>
			<td>
				<?php echo JHTML::_('select.booleanlist', 
					'mail_out_use_sec_auth', 
					'class="hTip" title="' . JText::_( 'COM_MAILSTER_MAILING_LIST_USE_SECURE_AUTHENTICATION_NO_PLAINTEXT_PW' )  . '"', 
					$this->row->mail_out_use_sec_auth ); ?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="4">&nbsp;</td>
		</tr>	
		<tr>	
			<td colspan="4">&nbsp;</td>
		</tr>	
	</table>
</div>
		
