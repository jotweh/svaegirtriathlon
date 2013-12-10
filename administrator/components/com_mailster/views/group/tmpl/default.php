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
	?>
	<script language="javascript" type="text/javascript">
		function submitbutton(task)
		{
			validateSubmittedForm(task);
		}
		<?php
		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6 / 1.7 / ...
			echo 'Joomla.submitbutton = function(pressbutton) { validateSubmittedForm(pressbutton); }';					
		}
		?>
		function validateSubmittedForm(task)
		{
			var form = document.adminForm;
			// check we aren't cancelling
			if (task == 'cancel')
			{	// no need to validate, we are cancelling		
				submitform( task );
				return;
			}		
			if (form.name.value == ""){
				alert( <?php echo $mstUtils->jsonEncode(JText::_( 'COM_MAILSTER_ADD_GROUP_NAME')); ?> );
				form.name.focus();
			}else{
				submitform( task );
			}
			
		}
	</script>

	<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="adminform">
		<tr>
			<td valign="top">
				<table class="adminform" width="400px">
					<tr>
						<td width="50px">
							<label for="name">
								<?php echo JText::_( 'COM_MAILSTER_GROUP_NAME' ).':'; ?>
							</label>
						</td>
						<td width="100px">
							<input class="inputbox" name="name" value="<?php echo $this->row->name; ?>" size="50" maxlength="45" id="name" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_mailster" />
	<input type="hidden" name="controller" value="groups" />
	<input type="hidden" name="view" value="group" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	</form>

	<?php
	//keep session alive while editing
	JHTML::_('behavior.keepalive');
	?>
