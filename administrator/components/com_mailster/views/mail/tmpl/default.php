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
	$log = & MstFactory::getLogger();	
	$mailUtils 	= & MstFactory::getMailUtils();
	$dateUtils = &MstFactory::getDateUtils();
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
			<td style="width:150px;text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_FROM_NAME' ).':'; ?></label></td>
			<td style="width:500px;"><?php echo $this->row->from_name; ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_FROM_EMAIL' ).':'; ?></label></td>
			<td style=""><?php echo $this->row->from_email; ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_MAIL_DATE' ).':'; ?></label></td>
			<td style=""><?php echo $dateUtils->formatDateAsConfigured($this->row->receive_timestamp); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_HAS_ATTACHMENTS' ).':'; ?></label></td>
			<td style=""><?php echo ($this->row->has_attachments == '1' ? JText::_( 'COM_MAILSTER_JYES' ) : JText::_( 'COM_MAILSTER_JNO' )); ?></td>
			<td>&nbsp;</td>
		</tr>
<?php 
	if($this->row->has_attachments == '1'){			
?>
		<tr>
			<td style="text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_ATTACHMENTS' ).':'; ?></label></td>
			<td style="">
				<?php
				$attachStr = '';
				$attachs = $this->row->attachments;
				for($i=0; $i < count($attachs); $i++){
					$attach = &$attachs[$i];
					if($attach->disposition == MstConsts::DISPOSITION_TYPE_ATTACH){
						$dwlLink = 'index.php?option=com_mailster&controller=mails&task=download&format=raw&attachId=' . $attach->id;
						$attachStr = $attachStr . '<a href="' . $dwlLink . '" >' . rawurldecode($attach->filename) . '</a><br/>';
					}
				} 
				echo $attachStr;
				?>
			</td>
			<td>&nbsp;</td>
		</tr>
<?php 
	}
?>
		<tr>
			<td style="text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_FORWARD_ERRORS' ).':'; ?></label></td>
			<td style=""><?php echo ($this->row->fwd_errors > 0 ? JText::_( 'COM_MAILSTER_JYES' ) . ' (' . $this->row->fwd_errors . ')' : JText::_( 'COM_MAILSTER_JNO' ));?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_FORWARD_COMPLETED' ).':'; ?></label><hr/></td>
			<td style=""><?php echo ($this->row->fwd_completed == '1' ? JText::_( 'COM_MAILSTER_JYES' )  : JText::_( 'COM_MAILSTER_JNO' )); ?> (<?php echo $dateUtils->formatDateAsConfigured($this->row->fwd_completed_timestamp); ?>)</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align:right;"><label><?php echo JText::_( 'COM_MAILSTER_SUBJECT' ).':'; ?></label></td>
			<td style=""><?php echo $this->row->subject; ?></td>
			<td>&nbsp;</td>
		</tr>	
		<tr style="min-height:110px;">
			<td style="width:150px;text-align:right;vertical-align:top;height:20px;"><label><?php echo JText::_( 'COM_MAILSTER_BODY' ).':'; ?></label></td>
			<td style="width:500px;min-height:100px;">
			<?php	
				if(is_null($this->row->html) || strlen(trim($this->row->html))<1){	
					$body =  nl2br($this->row->body); 
					$content = $body;
				}else{	
					$body =  $this->row->html;					
					$content = $mailUtils->getContentOfHtmlBody($body);
					$content = $mailUtils->replaceContentIdsWithAttachments($content, $this->row->attachments);
				}
				echo $content;
			?>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="mails" />
	<input type="hidden" name="view" value="mail" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="listID" value="<?php echo $this->listID; // when filter was active?>" /> 
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	</form>

	<?php
	//keep session alive while editing
	JHTML::_('behavior.keepalive');
	?>
