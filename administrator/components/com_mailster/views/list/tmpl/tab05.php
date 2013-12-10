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
<div style="display: none; padding-left:20px; padding-top:5px;" id="fifth" class="tabDiv">				
	<table class="adminform tabContentTbl">
		<tr>
			<td width="150px">
				<label for="allow_registration">
					<?php echo JText::_( 'COM_MAILSTER_ALLOW_REGISTRATION_SUBSCRIPTION' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php
				echo JHTML::_('select.booleanlist', 
				'allow_registration', 
				'class="hTip" title="' . JText::_( 'COM_MAILSTER_ALLOW_REGISTRATION_SUBSCRIPTION_WITH_SUBSCRIBE_FORM' ) . '"', 
				$this->row->allow_registration );
				?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="public_registration">
					<?php echo JText::_( 'COM_MAILSTER_PUBLIC_REGISTRATION_SUBSCRIPTION' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php
				echo JHTML::_('select.booleanlist', 
				'public_registration', 
				'class="hTip" title="' . JText::_( 'COM_MAILSTER_PUBLIC_REGISTRATION_SUBSCRIPTION_FOR_UNREGISTERED_USERS' ) . '"', 
				$this->row->public_registration );
				?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="copy_to_sender">
					<?php echo JText::_( 'COM_MAILSTER_MAIL_COPY_TO_SENDER' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php
				echo JHTML::_('select.booleanlist', 
				'copy_to_sender', 
				'class="hTip" title="' . JText::_( 'COM_MAILSTER_MAIL_COPY_TO_SENDER_SEND_MAIL_ALSO_TO_ORIGINAL_SENDER' ) . '"', 
				$this->row->copy_to_sender );
				?>
			</td>						
			<td>&nbsp;</td>
		</tr>		
		<?php 				
			$mstApp = & MstFactory::getApplication();	
			$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
			$plgHashOk = $mstApp->checkPluginProductHashes();
		?>
		<tr>
			<td width="150px">
				<label for="archive_mode">
					<?php echo JText::_( 'COM_MAILSTER_ARCHIVE_SENT_MAILS_DISABLED' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php
				if($pHashOk && $plgHashOk && !$isFree){
					echo JHTML::_('select.booleanlist', 
					'archive_mode', 
					'class="hTip" title="' . JText::_( 'COM_MAILSTER_ARCHIVE_SENT_MAILS_DISABLED_MAIL_CONTENT_IS_NOT_STORED' ) . '"', 
					$this->row->archive_mode );
				}else{
						echo '<span class="hTip" title="'.JText::_( 'COM_MAILSTER_ARCHIVE_SENT_MAILS_DISABLED_MAIL_CONTENT_IS_NOT_STORED' ).'">' . JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ) . '</span>';
				} ?>
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="mail_size_limit">
					<?php echo JText::_( 'COM_MAILSTER_EMAIL_SIZE_LIMIT' ).':'; ?>
				</label>
			</td>
			<td width="350px">
					<input class="inputbox hTip" name="mail_size_limit" 
					value="<?php echo $this->row->mail_size_limit; ?>" 
					style="width:80px;" maxlength="45" id="mail_size_limit"
					title="<?php echo JText::_( 'COM_MAILSTER_EMAIL_SIZE_LIMIT_MAXIMUM_ALLOWED_SIZE' ); ?>" />
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="150px">
				<label for="filter_mails">
					<?php echo JText::_( 'COM_MAILSTER_FILTER_MAILS_BY_CONTENT' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<?php
				if($pHashOk && $plgHashOk && !$isFree){
					echo JHTML::_('select.booleanlist', 
					'filter_mails', 
					'class="hTip" title="' . JText::_( 'COM_MAILSTER_FILTER_MAILS_BY_CONTENT_WORDS_TO_FILTER_IN_CONFIGURATION' ) . '"', 
					$this->row->filter_mails );
				}else{
						echo '<span class="hTip" title="'.JText::_( 'COM_MAILSTER_FILTER_MAILS_BY_CONTENT_WORDS_TO_FILTER_IN_CONFIGURATION' ).'">' . JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ) . '</span>';
				} ?>
			</td>						
			<td>&nbsp;</td>
		</tr>		
		<tr>
			<td width="150px" style="vertical-align:top;">
				<label for="sending_allowed">
					<?php echo JText::_( 'COM_MAILSTER_ALLOWED_TO_SEND_POST' ).':'; ?>
				</label>
			</td>
			<td width="350px">
				<input id="sending_public1"  type="radio" name="sending_public" value="1" class="hTipIA.sending_public_hTip" title="<?php echo JText::_( 'COM_MAILSTER_ALLOWED_TO_SEND_POST_YOU_CAN_LIMIT_THE_RIGHT_TO_SEND_TO_CERTAIN_PEOPLE' ); ?>" <?php echo $this->row->sending_public == 1 ? 'checked="checked"' : ''; ?> /> <span id="sending_public_hTip"><?php echo JText::_( 'COM_MAILSTER_EVERYBODY_PUBLIC' ); ?></span><br/>
				<input id="sending_public0"  type="radio" name="sending_public" value="0" <?php echo $this->row->sending_public == 0 ? 'checked="checked"' : ''; ?> /> <?php echo JText::_( 'COM_MAILSTER_SENDING_IS_RESTRICTED_ALLOWED_SENDERS' ); ?><br/>
				<input id="sending_recipients"  type="checkbox" name="sending_recipients" 	value="<?php echo $this->row->sending_recipients; ?>" 	onclick="this.value = this.checked ? 1:0;" <?php echo $this->row->sending_recipients == 1 ? 'checked="checked"' : ''; ?> 	style="margin-left:25px;" /> <?php echo JText::_( 'COM_MAILSTER_ALL_RECIPIENTS' ); ?><br/>
				<input id="sending_admin" 		type="checkbox" name="sending_admin" 		value="<?php echo $this->row->sending_admin; ?>" 		onclick="this.value = this.checked ? 1:0;" <?php echo $this->row->sending_admin == 1 ? 		'checked="checked"' : ''; ?> 	style="margin-left:25px;" /> <?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_ADMIN' ); ?><br/>
				<input id="sending_group" 		type="checkbox" name="sending_group" 		value="<?php echo $this->row->sending_group; ?>" 		onclick="this.value = this.checked ? 1:0;" <?php echo $this->row->sending_group == 1 ? 		'checked="checked"' : ''; ?> 	style="margin-left:25px;" /> <?php echo JText::_( 'COM_MAILSTER_GROUP' ); ?>
				<?php echo JHTMLSelect::genericlist($this->groups, 'sending_group_id', 'size="1" style="width:100px;margin-left:10px;"', 'id', 'name', $this->row->sending_group_id, 'sending_group_id'); ?>				
				<br/>				
			</td>						
			<td>&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="3">&nbsp;</td>
		</tr>	
	</table>
</div>
		
