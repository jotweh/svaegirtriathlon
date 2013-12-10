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
	$mstUtils = & MstFactory::getUtils();
	$mstUtils->loadJavascript();
	
	?>
	<script type="text/javascript">
		var $j = jQuery.noConflict(); 
		$j(document).ready(
		function () {	
			
		});
		function validateSubmittedForm(task)
		{
			var form = document.adminForm;			
			if (task == 'cancel') // check we aren't cancelling
			{	// no need to validate, we are cancelling		
				submitform( task );
				return;
			}else{
				submitform( task );
			}
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
	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">	
	<table class="adminform" style="margin:5px;">		
		<tr>
		<th><?php echo JText::_( 'COM_MAILSTER_MAILS_TO_RESEND' ) . ' (' . (count( $this->mails ) . ')'); ?></th>
		<th><?php echo JText::_( 'COM_MAILSTER_CHOOSE_TARGET_MAILING_LISTS' ); ?></th>
		</tr>
		<tr>		
			<td width="310px" style="vertical-align:top;">
				<table width="300px">
					<?php
						for($i=0, $n=count( $this->mails ); $i < $n; $i++) {
							$mail = &$this->mails[$i];
							?>
							<tr>
								<td><?php echo ($i+1); ?></td>
								<td>
									<input type="hidden" name="mails[]" value="<?php echo $mail->id; ?>" />	
									<?php echo $mail->subject; ?>
								</td>											
							</tr>
							<?php
						}					
					?>
				</table>
			</td>	
			<td width="210px" style="vertical-align:top;">
				<select id="targetLists" name="targetLists[]" multiple size="10" style="width:200px">				
				<?php
					for($i=0, $n=count( $this->lists ); $i < $n; $i++) {
						$list = &$this->lists[$i];
						?>
						<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
						<?php
					}							
				?>
				</select>
			</td>
			<td width="100px" style="vertical-align:top;">
				<input type="submit" value="<?php echo JText::_( 'COM_MAILSTER_RESEND' ); ?>" class="submitButton"
					title="<?php echo JText::_( 'COM_MAILSTER_RESEND_MAILS_TO_RECIPIENTS_OF_SELECTED_MAILING_LISTS' ); ?>" />
			</td>
			<td>&nbsp;</td>
		</tr>				
	</table>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="mails" />
	<input type="hidden" name="view" value="resend" />
	<input type="hidden" name="task" value="reEnqueueMails" />	
	</form>
	<?php
	JHTML::_('behavior.keepalive');
	?>
