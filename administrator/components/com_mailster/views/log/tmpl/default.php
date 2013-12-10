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
	$log = &MstFactory::getLogger();
	$mstUtils = & MstFactory::getUtils();
	$mstUtils->loadJavascript();
	
	$imgPath = 'components/com_mailster/assets/images/';
	
?>
	<form action="index.php" method="post" name="adminForm">		
		<script type="text/javascript">
			var $j = jQuery.noConflict();
			$j(document).ready(
			function () {	
				
			});
		</script>
		<table id="logTable" class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( 'COM_MAILSTER_NUM' ); ?></th>
					<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
					<th class="title"><?php echo JText::_( 'COM_MAILSTER_DATE' ); ?></th>
					<th class="title" width="100px"><?php echo JText::_( 'COM_MAILSTER_LOG_ENTRY_TYPE' ); ?></th>	
					<th><?php echo JText::_( 'COM_MAILSTER_LOG_SOURCE' ); ?></th>
					<th><?php echo JText::_( 'COM_MAILSTER_LOG_ENTRY' ); ?></th>					
					<th width="1%" nowrap="nowrap" style="vertical-align:center;"><?php echo JText::_( 'COM_MAILSTER_JID' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				$k=0;
				for($i=0, $n=count( $this->rows ); $i < $n; $i++) {
					$row = &$this->rows[$i];
					$checked = '<input type="checkbox" onclick="isChecked(this.checked);" value="' . $row->id .'" name="cid[]" id="cb' . $i . '">';
					$logClass = 'logEntry';
					switch($row->level){
						case MstConsts::LOG_LEVEL_ERROR:
							$logClass .= 'Error';
							break;
						case MstConsts::LOG_LEVEL_WARNING:
							$logClass .= 'Warning';
							break;
						case MstConsts::LOG_LEVEL_INFO:
							$logClass .= 'Info';
							break;
						case MstConsts::LOG_LEVEL_DEBUG:
							$logClass .= 'Debug';
							break;
							
					}					
				?>
					<tr class="<?php echo "row$k logEntry"; ?>">
						<td style="text-align:center;"><?php echo ($i+1); ?></td>						
						<td style="text-align:center;"><?php echo $checked; ?></td>	
						<td style="text-align:center;"><?php echo $row->log_time; ?></td>						
						<td style="text-align:center;" class="<?php echo $logClass; ?>"><?php echo $log->getLoggingLevelStr($row->level); ?></td>
						<td><?php echo $log->getLoggingTypeStr($row->type); ?></td>
						<td><pre style="margin:0px;padding:0px;"><?php echo wordwrap($row->msg, 150, '<br />'); ?></pre></td>	
						<td style="text-align:center;"><?php echo $row->id; ?>
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
		<input type="hidden" name="view" value="log" />
		<input type="hidden" name="controller" value="log" />
		<input type="hidden" name="task" id="task" value="" />
	</form>

