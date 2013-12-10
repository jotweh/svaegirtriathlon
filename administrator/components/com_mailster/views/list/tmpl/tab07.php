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
	$notifies = $this->notifies;
	$imgPath = 'components/com_mailster/assets/images/'; 
?>
<div style="display: none; padding-left:20px; padding-top:5px;" id="seventh" class="tabDiv">	
<select id="copyStationUsers" style="display:none;"><?php echo $this->listOptions['joomla_users']; ?></select>	
<select id="copyStationGroups" style="display:none;"><?php echo $this->listOptions['user_groups']; ?></select>		
	<table class="adminform tabContentTbl">
		<tr>
			<td width="220px">
				<label for="notify_not_fwd_sender">
					<?php echo JText::_( 'COM_MAILSTER_NOTIFY_SENDER_OF_UNFORWARDED_MAILS' ).':'; ?>
				</label>
			</td>
			<td width="300px">
				<?php
				echo JHTML::_('select.booleanlist', 
				'notify_not_fwd_sender', 
				'class="hTip" title="' . JText::_( 'COM_MAILSTER_NOTIFY_SENDER_OF_UNFORWARDED_MAILS_IF_MAIL_IS_BLOCKED_SENDER_GETS_NOTIFICATION_MAIL' ) . '"', 
				$this->row->notify_not_fwd_sender );
				?>
			</td>						
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>	
			<td colspan="5">
				<span class="hTipIB.notify_info_hTip" title="<?php echo JText::_( 'COM_MAILSTER_YOU_CAN_ADD_NOTIFICATIONS_FOR_EVENTS_SO_THAT_CERTAIN_PERSONS_ARE_INFORMED_VIA_EMAIL' ); ?>">&nbsp;</span>
				<span id="notify_info_hTip" title="<?php echo JText::_( 'COM_MAILSTER_YOU_CAN_ADD_NOTIFICATIONS_FOR_EVENTS_SO_THAT_CERTAIN_PERSONS_ARE_INFORMED_VIA_EMAIL' ); ?>"> <?php echo JText::_( 'COM_MAILSTER_NOTIFICATION_INFORMATION' ); ?></span>
			</td>
		</tr>
		<tr>
			<td rowspan="5" style="vertical-align: top;" colspan="3">	
				<?php 				
				$mstApp = & MstFactory::getApplication();	
				$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
				$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
				$plgHashOk = $mstApp->checkPluginProductHashes();
				
				if($pHashOk && $plgHashOk && !$isFree){
				?>
				<table id="notifiesTbl" class="notifiesTbl" style="width:550px;margin-left:10px;">
					<tr>
						<th width="10px">#</th>
						<th width="140px"><?php echo JText::_( 'COM_MAILSTER_WHEN_EVENT_TYPE_QUESTION' ); ?></th>
						<th colspan="2" width="280px"><?php echo JText::_( 'COM_MAILSTER_WHO_NOTIFY_TARGET_QUESTION' ); ?></th>
						<th width="90px">&nbsp;</th>
					</tr>
					<?php 
						for($i=0; $i<count($notifies); $i++){
							$notify = &$notifies[$i];
					?>
					
						<tr id="notifiesTbl_row<?php echo $i;?>">	
							<td><?php echo ($i+1);?></td>
							<td><select id="triggerType<?php echo $i;?>" name="triggerType<?php echo $i;?>" class="triggerTypeClass" style="width:130px;"><?php echo $notify->triggerTypes; ?></select></td>
							<td><select id="targetType<?php echo $i;?>" name="targetType<?php echo $i;?>" class="targetTypeClass"  style="width:130px;"><?php echo $notify->targetTypes; ?></select></td>
							<td><select id="targetId<?php echo $i;?>" name="targetId<?php echo $i;?>" class="targetIdClass" style="width:130px;<?php echo $notify->target_type == 0 ? 'display:none;' : ''; ?>"><?php echo $notify->targetChoice; ?></select></td>
							<td>
								<a id="removeNotifyButton<?php echo $i;?>" href="#" class="notifierRemoverClass"><img src="<?php
									$checkImg = $imgPath . '16-remove.png';
									echo $checkImg; ?>" style="vertical-align:middle;" /> <?php echo JText::_( 'COM_MAILSTER_DELETE_UC' ); ?></a>
								<input id="notifyId<?php echo $i;?>" name="notifyId<?php echo $i;?>" value="<?php echo $notify->id;?>" type="hidden">
								<div tabindex="-1" id="removeNotifyButtonProgressIndicator<?php echo $i;?>" style="display:inline; margin:5px; padding-right:20px;min-height:30px;width:30px;">&nbsp;</div>
							</td>
						</tr>	
					
					<?php 
						}
					?>			
				</table>
				<?php 
				}else{
					?>
					<img src="<?php echo  $imgPath . 'notify_mockup.png'; ?>" style="vertical-align:middle;" title="<?php echo JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ); ?>"/> 
				<?php 
				}
				?>
			</td>
			<td style="vertical-align: top;">
			<?php 				
				if($pHashOk && $plgHashOk && !$isFree){
				?>
				<a id="addNotifyButton" href="#"><img src="<?php
					$imgPath = 'components/com_mailster/assets/images/'; 
					$checkImg = $imgPath . '16-add.png';
					echo $checkImg; ?>" style="vertical-align:middle;" /> <?php echo JText::_( 'COM_MAILSTER_ADD_NOTIFICATION' ); ?></a>
				<?php
				}
				?>
			</td>
			<td colspan="2">&nbsp;</td>		
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
	</table>
</div>
		
