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
	$memberInfo = $this->memberInfo;
	$listMemberInfo = $this->listMemberInfo;
	$groupMemberInfo = $this->groupMemberInfo;
	
	$lists = $memberInfo['lists'];
	$groups = $memberInfo['groups'];
	$listGroups = $memberInfo['listGroups'];
	
	?>
	<script language="javascript" type="text/javascript">
		function validateSubmittedForm(task)
		{
			var form = document.adminForm;
			// check we aren't cancelling
			if (task == 'cancel')
			{	// no need to validate, we are cancelling		
				submitform( task );
				return;
			}		
			
			submitform( task );		
		}
		function submitbutton(task){
			validateSubmittedForm(task);
		}
		<?php
		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6 / 1.7 / ...
			echo 'Joomla.submitbutton = function(pressbutton) { validateSubmittedForm(pressbutton); }';					
		}
		?>
	</script>

	<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
		<tr>
			<td valign="top">
				<table class="adminform" width="400px">
				<?php if($this->isJUser == 0): ?>
					<tr>
						<td width="150px">
							<label for="name">
								<?php echo JText::_( 'COM_MAILSTER_USER_NAME' ).':'; ?>
							</label>
						</td>
						<td width="250px">
							<input class="inputbox" name="name" value="<?php echo $this->row->name; ?>" size="50" maxlength="255" id="name" />
						</td>		
						<td>&nbsp;</td>				
					</tr>					
					<tr>
						<td width="150px">
							<label for="email">
								<?php echo JText::_( 'COM_MAILSTER_EMAIL' ).':'; ?>
							</label>
						</td>
						<td width="250px">
							<input class="inputbox" name="email" value="<?php echo $this->row->email; ?>" size="50" maxlength="255" id="email" />
						</td>
						<td>&nbsp;</td>
					</tr>				
					<tr>
						<td width="150px">
							<label for="notes">
								<?php echo JText::_( 'COM_MAILSTER_DESCRIPTION' ).':'; ?>
							</label>
						</td>
						<td width="250px">
							<input class="inputbox" name="notes" value="<?php echo $this->row->notes; ?>" size="50" maxlength="255" id="notes" />
						</td>
						<td>&nbsp;</td>
					</tr>
					<?php else: ?>	
					<?php
						$userLink = 'index.php?option=com_users&amp;view=user&amp;task=edit&amp;cid[]='.$this->row->id;
					?>
					<tr>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td width="150px">
							<label for="name"><?php echo JText::_( 'COM_MAILSTER_USER_NAME' ).':'; ?></label>
						</td>
						<td width="250px"><?php echo $this->row->name; ?></td>
						<td>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_MAILSTER_EDIT_USER' );?>: <?php echo $this->row->name; ?>">
							<a href="<?php echo $userLink; ?>" target="_blank"><?php echo JText::_( 'COM_MAILSTER_EDIT_USER' );?> (<?php echo JText::_( 'COM_MAILSTER_JOOMLA_USER' ); ?>)</a>
							</span>
						</td>
						<td>&nbsp;</td>
					</tr>					
					<tr>
						<td width="150px">
							<label for="email"><?php echo JText::_( 'COM_MAILSTER_EMAIL' ).':'; ?></label>
						</td>
						<td width="250px"><?php echo $this->row->email; ?></td>
						<td>&nbsp;</td>
					</tr>				
					<?php endif; ?>
					<tr>
						<td colspan="4"><hr/></td>
					</tr>
					<tr>
						<td colspan="4"><strong><?php echo JText::_( 'COM_MAILSTER_USER_MEMBER_OF' ).':'; ?></strong></td>
					</tr>
					<tr>
						<td width="150px">
							<label for="groups">
								<?php echo JText::_( 'COM_MAILSTER_USER_GROUPS' ).':'; ?>
							</label>
						</td>
						<td width="250px">
							<table>
								<tr>
									<th style="width:130px;"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>
									<th><?php echo JText::_( 'COM_MAILSTER_GROUP_MEMBER' ); ?></th>
								</tr>
								<?php
								for($i=0, $n=count( $groupMemberInfo ); $i < $n; $i++) {
									$gInfo = &$groupMemberInfo[$i];
								?>
								<tr>
									<td style="width:130px;"><?php echo $gInfo->name; ?></td>
									<td><?php echo JHTML::_('select.booleanlist', 'is_group_member'.$gInfo->id, 
											'', $gInfo->is_group_member ); ?>
									</td>
								</tr>
								<?php 
								}
								?>
							</table>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="150px">
							<label for="lists">
								<?php echo JText::_( 'COM_MAILSTER_MAILING_LISTS' ).':'; ?>
							</label>
						</td>
						<td width="250px">
							<table>
								<tr>
									<th style="width:130px;"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>
									<th><?php echo JText::_( 'COM_MAILSTER_MAILING_LIST_RECIPIENT' ); ?></th>
								</tr>
								<?php
								for($i=0, $n=count( $listMemberInfo ); $i < $n; $i++) {
									$lInfo = &$listMemberInfo[$i];
								?>
								<tr>
									<td style="width:130px;"><?php echo $lInfo->name; ?></td>
									<td><?php echo JHTML::_('select.booleanlist', 'is_list_member'.$lInfo->id,
											 '', $lInfo->is_list_member ); ?>
									</td>
								</tr>
								<?php 
								}
								?>
							</table>
						</td>
					</tr>	
					<tr>
						<td width="150px">
							<label for="listGroups">
								<?php echo JText::_( 'COM_MAILSTER_MAILING_LISTS_VIA_GROUPS' ).':'; ?>
							</label>
						</td>
						<td width="250px">
						<?php
						for($i=0, $n=count( $listGroups ); $i < $n; $i++) {
							$list = $listGroups[$i];
							echo $list->name . '<br/>';
						}
						?>
						</td>
					</tr>
					<tr>
						<td colspan="4"><hr/></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="users" />
	<input type="hidden" name="view" value="user" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="isjuser" value="<?php echo $this->isJUser; ?>" />
	</form>

	<?php
	//keep session alive while editing
	JHTML::_('behavior.keepalive');
	?>
