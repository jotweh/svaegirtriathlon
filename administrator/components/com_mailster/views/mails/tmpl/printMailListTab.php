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
	
	jimport('joomla.utilities.date');
		
?>

<?php 
	function printMailListTab($rows, $tabId, $proOnly, $proTxt, $showFwd, $listID, $showFilterInfo){
		$imgPath = 'components/com_mailster/assets/images/';
		$attachmentImg = '<img src="' . $imgPath . '16-attachment.png" title="' . JText::_( 'COM_MAILSTER_HAS_ATTACHMENTS' ) . '" alt="" />';
		$blockedUserImg = '<img src="' . $imgPath . '16-blockedUser.png" title="' . JText::_( 'COM_MAILSTER_SENDER_BLOCKED' ) . '" alt="" />';
		$spamImg = '<img src="' . $imgPath . '16-spam.png" title="' . JText::_( 'COM_MAILSTER_FILTERED_MAIL' ) . '" alt="" />';
		$imgLink = 'components/com_mailster/assets/images/';
		$red_x = '16-publish_x.png';
		$green_t = '16-tick.png';
		$red_cross = '<img src="' . $imgLink . $red_x . '"';
		$green_tick = '<img src="' . $imgLink . $green_t . '"'; 
		$fwdErrors = $red_cross . ' title="' . JText::_( 'COM_MAILSTER_FORWARD_ERRORS' ) . '" alt="" />';
		$fwdComplete = $green_tick . ' title="' . JText::_( 'COM_MAILSTER_FORWARD_COMPLETED' ) . '" alt="" />';
	
		$rowCount = count( $rows );
?>
		<div style="display: none; padding:20px;" id="<?php echo $tabId; ?>" class="tabDiv">
			<table class="adminlist" cellspacing="1">
				<thead>
					<tr>
						<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
						<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAllXT(this.checked, '<?php echo $tabId; ?>', <?php echo  $rowCount; ?>);" /></th>
						<th width="5"><?php echo $attachmentImg; ?></th>
						<?php if($showFilterInfo){ ?>
						<th width="5">&nbsp;</th>	
						<?php }?>
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_FROM_NAME' ); ?></th>	
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_FROM_EMAIL' );  ?></th>	
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_SUBJECT' );  ?></th>	
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_DATE' );  ?></th>
						<?php if($showFwd){ ?>
							<th class="title"><?php echo JText::_( 'COM_MAILSTER_SENT_DATE' ); ?></th>
							<th width="1%" ><?php echo JText::_( 'COM_MAILSTER_ERRORS' ); ?></th>	
							<th width="1%" ><?php echo JText::_( 'COM_MAILSTER_SENT' ); ?></th>		
						<?php }?>
						<th class="title"><?php echo JText::_( 'COM_MAILSTER_IN_MAILING_LIST' ); ?></th>	
						<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_MAILSTER_JID' ); ?></th>
					</tr>
				</thead>
		
				<tbody>
				<?php
							
				$k = 0;			
				$mstApp = & MstFactory::getApplication();					
				$pHashOk = $mstApp->isProductHashCorrect('com_mailster', 'f7647518248eb8ef2b0a1e41b8a59f34');
				$isFree = $mstApp->isFreeEdition('com_mailster', 'free', 'f7647518248eb8ef2b0a1e41b8a59f34');
				$plgHashOk = $mstApp->checkPluginProductHashes();
				if(!$proOnly || ($pHashOk && $plgHashOk && !$isFree)){
					for($i=0, $n=$rowCount; $i < $n; $i++) {
						$row = &$rows[$i];
						$mailLink 		= 'index.php?option=com_mailster&amp;controller=mails&amp;task=view&amp;cid[]='.$row->id;
						if($listID > 0){
							$mailLink .= '&amp;listID='.$listID;
						}						
						$checked = '<input type="checkbox" onclick="isChecked(this.checked);" value="' .$row->id. '" name="cid[]" id="' . $tabId . $i . '">';
					?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo ($i+1); ?></td>
							<th><?php echo $checked; ?></th>
							<td><?php echo $row->has_attachments=='1' ? $attachmentImg : '&nbsp;'; ?></td>
							<?php if($showFilterInfo){ ?>
							<td>
								<?php 
								if($row->blocked_mail == '1'){
									echo $blockedUserImg;
								}elseif($row->blocked_mail == '2'){ 
									echo $spamImg;
								}else{
									echo '&nbsp;';
								} ?>
							</td>
							<?php }?>
							<td style="width:100px; padding-left:5px;"><?php echo $row->from_name; ?></td>	
							<td style="width:100px; padding-left:5px;"><?php echo $row->from_email; ?></td>	
							<td style="padding-left:10px;">
								<span class="editlinktip hasTip" title="<?php echo (($row->no_content == 0) ? JText::_( 'COM_MAILSTER_VIEW_MAIL' ) : JText::_( 'COM_MAILSTER_MAIL_NOT_ARCHIVED' ));?>">
									<?php
										if($row->no_content == 0){
									?>
									<a href="<?php echo $mailLink; ?>">
									<?php }?>
										<?php echo $row->subject != '' ? $row->subject : JText::_( 'COM_MAILSTER_NO_SUBJECT' ); ?>
									<?php
										if($row->no_content == 0){
									?>
									</a>
									<?php }?>
								</span>
							</td>	
							<td style="width:120px; text-align:center;"><?php 

							$dateUtils = &MstFactory::getDateUtils();
						    $receivedTimestamp = $dateUtils->formatDateAsConfigured($row->receive_timestamp);
						    $fwdCompletedTimestamp = $dateUtils->formatDateAsConfigured($row->fwd_completed_timestamp);
						    
							echo $receivedTimestamp;
							?></td>	
							<?php if($showFwd){ ?>
								<td style="width:120px; text-align:center;"><?php echo $row->fwd_completed == '1' ? $fwdCompletedTimestamp : ' - '; ?></td>
								<td style="text-align:center;"><?php echo $row->fwd_errors > 0 ? $fwdErrors : '&nbsp;'; ?></td>	
								<td style="text-align:center;"><?php echo $row->fwd_completed == '1' ? $fwdComplete : '&nbsp;'; ?></td>		
							<?php }?>
							<td style="padding-left:5px;"><?php echo $row->name; ?></td>
							<td align="center"><?php echo $row->id; ?></td>
						</tr>
					<?php
						$k = 1 - $k;  
					}
				}else{ ?>	
					<tr class="row0">
						<td colspan="12">&nbsp;</td>
					</tr>				
					<tr class="row1">
						<td colspan="12"><?php echo JText::_( 'COM_MAILSTER_AVAILABLE_IN_PRO_EDITION' ); ?></td>
					</tr>				
					<tr class="row0">
						<td colspan="12">&nbsp;</td>
					</tr>				
					<tr class="row1">
						<td colspan="12"><?php echo $proTxt; ?></td>
					</tr>				
					<tr class="row0">
						<td colspan="12">&nbsp;</td>
					</tr>
			<?php }?>				
				</tbody>		
				<tfoot>					
					<tr>
						<td colspan="13" class="erPagination"></td>
					</tr>
				</tfoot>
			</table>
		</div>
<?php
	} 
?>
