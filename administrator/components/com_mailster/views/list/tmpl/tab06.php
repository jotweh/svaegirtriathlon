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
<div style="display: none; padding-left:20px; padding-top:5px;" id="sixth" class="tabDiv">				
	<table class="adminform tabContentTbl">
		<tr>
			<td width="150px" style="vertical-align:top;">
				<label for="addressing_mode">
					<?php echo JText::_( 'COM_MAILSTER_RECIPIENT_ADDRESSING' ).':'; ?>
				</label>
			</td>
			<td colspan="3" style="vertical-align:top;">
				<input id="use_bcc" type="radio" name="addressing_mode" class="hTipIA.use_bcc_hTip" title="<?php echo JText::_( 'COM_MAILSTER_YOU_PROBABLY_CAN_ONLY_USE_BCC_WHEN_USING_A_SMTP_SERVER' ); ?>" value="1"/>
				<label for="use_bcc">
					<?php echo JText::_( 'COM_MAILSTER_USE_BCC_ADDRESSING' ) . ' (' . JText::_( 'COM_MAILSTER_RECOMMENDED' ) . ''  . ')'; ?>
					<span id="use_bcc_hTip">&nbsp;</span><br/>
				</label>
				<input class="inputbox" name="bcc_count"  style="margin-left:40px;margin-top:5px;" value="<?php echo $this->row->bcc_count; ?>" size="10" maxlength="8" id="bcc_count" />
				<?php echo JText::_( 'COM_MAILSTER_BCC_RECIPIENTS_PER_MAIL' ); ?><br/>		
				<input type="radio" name="addressing_mode" value="2" id="use_cc" />
				<label for="use_cc">
					<?php echo JText::_( 'COM_MAILSTER_USE_CC_ADDRESSING_SHOW_ALL_RECIPIENTS' ); ?>
				</label><br/>		
				<input type="radio" name="addressing_mode" value="0" id="use_to" />
				<label for="use_to">
					<?php echo JText::_( 'COM_MAILSTER_NO_BCC-CC_ADDRESSING_SEND_ONE_MAIL_PER_RECIPIENT' ); ?>
				</label><br/>
			</td>				
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="vertical-align:top;">
				<label for="mail_from_mode">
					<?php echo JText::_( 'COM_MAILSTER_MAIL_FROM_FIELD' ).':'; ?>
				</label>
			</td>
			<td colspan="4">				
				<?php echo $this->Lists['mail_from_mode']; ?>
				<span id="mail_from_hTip" style="margin-left:15px;vertical-align:middle;">(<?php echo JText::_( 'COM_MAILSTER_GLOBAL_SETTING' ) . ': ' . ($mstConfig->useMailingListAddressAsFromField() ? JText::_( 'COM_MAILSTER_MAILING_LIST_ADDRESS' ) : JText::_( 'COM_MAILSTER_SENDER_ADDRESS' )); ?>)</span>
			</td>		
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="vertical-align:top;">
				<label for="name_from_mode">
					<?php echo JText::_( 'COM_MAILSTER_NAME_FROM_FIELD' ).':'; ?>
				</label>
			</td>
			<td colspan="4">				
				<?php echo $this->Lists['name_from_mode']; ?>
				<span id="name_from_hTip" style="margin-left:15px;vertical-align:middle;">(<?php echo JText::_( 'COM_MAILSTER_GLOBAL_SETTING' ) . ': ' . ($mstConfig->useMailingListNameAsFromField() ? JText::_( 'COM_MAILSTER_MAILING_LIST_NAME' ) : JText::_( 'COM_MAILSTER_SENDER_NAME' )); ?>)</span>
			</td>		
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="vertical-align:top;">
				<label for="reply_to_sender">
					<?php echo JText::_( 'COM_MAILSTER_REPLY_DESTINATION' ).':'; ?>
				</label>
			</td>
			<td colspan="4">
				<input id="replyToList" type="radio" name="replyRecipient" class="hTipIA.reply_to_hTip" title="<?php echo JText::_( 'COM_MAILSTER_REPLY_DESTINATION_CONFIGURE_WHERE_REPLIES_TO_LIST_MAILS_SHOULD_BE_SENT_TO' ); ?>" value="0"> <span id="reply_to_hTip"><?php echo JText::_( 'COM_MAILSTER_REPLY_TO_MAILING_LIST' ); ?></span><br/>
				<input id="replyToSender"  type="radio" name="replyRecipient" value="1"> <?php echo JText::_( 'COM_MAILSTER_REPLY_TO_SENDER_ONLY' ); ?><br/>
				<input id="replyToSenderAndList"  type="radio" name="replyRecipient" value="2"> <?php echo JText::_( 'COM_MAILSTER_REPLY_TO_SENDER_OPTIONAL_TO_LIST_WITH_REPLY_TO_ALL' ); ?><br/>
				<input type="hidden" class="inputbox" name="reply_to_sender" value="<?php echo $this->row->reply_to_sender; ?>" size="50" maxlength="45" id="reply_to_sender" />								
			</td>		
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px" style="vertical-align:top;">
				<label for="bounce_mode">
					<?php echo JText::_( 'COM_MAILSTER_BOUNCES_DESTINATION' ).':'; ?>
				</label>
			</td>
			<td colspan="3">
				<input id="useNoBounceAddress" type="radio" name="bounceModeSettings" class="hTipIA.bounce_mode_hTip" title="<?php echo JText::_( 'COM_MAILSTER_BOUNCES_DESTINATION_USED_FOR_AUTO_REPLIES_LIKE_OUT_OF_OFFICE_AND_DELIVERY_STATUS_NOTIFICATIONS' ); ?>" value="<?php echo MstConsts::BOUNCE_MODE_LIST_ADDRESS; ?>" /> <span id="bounce_mode_hTip"><?php echo JText::_( 'COM_MAILSTER_NO_DEDICATED_BOUNCES_ADDRESS' ); ?></span><br/>
				<input id="useBounceAddress"  type="radio" name="bounceModeSettings" value="<?php echo MstConsts::BOUNCE_MODE_DEDICATED_ADDRESS; ?>" />  <span><?php echo JText::_( 'COM_MAILSTER_DEDICATED_BOUNCES_ADDRESS' ); ?></span>
				<input type="hidden" class="inputbox" name="bounce_mode" value="<?php echo $this->row->bounce_mode; ?>" size="50" maxlength="45" id="bounce_mode" /><br/>
				<input id="bounce_mail" class="inputbox" style="margin-left:40px;margin-top:5px;" name="bounce_mail" value="<?php echo $this->row->bounce_mail; ?>" size="40" maxlength="255" /> <span><?php echo JText::_( 'COM_MAILSTER_BOUNCES_ADDRESS' ); ?></span><br/>							
			</td>
			<td colspan="2">&nbsp;</td>
		</tr>	
		<?php 				
			$mstApp = & MstFactory::getApplication();	
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
		?>
		<tr>
			<td width="150px">
				<label for="throttle_hour_limit">
					<?php echo JText::_( 'COM_MAILSTER_SEND_THROTTLE_HOUR_LIMIT' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php
				if($pHashOk && $plgHashOk && !$isFree){
					?>
					<input class="inputbox hTip" name="throttle_hour_limit" 
					value="<?php echo $this->row->throttle_hour_limit; ?>" 
					size="10" maxlength="8" id="throttle_hour_limit"
					title="<?php echo JText::_( 'COM_MAILSTER_SEND_THROTTLE_HOUR_LIMIT_MAXIMUM_EMAILS_PER_HOUR' ); ?>" />
				<?php 
				}else{
						echo '<span class="hTip" title="'.JText::_( 'COM_MAILSTER_SEND_THROTTLE_HOUR_LIMIT_MAXIMUM_EMAILS_PER_HOUR' ).'">' . JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ) . '</span>';
				} ?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">						
				<label for="max_send_attempts">
					<?php echo JText::_( 'COM_MAILSTER_MAX_SEND_ATTEMPTS' ).':'; ?>
				</label>
			</td>
			<td colspan="3">
				<input id="max_send_attempts" class="inputbox hTip" 
				title="<?php echo JText::_( 'COM_MAILSTER_MAX_SEND_ATTEMPTS_WHEN_MAIL_SENDING_FAILS_IT_IS_RETRIED_THIS_OFTEN' ); ?>" 
				name="max_send_attempts" 
				value="<?php echo $this->row->max_send_attempts; ?>" 
				size="10" maxlength="8" />
			</td>	
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="6">&nbsp;</td>
		</tr>
	</table>
</div>
		
