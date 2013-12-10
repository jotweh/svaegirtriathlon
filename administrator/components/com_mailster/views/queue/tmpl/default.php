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
	
	$imgPath = 'components/com_mailster/assets/images/';
	$closedLockImg = '<img src="' . $imgPath . '16-lock-closed-red.png' . '" alt="" style="vertical-align:middle;" title="' . JText::_( 'COM_MAILSTER_EMAIL_LOCKED_FOR_SENDING' ) .'" />';
	$openLockImg = '<img src="' . $imgPath . '16-lock-open-grey.png' . '" alt="" style="vertical-align:middle;"title="' . JText::_( 'COM_MAILSTER_EMAIL_NOT_LOCKED_FOR_SENDING' ) . '" />';
?>
	<form action="index.php" method="post" name="adminForm">	
		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
					<th width="5"><?php echo JText::_( 'COM_MAILSTER_STATUS' ); ?></th>
					<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
					<th class="title"><?php echo JText::_( 'COM_MAILSTER_NAME' ); ?></th>	
					<th><?php echo JText::_( 'COM_MAILSTER_EMAIL' ); ?></th>	
					<th><?php echo JText::_( 'COM_MAILSTER_SUBJECT' ); ?></th>					
					<th width="1%" nowrap="nowrap" style="vertical-align:center;"><?php echo JText::_( 'COM_MAILSTER_JID' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$k=0;
				for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
					$row = &$this->rows[$i];
					$checked = '<input type="checkbox" onclick="isChecked(this.checked);" value="' . $row->mail_id . ':'.$row->email .'" name="cid[]" id="cb' . $i . '">';
					$mailLink 		= 'index.php?option=com_mailster&amp;controller=mails&amp;task=view&amp;cid[]='.$row->id;
				?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo ($i+1); ?></td>
						<td  style="text-align:center;horizonta-align:middle;"><?php echo ($row->is_locked == 1 ? $closedLockImg : $openLockImg); ?>
						<td><?php echo $checked; ?></td>
						
						<td><?php echo $row->name; ?></td>
						<td><?php echo $row->email; ?></td>		
						<td>						
							<a href="<?php echo $mailLink; ?>">
								<?php echo $row->subject != '' ? $row->subject : JText::_( 'COM_MAILSTER_NO_SUBJECT' ); ?>
							</a>	
						</td>
						<td><?php echo $row->mail_id; ?></td>
					</tr>
				<?php
					$k = 1 - $k;  
				} ?>

			</tbody>
			<tfoot>
				<tr>
					<td colspan="12">
						<div class="tabsFooter">
					        <?php 
					        	echo $this->pagination->getListFooter();
					        ?>
					    </div>
				    </td>
				</tr>
			</tfoot>			
		</table>

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_mailster" />
		<input type="hidden" name="view" value="queue" />
		<input type="hidden" name="controller" value="queue" />
		<input type="hidden" name="task" id="task" value="" />
	</form>

